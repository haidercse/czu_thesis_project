<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_compare_page_supports_up_to_five_programs_and_shows_available_fields(): void
    {
        $user = User::factory()->create();
        $university = University::create(['name' => 'Charles University', 'location' => 'Prague', 'website_url' => 'https://cuni.cz']);

        $programs = collect(range(1, 5))->map(function ($index) use ($university) {
            return Program::create([
                'university_id' => $university->id,
                'program_name' => 'Program ' . $index,
                'field_of_study' => 'Business & Economics',
                'tuition_fee_annual' => 3000 + $index,
                'application_deadline' => '2027-04-' . str_pad((string) ($index + 10), 2, '0', STR_PAD_LEFT),
                'language_proficiency_requirement' => 'IELTS ' . (5 + $index / 2),
                'minimum_gpa' => 3.0 + ($index * 0.1),
            ]);
        });

        $selectedIds = implode(',', $programs->pluck('id')->all());

        $response = $this->actingAs($user)->get('/programs/compare?ids=' . $selectedIds);

        $response->assertOk();
        $response->assertSee('Compare programs');
        $response->assertSee('City');
        $response->assertSee('Minimum GPA');
        $response->assertSee('Application fee');
        $this->assertCount(5, $programs);
    }
}
