@extends('layouts.members')

@section('title', 'Pro členky sboru | Arytmie Praha')

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <section class="border-t border-slate-700/80 bg-gradient-to-b from-slate-800 to-slate-900">
        <div class="mx-auto max-w-lg px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">Pro členky sboru</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-white">Členská sekce</h1>
            <p class="mt-3 text-sm leading-6 text-slate-400">
                Zadejte společné heslo od vedení sboru. Nejde o přihlášení k účtu — stačí jedno heslo pro všechny.
            </p>

            <form method="post" action="{{ route('members.unlock') }}" class="mt-10 space-y-6">
                @csrf
                <div>
                    <label for="members-password" class="block text-sm font-medium text-slate-200">Heslo</label>
                    <input
                        id="members-password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="mt-2 block w-full rounded-xl border border-slate-600 bg-slate-900/80 px-4 py-3 text-sm text-white outline-none ring-orange-400/30 transition placeholder:text-slate-500 focus:border-orange-400 focus:ring-2"
                        autofocus
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    class="w-full rounded-full bg-orange-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600"
                >
                    Otevřít sekci
                </button>
            </form>
        </div>
    </section>
@endsection
