<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPhaseOneTwoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_core_metrics(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

        $university = University::create([
            'name' => 'University of Prague',
            'location' => 'Prague',
            'website_url' => 'https://example.com',
        ]);

        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'IT',
            'tuition_fee_annual' => 4500,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'B2',
        ]);

        Application::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSeeText('Universities');
        $response->assertSeeText('Programs');
        $response->assertSeeText('Applications');
    }

    public function test_university_creation_uses_json_for_ajax_requests(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

        $response = $this->actingAs($user)->postJson('/admin/universities', [
            'name' => 'Czech Technical University',
            'location' => 'Prague',
            'website_url' => 'https://cvut.cz',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Czech Technical University');
    }

    public function test_duplicate_university_names_are_rejected_for_clean_records(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

        University::create([
            'name' => 'Masaryk University',
            'location' => 'Brno',
            'website_url' => 'https://muni.cz',
        ]);

        $response = $this->actingAs($user)->postJson('/admin/universities', [
            'name' => 'masaryk university',
            'location' => 'Brno',
            'website_url' => 'https://example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_duplicate_programs_for_the_same_university_are_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

        $university = University::create([
            'name' => 'University of Prague',
            'location' => 'Prague',
            'website_url' => 'https://example.com',
        ]);

        Program::create([
            'university_id' => $university->id,
            'program_name' => 'Computer Science',
            'field_of_study' => 'IT',
            'tuition_fee_annual' => 4500,
            'application_deadline' => '2027-05-15',
            'language_proficiency_requirement' => 'B2',
            'official_source_url' => 'https://www.university.cz/computer-science',
        ]);

        $response = $this->actingAs($user)->postJson('/admin/programs', [
            'university_id' => $university->id,
            'program_name' => 'computer science',
            'field_of_study' => 'IT',
            'tuition_fee_annual' => 4700,
            'application_deadline' => '2027-06-15',
            'language_proficiency_requirement' => 'C1',
            'minimum_gpa' => 3.0,
            'official_source_url' => 'https://www.university.cz/computer-science-2027',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['program_name']);
    }
}
