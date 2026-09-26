# 14 --- Admin Universities

## Objective

Improve the existing university CRUD for reliable platform data
management.

## Existing Direction

University CRUD already exists in the admin backend.

## Requirements

-   Inspect existing controller, model, migration and views.
-   Preserve existing CRUD behavior.
-   Improve validation.
-   Prevent invalid/duplicate records where appropriate.
-   Support relevant fields required by program pages.
-   Make official website/source information manageable.
-   Improve error/success feedback.
-   Use AJAX only where it genuinely improves the existing UI; do not
    rewrite the whole admin.
-  Need to make datatable for all crud     

## Security Note

The project currently has a known role/middleware gap. Do not silently
assume all authenticated users are administrators.

## Completion Criteria

Admin users can maintain clean university records without damaging
related programs.
