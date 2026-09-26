<?php

namespace App\Services;

use App\Models\Program;
use App\Models\UserProfile;
use Illuminate\Support\Collection;

class ProgramRecommendationService
{
    public function recommend(UserProfile $profile): Collection
    {
        return Program::with('university')->get()->map(function (Program $program) use ($profile) {
            $score = 0;
            $reasons = [];

            if ($this->matchesField($profile->target_field, $program->field_of_study)) {
                $score += 40;
                $reasons[] = 'Matches your target field';
            }

            if (!$profile->annual_budget) {
                $score += 10;
            } elseif ((float) $program->tuition_fee_annual <= (float) $profile->annual_budget) {
                $score += 25;
                $reasons[] = 'Within your annual budget';
            }

            if (!$program->minimum_gpa || !$profile->gpa) {
                $score += 10;
            } elseif ((float) $profile->gpa >= (float) $program->minimum_gpa) {
                $score += 15;
                $reasons[] = 'Meets the minimum GPA';
            }

            $languageScore = $this->languageScore($profile->language_test_score, $program->language_proficiency_requirement);
            $score += $languageScore['points'];
            if ($languageScore['reason']) {
                $reasons[] = $languageScore['reason'];
            }

            if ($this->matchesPreferredCity($profile->preferred_city, $program->university?->location)) {
                $score += 10;
                $reasons[] = 'Preferred city matches your choice';
            }

            if ($this->matchesTargetIntake($profile->target_intake, $program->application_deadline)) {
                $score += 5;
                $reasons[] = 'Application deadline aligns with your target intake';
            }

            $program->recommendation_score = min($score, 100);
            $program->recommendation_reasons = array_values(array_unique($reasons));

            return $program;
        })->sortByDesc('recommendation_score')->values();
    }

    private function matchesField(?string $targetField, ?string $programField): bool
    {
        if (!$targetField || !$programField) {
            return false;
        }

        return str_contains(strtolower($programField), strtolower($targetField))
            || str_contains(strtolower($targetField), strtolower($programField));
    }

    private function matchesPreferredCity(?string $preferredCity, ?string $universityLocation): bool
    {
        if (!$preferredCity || !$universityLocation) {
            return false;
        }

        return str_contains(strtolower($universityLocation), strtolower($preferredCity))
            || str_contains(strtolower($preferredCity), strtolower($universityLocation));
    }

    private function matchesTargetIntake(?string $targetIntake, ?string $applicationDeadline): bool
    {
        if (!$targetIntake || !$applicationDeadline) {
            return false;
        }

        $targetYear = preg_match('/\d{4}/', (string) $targetIntake, $targetMatches) ? $targetMatches[0] : null;
        $deadlineYear = preg_match('/\d{4}/', (string) $applicationDeadline, $deadlineMatches) ? $deadlineMatches[0] : null;

        return $targetYear && $deadlineYear && $targetYear === $deadlineYear;
    }

    private function languageScore($studentScore, ?string $requirement): array
    {
        if (!$requirement || !$studentScore) {
            return ['points' => 10, 'reason' => null];
        }

        preg_match('/([0-9]+(?:\.[0-9]+)?)/', $requirement, $matches);
        if (!$matches) {
            return ['points' => 10, 'reason' => null];
        }

        if ((float) $studentScore >= (float) $matches[1]) {
            return ['points' => 15, 'reason' => 'Meets the language requirement'];
        }

        return ['points' => 0, 'reason' => null];
    }
}
