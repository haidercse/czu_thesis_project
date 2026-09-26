<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramSearchImprovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_university_and_language_and_sorts_deadlines(): void
    {
        $user = User::factory()->create();

        $charles = University::create(['name' => 'Charles University', 'location' => 'Prague']);
        $life = University::create(['name' => 'Czech University of Life Sciences Prague', 'location' => 'Prague']);

        $older = Program::create([
            'university_id' => $life->id,
            'program_name' => 'Economics and Management',
            'field_of_study' => 'Business & Economics',
            'tuition_fee_annual' => 3500,
            'application_deadline' => '2028-04-15',
            'language_proficiency_requirement' => 'IELTS 6.0',
            'minimum_gpa' => 3.0,
        ]);

        $target = Program::create([
            'university_id' => $charles->id,
            'program_name' => 'International Relations',
            'field_of_study' => 'Social Sciences',
            'tuition_fee_annual' => 6200,
            'application_deadline' => '2027-02-28',
            'language_proficiency_requirement' => 'IELTS 6.5',
            'minimum_gpa' => 3.0,
        ]);

        $response = $this->actingAs($user)->get('/programs/search?university_id=' . $charles->id . '&language=IELTS&sort=deadline_asc');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame($target->id, $response->json()[0]['id']);
    }
}
