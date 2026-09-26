# 03 --- Program Search Improvement

## Objective

Improve the existing program search and filtering experience.

## Preserve

The project already supports program search and filtering. Do not
rebuild it.

## Improve

Consider: - Search by program name - University filtering - Study
field/category filtering - Language filtering - Degree level -
Location/city - Tuition/fee range where data exists - Application
deadline - Intake - Sorting - Clear/reset filters - Pagination -
Saved/favorite action integration

## Requirements

-   Inspect the current search implementation first.
-   Use existing database fields.
-   Do not introduce filters for data that does not exist.
-   Keep query performance reasonable.
-   Preserve current URLs where practical.
-   Make empty states and no-result states clear.

## Deliverables

Updated search UI, query logic and tests where appropriate.

## Completion Criteria

Students can quickly narrow the existing program catalogue and
understand why results are shown.
