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

        $summary = [
            'total' => User::count(),
            'superadmins' => User::where('role', 'superadmin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'doctors' => User::where('role', 'doctor')->count(),
            'assistants' => User::where('role', 'asistente')->count(),
            'pending' => User::where('role', 'pendiente')->count(),
        ];

        return view('admin.users.index', compact('users', 'summary', 'search'));
    }

    public function edit(User $user)
    {
        $user->load('dentist');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['pendiente', 'doctor', 'asistente', 'admin', 'superadmin'])],
        ]);

        if ($user->role === 'superadmin' && $data['role'] !== 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
            return back()->withErrors(['role' => 'Debe existir al menos un superadministrador activo.'])->withInput();
        }

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

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->session()->get('access_user_id') === $user->id) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['user' => 'No puedes eliminar el usuario con el que tienes la sesion activa.']);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente. Ya no podra acceder al sistema.');
    }
}
