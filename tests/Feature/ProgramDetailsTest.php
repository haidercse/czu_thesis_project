<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_program_details_and_official_source_link(): void
    {
        $user = User::factory()->create();
        $university = University::create([
            'name' => 'Charles University',
            'location' => 'Prague',
            'website_url' => 'https://cuni.cz',
        ]);

        $program = Program::create([
            'university_id' => $university->id,
            'program_name' => 'International Relations',
            'field_of_study' => 'Social Sciences',
            'tuition_fee_annual' => 6200,
            'application_deadline' => '2027-02-28',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $response = $this->actingAs($user)->get('/programs/' . $program->id);

        $response->assertOk();
        $response->assertSee('International Relations');
        $response->assertSee('Charles University');
        $response->assertSee('Official source');
        $response->assertSee('https://cuni.cz');
    }
}
