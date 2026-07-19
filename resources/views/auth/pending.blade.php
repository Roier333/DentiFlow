<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta en revision - DentiFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
@php
    $name = session('pending_name');
    $email = session('pending_email');
@endphp
<body class="min-h-screen bg-[#fbf0ff] font-['DM_Sans'] text-[#21152e] antialiased">
<main class="flex min-h-screen items-center justify-center px-5 py-10">
    <section class="w-full max-w-3xl overflow-hidden rounded-[1.35rem] border border-[#ead2fb] bg-white shadow-[0_24px_80px_rgba(81,37,105,.12)]">
        <div class="bg-[#512569] px-6 py-8 text-white sm:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#9700cf] shadow-lg shadow-[#21152e]/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7.3 3.7c1.8-.8 3 .3 4.7.3s2.9-1.1 4.7-.3c2.6 1.2 3 4.8 1.9 7.3-.9 2.1-1.4 3.4-1.6 6.6-.1 1.7-.9 3.4-2.1 3.4-1.7 0-1.2-4.8-2.9-4.8S10.8 21 9.1 21C7.9 21 7.1 19.3 7 17.6c-.2-3.2-.7-4.5-1.6-6.6C4.3 8.5 4.7 4.9 7.3 3.7Z"/></svg>
                </span>
                <div>
                    <p class="text-base font-bold leading-none">DentiFlow</p>
                    <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.22em] text-white/45">Cuenta recibida</p>
                </div>
            </div>

            <p class="mt-10 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-[#ead2fb]">Revision pendiente</p>
            <h1 class="mt-5 max-w-2xl text-3xl font-bold leading-tight sm:text-4xl">Tu cuenta esta esperando autorizacion.</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/65">Para proteger la información clínica, una persona encargada debe confirmar tus datos y asignarte el rol correcto antes de permitir el acceso al sistema.</p>
        </div>

        <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_260px]">
            <div class="space-y-4">
                @if($name || $email)
                    <div class="rounded-2xl border border-[#ead2fb] bg-[#fbf0ff] p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#ad79d3]">Solicitud registrada</p>
                        @if($name)<p class="mt-2 text-lg font-bold text-[#21152e]">{{ $name }}</p>@endif
                        @if($email)<p class="mt-1 text-sm font-semibold text-[#6b5778]">{{ $email }}</p>@endif
                    </div>
                @endif

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h2 class="font-bold text-slate-900">Motivo de la espera</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Las cuentas nuevas no reciben permisos automaticamente. Administracion revisara si debes entrar como doctor, asistente o administrador, y despues activara tu acceso.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h2 class="font-bold text-slate-900">Que sigue</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Cuando tu rol sea asignado, podras iniciar sesion con el mismo correo y contrasena que acabas de registrar.</p>
                </div>
            </div>

            <aside class="space-y-3">
                <div class="rounded-2xl border border-[#ead2fb] bg-[#f6e8ff] p-5">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#9700cf]">Estado</p>
                    <p class="mt-3 text-2xl font-bold text-[#512569]">Pendiente</p>
                    <p class="mt-2 text-xs leading-5 text-[#6b5778]">Tu cuenta ya existe, solo falta que sea aprobada.</p>
                </div>

                <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#d3a2f2] transition hover:bg-[#512569]">Volver al login</a>
            </aside>
        </div>
    </section>
</main>
</body>
</html>
