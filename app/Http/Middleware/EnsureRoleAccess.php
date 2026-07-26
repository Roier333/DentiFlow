<?php

namespace App\Http\Middleware;

use App\Models\ClinicSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (!$routeName || Str::is(['role.select', 'login', 'register', 'register.pending', 'password.*', 'storage.*', 'logout'], $routeName)) {
            return $next($request);
        }

        $authenticatedUser = $request->user();
        if ($authenticatedUser && (
            (int) session('access_user_id') !== (int) $authenticatedUser->id
            || session('access_role') !== $authenticatedUser->role
        )) {
            $request->session()->put([
                'access_role' => $authenticatedUser->role,
                'access_user_id' => $authenticatedUser->id,
                'access_name' => $authenticatedUser->name,
                'access_email' => $authenticatedUser->email,
                'access_gender' => $authenticatedUser->gender,
            ]);
        }

        $role = $authenticatedUser?->role ?? session('access_role');

        if (!$role) {
            return redirect()->route('role.select');
        }

        $userId = $request->session()->get('access_user_id');
        if ($userId && ! User::whereKey($userId)->exists()) {
            $request->session()->forget(['access_role', 'access_user_id', 'access_name', 'access_email', 'access_gender']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('warning', 'Tu usuario ya no esta activo. Contacta a administracion para recuperar el acceso.');
        }

        if ($role === 'pendiente') {
            return redirect()->route('register.pending');
        }

        if ($role !== 'superadmin' && Str::is('admin.*', $routeName)) {
            $roleLabel = match ($role) {
                'doctor' => 'Doctor',
                'asistente' => 'Asistente',
                'admin' => 'Administrador operativo',
                default => 'usuario',
            };

            return redirect()->route('login')->with(
                'warning',
                'La sesión activa pertenece al perfil '.$roleLabel.'. Para entrar a la administración técnica, inicia sesión con una cuenta de superadministrador.'
            );
        }

        if ($this->maintenanceBlocks($role, $routeName)) {
            return redirect()->route('inicio')->with('access_denied', 'El sistema esta en modo mantenimiento. Solo administracion puede operar modulos internos.');
        }

        $settings = ClinicSetting::current();
        $permissions = [
            'superadmin' => ['inicio', 'admin.*', 'dentistas*', 'perfil*', 'logout'],
            'admin' => ['inicio', 'calendario*', 'citas*', 'pacientes*', 'expedientes*', 'reportes*', 'inventario*', 'perfil*', 'logout'],
            'doctor' => ['inicio', 'calendario*', 'citas*', 'pacientes*', 'expedientes*', 'reportes*', 'perfil*', 'logout'],
            'asistente' => $settings->assistantAllowedRoutes(),
        ];

        if (!isset($permissions[$role]) || !Str::is($permissions[$role], $routeName)) {
            return redirect()->route('inicio')->with('access_denied', 'Tu perfil no tiene permiso para acceder a esa sección.');
        }

        return $next($request);
    }

    private function maintenanceBlocks(string $role, string $routeName): bool
    {
        if ($role === 'superadmin' || ! ClinicSetting::current()->maintenance_mode_enabled) {
            return false;
        }

        // Durante mantenimiento se conserva inicio/perfil/logout para evitar dejar usuarios atrapados.
        return ! Str::is(['inicio', 'perfil*', 'logout'], $routeName);
    }
}

