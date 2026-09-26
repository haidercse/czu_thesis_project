# 06 --- Application Workspace

## Objective

Turn the existing application/checklist foundation into a central
workspace for each university program application.

## Workspace Should Show

-   University
-   Program
-   Application status
-   Deadline
-   Progress
-   Checklist
-   Required documents
-   Uploaded documents
-   Missing items
-   Next action
-   Notes where appropriate

## Requirements

-   Reuse the existing Application and ApplicationStep structures.
-   Do not create a second application system.
-   Keep student data separated between applications.
-   Make application state easy to understand.
-   Ensure document/checklist relationships remain consistent.

## Suggested Statuses

Use existing statuses if already defined. If statuses are missing, use a
small, clear set such as: - Draft - Preparing - Ready - Submitted -
Completed

Do not break existing status values.

## Completion Criteria

A student can open one application and understand what has been
completed, what is missing and what should be done next.
