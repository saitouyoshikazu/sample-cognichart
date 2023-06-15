<?php
    use App\Domain\ValueObjects\Phase;
    $adminUserFactory = app('App\Domain\AdminUser\AdminUserFactoryInterface');
    $adminUserEntity = $adminUserFactory->create(
        \Auth::user()->id,
        \Auth::user()->name,
        \Auth::user()->email,
        \Auth::user()->is_super,
        \Auth::user()->updated_at
    );
?>
<nav class="nav nav-pills flex-column">
    @if ($adminUserEntity->isSuperUser())
    <a href="{{ route('adminuser/list') }}" class="nav-link {{ ($adminMenu === 'AdminUser') ? ' active' : '' }}">AdminUser</a>
    @endif
    <a href="{{ route('chart/list', ['chart_phase' => Phase::released]) }}" class="nav-link {{ ($adminMenu === 'Chart') ? ' active' : '' }}">Chart</a>
    <a href="{{ route('chartterm/list') }}" class="nav-link {{ ($adminMenu === 'ChartTerm') ? ' active' : '' }}">ChartTerm</a>
    <a href="{{ route('chartrankingitem/notattached') }}" class="nav-link {{ ($adminMenu === 'ChartRankingItem') ? ' active' : '' }}">ChartRankingItem</a>
    <a href="{{ route('artist/search', ['artist_phase' => Phase::provisioned]) }}" class="nav-link {{ ($adminMenu === 'Artist') ? ' active' : '' }}">Artist</a>
    <a href="{{ route('music/search', ['music_phase' => Phase::provisioned]) }}" class="nav-link {{ ($adminMenu === 'Music') ? ' active' : '' }}">Music</a>
</nav>
