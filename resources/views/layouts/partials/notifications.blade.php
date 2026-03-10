@php
    $unread = auth()->check() ? auth()->user()->unreadNotifications()->latest()->take(10)->get() : collect();
    $count = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        @if($count > 0)
            <span class="badge badge-warning navbar-badge">{{ $count }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header">{{ $count }} notificaciones</span>
        <div class="dropdown-divider"></div>

        @forelse($unread as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item">
                <strong>{{ $notification->data['title'] ?? 'Notificación' }}</strong>
                <div class="small text-muted">{{ $notification->data['message'] ?? '' }}</div>
            </a>
            <div class="dropdown-divider"></div>
        @empty
            <span class="dropdown-item text-muted">Sin notificaciones</span>
        @endforelse
    </div>
</li>