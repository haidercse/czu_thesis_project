# 02 --- Student Profile Improvement

## Objective

Improve the existing student profile so it contains the information
needed for personalization, eligibility checks and program
recommendations.

## Existing Direction

The project already has a profile foundation. The remaining profile work
should extend it rather than create a parallel profile system.

## Profile Data

Support the existing fields and, where appropriate, add: - Country -
Previous degree - Previous institution - Target field - GPA - English
language test - English test score - Budget - Preferred city - Target
intake

## Requirements

-   Inspect the existing User/UserProfile implementation first.
-   Reuse existing fields and migrations.
-   Add only missing fields.
-   Validate user input.
-   Keep profile editing simple and mobile-friendly.
-   Show completion status where useful.
-   Make profile data available to later eligibility/recommendation
    features.

## Deliverables

-   Updated model/migration if needed
-   Updated controller/request validation
-   Updated profile view
-   Updated routes only if required
-   Tests for validation and saving

## Completion Criteria

A student can maintain the information required by later platform
features without entering the same data in multiple places.
