<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPhaseOneTwoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_core_metrics(): void
    {
        $user = User::factory()->create();

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

        $response = $this->actingAs($user)->postJson('/admin/universities', [
            'name' => 'Czech Technical University',
            'location' => 'Prague',
            'website_url' => 'https://cvut.cz',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Czech Technical University');
    }
}
