/* ============================================================
   Shared jQuery behaviors.
   Comments mark exactly where a Laravel Ajax call (Section 7.4.2)
   will later replace the mock-data logic.
   ============================================================ */

$(function () {
  // Mobile nav toggle
  $("#navToggle").on("click", function () {
    $(".app-nav").toggleClass("open");
  });

  // Highlight active nav link based on current page
  const current = window.location.pathname.split("/").pop();
  $(".app-nav nav a").each(function () {
    if ($(this).attr("href") === current) $(this).addClass("active");
  });
});

function showToast(message) {
  const $toast = $("#toast");
  $toast.text(message).addClass("show");
  setTimeout(() => $toast.removeClass("show"), 2200);
}

/* ---------------------------------------------------------------
   PROGRAM SEARCH (programs.html)
   Laravel equivalent: ProgramController@search returns JSON,
   this function's rendering logic stays almost identical —
   only the data source changes from PROGRAMS[] to an Ajax response.
   See Section 7.5.2 of the thesis.
--------------------------------------------------------------- */
function initProgramSearch() {
  function renderPrograms(list) {
    const $results = $("#programResults");
    $results.empty();

    if (list.length === 0) {
      $results.append(
        '<p class="muted">No programs match these filters. Try widening your search.</p>'
      );
      return;
    }

    list.forEach((p) => {
      const row = $(`
        <div class="program-row" data-id="${p.id}">
          <div>
            <h3 style="margin-bottom:0.2rem">${p.program_name}</h3>
            <div class="muted" style="font-size:0.9rem">${universityName(p.university_id)}</div>
            <div class="meta">
              <span>€${p.tuition_fee_annual.toLocaleString()} / year</span>
              <span>Deadline ${formatDate(p.application_deadline)}</span>
              <span>${p.language_proficiency_requirement}</span>
            </div>
          </div>
          <div style="display:flex; gap:0.5rem; flex-shrink:0">
            <button class="btn btn-ghost btn-sm compare-btn">+ Compare</button>
            <a href="checklist.html?program=${p.id}" class="btn btn-primary btn-sm">Start application</a>
          </div>
        </div>
      `);
      $results.append(row);
    });

    $("#resultCount").text(list.length + (list.length === 1 ? " program found" : " programs found"));
  }

  function applyFilters() {
    const field = $("#filterField").val();
    const uni = $("#filterUniversity").val();
    const maxTuition = parseFloat($("#filterTuition").val()) || Infinity;
    const query = $("#filterQuery").val().trim().toLowerCase();

    const filtered = PROGRAMS.filter((p) => {
      if (field && p.field_of_study !== field) return false;
      if (uni && String(p.university_id) !== uni) return false;
      if (p.tuition_fee_annual > maxTuition) return false;
      if (query && !p.program_name.toLowerCase().includes(query)) return false;
      return true;
    });

    renderPrograms(filtered);
  }

  // Populate university filter
  UNIVERSITIES.forEach((u) => {
    $("#filterUniversity").append(`<option value="${u.id}">${u.name}</option>`);
  });

  // Debounced live filtering (Section 7.4.2 pattern)
  let debounceTimer;
  $("#filterField, #filterUniversity, #filterTuition").on("change", applyFilters);
  $("#filterQuery").on("input", function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 250);
  });

  $(document).on("click", ".compare-btn", function () {
    $(this).text("✓ Added").prop("disabled", true);
    showToast("Added to comparison");
  });

  applyFilters();
}

