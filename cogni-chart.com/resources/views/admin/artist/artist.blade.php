@php
    use App\Domain\ValueObjects\Phase;
@endphp
<div class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                {{ !empty($artistEntity) ? $artistEntity->artistName()->value() : '' }}
            </div>
        </div>
    </nav>
    <div class="card-body w-100">
        <form action="javascript: void(0);" method="post">
            {{ csrf_field() }}
            @if (!empty($chartRankingItemEntity))
            <input type="hidden" name="chartrankingitem_id" value="{{ $chartRankingItemEntity->id()->value() }}">
            @endif
            <input type="hidden" name="artist_phase" value="{{ $artist_phase }}">
            <input type="hidden" name="artist_id" value="{{ !empty($artistEntity) ? $artistEntity->id()->value() : '' }}">
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('itunes_artist_id') }}</label>
                <input type="text" name="itunes_artist_id" value="{{ !empty($artistEntity) ? $artistEntity->iTunesArtistId()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('artist_name') }}</label>
                <input type="text" name="artist_name" value="{{ !empty($artistEntity) ? $artistEntity->artistName()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row justify-content-around">
                @if (empty($artistEntity))
                <button type="button" class="btn btn-primary" onClick="$(this).parents('form').attr('action', '{{ route('artist/register') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-plus"></i>&nbsp;Register
                </button>
                @else
                <button type="button" class="btn btn-info" onClick="$(this).parents('form').attr('action', '{{ route('artist/modify') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-edit"></i>&nbsp;Modify
                </button>
                @if ($artist_phase === Phase::provisioned)
                <button type="button" class="btn btn-danger" onClick="$(this).parents('form').attr('action', '{{ route('artist/delete') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-trash-alt"></i>&nbsp;Delete
                </button>
                <button type="button" class="btn btn-primary" onClick="$(this).parents('form').attr('action', '{{ route('artist/release') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-home"></i><i class="fas fa-arrow-right"></i><i class="fas fa-globe-americas"></i>&nbsp;Release
                </button>
                @else
                <button type="button" class="btn btn-danger" onClick="$(this).parents('form').attr('action', '{{ route('artist/rollback') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-globe-americas"></i><i class="fas fa-arrow-right"></i><i class="fas fa-home"></i>&nbsp;Rollback
                </button>
                @endif
                @endif
            </div>
        </form>
    </div>
</div>
