@extends('layouts.app')

@section('title', 'Historial de citas')

@section('content')
<div class="space-y-6">
    <header class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-4xl font-bold text-slate-900">Historial de citas</h1>
            <p class="mt-2 text-sm text-slate-500">{{ session('access_role') === 'doctor' ? 'Consulta y recupera tus citas canceladas.' : 'Consulta el historial del consultorio y recupera citas canceladas.' }}</p>
        </div>
        <a href="{{ route('citas') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-600">&larr; Volver a citas</a>
    </header>

    @include('partials.flash-success')
    @if($errors->any())
        <div class="rounded-xl border border-fuchsia-200 bg-fuchsia-50 p-4 text-sm font-semibold text-fuchsia-700">{{ $errors->first() }}</div>
    @endif

    <form method="GET" class="grid gap-3 rounded-2xl border border-[#ead2fb] bg-white p-4 shadow-sm sm:grid-cols-[1fr_220px_auto]">
        <input type="search" name="search" value="{{ $search }}" placeholder="Paciente, doctor o tratamiento" class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#9700cf]">
        <select name="estado" class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#9700cf]">
            @foreach(['Todos', 'Cancelada', 'Terminada'] as $option)
                <option value="{{ $option }}" @selected($estado === $option)>{{ $option === 'Todos' ? 'Canceladas y terminadas' : $option }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white">Filtrar</button>
    </form>

    <section class="overflow-hidden rounded-2xl border border-[#ead2fb] bg-white shadow-[0_12px_40px_rgba(81,37,105,.08)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="bg-[#9700cf] text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th class="px-5 py-4">N.&ordm; de cita</th>
                        <th class="px-5 py-4">Paciente</th>
                        <th class="px-5 py-4">Doctor</th>
                        <th class="px-5 py-4">Fecha y hora</th>
                        <th class="px-5 py-4">Tratamiento</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($citas as $cita)
                        <tr class="text-sm text-slate-600 hover:bg-[#fbf0ff]/50">
                            <td class="px-5 py-4 font-bold text-slate-700">#{{ $cita->id }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $cita->patient?->nombre ?? 'Sin paciente' }}</td>
                            <td class="px-5 py-4">{{ $cita->dentist?->nombre ?? 'Sin doctor' }}</td>
                            <td class="px-5 py-4">{{ $cita->date->format('d/m/Y') }} &middot; {{ date('H:i', strtotime($cita->time)) }}</td>
                            <td class="px-5 py-4">{{ $cita->tipo }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $cita->estado === 'Cancelada' ? 'bg-fuchsia-100 text-fuchsia-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $cita->estado }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-center">
                                    <button type="button" popovertarget="appointment-history-actions-{{ $cita->id }}" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]" aria-label="Acciones de cita #{{ $cita->id }}">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.7" /><circle cx="12" cy="12" r="1.7" /><circle cx="18" cy="12" r="1.7" /></svg>
                                    </button>
                                    <div id="appointment-history-actions-{{ $cita->id }}" popover class="w-56 overflow-hidden rounded-2xl border border-[#ead2fb] bg-white p-0 text-left shadow-2xl">
                                        <div class="border-b border-slate-100 px-5 py-4">
                                            <p class="font-bold text-slate-800">Acciones</p>
                                            <p class="mt-1 truncate text-xs text-slate-400">Cita #{{ $cita->id }}</p>
                                        </div>
                                        <div class="space-y-1 p-2 text-sm font-semibold">
                                    <a href="{{ route('citas.show', $cita->id) }}" class="block rounded-xl px-3 py-2.5 text-[#9700cf] transition hover:bg-[#f6e8ff]">Detalles</a>
                                    @if($cita->estado === 'Cancelada')
                                        <form method="POST" action="{{ route('citas.restaurar', $cita->id) }}" onsubmit="return confirm('¿Restaurar esta cita como pendiente?');">
                                            @csrf
                                            <button class="block w-full rounded-xl px-3 py-2.5 text-left text-slate-600 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]">Restaurar</button>
                                        </form>
                                    @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-14 text-center text-sm text-slate-400">No hay citas en el historial con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($citas->hasPages())<div class="border-t border-slate-100 p-4">{{ $citas->links() }}</div>@endif
    </section>
</div>
@endsection

