<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $users = User::with('dentist')
            ->whereIn('role', ['pendiente', 'doctor', 'asistente'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhereHas('dentist', fn ($dentistQuery) => $dentistQuery->where('nombre', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        $pendingUsers = User::query()
            ->where('role', 'pendiente')
            ->orderBy('created_at')
            ->get();

        $summary = [
            'total' => User::count(),
            'superadmins' => User::where('role', 'superadmin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'doctors' => User::where('role', 'doctor')->count(),
            'assistants' => User::where('role', 'asistente')->count(),
            'pending' => User::where('role', 'pendiente')->count(),
        ];

        return view('admin.users.index', compact('users', 'pendingUsers', 'summary', 'search'));
    }

    public function edit(User $user)
    {
        $this->ensureOperationalUser($user);
        $user->load('dentist');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureOperationalUser($user);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['pendiente', 'doctor', 'asistente'])],
        ]);

        $user->update([
            ...$data,
            'requested_role' => $data['role'] === 'pendiente'
                ? $user->requested_role
                : null,
        ]);

        if ($user->role === 'doctor') {
            $user->dentist()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nombre' => $data['name'],
                    'telefono' => $user->telefono ?: 'No registrado',
                    'especialidad' => $user->puesto ?: 'Odontologia general',
                ]
            );
        } elseif ($user->dentist) {
            $user->dentist->update(['nombre' => $data['name']]);
        }

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado correctamente.');
    }

    public function approve(User $user)
    {
        $this->ensureOperationalUser($user);

        if ($user->role !== 'pendiente') {
            return redirect()
                ->route('admin.users')
                ->withErrors(['user' => 'Esta cuenta ya fue revisada.']);
        }

        if (! in_array($user->requested_role, ['doctor', 'asistente'], true)) {
            return redirect()
                ->route('admin.users.edit', $user)
                ->withErrors(['role' => 'La solicitud no tiene un perfil válido. Selecciona el rol manualmente.']);
        }

        $approvedRole = $user->requested_role;
        $user->update([
            'role' => $approvedRole,
            'requested_role' => null,
        ]);

        if ($approvedRole === 'doctor') {
            $user->dentist()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nombre' => $user->name,
                    'telefono' => $user->telefono ?: 'No registrado',
                    'especialidad' => $user->puesto ?: 'Odontologia general',
                ]
            );
        }

        $roleLabel = $approvedRole === 'doctor' ? 'Doctor' : 'Asistente';

        return redirect()
            ->route('admin.users')
            ->with('success', $user->name.' fue aprobado como '.$roleLabel.'.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureOperationalUser($user);

        if ((int) $request->session()->get('access_user_id') === $user->id) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['user' => 'No puedes eliminar el usuario con el que tienes la sesion activa.']);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente. Ya no podra acceder al sistema.');
    }

    private function ensureOperationalUser(User $user): void
    {
        abort_unless(in_array($user->role, ['pendiente', 'doctor', 'asistente'], true), 403);
    }
}
