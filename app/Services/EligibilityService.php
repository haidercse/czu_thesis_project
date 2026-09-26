<?php

namespace App\Services;

use App\Models\Program;
use App\Models\UserProfile;

class EligibilityService
{
    public function evaluate(Program $program, ?UserProfile $profile): array
    {
        if (! $profile) {
            return [
                'status' => 'missing_information',
                'status_label' => 'Missing information',
                'reasons' => [
                    'Complete your profile to check whether this program is a match.',
                ],
            ];
        }

        $reasons = [];
        $missing = [];
        $requirementsNotMet = [];

        if ($profile->target_field && $program->field_of_study) {
            if ($this->matchesField($profile->target_field, $program->field_of_study)) {
                $reasons[] = 'Your target field matches this program.';
            } else {
                $requirementsNotMet[] = 'Your target field does not match this program.';
            }
        } elseif (! $profile->target_field) {
            $missing[] = 'target field';
            $reasons[] = 'Missing target field information.';
        }

        if ($program->minimum_gpa) {
            if ($profile->gpa === null || $profile->gpa === '') {
                $missing[] = 'GPA';
                $reasons[] = 'Missing GPA information.';
            } elseif ((float) $profile->gpa >= (float) $program->minimum_gpa) {
                $reasons[] = 'GPA meets the program requirement.';
            } else {
                $requirementsNotMet[] = 'GPA is below the program minimum.';
                $reasons[] = 'GPA does not meet the program requirement.';
            }
        }

        if ($program->language_proficiency_requirement) {
            $languageCheck = $this->languageRequirementStatus($profile->language_test_score, $program->language_proficiency_requirement);

            if ($languageCheck['status'] === 'missing') {
                $missing[] = 'language test score';
                $reasons[] = 'Missing language test score information.';
            } elseif ($languageCheck['status'] === 'met') {
                $reasons[] = 'Language score meets the program requirement.';
            } else {
                $requirementsNotMet[] = 'Language score is below the program requirement.';
                $reasons[] = 'Language score does not meet the program requirement.';
            }
        }

        $reasons = array_merge($reasons, $requirementsNotMet);

        if ($requirementsNotMet) {
            return [
                'status' => 'requirement_not_met',
                'status_label' => 'Requirement not met',
                'reasons' => array_values(array_unique($reasons)),
            ];
        }

        if ($missing) {
            return [
                'status' => 'needs_review',
                'status_label' => 'Needs review',
                'reasons' => array_values(array_unique($reasons)),
            ];
        }

        return [
            'status' => 'eligible',
            'status_label' => 'Eligible',
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    private function matchesField(?string $targetField, ?string $programField): bool
    {
        if (! $targetField || ! $programField) {
            return false;
        }

        return str_contains(strtolower($programField), strtolower($targetField))
            || str_contains(strtolower($targetField), strtolower($programField));
    }

    private function languageRequirementStatus($studentScore, ?string $requirement): array
    {
        if ($studentScore === null || $studentScore === '') {
            return ['status' => 'missing'];
        }

        preg_match('/([0-9]+(?:\.[0-9]+)?)/', (string) $requirement, $matches);

        if (! $matches) {
            return ['status' => 'review'];
        }

        if ((float) $studentScore >= (float) $matches[1]) {
            return ['status' => 'met'];
        }

        return ['status' => 'not_met'];
    }
}
