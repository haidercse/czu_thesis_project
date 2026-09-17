/* ============================================================
   Mock data layer — mirrors the Laravel Eloquent models from
   Chapter 6 (universities, programs, application_steps).
   When porting to Laravel: replace these arrays with data
   passed from the Controller into the Blade view, e.g.
   $programs = Program::with('university')->get();
   ============================================================ */

const UNIVERSITIES = [
  { id: 1, name: "Charles University", city: "Prague" },
  { id: 2, name: "Czech University of Life Sciences Prague", city: "Prague" },
  { id: 3, name: "Masaryk University", city: "Brno" },
  { id: 4, name: "Czech Technical University in Prague", city: "Prague" },
  { id: 5, name: "University of Economics, Prague", city: "Prague" },
];

const PROGRAMS = [
  {
    id: 101,
    university_id: 5,
    program_name: "International Business",
    field_of_study: "Business & Economics",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 4200,
    application_deadline: "2027-03-31",
    min_gpa_requirement: 2.5,
    language_proficiency_requirement: "IELTS 6.0",
  },
  {
    id: 102,
    university_id: 2,
    program_name: "Economics and Management",
    field_of_study: "Business & Economics",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 3500,
    application_deadline: "2027-04-15",
    min_gpa_requirement: 2.3,
    language_proficiency_requirement: "IELTS 5.5",
  },
  {
    id: 103,
    university_id: 4,
    program_name: "Computer Science",
    field_of_study: "Engineering",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 5000,
    application_deadline: "2027-03-15",
    min_gpa_requirement: 2.5,
    language_proficiency_requirement: "IELTS 6.0",
  },
  {
    id: 104,
    university_id: 1,
    program_name: "International Relations",
    field_of_study: "Social Sciences",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 6200,
    application_deadline: "2027-02-28",
    min_gpa_requirement: 2.0,
    language_proficiency_requirement: "IELTS 6.5",
  },
  {
    id: 105,
    university_id: 3,
    program_name: "Applied Mathematics",
    field_of_study: "Natural Sciences",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 3900,
    application_deadline: "2027-04-30",
    min_gpa_requirement: 2.5,
    language_proficiency_requirement: "IELTS 5.5",
  },
  {
    id: 106,
    university_id: 5,
    program_name: "Marketing Management",
    field_of_study: "Business & Economics",
    degree_type: "Master",
    duration_years: 2,
    language_of_instruction: "English",
    tuition_fee_annual: 4500,
    application_deadline: "2027-03-31",
    min_gpa_requirement: 2.3,
    language_proficiency_requirement: "IELTS 6.0",
  },
];

function universityName(id) {
  const u = UNIVERSITIES.find((u) => u.id === id);
  return u ? u.name + ", " + u.city : "";
}

/* Mirrors application_steps + user_application_steps tables (Section 6.2.2) */
const CHECKLIST_STEPS = [
  {
    id: 1,
    name: "Research qualification recognition requirements",
    description:
      "Check whether your previous degree needs formal recognition (nostrifikace) before applying, based on your country of origin.",
    status: "completed",
  },
  {
    id: 2,
    name: "Prepare and certify academic documents",
    description:
      "Obtain certified copies of your transcript and diploma, with apostille or embassy legalization as required for your country.",
    status: "in_progress",
  },
  {
    id: 3,
    name: "Translate documents into English or Czech",
    description:
      "Arrange certified translation of any documents not already issued in English or Czech.",
    status: "not_started",
  },
  {
    id: 4,
    name: "Submit qualification recognition application",
    description:
      "Submit your recognition application to the relevant Czech authority. Typical processing time is 30–60 days.",
    status: "not_started",
  },
  {
    id: 5,
    name: "Submit university application",
    description:
      "Complete and submit your application directly through the chosen university's admission portal before the deadline.",
    status: "not_started",
  },
  {
    id: 6,
    name: "Apply for a Czech study visa",
    description:
      "Once admitted, apply for your long-term study visa at the nearest Czech embassy. Processing can take 60–90 days.",
    status: "not_started",
  },
];

/* Mirrors documents table (Section 6.2.3) */
const DOCUMENTS = [
  { id: 1, name: "Bachelor_Transcript.pdf", type: "Transcript", size: "1.2 MB", date: "2026-08-14" },
  { id: 2, name: "Passport_Copy.jpg", type: "Passport", size: "0.6 MB", date: "2026-08-14" },
];

const FAQS = [
  {
    q: "What is qualification recognition and how long does it take?",
    a: "Qualification recognition (nostrifikace) is the official process of confirming that your previous degree is equivalent to a Czech qualification. It is usually required before or during a university application and typically takes 30–60 days, depending on the authority and your country of origin.",
  },
  {
    q: "Do I need to certify my documents with an apostille?",
    a: "If your home country is a signatory to the Hague Apostille Convention, an apostille is usually sufficient. If not, your documents will need embassy legalization instead, which can take longer.",
  },
  {
    q: "How much does a Czech study visa cost and how long does it take?",
    a: "Visa fees and processing times vary by country, but you should generally allow 60–90 days and apply as soon as you receive your letter of admission.",
  },
];
