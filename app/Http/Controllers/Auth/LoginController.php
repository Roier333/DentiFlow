<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\DatabaseConnectionRetry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function store(Request $request, DatabaseConnectionRetry $databaseRetry): RedirectResponse|Response
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'nullable|in:doctor,asistente,admin',
        ], [
            'email.required' => 'El correo electronico es obligatorio.',
            'email.email' => 'Ingresa un correo electronico valido.',
            'password.required' => 'La contrasena es obligatoria.',
        ]);

        try {
            $user = $databaseRetry->run(
                fn () => User::where('email', $request->input('email'))->first()
            );
        } catch (PDOException $exception) {
            if (! $databaseRetry->wasConnectionLost($exception)) {
                throw $exception;
            }

            report($exception);

            return response(
                'El servicio de base de datos no esta disponible temporalmente. Intenta de nuevo en unos segundos.',
                503,
                ['Retry-After' => '10'],
            );
        }

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])->withInput($request->only('email'));
        }

        if ($user->role === 'pendiente') {
            $request->session()->regenerate();

            return redirect()
                ->route('register.pending')
                ->with([
                    'pending_name' => $user->name,
                    'pending_email' => $user->email,
                ]);
        }

        $selectedRole = $request->input('role');
        if ($selectedRole && $user->role !== $selectedRole) {
            return back()
                ->withErrors(['email' => 'Esta cuenta no pertenece al acceso de '.($selectedRole === 'doctor' ? 'Doctor' : ($selectedRole === 'asistente' ? 'Asistente' : 'Administrador')).'.'])
                ->withInput($request->only('email', 'role'));
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        $request->session()->put([
            'access_role' => $user->role,
            'access_user_id' => $user->id,
            'access_name' => $user->name,
            'access_email' => $user->email,
            'access_gender' => $user->gender,
        ]);

        // Railway usa sesiones en MySQL. Guardarlas antes de redirigir evita que
        // el siguiente request llegue sin la autenticacion recien creada.
        $request->session()->save();

        return redirect()->route('inicio');
    }
}
