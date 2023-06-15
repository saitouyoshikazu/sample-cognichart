<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-magic">
            <h3 class="heading">{{ __("Reset promotion videos") }}</h3>
            <p class="sentence">
                {{ __("There is a possibility that the promotion video will be removed from YouTube after you added to your playlist.") }}
            </p>
            <p class="sentence">
                {{ __("Cogni Chart maintains the URLs of promotion videos.") }}
            </p>
            <p class="sentence">
                {{ __("So, when you notice that the promotion video of your playlist is not being played, if you reset to the URLs of promotion videos managed by Cogni Chart, there is a possibility that the promotion video may be playable again.") }}
            </p>
            <p class="sentence">
                {{ __("(It may take a few weeks until the URL of promotion video to be fixed.)") }}
            </p>
            <ol>
                <li>
                    <p class="sentence">
                        {{ __("Click \"playlists\" menu item to open dialog.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click playlists menu item to open dialog." data-original="{{ asset("png/click-playlists-menu-item.png") }}">
                    </div>
                </li>
                <li>
                    <p class="sentence">
                        {{ __("Choose playlist.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Choose playlist." data-original="{{ asset("png/your-playlists-choose-playlist.png") }}">
                    </div>
                </li>
                <li>
                    <p class="sentence">
                        {{ __("Click Reset PV button.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click Reset PV button." data-original="{{ asset("png/click-reset-pv-button.png") }}">
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev swiper-button-preve-none resetpromotionvideos-swiper-button-prev">
        <i class="fas fa-angle-left fa-2x"></i>
    </div>
    <div class="swiper-button-next swiper-button-next-none resetpromotionvideos-swiper-button-next">
        <i class="fas fa-angle-right fa-2x"></i>
    </div>
</div>
