@extends('layouts.admin')

@section('content')
<div class="row g-4">
    @php
        $cards = [
            ['label' => __('messages.dashboard_stats.total_cards'), 'value' => $total_cards, 'icon' => 'fa-id-card', 'tone' => '#0f5132'],
            ['label' => __('messages.dashboard_stats.generated_cards'), 'value' => $generated_cards, 'icon' => 'fa-qrcode', 'tone' => '#1d4ed8'],
            ['label' => __('messages.men'), 'value' => $men, 'icon' => 'fa-mars', 'tone' => '#2563eb'],
            ['label' => __('messages.women'), 'value' => $women, 'icon' => 'fa-venus', 'tone' => '#be185d'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background-color: {{ $card['tone'] }};">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="display-6 fw-bold mb-0">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card me-2 text-success"></i> {{ __('messages.dashboard_stats.latest_cards') }}</h5>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.dashboard_stats.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.licence') }}</th>
                            <th>{{ __('messages.full_name') }}</th>
                            <th>{{ __('messages.gender') }}</th>
                            <th>{{ __('messages.grade') }}</th>
                            <th class="text-end">{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCards as $card)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $card->licence_number }}</span></td>
                                <td class="fw-semibold">{{ $card->full_name }}</td>
                                <td>{{ $card->gender === 'F' ? __('messages.female') : __('messages.male') }}</td>
                                <td>{{ $card->grade ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.cards.show', $card) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-id-card mb-3" style="font-size: 42px; opacity: 0.2;"></i>
                                    <div>{{ __('messages.dashboard_stats.no_cards_yet') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('messages.app_name') }}</h5>
                <p class="text-muted mb-4">{{ __('messages.dashboard_stats.platform_description') }}</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.cards.create') }}" class="btn text-white" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('messages.dashboard_stats.add_card') }}
                    </a>
                    <a href="{{ route('admin.settings', ['tab' => 'official-info']) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sliders-h me-1"></i> {{ __('messages.dashboard_stats.customize_settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-stat {
        border-radius: 8px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex: 0 0 auto;
    }
</style>
@endsection
