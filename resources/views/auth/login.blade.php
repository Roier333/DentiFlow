<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - DentiFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#fbf0ff] font-['DM_Sans'] text-[#21152e] antialiased">
<main class="min-h-screen lg:grid lg:grid-cols-[minmax(360px,42%)_1fr]">
    <section class="relative overflow-hidden bg-[#512569] px-6 py-8 text-white sm:px-10 lg:min-h-screen lg:px-12 lg:py-10">
        <div class="absolute inset-0 opacity-30" style="background-image: linear-gradient(135deg, rgba(234,210,251,.22) 0 1px, transparent 1px), linear-gradient(45deg, rgba(151,0,207,.18) 0 1px, transparent 1px); background-size: 42px 42px;"></div>
        <div class="absolute inset-x-0 bottom-0 h-56 bg-gradient-to-t from-[#21152e] to-transparent"></div>

        <div class="relative z-10 flex min-h-[520px] flex-col lg:min-h-[calc(100vh-5rem)]">
            <header class="flex items-center">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#9700cf] shadow-lg shadow-[#21152e]/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7.3 3.7c1.8-.8 3 .3 4.7.3s2.9-1.1 4.7-.3c2.6 1.2 3 4.8 1.9 7.3-.9 2.1-1.4 3.4-1.6 6.6-.1 1.7-.9 3.4-2.1 3.4-1.7 0-1.2-4.8-2.9-4.8S10.8 21 9.1 21C7.9 21 7.1 19.3 7 17.6c-.2-3.2-.7-4.5-1.6-6.6C4.3 8.5 4.7 4.9 7.3 3.7Z"/></svg>
                    </span>
                    <div>
                        <p class="text-base font-bold leading-none">DentiFlow</p>
                        <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.22em] text-white/45">Gestión clínica</p>
                    </div>
                </div>
            </header>

            <div class="flex flex-1 flex-col justify-center py-14 lg:py-16">
                <div class="mx-auto max-w-lg text-left">
                <h1 class="text-5xl font-bold leading-tight tracking-tight sm:text-6xl">Plataforma centralizada de gestión</h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-white/70">Autentíquese con sus credenciales autorizadas, para habilitar de manera automática las funciones de su rol.</p>
                </div>
            </div>

        </div>
    </section>

    <section class="flex min-h-screen items-center px-5 py-8 sm:px-8 lg:h-screen lg:min-h-0 lg:overflow-hidden lg:px-12 lg:py-6">
        <div class="mx-auto w-full max-w-2xl">
            @if(session('warning'))
                <div class="mb-6 rounded-2xl border border-fuchsia-200 bg-fuchsia-50 px-4 py-3 text-sm font-semibold text-fuchsia-700">{{ session('warning') }}</div>
            @endif
            @include('partials.flash-success')

            <div>
                <form method="POST" action="{{ route('login') }}" class="rounded-[1.35rem] border border-[#ead2fb] bg-white p-6 shadow-[0_18px_60px_rgba(81,37,105,.08)] sm:p-7">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-6 flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ead2fb] text-[#9700cf]">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M5 22v-3a7 7 0 0 1 14 0v3"/></svg>
                        </span>
                        <div>
                            <p class="text-2xl font-bold text-[#21152e]">Iniciar sesión</p>

                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="email" class="mb-2.5 block text-base font-semibold text-slate-700">Correo electrónico</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nombre@clinica.com" autocomplete="email" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-base outline-none transition focus:border-[#9700cf] focus:ring-4 focus:ring-[#ead2fb]">
                        </div>

                        <div>
                            <div class="mb-2.5 flex justify-between gap-3">
                                <label for="password" class="text-base font-semibold text-slate-700">Contraseña</label>
                                <a id="recover-password" href="{{ route('password.forgot') }}" class="text-sm font-semibold text-[#9700cf]">¿Olvidaste tu contraseña?</a>
                            </div>
                            <div class="relative">
                                <input id="password" type="password" name="password" placeholder="********" autocomplete="current-password" required class="w-full rounded-xl border border-slate-200 bg-white py-3.5 pl-4 pr-12 text-base outline-none transition focus:border-[#9700cf] focus:ring-4 focus:ring-[#ead2fb]">
                                <button id="toggle-password" type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-[#9700cf] focus:outline-none" aria-label="Mostrar contraseña" aria-pressed="false">
                                    <svg id="eye-open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                                        <circle cx="12" cy="12" r="2.5"/>
                                    </svg>
                                    <svg id="eye-closed" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="m3 3 18 18"/>
                                        <path d="M10.6 6.2A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a16.3 16.3 0 0 1-2.1 2.8M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 6 9.5 6a9.8 9.8 0 0 0 3.2-.5"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-[#9700cf] py-3.5 text-base font-bold text-white shadow-lg shadow-[#d3a2f2] transition hover:bg-[#512569]">Entrar a DentiFlow</button>
                    </div>

                    <div class="mt-5 rounded-2xl border border-[#ead2fb] bg-[#fbf0ff] px-4 py-3.5 text-center">
                        <p class="text-sm font-semibold text-[#6b5778]">¿Aún no tienes cuenta?</p>
                        <a href="{{ route('register') }}" class="mt-1 inline-flex text-base font-bold text-[#9700cf] transition hover:text-[#512569]">Crear cuenta</a>
                    </div>
                </form>
            </div>

            <footer class="mt-4 flex flex-col gap-2 text-sm text-[#8a7399] sm:flex-row sm:items-center sm:justify-between">
                <span>© {{ date('Y') }} DentiFlow</span>
                <span>Acceso para personal autorizado</span>
            </footer>
        </div>
    </section>
</main>
@if(session('logout_success'))
    <div id="logout-toast" role="status" aria-live="polite" class="fixed right-5 top-5 z-50 flex max-w-sm items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800 shadow-xl transition duration-500">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
        </span>
        <span>{{ session('logout_success') }}</span>
    </div>
@endif
<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    togglePassword.addEventListener('click', () => {
        const passwordIsVisible = passwordInput.type === 'text';

        passwordInput.type = passwordIsVisible ? 'password' : 'text';
        eyeOpen.classList.toggle('hidden', !passwordIsVisible);
        eyeClosed.classList.toggle('hidden', passwordIsVisible);
        togglePassword.setAttribute('aria-label', passwordIsVisible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        togglePassword.setAttribute('aria-pressed', String(!passwordIsVisible));
    });

    const recoverLink = document.getElementById('recover-password');
    const emailInput = document.getElementById('email');

    recoverLink.addEventListener('click', (event) => {
        const emailValue = emailInput.value.trim();
        if (!emailValue) {
            return;
        }

        const url = new URL(recoverLink.href, window.location.origin);
        url.searchParams.set('email', emailValue);
        recoverLink.href = url.toString();
    });

    const logoutToast = document.getElementById('logout-toast');
    if (logoutToast) {
        window.setTimeout(() => {
            logoutToast.classList.add('translate-y-2', 'opacity-0');
            window.setTimeout(() => logoutToast.remove(), 500);
        }, 4000);
    }
</script>
</body>
</html>
