# 08 --- Document Integration

## Objective

Connect document management with application checklist steps.

## Existing Direction

The project already has document management. The existing task
specification calls for linking an uploaded document type to the
relevant checklist step and moving that step to `in_progress`, not
automatically to completed.

## Requirements

-   Inspect current document types and checklist steps.
-   Reuse existing document records.
-   Map compatible document types to checklist requirements.
-   On successful upload, update the relevant checklist/application step
    appropriately.
-   Do not mark a document as approved unless an actual verification
    workflow exists.
-   Preserve download/delete functionality.
-   Handle replacement uploads safely.

## Document Status Direction

Where supported, prepare for: - Missing - Uploaded - In Review -
Approved - Rejected - Resubmission

Do not introduce statuses that conflict with existing database values.

## Completion Criteria

Uploading a relevant document visibly updates the corresponding
application requirement without falsely claiming approval.
