@props(['agency', 'flight'])
@php
    $countries = ['SN' => 'Sénégal', 'KM' => 'Comores', 'FR' => 'France'];
    $cities = ['SN' => 'Dakar', 'KM' => 'Moroni', 'FR' => 'Paris'];
    $percentage = round($flight->remaining_kg / $flight->total_kg * 100);
    $class = $flight->remaining_kg === 0 ? 'full' : ($percentage <= 30 ? 'low' : '');
    $deadline = $flight->flight_date->copy()->subDay();
    $message = 'Bonjour ' . $agency->name . ', je voudrais réserver des kilos sur le vol du ' . $flight->flight_date->locale('fr')->isoFormat('D MMMM YYYY') . ' (' . $cities[$flight->from_country] . ' → ' . $cities[$flight->to_country] . '). Quel est le tarif et jusqu\'à quand puis-je déposer mon colis ?';
@endphp
<article class="fc {{ $class }}">
    <div class="head">
        <div class="date">
            <b>{{ $flight->flight_date->day }}</b>
            <span>{{ $flight->flight_date->locale('fr')->isoFormat('MMM') }}</span>
        </div>
        <div>
            <p class="rt">{{ $cities[$flight->from_country] }} → {{ $cities[$flight->to_country] }}</p>
            <p class="wk"><em>{{ $flight->flight_date->locale('fr')->isoFormat('dddd') }}</em> · ✈ {{ $flight->airline }}</p>
        </div>
    </div>
    <div>
        <p class="kgl">
            {{ $flight->remaining_kg === 0 ? '<strong>Complet</strong>' : '<strong>' . $flight->remaining_kg . '</strong> kg restants' }} · sur {{ $flight->total_kg }} kg
        </p>
        <div class="bar" role="img" aria-label="{{ $percentage }}% de places restantes"><i style="width:{{ $percentage }}%"></i></div>
    </div>
    <p class="dep">Dépôt avant le {{ $deadline->locale('fr')->isoFormat('D MMMM') }}, 18h</p>
    <div class="foot2">
        <span class="pr">{{ $flight->price_per_kg }} {{ $flight->currency }}/kg</span>
        @if($flight->remaining_kg > 0)
            <a class="btn wa" href="https://wa.me/{{ $agency->whatsapp }}?text={{ urlencode($message) }}" target="_blank" rel="noopener">Réserver</a>
        @else
            <span class="sub2">Plus de places</span>
        @endif
    </div>
</article>
