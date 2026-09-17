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
            <input type="text" id="filterQuery" />
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
            <label>Max tuition (EUR/year)</label>
            <input type="number" id="filterTuition" />
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
                $r.append('<p class="muted">No programs found.</p>');
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
                            </div>
                        </div>
                        <div class="program-actions">
                            <button type="button" class="btn btn-ghost btn-sm compare-program" data-id="${p.id}">+ Compare</button>
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
                max_tuition: $('#filterTuition').val(),
                keyword: $('#filterQuery').val()
            }, renderPrograms);
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

        $('#filterField, #filterTuition').on('change', loadPrograms);
        $('#filterQuery').on('input', function () {
            clearTimeout(window._t);
            window._t = setTimeout(loadPrograms, 250);
        });

        updateCompareButton();
        loadPrograms();
    </script>
@endpush
