@extends('layouts.app')

@section('title', 'Seguridad de la cuenta')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-[#9700cf]">Seguridad</p>
            <h1 class="mt-1 text-4xl font-bold text-slate-900">Cambiar contrase&ntilde;a</h1>
            <p class="mt-2 text-sm text-slate-500">Elige el m&eacute;todo que puedas verificar.</p>
        </div>
        <a href="{{ route('perfil') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600">&larr; Volver al perfil</a>
    </header>

    @include('partials.flash-success')
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-[#ead2fb] bg-white p-6 shadow-[0_18px_60px_rgba(81,37,105,.08)]">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#ead2fb] text-[#9700cf]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 11h14v10H5zM8 11V7a4 4 0 0 1 8 0v4"/></svg>
            </span>
            <h2 class="mt-4 text-xl font-bold text-slate-800">Recuerdo mi contrase&ntilde;a</h2>
            <p class="mt-2 text-sm text-slate-500">Confirma la contrase&ntilde;a actual antes de establecer una nueva.</p>
            <form method="POST" action="{{ route('perfil.password.update') }}" class="mt-6 space-y-4">
                @csrf
                <label class="block text-sm font-semibold text-slate-600">Contrase&ntilde;a actual
                    <input type="password" name="current_password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                </label>
                <label class="block text-sm font-semibold text-slate-600">Nueva contrase&ntilde;a
                    <input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                </label>
                <label class="block text-sm font-semibold text-slate-600">Confirmar nueva contrase&ntilde;a
                    <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                </label>
                <button class="w-full rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white">Actualizar contrase&ntilde;a</button>
            </form>
        </section>

        <section class="rounded-3xl border border-[#ead2fb] bg-white p-6 shadow-[0_18px_60px_rgba(81,37,105,.08)]">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#ead2fb] text-[#9700cf]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
            </span>
            <h2 class="mt-4 text-xl font-bold text-slate-800">No recuerdo mi contrase&ntilde;a</h2>
            <p class="mt-2 text-sm text-slate-500">Recibe un c&oacute;digo de 6 d&iacute;gitos. Expira en 10 minutos y funciona una sola vez.</p>
            <form method="POST" action="{{ route('perfil.password.code.send') }}" class="mt-6 space-y-4">
                @csrf
                <label class="block text-sm font-semibold text-slate-600">Correo de la cuenta
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                </label>
                <button class="w-full rounded-xl border border-[#9700cf] px-5 py-3 text-sm font-bold text-[#9700cf]">Enviar c&oacute;digo</button>
            </form>

            @if(session('code_sent') || session()->has('password_otp_hash'))
                <form method="POST" action="{{ route('perfil.password.code.reset') }}" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email', $usuario->email) }}">
                    <label class="block text-sm font-semibold text-slate-600">C&oacute;digo de un solo uso
                        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required placeholder="000000" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-xl font-bold tracking-[.35em] outline-none focus:border-[#9700cf]">
                    </label>
                    <label class="block text-sm font-semibold text-slate-600">Nueva contrase&ntilde;a
                        <input type="password" name="password" minlength="8" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                    </label>
                    <label class="block text-sm font-semibold text-slate-600">Confirmar contrase&ntilde;a
                        <input type="password" name="password_confirmation" minlength="8" required class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-[#9700cf]">
                    </label>
                    <button class="w-full rounded-xl bg-[#512569] px-5 py-3 text-sm font-bold text-white">Verificar y cambiar</button>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection

