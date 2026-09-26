@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Find your program</h1>
            <p class="lede">Search English-taught master's programs.</p>
        </div>
    </div>

    <div class="filter-bar">
        <div class="field" style="margin:0">
            <label>Search by name</label>
            <input type="text" id="filterQuery" placeholder="Program name" />
        </div>
        <div class="field" style="margin:0">
            <label>Field of study</label>
            <select id="filterField">
                <option value="">All fields</option>
                <option>Business & Economics</option>
                <option>Social Sciences</option>
                <option>Natural Sciences</option>
            </select>
        </div>
        <div class="field" style="margin:0">
            <label>University</label>
            <select id="filterUniversity">
                <option value="">All universities</option>
                @foreach ($universities as $university)
                    <option value="{{ $university->id }}">{{ $university->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="margin:0">
            <label>Location</label>
            <select id="filterLocation">
                <option value="">All locations</option>
                @foreach ($locations as $location)
                    <option value="{{ $location }}">{{ $location }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="margin:0">
            <label>Language requirement</label>
            <select id="filterLanguage">
                <option value="">Any language</option>
                <option>IELTS</option>
                <option>TOEFL</option>
                <option>Cambridge</option>
            </select>
        </div>
        <div class="field" style="margin:0">
            <label>Max tuition (EUR/year)</label>
            <input type="number" id="filterTuition" placeholder="Any" />
        </div>
        <div class="field" style="margin:0">
            <label>Sort by</label>
            <select id="filterSort">
                <option value="deadline_asc">Deadline: soonest</option>
                <option value="deadline_desc">Deadline: latest</option>
                <option value="tuition_asc">Tuition: lowest</option>
                <option value="tuition_desc">Tuition: highest</option>
                <option value="name_asc">Name: A-Z</option>
            </select>
        </div>
        <div class="field" style="margin:0; align-self:flex-end;">
            <button type="button" class="btn btn-ghost btn-sm" id="clearFilters">Clear filters</button>
        </div>
    </div>

    <p class="muted" id="resultCount"></p>
    <div class="panel" id="programResults" style="padding:0.25rem 1.75rem;"></div>
    <button type="button" class="btn btn-gold compare-float" id="compareFloat" style="display:none">Compare (0)</button>
@endsection

@push('scripts')
    <script>
        const compareKey = 'compare_programs';

        function getCompareIds() {
            try {
                return JSON.parse(sessionStorage.getItem(compareKey)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveCompareIds(ids) {
            sessionStorage.setItem(compareKey, JSON.stringify(ids));
            updateCompareButton();
        }

        function updateCompareButton() {
            const ids = getCompareIds();
            $('#compareFloat').toggle(ids.length > 0).text('Compare (' + ids.length + ')');
            $('.compare-program').each(function() {
                const id = Number($(this).data('id'));
                $(this).text(ids.includes(id) ? 'Added' : '+ Compare');
            });
        }

        function renderPrograms(list) {
            const $r = $('#programResults');
            $r.empty();

            if (list.length === 0) {
                $r.append('<p class="muted">No programs match your current filters. Try clearing one or more filters.</p>');
                $('#resultCount').text('0 programs found');
                return;
            }

            list.forEach(p => {
                $r.append(`
                    <div class="program-row">
                        <div>
                            <h3 style="margin-bottom:0.2rem">${p.program_name}</h3>
                            <div class="muted" style="font-size:0.9rem">${p.university.name}, ${p.university.location}</div>
                            <div class="meta">
                                <span>EUR ${p.tuition_fee_annual} / year</span>
                                <span>Deadline ${p.application_deadline}</span>
                                <span>${p.language_proficiency_requirement || 'Language info not listed'}</span>
                            </div>
                        </div>
                        <div class="program-actions">
                            <button type="button" class="btn btn-ghost btn-sm compare-program" data-id="${p.id}">+ Compare</button>
                            <form method="POST" action="/saved-programs/${p.id}">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm">Save</button>
                            </form>
                            <form method="POST" action="{{ route('applications.store') }}">
                                @csrf
                                <input type="hidden" name="program_id" value="${p.id}">
                                <button type="submit" class="btn btn-primary btn-sm">Start application</button>
                            </form>
                        </div>
                    </div>
                `);
            });

            $('#resultCount').text(list.length + ' programs found');
            updateCompareButton();
        }

        function loadPrograms() {
            $.get('{{ route('programs.search') }}', {
                field: $('#filterField').val(),
                university_id: $('#filterUniversity').val(),
                location: $('#filterLocation').val(),
                language: $('#filterLanguage').val(),
                max_tuition: $('#filterTuition').val(),
                keyword: $('#filterQuery').val(),
                sort: $('#filterSort').val()
            }, renderPrograms);
        }

        function clearFilters() {
            $('#filterQuery').val('');
            $('#filterField').val('');
            $('#filterUniversity').val('');
            $('#filterLocation').val('');
            $('#filterLanguage').val('');
            $('#filterTuition').val('');
            $('#filterSort').val('deadline_asc');
            loadPrograms();
        }

        $(document).on('click', '.compare-program', function() {
            const id = Number($(this).data('id'));
            const ids = getCompareIds();

            if (ids.includes(id)) {
                saveCompareIds(ids.filter(existingId => existingId !== id));
                return;
            }

            if (ids.length >= 5) {
                alert('You can compare up to 5 programs.');
                return;
            }

            ids.push(id);
            saveCompareIds(ids);
        });

        $('#compareFloat').on('click', function() {
            const ids = getCompareIds();
            if (ids.length === 0) return;
            window.location.href = '{{ route('programs.compare') }}?ids=' + ids.join(',');
        });

        $('#filterField, #filterUniversity, #filterLocation, #filterLanguage, #filterTuition, #filterSort').on('change', loadPrograms);
        $('#filterQuery').on('input', function () {
            clearTimeout(window._t);
            window._t = setTimeout(loadPrograms, 250);
        });
        $('#clearFilters').on('click', clearFilters);

        updateCompareButton();
        loadPrograms();
    </script>
@endpush
