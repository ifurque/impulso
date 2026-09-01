@if(!empty($globalInbox))
<details class="business-inbox" data-global-inbox>
  <summary aria-label="Abrir bandeja de mensajes y notificaciones">
    <span aria-hidden="true">▰</span>
    @if(($globalInbox['total'] ?? 0) > 0)
      <b>{{ $globalInbox['total'] }}</b>
    @endif
  </summary>
  <div class="business-inbox-menu">
    <div class="business-inbox-title">
      <div>
        <strong>{{ $globalInbox['title'] }}</strong>
        <span>{{ $globalInbox['subtitle'] }}</span>
      </div>
      <span class="inbox-close">×</span>
    </div>

    <div class="inbox-tabs" role="tablist">
      <button class="inbox-tab is-active" type="button" data-inbox-tab="messages">
        {{ $globalInbox['message_tab'] }} <b>{{ $globalInbox['messages']->count() }}</b>
      </button>
      <button class="inbox-tab" type="button" data-inbox-tab="notifications">
        {{ $globalInbox['notification_tab'] }} <b>{{ $globalInbox['notifications']->count() }}</b>
      </button>
    </div>

    <div class="inbox-pane is-active" data-inbox-pane="messages">
      @forelse($globalInbox['messages'] as $message)
        <a href="{{ $message['href'] }}" class="inbox-conversation">
          <span class="inbox-avatar">{{ $message['avatar'] ?? 'M' }}</span>
          <span>
            <strong>{{ $message['title'] }}</strong>
            <small>{{ $message['subtitle'] }}</small>
            <em>{{ $message['excerpt'] }}</em>
          </span>
          <i>{{ $message['badge'] ?? 'Nuevo' }}</i>
        </a>
      @empty
        <div class="inbox-empty">
          <strong>Tu bandeja esta al dia.</strong>
          <span>No hay mensajes recientes.</span>
        </div>
      @endforelse
    </div>

    <div class="inbox-pane" data-inbox-pane="notifications">
      @forelse($globalInbox['notifications'] as $notification)
        <a href="{{ $notification['href'] }}" class="inbox-notification">
          <span class="notification-icon">{{ $notification['icon'] ?? 'i' }}</span>
          <span>
            <strong>{{ $notification['title'] }}</strong>
            <small>{{ $notification['subtitle'] }}</small>
            <em>{{ $notification['excerpt'] }}</em>
          </span>
        </a>
      @empty
        <div class="inbox-empty">
          <strong>No hay novedades.</strong>
          <span>Cuando haya actividad aparecera aca.</span>
        </div>
      @endforelse
    </div>
  </div>
</details>

<script>
  (() => {
    const inbox = document.querySelector('[data-global-inbox]');
    if (!inbox) return;

    const tabs = inbox.querySelectorAll('.inbox-tab');
    const panes = inbox.querySelectorAll('.inbox-pane');

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        tabs.forEach((item) => item.classList.remove('is-active'));
        panes.forEach((item) => item.classList.remove('is-active'));

        tab.classList.add('is-active');
        inbox.querySelector(`[data-inbox-pane="${tab.dataset.inboxTab}"]`)?.classList.add('is-active');
      });
    });
  })();
</script>
@endif
