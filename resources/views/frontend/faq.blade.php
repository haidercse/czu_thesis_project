@extends('layouts.app')

@section('content')
<div class="page-head">
  <div><h1>FAQ &amp; Help</h1><p class="lede">Common questions about applying to Czech universities.</p></div>
</div>

<div id="faqList">
  @php
    $faqs = [
      ['q' => 'What is qualification recognition and how long does it take?', 'a' => 'Qualification recognition (nostrifikace) confirms your previous degree is equivalent to a Czech qualification. It is usually required before or during a university application and typically takes 30-60 days, depending on the authority and your country of origin.'],
      ['q' => 'Do I need an apostille on my documents?', 'a' => 'If your home country is a signatory to the Hague Apostille Convention, an apostille is usually sufficient. Otherwise, your documents will need embassy legalization instead, which can take longer.'],
      ['q' => 'How much does a Czech study visa cost and how long does it take?', 'a' => 'Fees and processing times vary by country. Generally allow 60-90 days and apply as soon as you receive your letter of admission.'],
      ['q' => 'Do I have to use a commercial agency?', 'a' => 'No. This application is designed to let you manage the process independently, free of charge. Some applicants still choose to use an agency or get informal help from someone they know; the checklist and document tracker here work either way.'],
    ];

    $glossary = [
      ['term' => 'Apostille', 'definition' => "Official certification verifying a document's authenticity for international use."],
      ['term' => 'Nostrifikace (qualification recognition)', 'definition' => 'The Czech process confirming a foreign degree is equivalent to a Czech one.'],
      ['term' => 'ECTS credits', 'definition' => 'European Credit Transfer System, the standard unit for measuring study workload in EU higher education.'],
    ];

    $resources = [
      ['label' => 'Ministry of Education qualification recognition portal', 'url' => 'https://www.msmt.cz/'],
      ['label' => 'Study in Czechia (official government study portal)', 'url' => 'https://www.studyin.cz/'],
      ['label' => 'Czech embassy locator', 'url' => 'https://www.mzv.cz/jnp/en/about_the_ministry/czech_embassies_abroad/index.html'],
    ];
  @endphp

  @foreach($faqs as $item)
    <div class="panel faq-item" style="margin-bottom:0.75rem; cursor:pointer;">
      <strong>{{ $item['q'] }}</strong>
      <p class="muted faq-answer" style="display:none; margin-top:0.6rem;">{{ $item['a'] }}</p>
    </div>
  @endforeach
</div>

<div class="panel" style="margin-top:2rem;">
  <h2>Glossary</h2>
  @foreach($glossary as $item)
    <div style="padding:0.85rem 0; border-bottom:1px solid var(--line);">
      <strong>{{ $item['term'] }}</strong>
      <p class="muted" style="margin:0.25rem 0 0;">{{ $item['definition'] }}</p>
    </div>
  @endforeach
</div>

<div class="panel" style="margin-top:1rem;">
  <h2>External Resources</h2>
  <ul style="margin-bottom:0; padding-left:1.2rem;">
    @foreach($resources as $resource)
      <li><a href="{{ $resource['url'] }}" target="_blank" rel="noopener">{{ $resource['label'] }}</a></li>
    @endforeach
  </ul>
</div>
@endsection

@push('scripts')
<script>
$('.faq-item').on('click', function() {
  $(this).find('.faq-answer').slideToggle(150);
});
</script>
@endpush
