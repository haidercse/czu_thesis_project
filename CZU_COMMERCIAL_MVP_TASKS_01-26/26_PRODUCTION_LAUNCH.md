# 26 --- Production Launch

## Objective

Prepare the CZU application platform for a real public launch.

## Launch Checklist

### Application

-   Core student workflow tested
-   Program data reviewed
-   Official source links checked
-   Deadlines reviewed
-   Error pages handled
-   Empty states handled
-   Mobile UI checked

### Security

-   RBAC enabled
-   Authorization tested
-   Private documents protected
-   File uploads validated
-   Environment secrets protected
-   Debug mode disabled in production
-   HTTPS configured

### Database

-   Production migrations tested
-   Backups configured
-   Seed/demo data reviewed
-   Indexes reviewed for common queries

### Performance

-   Search queries checked
-   N+1 queries reviewed
-   Caching considered where appropriate
-   Assets optimized
-   Queue workers configured if notifications/jobs are used

### Operations

-   Logging configured
-   Error monitoring configured
-   Queue monitoring configured if applicable
-   Backup/restore process tested

### Product

-   Free vs premium scope clearly defined
-   Terms/privacy pages prepared as appropriate
-   Official-source disclaimer clearly presented
-   Student onboarding tested
-   Support/contact flow available

## Final Rule

Do not launch until the core application workflow, authorization and
data integrity are reliable.

## Completion Criteria

A new student can register, create a profile, find a program, understand
requirements, create/manage an application, manage documents and track
progress without encountering critical workflow or security failures.
