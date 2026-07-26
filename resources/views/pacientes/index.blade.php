@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')

@php
    $pacientesCollection = $pacientes instanceof \Illuminate\Pagination\AbstractPaginator
        ? collect($pacientes->items())
        : collect($pacientes);
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9700cf]/70">Directorio clínico</p>
            <h1 class="mt-2 text-4xl font-bold text-slate-900">Pacientes</h1>
            <p class="mt-2 max-w-2xl text-slate-500">
                Registra pacientes y consulta sus datos principales.
            </p>
        </div>

        <a href="{{ route('pacientes.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#9700cf] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#512569]">
            Nuevo paciente
        </a>
    </div>

    @include('partials.flash-success')

    <form action="{{ route('pacientes') }}" method="GET" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[minmax(220px,1.5fr)_minmax(170px,0.9fr)_auto] lg:items-end">
            <label class="block">
                <span class="text-sm font-medium text-slate-600">Buscar</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nombre, telefono o correo"
                    class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 outline-none transition focus:border-[#9700cf] focus:ring-2 focus:ring-[#9700cf]/20">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-600">Ordenar</span>
                <select name="order" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-700 outline-none transition focus:border-[#9700cf] focus:ring-2 focus:ring-[#9700cf]/20">
                    <option value="visita_desc" {{ request('order', 'visita_desc') === 'visita_desc' ? 'selected' : '' }}>Visita reciente</option>
                    <option value="visita_asc" {{ request('order') === 'visita_asc' ? 'selected' : '' }}>Visita antigua</option>
                    <option value="nombre_asc" {{ request('order') === 'nombre_asc' ? 'selected' : '' }}>Nombre A-Z</option>
                    <option value="nombre_desc" {{ request('order') === 'nombre_desc' ? 'selected' : '' }}>Nombre Z-A</option>
                </select>
            </label>

            <div class="flex gap-3">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#9700cf] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#512569] lg:flex-none">
                    Aplicar
                </button>

                @if(count(request()->query()) > 0)
                    <a href="{{ route('pacientes') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Directorio de pacientes</h2>
                <p class="text-sm text-slate-500">Mostrando {{ $pacientesCollection->count() }} resultado{{ $pacientesCollection->count() === 1 ? '' : 's' }}</p>
            </div>
            <a href="{{ route('pacientes.create') }}" class="text-sm font-semibold text-[#9700cf] hover:text-[#512569]">
                Agregar paciente
            </a>
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-[860px] text-left">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Paciente</th>
                        <th class="px-5 py-4 font-semibold">Teléfono</th>
                        <th class="px-5 py-4 font-semibold">Ultima visita</th>
                        <th class="px-5 py-4 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pacientes as $paciente)
                        @php
                            $segments = explode(' ', trim($paciente['nombre'] ?? ''));
                            $initials = collect($segments)->filter()->map(fn($segment) => strtoupper(substr($segment, 0, 1)))->take(2)->join('');
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#ead2fb] text-sm font-bold text-[#9700cf] ring-1 ring-purple-200">
                                        {{ $initials ?: 'PX' }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $paciente['nombre'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">Expediente disponible</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4"><a href="tel:{{ preg_replace('/\D+/', '', $paciente['telefono']) }}" class="inline-flex items-center gap-2 font-semibold text-[#9700cf] hover:text-[#512569]" aria-label="Llamar a {{ $paciente['nombre'] }}">{{ $paciente['telefono'] }}</a></td>
                            <td class="px-5 py-4 text-slate-600">{{ $paciente->ultima_visita?->format('d/m/Y') ?? 'Nuevo' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-center">
                                    <button type="button" popovertarget="patient-actions-desktop-{{ $paciente['id'] }}" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]" aria-label="Acciones de {{ $paciente['nombre'] }}">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.7" /><circle cx="12" cy="12" r="1.7" /><circle cx="18" cy="12" r="1.7" /></svg>
                                    </button>
                                    <div id="patient-actions-desktop-{{ $paciente['id'] }}" popover class="w-64 overflow-hidden rounded-2xl border border-[#ead2fb] bg-white p-0 text-left shadow-2xl">
                                        <div class="border-b border-slate-100 px-5 py-4">
                                            <p class="font-bold text-slate-800">Acciones</p>
                                            <p class="mt-1 truncate text-xs text-slate-400">{{ $paciente['nombre'] }}</p>
                                        </div>
                                        <div class="space-y-1 p-2 text-sm font-semibold">
                                            <a href="{{ route('pacientes.edit', ['paciente' => $paciente['id'] ?? $paciente['nombre']]) }}" class="block rounded-xl px-3 py-2.5 text-slate-600 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]">Editar</a>
                                            <button type="button" data-delete-url="{{ route('pacientes.destroy', ['paciente' => $paciente['id'] ?? $paciente['nombre']]) }}" data-patient-name="{{ $paciente['nombre'] }}" class="js-delete-patient block w-full text-left rounded-xl px-3 py-2.5 text-fuchsia-600 transition hover:bg-fuchsia-50">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-5 py-12 text-center text-slate-500" colspan="4">
                                No se encontraron pacientes con los filtros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($pacientes as $paciente)
                @php
                    $segments = explode(' ', trim($paciente['nombre'] ?? ''));
                    $initials = collect($segments)->filter()->map(fn($segment) => strtoupper(substr($segment, 0, 1)))->take(2)->join('');
                @endphp
                <article class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#ead2fb] text-sm font-bold text-[#9700cf] ring-1 ring-purple-200">
                                {{ $initials ?: 'PX' }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-semibold text-slate-900">{{ $paciente['nombre'] }}</h3>
                                <a href="tel:{{ preg_replace('/\D+/', '', $paciente['telefono']) }}" class="mt-1 inline-flex text-sm font-semibold text-[#9700cf]">{{ $paciente['telefono'] }}</a>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <button type="button" popovertarget="patient-actions-mobile-{{ $paciente['id'] }}" class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]" aria-label="Acciones de {{ $paciente['nombre'] }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="6" cy="12" r="1.7" /><circle cx="12" cy="12" r="1.7" /><circle cx="18" cy="12" r="1.7" /></svg>
                            </button>
                            <div id="patient-actions-mobile-{{ $paciente['id'] }}" popover class="w-64 overflow-hidden rounded-2xl border border-[#ead2fb] bg-white p-0 text-left shadow-2xl">
                                <div class="border-b border-slate-100 px-5 py-4">
                                    <p class="font-bold text-slate-800">Acciones</p>
                                    <p class="mt-1 truncate text-xs text-slate-400">{{ $paciente['nombre'] }}</p>
                                </div>
                                <div class="space-y-1 p-2 text-sm font-semibold">
                                    <a href="{{ route('pacientes.edit', ['paciente' => $paciente['id'] ?? $paciente['nombre']]) }}" class="block rounded-xl px-3 py-2.5 text-slate-600 transition hover:bg-[#f6e8ff] hover:text-[#9700cf]">Editar</a>
                                    <button type="button" data-delete-url="{{ route('pacientes.destroy', ['paciente' => $paciente['id'] ?? $paciente['nombre']]) }}" data-patient-name="{{ $paciente['nombre'] }}" class="js-delete-patient block w-full text-left rounded-xl px-3 py-2.5 text-fuchsia-600 transition hover:bg-fuchsia-50">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Ultima visita</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ $paciente->ultima_visita?->format('d/m/Y') ?? 'Nuevo' }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <div class="px-5 py-12 text-center text-slate-500">
                    No se encontraron pacientes con los filtros actuales.
                </div>
            @endforelse
        </div>
    </div>

    @if(is_object($pacientes) && method_exists($pacientes, 'links'))
        <div class="rounded-lg bg-white p-4 shadow-sm">
            {{ $pacientes->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<!-- Delete confirmation modal -->
<div id="delete-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/50"></div>
    <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900">Confirmar eliminación</h3>
        <p id="delete-confirm-message" class="mt-3 text-sm text-slate-600">¿Eliminar paciente?</p>
        <div class="mt-6 flex justify-end gap-3">
            <button id="delete-cancel" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700">Cancelar</button>
            <button id="delete-confirm" class="rounded-lg bg-fuchsia-600 px-4 py-2 text-sm font-semibold text-white">Eliminar</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('delete-confirm-modal');
    const msg = document.getElementById('delete-confirm-message');
    const btnCancel = document.getElementById('delete-cancel');
    const btnConfirm = document.getElementById('delete-confirm');
    let pending = null; // { url, btn }

    function showModal(patientName, url, btn) {
        pending = { url, btn };
        msg.textContent = `¿Confirmas eliminar al paciente "${patientName}"? Esta acción no se puede deshacer.`;
        modal.classList.remove('hidden');
    }

    function hideModal() {
        pending = null;
        modal.classList.add('hidden');
    }

    document.addEventListener('click', (e) => {
        const el = e.target.closest('.js-delete-patient');
        if (!el) return;
        e.preventDefault();
        const url = el.getAttribute('data-delete-url');
        const patientName = el.getAttribute('data-patient-name') || 'Paciente';
        if (!url) return;
        showModal(patientName, url, el);
    });

    btnCancel.addEventListener('click', (e) => {
        e.preventDefault();
        hideModal();
    });

    btnConfirm.addEventListener('click', async (e) => {
        e.preventDefault();
        if (!pending) return hideModal();

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const res = await fetch(pending.url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) {
                const json = await res.json().catch(() => ({}));
                throw new Error(json.message || 'No se pudo eliminar el paciente.');
            }

            const data = await res.json().catch(() => ({}));
            const tr = pending.btn.closest('tr');
            const article = pending.btn.closest('article');
            if (tr) tr.remove();
            else if (article) article.remove();
            else {
                location.reload();
            }

            showToast(data.message || 'Paciente eliminado correctamente.');
        } catch (err) {
            showToast(err.message || 'No se pudo eliminar el paciente.');
        } finally {
            hideModal();
        }
    });

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed right-4 top-4 z-50 rounded-lg bg-slate-800 px-4 py-2 text-sm text-white shadow-lg';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3500);
    }
})();
</script>
@endpush
