# 22 --- RBAC and Security

## Objective

Fix the known authorization gap and harden access control before
production.

## Known Direction

The project specification identifies that admin/role distinction is not
currently fully implemented.

## Requirements

-   Implement clear roles, at minimum student/admin as required by the
    application.
-   Protect admin routes with proper authorization.
-   Do not rely only on `auth` middleware for admin access.
-   Review policies/authorization for user, application and document
    data.
-   Protect private document access.
-   Validate ownership before student actions.
-   Review CSRF, mass assignment, file upload validation and
    authorization.
-   Avoid exposing sensitive IDs/data unnecessarily.

## 2FA

Treat 2FA as a later security enhancement unless required immediately.

## Completion Criteria

Users cannot access another user's private application/document data and
non-admin users cannot access admin functions.
