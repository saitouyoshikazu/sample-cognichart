<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-hazel">
            <h3 class="heading">{{ __("How to edit musics") }}</h3>
            <p class="sentence">
                {{ __("You can change the order of musics, and delete music from your playlist.") }}
            </p>
            <ul>
                <li>
                    <a href="javascript:void(0);" class="swiper-pagination-link" data-fire-pagination="Go to slide 2">
                        {{ __("Change the order of musics") }}
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="swiper-pagination-link" data-fire-pagination="Go to slide 3">
                        {{ __("Delete music from playlist") }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-hazel">
            <h3 class="heading">{{ __("How to edit musics") }}</h3>
            <h5 class="subheading">{{ __("Change the order of musics") }}</h5>
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
                        {{ __("Drag and drop music.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Drag and drop music." data-original="{{ asset("png/drag-and-drop-music.png") }}">
                    </div>
                </li>
            </ol>
        </div>
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-hazel">
            <h3 class="heading">{{ __("How to edit musics") }}</h3>
            <h5 class="subheading">{{ __("Delete music from playlist") }}</h5>
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
                        {{ __("Click trash icon and click ok on confirmation dialog.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click trash icon and click ok on confirmation dialog." data-original="{{ asset("png/music-click-trash-icon.png") }}">
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev swiper-button-preve-none howtoeditmusics-swiper-button-prev">
        <i class="fas fa-angle-left fa-2x"></i>
    </div>
    <div class="swiper-button-next swiper-button-next-none howtoeditmusics-swiper-button-next">
        <i class="fas fa-angle-right fa-2x"></i>
    </div>
</div>
