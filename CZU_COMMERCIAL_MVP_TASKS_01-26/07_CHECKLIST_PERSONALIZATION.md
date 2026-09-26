# 07 --- Checklist Personalization

## Objective

Improve application checklists so requirements can vary by student
country and program.

## Existing Direction

The project specification includes country-based checklist
personalization using `applicable_countries`.

## Requirements

-   Inspect the current checklist implementation first.
-   Reuse existing checklist/application-step structures.
-   Support country-specific applicability.
-   Support program-specific requirements where the existing
    architecture allows it.
-   Avoid showing irrelevant checklist items.
-   Keep required and optional items distinguishable.
-   Do not mark a requirement complete merely because a document was
    uploaded unless the existing business rule explicitly supports that
    behavior.

## Deliverables

Updated data logic, UI and tests.

## Completion Criteria

The checklist is personalized enough to reduce irrelevant work while
remaining understandable to students.
