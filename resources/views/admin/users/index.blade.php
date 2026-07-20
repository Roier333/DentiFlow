@extends('layouts.app')

@section('title', 'Usuarios del sistema')

@section('content')
@php
    $roleOptions = [
        'doctor' => 'Doctor',
        'asistente' => 'Asistente',
        'admin' => 'Administrador',
    ];

    $roleBadges = [
        'doctor' => 'bg-[#f6e8ff] text-[#9700cf]',
        'asistente' => 'bg-violet-50 text-violet-700',
        'admin' => 'bg-slate-900 text-white',
    ];
@endphp
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold text-[#9700cf]">Administración</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Usuarios del sistema</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Consulta las cuentas existentes y administra sus accesos.</p>
        </div>
    </section>

    @include('partials.flash-success')
    @if($errors->any())
        <div class="rounded-xl border border-fuchsia-200 bg-fuchsia-50 px-4 py-3 text-sm font-semibold text-fuchsia-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_4px_24px_rgba(30,34,60,.05)]">
        <div class="border-b border-slate-100 p-5">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col gap-3 sm:flex-row">
                <input type="search" name="search" value="{{ $search }}" placeholder="Buscar por nombre, correo o rol" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[#9700cf] focus:ring-4 focus:ring-[#ead2fb]">
                <button class="rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#8200b8]">Buscar</button>
                @if($search !== '')
                    <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">Correo</th>
                        <th class="px-5 py-3">Rol</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $user->name }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $user->email }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $roleBadges[$user->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roleOptions[$user->role] ?? 'Sin rol activo' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-semibold text-[#9700cf] hover:text-[#8200b8]">Editar</a>
                                    <button type="button" onclick="document.getElementById('delete-user-{{ $user->id }}')?.showModal()" class="text-sm font-semibold text-fuchsia-600 hover:text-fuchsia-700">Eliminar</button>
                                </div>
                                <dialog id="delete-user-{{ $user->id }}" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-3xl p-0 shadow-2xl backdrop:bg-slate-950/50">
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="bg-white">
                                        @csrf
                                        @method('DELETE')
                                        <div class="border-b border-slate-100 px-6 py-5">
                                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#9700cf]">Eliminar usuario</p>
                                            <h2 class="mt-1 text-2xl font-bold text-slate-900">Confirmar eliminación</h2>
                                            <p class="mt-2 text-sm text-slate-500">Este usuario ya no podrá acceder al sistema.</p>
                                        </div>
                                        <div class="px-6 py-5">
                                            <div class="rounded-2xl bg-slate-50 p-4 text-left">
                                                <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                                            <button type="button" onclick="this.closest('dialog')?.close()" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600">Cancelar</button>
                                            <button class="rounded-xl bg-[#9700cf] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#d3a2f2]">Confirmar</button>
                                        </div>
                                    </form>
                                </dialog>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400">No hay usuarios que coincidan con la búsqueda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    </section>
</div>
@endsection

