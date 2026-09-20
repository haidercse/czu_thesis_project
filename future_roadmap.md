# CZU Thesis Project — Future Enhancements & Feature Roadmap

This document outlines the planned features, architectural improvements, and strategic milestones to transform the **Study Czechia Guide (CZU Thesis Project)** into a fully featured, scalable, and production-ready platform for international student application management.

---

## 🎯 Phase 1: Security & Advanced Access Control (RBAC)

- [ ] **Role-Based Access Control (RBAC):**
  - Integrate `spatie/laravel-permission` to introduce explicit roles (`Admin`, `University Officer`, `Student`).
  - Restrict `/admin/*` routes dynamically using policies and middleware instead of basic authentication check.
- [ ] **Multi-Factor Authentication (2FA):**
  - Implement two-factor authentication via Laravel Fortify for administrative and high-privilege accounts.
- [ ] **Social Authentication:**
  - Add Google and LinkedIn OAuth login options via Laravel Socialite for streamlined student onboarding.

---

## 📬 Phase 2: Automated Communication & Real-time Notifications

- [ ] **Automated Email Reminders & Queues:**
  - Set up Laravel Queues (Redis/Database background jobs) to send automated email alerts:
    - Upcoming application deadlines (e.g., 30, 14, and 7-day reminders).
    - Status updates on document verification.
- [ ] **In-App Real-time Notifications:**
  - Implement real-time websocket broadcasting using Laravel Reverb or Pusher to inform students instantly when document statuses change or steps are updated.

---

## 📄 Phase 3: Advanced Application Workflow & Document Processing

- [ ] **Application PDF Export:**
  - Allow applicants to generate and download a comprehensive PDF summary of their completed checklist, personal details, and submitted document list using `barryvdh/laravel-dompdf`.
- [ ] **Document Verification & Review Workflow:**
  - Enable administrators to assign statuses (`Approved`, `Rejected`, `Resubmission Required`) with feedback notes on individual uploaded documents.
  - Automatically notify students when a resubmission is required.
- [ ] **Automated Document OCR Validation (Experimental):**
  - Integrate OCR tools (e.g., Tesseract or Google Cloud Vision API) to automatically verify document formats and check basic data consistency.

---

## 🤖 Phase 4: AI Recommendations & Smart Student Support

- [ ] **AI-Powered Program Recommendation Engine:**
  - Build a matching algorithm that compares a student's profile (GPA, English proficiency score, budget, academic background) with available programs to suggest optimal matches.
- [ ] **AI Assistant / Helpdesk Integration:**
  - Embed an AI chatbot (powered by OpenAI / Gemini API) pretrained on Czech university admission guidelines, visa procedures, and nostrification requirements to answer FAQs dynamically.

---

## 📊 Phase 5: Admin Analytics, Reporting & Data Portability

- [ ] **Interactive Admin Analytics Dashboard:**
  - Implement Chart.js / ApexCharts to display real-time insights:
    - Application conversion rates.
    - Top applicant home countries.
    - Most popular degree programs and disciplines.
- [ ] **Data Export (Excel/CSV):**
  - Add data export capability using `maatwebsite/excel` to export student registries, application summaries, and document logs for institutional record-keeping.

---

## ⚡ Phase 6: Architecture, Performance & Scalability

- [ ] **Caching Layer (Redis Integration):**
  - Implement Redis caching for search queries, field options, and public program data to ensure sub-millisecond search performance.
- [ ] **RESTful API & Sanctum Authentication:**
  - Expose API endpoints using Laravel Sanctum to support future native mobile apps (iOS / Android / Flutter).
- [ ] **Automated Testing Suite:**
  - Write automated feature and unit tests with PHPUnit / Pest to ensure seamless CI/CD execution and guard against regressions.

---

## 🛠 Tech Stack Overview for Upgrades

| Feature Category | Technical Tool / Package |
| :--- | :--- |
| **Authentication & Roles** | `spatie/laravel-permission`, `laravel/fortify`, `laravel/socialite` |
| **Queues & Caching** | Redis, Horizon, Mailtrap / Amazon SES |
| **Real-time Engine** | Laravel Reverb / Pusher |
| **Exports & Reporting** | `barryvdh/laravel-dompdf`, `maatwebsite/excel` |
| **AI Capabilities** | OpenAI API / Gemini API |
| **API Layer** | Laravel Sanctum |
