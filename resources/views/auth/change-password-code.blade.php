<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña - DentiFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#fbf0ff] font-['DM_Sans'] text-[#21152e] antialiased">
<main class="flex min-h-screen items-center justify-center px-5 py-10">
    <section class="w-full max-w-md rounded-[1.35rem] border border-[#ead2fb] bg-white p-6 shadow-[0_18px_60px_rgba(81,37,105,.08)]">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-[#ad79d3]">Cambiar contraseña</p>
                <h1 class="text-3xl font-bold text-[#21152e]">Nueva contraseña</h1>
                <p class="mt-2 text-sm leading-6 text-[#6b5778]">Ingresa tu nueva contraseña. El acceso solo está permitido después de validar el código.</p>
            </div>
            <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300">Volver al inicio</a>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="mb-5 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-700">{{ session('warning') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-fuchsia-200 bg-fuchsia-50 px-4 py-3 text-sm font-semibold text-fuchsia-700">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.forgot.change') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-semibold text-slate-600">Nueva contraseña
                <input type="password" name="password" minlength="8" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#9700cf] focus:ring-4 focus:ring-[#ead2fb]" placeholder="********">
            </label>
            <label class="block text-sm font-semibold text-slate-600">Confirmar contraseña
                <input type="password" name="password_confirmation" minlength="8" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#9700cf] focus:ring-4 focus:ring-[#ead2fb]" placeholder="********">
            </label>
            <button type="submit" class="w-full rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#512569]">Actualizar contraseña</button>
        </form>
    </section>
</main>
</body>
</html>