/* ---------------------------------------------------------------
   APPLICATION CHECKLIST (checklist.html)
   Laravel equivalent: PATCH /steps/{step} — Section 7.3.2 / 7.4.2
--------------------------------------------------------------- */
function initChecklist() {
  function progressPercent() {
    const completed = CHECKLIST_STEPS.filter((s) => s.status === "completed").length;
    return Math.round((completed / CHECKLIST_STEPS.length) * 100);
  }

  function statusLabel(status) {
    return { not_started: "Not started", in_progress: "In progress", completed: "Completed" }[status];
  }
  function statusBadgeClass(status) {
    return { not_started: "badge-pending", in_progress: "badge-progress", completed: "badge-complete" }[status];
  }

  function renderChecklist() {
    const $list = $("#stepList");
    $list.empty();

    CHECKLIST_STEPS.forEach((step, i) => {
      const $step = $(`
        <div class="step ${step.status === "completed" ? "is-complete" : ""}" data-id="${step.id}">
          <div class="step-num">${step.status === "completed" ? "✓" : i + 1}</div>
          <div>
            <div class="step-title">${step.name}</div>
            <p class="step-desc">${step.description}</p>
          </div>
          <div style="text-align:right">
            <span class="badge ${statusBadgeClass(step.status)}">${statusLabel(step.status)}</span><br/>
            <select class="step-status-select" data-step-id="${step.id}" style="margin-top:0.5rem; font-size:0.8rem; padding:0.3rem 0.4rem;">
              <option value="not_started" ${step.status === "not_started" ? "selected" : ""}>Not started</option>
              <option value="in_progress" ${step.status === "in_progress" ? "selected" : ""}>In progress</option>
              <option value="completed" ${step.status === "completed" ? "selected" : ""}>Completed</option>
            </select>
          </div>
        </div>
      `);
      $list.append($step);
    });

    const pct = progressPercent();
    $("#progressFill").css("width", pct + "%");
    $("#progressLabel").text(pct + "% complete");
  }

  // Ajax-style update (Section 7.3.2 PATCH /steps/{step} equivalent)
  $(document).on("change", ".step-status-select", function () {
    const stepId = parseInt($(this).data("step-id"));
    const newStatus = $(this).val();
    const step = CHECKLIST_STEPS.find((s) => s.id === stepId);
    step.status = newStatus;

    // In Laravel this block becomes:
    // $.ajax({ url: '/steps/' + stepId, method: 'PATCH', data: { status: newStatus }, success: ... })
    renderChecklist();
    showToast("Step updated");
  });

  renderChecklist();
}

/* ---------------------------------------------------------------
   DOCUMENT MANAGEMENT (documents.html)
   Laravel equivalent: POST /documents — Section 7.4.3
--------------------------------------------------------------- */
function initDocuments() {
  function renderDocs() {
    const $list = $("#docList");
    $list.empty();
    if (DOCUMENTS.length === 0) {
      $list.append('<p class="muted">No documents uploaded yet.</p>');
      return;
    }
    DOCUMENTS.forEach((d) => {
      $list.append(`
        <div class="doc-row">
          <span>📄</span>
          <div>
            <div style="font-weight:600">${d.name}</div>
            <div class="muted" style="font-size:0.8rem">${d.type} · ${d.size} · uploaded ${d.date}</div>
          </div>
          <button class="btn btn-ghost btn-sm">Download</button>
          <button class="btn btn-ghost btn-sm remove-doc" data-id="${d.id}">Remove</button>
        </div>
      `);
    });
  }

  const $dropzone = $("#dropzone");
  const $fileInput = $("#fileInput");

  $dropzone.on("click", () => $fileInput.trigger("click"));

  $dropzone.on("dragover", function (e) {
    e.preventDefault();
    $(this).addClass("drag-over");
  });
  $dropzone.on("dragleave", function () {
    $(this).removeClass("drag-over");
  });
  $dropzone.on("drop", function (e) {
    e.preventDefault();
    $(this).removeClass("drag-over");
    handleFiles(e.originalEvent.dataTransfer.files);
  });
  $fileInput.on("change", function () {
    handleFiles(this.files);
  });

  function handleFiles(files) {
    if (!files.length) return;
    const file = files[0];
    const allowed = ["application/pdf", "image/jpeg", "image/png"];
    if (!allowed.includes(file.type)) {
      showToast("Only PDF, JPG, or PNG files are accepted");
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      showToast("File exceeds the 5MB limit");
      return;
    }

    // In Laravel this becomes a FormData Ajax POST to /documents
    DOCUMENTS.push({
      id: Date.now(),
      name: file.name,
      type: $("#docType").val() || "Other",
      size: (file.size / (1024 * 1024)).toFixed(1) + " MB",
      date: new Date().toISOString().slice(0, 10),
    });
    renderDocs();
    showToast("Document uploaded");
  }

  $(document).on("click", ".remove-doc", function () {
    const id = parseInt($(this).data("id"));
    const idx = DOCUMENTS.findIndex((d) => d.id === id);
    if (idx > -1) DOCUMENTS.splice(idx, 1);
    renderDocs();
  });

  renderDocs();
}

/* ---------------------------------------------------------------
   FAQ (documents.html footer / faq section reused on dashboard)
--------------------------------------------------------------- */
function initFaq() {
  const $wrap = $("#faqList");
  FAQS.forEach((item, i) => {
    $wrap.append(`
      <div class="panel" style="margin-bottom:0.75rem; cursor:pointer" data-i="${i}">
        <strong>${item.q}</strong>
        <p class="muted faq-answer" style="display:none; margin-top:0.6rem">${item.a}</p>
      </div>
    `);
  });
  $wrap.on("click", "> div", function () {
    $(this).find(".faq-answer").slideToggle(150);
  });
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" });
}
