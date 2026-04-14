<?php

namespace App\Livewire\Admin;

use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class NewsManager extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public bool $is_published = false;

    public ?string $published_at = null;

    public function updatedTitle(string $value): void
    {
        if ($this->editingId === null && $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $validated['slug'] !== '' ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['slug'] = $this->resolveUniqueSlug($slug, $this->editingId);

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if (! $validated['is_published']) {
            $validated['published_at'] = null;
        }

        $record = News::updateOrCreate(
            ['id' => $this->editingId],
            $validated
        );

        $record->refresh();

        $this->resetForm();

        if (! $record->is_published) {
            session()->flash(
                'status',
                'Aktualita byla uložena jako koncept. Na úvodní stránce se nezobrazí, dokud nezaškrtnete „Publikováno“ (nebo v seznamu vpravo kliknete na „Publikovat“).'
            );
        } elseif ($record->published_at && $record->published_at->isFuture()) {
            session()->flash(
                'status',
                'Aktualita je uložená, ale na úvodní stránce se zobrazí až od '.$record->published_at->format('d.m.Y H:i').' (pole „Publikovat od“ je v budoucnosti). Do té doby ji uvidíte jen tady v administraci.'
            );
        } else {
            session()->flash(
                'status',
                'Aktualita je publikovaná a měla by být vidět na úvodní stránce v sekci Aktuality (případně obnovte stránku Ctrl+F5).'
            );
        }
    }

    public function edit(int $id): void
    {
        $news = News::findOrFail($id);
        $this->editingId = $news->id;
        $this->title = $news->title;
        $this->slug = $news->slug;
        $this->excerpt = $news->excerpt ?? '';
        $this->content = $news->content;
        $this->seo_title = $news->seo_title ?? '';
        $this->seo_description = $news->seo_description ?? '';
        $this->is_published = (bool) $news->is_published;
        $this->published_at = $news->published_at?->format('Y-m-d\TH:i');
    }

    public function delete(int $id): void
    {
        News::whereKey($id)->delete();
        $this->resetPage();
        session()->flash('status', 'Aktualita byla smazána.');
    }

    public function togglePublished(int $id): void
    {
        $news = News::findOrFail($id);
        $news->is_published = ! $news->is_published;
        $news->published_at = $news->is_published ? ($news->published_at ?? Carbon::now()) : null;
        $news->save();
    }

    public function cancelEditing(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'title',
            'slug',
            'excerpt',
            'content',
            'seo_title',
            'seo_description',
            'is_published',
            'published_at',
        ]);
    }

    private function resolveUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'aktualita';
        $candidate = $base;
        $counter = 1;

        while (
            News::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    public function render()
    {
        return view('livewire.admin.news-manager', [
            'newsItems' => News::query()->latest()->paginate(10),
        ])->layout('layouts.app');
    }
}
