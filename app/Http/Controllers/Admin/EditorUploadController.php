<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EditorUploadController extends Controller
{
    /**
     * Obrázky pro Markdown editor (EasyMDE) — ukládá do veřejného disku /storage.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $upload = $request->file('image') ?? $request->file('file');

        $validated = Validator::make(
            ['upload' => $upload],
            ['upload' => ['required', 'file', 'image', 'max:10240']]
        )->validate();

        $path = $validated['upload']->store('editor-uploads', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'data' => [
                'filePath' => $url,
            ],
        ]);
    }
}
