@extends('layout.teacher')
@include('components.alerts')

@section('content')
<div class="container">

    <div class="d-flex justify-content-end align-items-center mb-3">
        @if($activitiesType->isNotEmpty() && $tab !== 'read')
        <form action="{{ route('teacherNotification.update') }}" method="POST">
            @csrf
            {{-- Pass current tab so controller redirects back correctly --}}
            <input type="hidden" name="tab" value="{{ $tab }}">
            <button type="submit" name="allRead" value="1" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        </form>
        @endif
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-4 overflow-auto">
        <ul class="nav nav-tabs flex-nowrap text-nowrap">
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'unread' ? 'active font-weight-bold' : '' }}"
                    href="{{ route('teacher.notification', ['tab' => 'unread']) }}">
                    Unread
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'read' ? 'active font-weight-bold' : '' }}"
                    href="{{ route('teacher.notification', ['tab' => 'read']) }}">
                    Read History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'all' ? 'active font-weight-bold' : '' }}"
                    href="{{ route('teacher.notification', ['tab' => 'all']) }}">
                    All
                </a>
            </li>
        </ul>
    </div>

    {{-- Notification List --}}
    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush">
            @forelse ($activitiesType as $activity)
            <div class="list-group-item p-4 {{ $activity->is_read == 0 ? 'bg-white border-left border-primary' : 'bg-light text-muted' }}"
                style="{{ $activity->is_read == 0 ? 'border-left-width: 4px !important;' : '' }}">

                <div class="d-flex w-100 justify-content-between align-items-center">
                    {{-- Content Section --}}
                    <div class="pr-3">
                        <h6 class="mb-1 {{ $activity->is_read == 0 ? 'font-weight-bold text-dark' : '' }}">
                            {{-- Adjust these fields based on your actual table columns (e.g., $activity->title) --}}
                            {{ $activity->title ?? $activity->description ?? 'Notification Alert' }}
                        </h6>
                        <p class="mb-1 small">
                            {{ $activity->message ?? $activity->details ?? '' }}
                        </p>
                        <small class="{{ $activity->is_read == 0 ? 'text-primary' : 'text-muted' }}">
                            {{ $activity->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Individual Toggle Form --}}
                    <form action="{{ route('teacherNotification.update') }}" method="POST">
                        @csrf
                        {{-- Inputs expected by updateNotification method --}}
                        <input type="hidden" name="notification" value="{{ $activity->id }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">

                        <button type="submit"
                            class="btn btn-sm {{ $activity->is_read == 0 ? 'btn-light text-muted' : 'btn-white text-primary' }}"
                            title="{{ $activity->is_read == 0 ? 'Mark as Read' : 'Mark as Unread' }}">
                            @if($activity->is_read == 0)
                            <i class="fas fa-envelope-open"></i> <span class="d-none d-md-inline">Mark Read</span>
                            @else
                            <i class="fas fa-envelope"></i> <span class="d-none d-md-inline">Mark Unread</span>
                            @endif
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5 my-4">
                {{-- Icon with soft background --}}
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 3rem;">
                    🔕
                </div>

                {{-- Text --}}
                <h5 class="text-dark font-weight-bold">No notifications yet</h5>
                <p class="text-muted mb-0">
                    You are all caught up! We'll notify you when something new happens.
                </p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{-- Appends the current tab to pagination links --}}
        {{ $activitiesType->links() }}
    </div>
</div>
@endsection