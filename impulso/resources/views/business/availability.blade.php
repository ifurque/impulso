@extends('layouts.app')
@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Panel de {{ $business->name }}</p>
      <h1>Horarios de atención</h1>
      <p class="muted">Configura cuándo estás disponible para atender turnos.</p>
    </div>
    <a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a>
  </div>

  <div class="panel" style="max-width: 800px;">
    <form method="POST" action="{{ route('availability.update', $business) }}" class="form">
      @csrf
      
      <div class="form-section">
        <label class="check">
          <input type="hidden" name="delivery_enabled" value="0">
          <input type="checkbox" id="delivery-enabled" name="delivery_enabled" value="1" @if($business->delivery_enabled) checked @endif>
          <span>Este emprendimiento realiza entregas a domicilio</span>
        </label>
      </div>

      <div class="form-section delivery-grid" id="delivery-settings">
        <label>
          Radio de entrega (km)
          <input type="number" name="delivery_radius_km" min="0" max="50" value="{{ old('delivery_radius_km', $business->delivery_radius_km ?? 0) }}">
        </label>
        <label>
          Costo de envío
          <input type="number" name="delivery_cost" min="0" step="50" value="{{ old('delivery_cost', $business->delivery_cost ?? 0) }}">
        </label>
      </div>

      <div class="form-section payment-grid" id="payment-settings">
        <div>
          <p style="margin: 0; font-weight: 600;">Pagos que acepta el cliente</p>
          <div class="choice-list">
            @foreach(['efectivo','transferencia','tarjeta','mercado_pago','qr'] as $method)
              <label class="check small-check">
                <input type="checkbox" name="customer_payment_methods[]" value="{{ $method }}" @checked(in_array($method, $business->payment_methods_customer ?? []))>
                <span>{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
              </label>
            @endforeach
          </div>
        </div>
        <div>
          <p style="margin: 0; font-weight: 600;">Pagos que usa el emprendedor</p>
          <div class="choice-list">
            @foreach(['transferencia','mercado_pago','efectivo','tarjeta','qr'] as $method)
              <label class="check small-check">
                <input type="checkbox" name="business_payment_methods[]" value="{{ $method }}" @checked(in_array($method, $business->payment_methods_business ?? []))>
                <span>{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="form-section appointment-settings">
        <label class="check">
          <input type="hidden" name="appointments_enabled" value="0">
          <input type="checkbox" id="appointments-enabled" name="appointments_enabled" value="1" @if($business->appointments_enabled) checked @endif>
          <span>Habilitar turnos y citas para este emprendimiento</span>
        </label>
        <p class="muted" style="font-size: 0.85rem; margin-top: 8px;">Los clientes podrán solicitar turnos cuando esta opción esté habilitada.</p>
      </div>

      <div class="form-section slot-duration-field" id="appointments-settings">
        <label for="appointment_slot_duration">Duración de cada turno</label>
        <select id="appointment_slot_duration" name="appointment_slot_duration" required>
          @foreach([15 => '15 minutos', 30 => '30 minutos', 60 => '1 hora'] as $duration => $label)
            <option value="{{ $duration }}" @selected(($business->appointment_slot_duration ?? 30) == $duration)>{{ $label }}</option>
          @endforeach
        </select>
        <p class="muted" style="font-size: 0.85rem; margin-top: 8px;">La agenda se generará automáticamente dentro del horario de cada día.</p>
      </div>

      <hr>

      <h3 style="margin: 20px 0 16px;">Días y horarios de atención</h3>

      @foreach($days as $dayKey => $dayLabel)
        <div class="hours-section" data-hours-section>
          <div class="hours-header">
            <div class="day-label">
              <strong>{{ $dayLabel }}</strong>
            </div>
            <label class="check closed-check">
              <input type="checkbox" name="hours[{{ $loop->index }}][is_closed]" value="1" @if($availability[$dayKey]->is_closed) checked @endif>
              <span>Cerrado</span>
            </label>
          </div>

          <input type="hidden" name="hours[{{ $loop->index }}][day_of_week]" value="{{ $dayKey }}">

          <div class="hours-inputs">
            <div class="time-input">
              <span>Desde</span>
              <input
                type="time"
                name="hours[{{ $loop->index }}][opening_time]"
                value="{{ $availability[$dayKey]->opening_time ?? '09:00' }}"
                @if($availability[$dayKey]->is_closed) disabled @endif
              >
            </div>
            <div class="time-input">
              <span>Hasta</span>
              <input
                type="time"
                name="hours[{{ $loop->index }}][closing_time]"
                value="{{ $availability[$dayKey]->closing_time ?? '18:00' }}"
                @if($availability[$dayKey]->is_closed) disabled @endif
              >
            </div>
          </div>
        </div>
      @endforeach

      <hr>

      <button class="button full" style="margin-top: 24px;">Guardar cambios</button>
    </form>
  </div>
</section>

<style>
  .form-section { margin-bottom: 20px; }
  .delivery-grid, .payment-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px; }
  .check { display: flex !important; align-items: center; font-size: 0.9rem !important; font-weight: 400 !important; gap: 10px; }
  .small-check { margin-bottom: 6px; }
  .check input { width: auto; }
  .choice-list { display: grid; gap: 8px; margin-top: 10px; }
  .section-disabled { opacity: 0.55; pointer-events: none; }
  .hours-section { margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
  .hours-header { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 15px; margin-bottom: 12px; }
  .day-label { font-weight: 600; margin: 0; }
  .closed-check { margin: 0; }
  .hours-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .time-input { display: grid; gap: 8px; }
  .time-input label { font-size: 0.82rem; font-weight: 600; }
  .time-input input { border: 1px solid var(--line); padding: 10px; border-radius: 4px; }
  .time-input input:disabled { background: #f0f0f0; color: #999; }
  h3 { margin: 0 0 16px; font-size: 1.1rem; }
  @media (max-width: 760px) { .delivery-grid, .payment-grid, .hours-inputs { grid-template-columns:1fr; } .hours-header { grid-template-columns:1fr; gap:8px; } }
</style>

<script>
  const deliveryCheckbox = document.querySelector('#delivery-enabled');
  const deliverySettings = document.querySelector('#delivery-settings');
  const paymentSettings = document.querySelector('#payment-settings');

  const appointmentsCheckbox = document.querySelector('#appointments-enabled');
  const appointmentsSettings = document.querySelector('#appointments-settings');
  const hoursSections = document.querySelectorAll('[data-hours-section]');

  const setSectionState = (section, enabled) => {
    if (!section) return;
    section.classList.toggle('section-disabled', !enabled);
    section.querySelectorAll('input, select, textarea, button').forEach((field) => {
      field.disabled = !enabled;
    });
  };

  const setHoursState = (enabled) => {
    hoursSections.forEach((section) => {
      section.classList.toggle('section-disabled', !enabled);

      const closedCheck = section.querySelector('.closed-check input');
      const timeInputs = section.querySelectorAll('.time-input input');

      if (!closedCheck) {
        return;
      }

      closedCheck.disabled = !enabled;
      timeInputs.forEach((input) => {
        input.disabled = !enabled || closedCheck.checked;
      });
    });
  };

  const syncDelivery = () => {
    const enabled = !!deliveryCheckbox?.checked;
    setSectionState(deliverySettings, enabled);
    setSectionState(paymentSettings, enabled);
  };

  const syncAppointments = () => {
    const enabled = !!appointmentsCheckbox?.checked;
    setSectionState(appointmentsSettings, enabled);
    setHoursState(enabled);
  };

  deliveryCheckbox?.addEventListener('change', syncDelivery);
  appointmentsCheckbox?.addEventListener('change', syncAppointments);

  document.querySelectorAll('.closed-check input').forEach((checkbox, index) => {
    const section = checkbox.closest('.hours-section');
    const timeInputs = section.querySelectorAll('.time-input input');
    
    checkbox.addEventListener('change', () => {
      timeInputs.forEach(input => {
        input.disabled = checkbox.checked || !appointmentsCheckbox?.checked;
      });
    });
  });

  syncDelivery();
  syncAppointments();
</script>
@endsection
