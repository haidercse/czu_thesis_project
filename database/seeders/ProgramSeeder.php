<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\University;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $uni1 = University::create(['name' => 'Charles University', 'location' => 'Prague']);
        $uni2 = University::create(['name' => 'Czech University of Life Sciences Prague', 'location' => 'Prague']);
        $uni3 = University::create(['name' => 'Masaryk University', 'location' => 'Brno']);

        Program::create(['university_id' => $uni2->id, 'program_name' => 'Economics and Management', 'field_of_study' => 'Business & Economics', 'tuition_fee_annual' => 3500, 'application_deadline' => '2027-04-15', 'language_proficiency_requirement' => 'IELTS 5.5']);
        Program::create(['university_id' => $uni1->id, 'program_name' => 'International Relations', 'field_of_study' => 'Social Sciences', 'tuition_fee_annual' => 6200, 'application_deadline' => '2027-02-28', 'language_proficiency_requirement' => 'IELTS 6.5']);
        Program::create(['university_id' => $uni3->id, 'program_name' => 'Applied Mathematics', 'field_of_study' => 'Natural Sciences', 'tuition_fee_annual' => 3900, 'application_deadline' => '2027-04-30', 'language_proficiency_requirement' => 'IELTS 5.5']);
    }
}
