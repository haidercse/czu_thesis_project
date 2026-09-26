# 16 --- Admin Document Review

## Objective

Add a controlled document review workflow for administrators.

## Requirements

-   Inspect the existing document model and storage flow.
-   Allow authorized reviewers to view relevant student/application
    document metadata.
-   Support review states only if the database can represent them
    safely.
-   Possible states:
    -   Uploaded
    -   In Review
    -   Approved
    -   Rejected
    -   Resubmission
-   Require a reason/comment for rejection where appropriate.
-   Do not expose private documents to unauthorized users.
-   Do not modify files merely for viewing.

## Important

This is a review workflow, not an official university verification
service.

## Completion Criteria

A reviewer can process a submitted document and the student can see the
resulting status/instructions.
