<?php

namespace Tests\Feature;

use App\Models\Dentist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_registered_users_section_without_create_form(): void
    {
        User::factory()->create(['name' => 'Usuario Registrado', 'email' => 'usuario@example.test', 'role' => 'doctor']);

        $this->withSession(['access_role' => 'admin'])
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Usuarios del sistema')
            ->assertSee('Usuario Registrado')
            ->assertDontSee('Dashboard clinico')
            ->assertDontSee('Asignar rol')
            ->assertDontSee('Perfil clínico')
            ->assertDontSee('Crear usuario')
            ->assertDontSee('admin.users.store');
    }

    public function test_admin_cannot_create_users_from_users_section(): void
    {
        $this->withSession(['access_role' => 'admin'])
            ->post('/administracion/usuarios', [
                'name' => 'Usuario Nuevo',
                'email' => 'nuevo@example.test',
                'role' => 'doctor',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertStatus(405);

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.test']);
    }

    public function test_admin_can_update_registered_user_basic_data(): void
    {
        $user = User::factory()->create(['name' => 'Nombre Anterior', 'email' => 'anterior@example.test', 'role' => 'doctor']);

        $this->withSession(['access_role' => 'admin'])
            ->put(route('admin.users.update', $user), [
                'name' => 'Nombre Actualizado',
                'email' => 'actualizado@example.test',
                'role' => 'doctor',
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre Actualizado',
            'email' => 'actualizado@example.test',
            'role' => 'doctor',
        ]);
    }

    public function test_admin_can_approve_pending_user_with_role(): void
    {
        $user = User::factory()->create([
            'name' => 'Pendiente Aprobacion',
            'email' => 'pendiente.admin@example.test',
            'role' => 'pendiente',
        ]);

        $this->withSession(['access_role' => 'admin'])
            ->put(route('admin.users.update', $user), [
                'name' => 'Dra. Aprobada',
                'email' => 'aprobada@example.test',
                'role' => 'doctor',
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Dra. Aprobada',
            'email' => 'aprobada@example.test',
            'role' => 'doctor',
        ]);

        $this->assertDatabaseHas('dentists', [
            'user_id' => $user->id,
            'nombre' => 'Dra. Aprobada',
            'especialidad' => 'Odontologia general',
        ]);
    }

    public function test_superadmin_sees_requested_profile_and_can_approve_it_directly(): void
    {
        $user = User::factory()->create([
            'name' => 'Solicitud Doctora',
            'email' => 'solicitud.doctora@example.test',
            'role' => 'pendiente',
            'requested_role' => 'doctor',
        ]);

        $this->withSession(['access_role' => 'admin'])
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Solicitudes pendientes')
            ->assertSee('Solicitud Doctora')
            ->assertSee('Solicitó: Doctor')
            ->assertSee('Aprobar como Doctor');

        $this->withSession(['access_role' => 'admin'])
            ->put(route('admin.users.approve', $user))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success', 'Solicitud Doctora fue aprobado como Doctor.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'doctor',
            'requested_role' => null,
        ]);
        $this->assertDatabaseHas('dentists', [
            'user_id' => $user->id,
            'nombre' => 'Solicitud Doctora',
        ]);
    }

    public function test_direct_approval_rejects_pending_user_without_requested_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'pendiente',
            'requested_role' => null,
        ]);

        $this->withSession(['access_role' => 'admin'])
            ->put(route('admin.users.approve', $user))
            ->assertRedirect(route('admin.users.edit', $user))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'pendiente',
        ]);
    }

    public function test_admin_can_assign_assistant_role_from_users_section(): void
    {
        $user = User::factory()->create([
            'name' => 'Usuario Recepcion',
            'email' => 'recepcion@example.test',
            'role' => 'pendiente',
        ]);

        $this->withSession(['access_role' => 'admin'])
            ->put(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'asistente',
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'asistente',
        ]);

        $this->assertDatabaseMissing('dentists', [
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_delete_user_and_removed_user_cannot_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => 'Usuario Eliminable',
            'email' => 'eliminable@example.test',
            'role' => 'doctor',
            'password' => 'password',
        ]);

        Dentist::create([
            'user_id' => $user->id,
            'nombre' => $user->name,
            'telefono' => 'No registrado',
        ]);

        $this->withSession(['access_role' => 'admin', 'access_user_id' => $admin->id])
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success', 'Usuario eliminado correctamente. Ya no podra acceder al sistema.');

        $this->assertDatabaseMissing('users', ['email' => 'eliminable@example.test']);
        $this->assertDatabaseHas('dentists', [
            'nombre' => 'Usuario Eliminable',
            'user_id' => null,
        ]);

        $this->post('/login', [
            'email' => 'eliminable@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_deleted_user_active_session_is_rejected_from_protected_sections(): void
    {
        $user = User::factory()->create([
            'email' => 'sesion.eliminada@example.test',
            'role' => 'asistente',
        ]);

        $user->delete();

        $this->withSession([
            'access_role' => 'asistente',
            'access_user_id' => $user->id,
            'access_name' => 'Sesion Eliminada',
            'access_email' => 'sesion.eliminada@example.test',
        ])
            ->get(route('inicio'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('warning', 'Tu usuario ya no esta activo. Contacta a administracion para recuperar el acceso.')
            ->assertSessionMissing('access_role');
    }

    public function test_admin_cannot_delete_own_active_user(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin.actual@example.test',
            'role' => 'admin',
        ]);

        $this->withSession(['access_role' => 'admin', 'access_user_id' => $admin->id])
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin.actual@example.test',
        ]);
    }
}
