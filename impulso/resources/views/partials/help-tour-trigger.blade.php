@php
  $helpTourPayload = json_encode($steps, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
@endphp
{{-- Botón "?" que abre la guía de ayuda; los pasos viajan serializados en data-help-tour --}}
<button
  class="help-tour-trigger"
  type="button"
  aria-label="{{ $label ?? 'Abrir guía de esta pantalla' }}"
  title="{{ $label ?? 'Ayuda guiada' }}"
  aria-haspopup="dialog"
  data-help-tour="{{ $helpTourPayload }}"
>?</button>
