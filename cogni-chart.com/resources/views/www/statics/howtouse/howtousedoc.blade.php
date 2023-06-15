<div class="w-100">
    <div class="wakeup-board-wrapper" id="wakeup-board-wrapper">
    </div>
</div>
<div class="w-100">
    <div class="swiper-container tmp-menu-swiper-container pl-4 pr-4">
        <div class="swiper-wrapper h-100">
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#howtocreateplaylist">
                    {{ __("How to create playlist") }}
                </span>
            </div>
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#howtoplayplaylist">
                    {{ __("How to append music, play playlist") }}
                </span>
            </div>
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#howtoeditplaylists">
                    {{ __("How to edit playlists") }}
                </span>
            </div>
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#howtoeditmusics">
                    {{ __("How to edit musics") }}
                </span>
            </div>
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#resetpromotionvideos">
                    {{ _("Reset promotion videos") }}
                </span>
            </div>
            <div class="swiper-slide menu-swiper-item h-100">
                <span class="transform-y-center" data-wakeup-target="#exportplaylistimportplaylist">
                    {{ __("Export playlist, import playlist") }}
                </span>
            </div>
        </div>
        <div class="swiper-button-prev swiper-button-preve-none swiper-button-preve-sm menu-swiper-button-prev">
            <i class="fas fa-angle-left" style="color: #333333;"></i>
        </div>
        <div class="swiper-button-next swiper-button-next-none swiper-button-next-sm menu-swiper-button-next">
            <i class="fas fa-angle-right" style="color: #333333;"></i>
        </div>
    </div>
</div>

<div class="d-none" id="howtocreateplaylist">
    @include('www.statics.howtouse.howtocreate')
</div>
<div class="d-none" id="howtoplayplaylist">
    @include('www.statics.howtouse.howtoplayplaylist')
</div>
<div class="d-none" id="howtoeditplaylists">
    @include('www.statics.howtouse.howtoeditplaylists')
</div>
<div class="d-none" id="howtoeditmusics">
    @include('www.statics.howtouse.howtoeditmusics')
</div>
<div class="d-none" id="resetpromotionvideos">
    @include('www.statics.howtouse.resetpromotionvideos')
</div>
<div class="d-none" id="exportplaylistimportplaylist">
    @include('www.statics.howtouse.exportplaylistimportplaylist')
</div>
