<?php

namespace Tests\Feature;

use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_operational_admin_combines_doctor_and_assistant_modules_without_technical_access(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administradora Operativa',
            'role' => 'admin',
        ]);
        $dentist = Dentist::create([
            'nombre' => 'Dra. Responsable',
            'telefono' => '2220000101',
        ]);
        $patient = Patient::create([
            'nombre' => 'Paciente Administración',
            'telefono' => '2220000102',
            'direccion' => 'Dirección de prueba',
            'estado' => 'Activo',
        ]);
        Treatment::create([
            'paciente' => '',
            'tratamiento' => 'Consulta administrativa',
            'tipo' => 'General',
            'fecha' => now()->toDateString(),
            'estado' => 'Activo',
            'costo' => 500,
        ]);
        $session = [
            'access_role' => 'admin',
            'access_user_id' => $admin->id,
            'access_name' => $admin->name,
            'access_email' => $admin->email,
        ];

        foreach (['inicio', 'calendario', 'citas', 'pacientes', 'expedientes', 'pacientes.tratamientos', 'inventario', 'reportes'] as $route) {
            $this->withSession($session)->get(route($route))->assertOk();
        }

        $this->withSession($session)
            ->get(route('inicio'))
            ->assertSee('Administración clínica')
            ->assertSee('Nuevo tratamiento')
            ->assertSee('Alertas de inventario');

        $this->withSession($session)
            ->get(route('admin.users'))
            ->assertRedirect(route('login'));

        $this->withSession($session)->post(route('pacientes.clinical.store', $patient), [
            'dentist_id' => $dentist->id,
            'recorded_at' => now()->format('Y-m-d H:i'),
            'reason' => 'Valoración administrativa',
        ])->assertRedirect();

        $this->assertDatabaseHas('clinical_records', [
            'patient_id' => $patient->id,
            'dentist_id' => $dentist->id,
            'reason' => 'Valoración administrativa',
        ]);
    }

    public function test_superadmin_keeps_the_existing_technical_dashboard(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $session = [
            'access_role' => 'superadmin',
            'access_user_id' => $superadmin->id,
            'access_name' => $superadmin->name,
            'access_email' => $superadmin->email,
        ];

        $this->withSession($session)
            ->get(route('inicio'))
            ->assertOk()
            ->assertSee('Control del sistema')
            ->assertSee('Centro de superadministración');

        $this->withSession($session)
            ->get(route('admin.users'))
            ->assertOk();

        $this->withSession($session)
            ->get(route('inventario'))
            ->assertRedirect(route('inicio'));
    }
}
