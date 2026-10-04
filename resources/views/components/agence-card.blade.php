@props(['agency', 'from' => '_', 'to' => '_'])
@php
    $countries = ['SN' => 'Sénégal', 'KM' => 'Comores', 'FR' => 'France'];
    $cities = ['SN' => 'Dakar', 'KM' => 'Moroni', 'FR' => 'Paris'];
    $initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1), explode(' ', $agency->name)));
    $initials = mb_substr($initials, 0, 2);

    $flights = $agency->flights
        ->filter(fn($f) => $f->flight_date->isFuture() && $f->status !== 'cancelled')
        ->filter(fn($f) => $from === '_' || $f->from_country === $from)
        ->filter(fn($f) => $to === '_' || $f->to_country === $to)
        ->sortBy('flight_date');

    $nextFlight = $flights->first(fn($f) => $f->remaining_kg > 0);
@endphp
<article class="ag">
    <div class="ag-h"><div class="av">{{ $initials }}</div><div class="ag-t"><h3>{{ $agency->name }}</h3><p>📍 {{ $agency->address }}</p></div></div>
    <div class="tags">
        @foreach($agency->routes as $route)
            <span class="tag">{{ $countries[$route->from_country] }} → {{ $countries[$route->to_country] }}</span>
        @endforeach
    </div>
    @if($nextFlight)
        <div class="nx">
            <p class="nx-l">Prochain vol avec places</p>
            <div class="nx-b">
                <div class="date">
                    <b>{{ $nextFlight->flight_date->day }}</b>
                    <span>{{ $nextFlight->flight_date->locale('fr')->isoFormat('MMM') }}</span>
                </div>
                <div class="nx-i">
                    <p class="rt">{{ $cities[$nextFlight->from_country] }} → {{ $cities[$nextFlight->to_country] }}</p>
                    <p class="kgl"><strong>{{ $nextFlight->remaining_kg }}</strong> kg restants · {{ $nextFlight->price_per_kg }} {{ $nextFlight->currency }}/kg</p>
                    <div class="bar"><i style="width:{{ round($nextFlight->remaining_kg / $nextFlight->total_kg * 100) }}%"></i></div>
                </div>
            </div>
        </div>
    @else
        <div class="nx nx-0">
            <p>{{ $flights->count() > 0 ? 'Tous les vols sont complets pour le moment.' : 'Consultez les vols de l\'agence.' }}</p>
        </div>
    @endif
    <div class="ag-f">
        <a class="btn" href="{{ route('agence.show', ['slug' => $agency->slug, 'from' => $from, 'to' => $to]) }}">Voir les vols</a>
        <a class="btn o" href="https://wa.me/{{ $agency->whatsapp }}?text={{ urlencode('Bonjour ' . $agency->name . ', je voudrais des informations sur vos envois de colis.') }}" target="_blank" rel="noopener" aria-label="Écrire à {{ $agency->name }} sur WhatsApp">WhatsApp</a>
    </div>
</article>
