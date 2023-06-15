@php
    use App\Domain\ValueObjects\Phase;
@endphp
<div class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                {{ !empty($musicEntity) ? $musicEntity->musicTitle()->value() : '' }} / {{ !empty($artistEntity) ? $artistEntity->artistName()->value() : '' }}
            </div>
        </div>
    </nav>
    <div class="card-body w-100">
        <form action="javascript:void(0);" class="inline-form"  method="post">
            {{ csrf_field() }}
            @if (!empty($chartRankingItemEntity))
            <input type="hidden" name="chartrankingitem_id" value="{{ $chartRankingItemEntity->id()->value() }}">
            @endif
            <input type="hidden" name="music_phase" value="{{ $music_phase }}">
            <input type="hidden" name="music_id" value="{{ !empty($musicEntity) ? $musicEntity->id()->value() : '' }}">
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('itunes_artist_id') }}</label>
                <input type="text" name="itunes_artist_id" value="{{ !empty($musicEntity) ?  $musicEntity->iTunesArtistId()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('music_title') }}</label>
                <input type="text" name="music_title" value="{{ !empty($musicEntity) ? $musicEntity->musicTitle()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row form-group{{ !empty($musicEntity) && empty($musicEntity->iTunesBaseUrl()) ? ' bg-warning' : '' }}">
                <label class="col-form-label col-3">{{ __('itunes_base_url') }}</label>
                <input type="text" name="itunes_base_url" value="{{ !empty($musicEntity) && !empty($musicEntity->iTunesBaseUrl())? $musicEntity->iTunesBaseUrl()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('promotion_video_url') }}</label>
                <input type="text" name="promotion_video_url" value="{{ (!empty($musicEntity) && !empty($musicEntity->promotionVideoUrl())) ? $musicEntity->promotionVideoUrl()->value() : '' }}" class="form-control col-9">
            </div>
            @if (!empty($musicEntity) && !empty($musicEntity->promotionVideoUrl()))
            <div class="form-row">
                <div id="promotionvideo-{{ $musicEntity->promotionVideoUrl()->value() }}" class="lazypromotionvideo" data-promotionvideoid="{{ $musicEntity->promotionVideoUrl()->value() }}"></div>
            </div>
            @endif
            <div class="form-row form-group">
                <label class="col-form-label col-3">{{ __('thumbnail_url') }}</label>
                <input type="text" name="thumbnail_url" value="{{ (!empty($musicEntity) && !empty($musicEntity->thumbnailUrl())) ? $musicEntity->thumbnailUrl()->value() : '' }}" class="form-control col-9">
            </div>
            <div class="form-row justify-content-around">
                @if (empty($musicEntity))
                <button type="button" class="btn btn-primary" onClick="$(this).parents('form').attr('action', '{{ route('music/register') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-plus"></i>&nbsp;Register
                </button>
                @else
                <button type="button" class="btn btn-info" onClick="$(this).parents('form').attr('action', '{{ route('music/modify') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-edit"></i>&nbsp;Modify
                </button>
                @if ($music_phase === Phase::provisioned)
                <button type="button" class="btn btn-danger" onClick="$(this).parents('form').attr('action', '{{ route('music/delete') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-trash-alt"></i>&nbsp;Delete
                </button>
                <button type="button" class="btn btn-primary" onClick="$(this).parents('form').attr('action', '{{ route('music/release') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-home"></i><i class="fas fa-arrow-right"></i><i class="fas fa-globe-americas"></i>&nbsp;Release
                </button>
                @else
                <button type="button" class="btn btn-danger" onClick="$(this).parents('form').attr('action', '{{ route('music/rollback') }}'); $(this).parents('form').submit();">
                    <i class="fas fa-globe-americas"></i><i class="fas fa-arrow-right"></i><i class="fas fa-home"></i>&nbsp;Rollback
                </button>
                @endif
                @endif
            </div>
        </form>
    </div>
</div>
