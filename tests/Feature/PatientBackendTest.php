<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_be_created_without_last_visit_and_is_shown_as_new(): void
    {
        $this->withSession(['access_role' => 'doctor'])
            ->post(route('pacientes.store'), [
                'nombre' => 'Paciente Sin Visita',
                'telefono' => '2220000000',
                'email' => '',
                'estado' => 'Activo',
            ])
            ->assertRedirect(route('pacientes'));

        $this->assertDatabaseHas('patients', [
            'nombre' => 'Paciente Sin Visita',
            'ultima_visita' => null,
        ]);

        $this->withSession(['access_role' => 'doctor'])
            ->get(route('pacientes'))
            ->assertOk()
            ->assertSee('Paciente Sin Visita')
            ->assertSee('Nuevo');
    }

    public function test_patient_duplicate_email_uses_readable_validation_message(): void
    {
        Patient::create([
            'nombre' => 'Paciente Existente',
            'telefono' => '2220000000',
            'email' => 'paciente@example.test',
            'estado' => 'Activo',
        ]);

        $response = $this->withSession(['access_role' => 'doctor'])
            ->from(route('pacientes.create'))
            ->post(route('pacientes.store'), [
                'nombre' => 'Paciente Duplicado',
                'telefono' => '2220000001',
                'email' => 'paciente@example.test',
                'estado' => 'Activo',
            ]);

        $response->assertRedirect(route('pacientes.create'));
        $response->assertSessionHasErrors(['email' => 'Ese correo ya esta registrado en otro paciente.']);
    }

    public function test_patient_name_and_phone_only_accept_allowed_characters(): void
    {
        $response = $this->withSession(['access_role' => 'doctor'])
            ->from(route('pacientes.create'))
            ->post(route('pacientes.store'), [
                'nombre' => 'Paciente 123!',
                'telefono' => '222-000-0000',
                'estado' => 'Activo',
            ]);

        $response->assertRedirect(route('pacientes.create'));
        $response->assertSessionHasErrors([
            'nombre' => 'El nombre completo solo debe contener letras y espacios.',
            'telefono' => 'El telefono solo debe contener numeros.',
        ]);
    }

    public function test_emergency_contact_name_and_phone_only_accept_allowed_characters(): void
    {
        $response = $this->withSession(['access_role' => 'doctor'])
            ->from(route('pacientes.create'))
            ->post(route('pacientes.store'), [
                'nombre' => 'Paciente Valido',
                'telefono' => '2220000000',
                'estado' => 'Activo',
                'contacto_emergencia' => [
                    'nombre' => 'Contacto 123!',
                    'telefono' => '222-000-0001',
                ],
            ]);

        $response->assertRedirect(route('pacientes.create'));
        $response->assertSessionHasErrors([
            'contacto_emergencia.nombre' => 'El nombre del contacto de emergencia solo debe contener letras y espacios.',
            'contacto_emergencia.telefono' => 'El telefono del contacto de emergencia solo debe contener numeros.',
        ]);
    }

    public function test_assistant_can_open_expedientes_section(): void
    {
        Patient::create([
            'nombre' => 'Paciente Expediente',
            'telefono' => '2220000000',
            'estado' => 'Activo',
        ]);

        $this->withSession(['access_role' => 'asistente'])
            ->get(route('expedientes'))
            ->assertOk()
            ->assertSee('Expedientes')
            ->assertSee('Paciente Expediente');
    }

    public function test_doctor_expedientes_are_scoped_to_own_patients(): void
    {
        $doctor = User::create([
            'name' => 'Dra. Expedientes',
            'email' => 'expedientes@example.test',
            'role' => 'doctor',
            'password' => 'password',
        ]);
        $ownDentist = Dentist::create(['user_id' => $doctor->id, 'nombre' => 'Dra. Expedientes', 'telefono' => '2220000000']);
        $otherDentist = Dentist::create(['nombre' => 'Dr. Ajeno', 'telefono' => '2220000001']);
        $ownPatient = Patient::create(['nombre' => 'Paciente Propio Expediente', 'telefono' => '2220000002', 'estado' => 'Activo']);
        $otherPatient = Patient::create(['nombre' => 'Paciente Ajeno Expediente', 'telefono' => '2220000003', 'estado' => 'Activo']);

        Appointment::create([
            'patient_id' => $ownPatient->id,
            'dentist_id' => $ownDentist->id,
            'date' => now()->toDateString(),
            'time' => '10:00:00',
            'tipo' => 'Consulta',
            'estado' => 'Confirmada',
        ]);
        Appointment::create([
            'patient_id' => $otherPatient->id,
            'dentist_id' => $otherDentist->id,
            'date' => now()->toDateString(),
            'time' => '11:00:00',
            'tipo' => 'Consulta',
            'estado' => 'Confirmada',
        ]);

        $this->withSession([
                'access_role' => 'doctor',
                'access_user_id' => $doctor->id,
                'access_email' => $doctor->email,
                'access_name' => $doctor->name,
            ])
            ->get(route('expedientes'))
            ->assertOk()
            ->assertSee('Paciente Propio Expediente')
            ->assertDontSee('Paciente Ajeno Expediente');
    }
}
