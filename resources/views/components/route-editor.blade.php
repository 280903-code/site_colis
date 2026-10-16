@props(['routes' => []])
@php
    $routeRows = collect(old('routes', $routes))
        ->map(function ($route) {
            if (is_string($route)) {
                $route = json_decode($route, true);
            }

            if (! is_array($route)) {
                return ['from' => '', 'to' => ''];
            }

            return [
                'from' => is_string($route['from'] ?? null) ? \App\Support\Country::label($route['from']) : '',
                'to' => is_string($route['to'] ?? null) ? \App\Support\Country::label($route['to']) : '',
            ];
        })
        ->values();

    if ($routeRows->isEmpty()) {
        $routeRows->push(['from' => '', 'to' => '']);
    }

    $routeError = collect($errors->getMessages())
        ->filter(fn($messages, $field) => str_starts_with($field, 'routes.'))
        ->flatten()
        ->first();
@endphp
<div class="route-editor" data-route-editor>
    <datalist id="route-country-options">
        @foreach(\App\Support\Country::suggestions() as $country)
            <option value="{{ $country }}"></option>
        @endforeach
    </datalist>
    <div class="route-editor__rows" data-route-rows>
        @foreach($routeRows as $index => $route)
            <div class="route-editor__row" data-route-row>
                <label>
                    Départ
                    <input type="text" name="routes[{{ $index }}][from]" value="{{ $route['from'] }}" list="route-country-options" placeholder="Ex. Sénégal" maxlength="100" required>
                </label>
                <label>
                    Destination
                    <input type="text" name="routes[{{ $index }}][to]" value="{{ $route['to'] }}" list="route-country-options" placeholder="Ex. Chine" maxlength="100" required>
                </label>
                <button type="button" class="route-editor__remove" data-remove-route aria-label="Supprimer ce trajet">Supprimer</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="route-editor__add" data-add-route>Ajouter un trajet</button>
    <template data-route-template>
        <div class="route-editor__row" data-route-row>
            <label>
                Départ
                <input type="text" name="routes[__INDEX__][from]" list="route-country-options" placeholder="Ex. Sénégal" maxlength="100" required>
            </label>
            <label>
                Destination
                <input type="text" name="routes[__INDEX__][to]" list="route-country-options" placeholder="Ex. Chine" maxlength="100" required>
            </label>
            <button type="button" class="route-editor__remove" data-remove-route aria-label="Supprimer ce trajet">Supprimer</button>
        </div>
    </template>
    @if($errors->has('routes') || $routeError)
        <div class="error">{{ $errors->first('routes') ?: $routeError }}</div>
    @endif
</div>

@pushOnce('scripts', 'route-editor-script')
<script>
document.querySelectorAll('[data-route-editor]').forEach((editor) => {
  const rows = editor.querySelector('[data-route-rows]');
  const template = editor.querySelector('[data-route-template]');

  const updateIndexes = () => {
    rows.querySelectorAll('[data-route-row]').forEach((row, index) => {
      row.querySelectorAll('[name]').forEach((input) => {
        input.name = input.name.replace(/routes\[\d+\]/, `routes[${index}]`);
      });
    });
  };

  editor.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-add-route]');
    const removeButton = event.target.closest('[data-remove-route]');

    if (addButton) {
      const index = rows.querySelectorAll('[data-route-row]').length;
      const row = template.content.firstElementChild.cloneNode(true);
      row.querySelectorAll('[name]').forEach((input) => {
        input.name = input.name.replace('__INDEX__', index);
      });
      rows.append(row);
      updateIndexes();
    }

    if (removeButton) {
      const row = removeButton.closest('[data-route-row]');

      if (rows.querySelectorAll('[data-route-row]').length === 1) {
        row.querySelectorAll('input').forEach((input) => {
          input.value = '';
        });
      } else {
        row.remove();
        updateIndexes();
      }
    }
  });
});
</script>
@endPushOnce
