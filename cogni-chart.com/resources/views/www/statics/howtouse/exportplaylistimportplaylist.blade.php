<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-sweet-period">
            <h3 class="heading">{{ __("Export playlist, import playlist") }}</h3>
            <div class="alert alert-danger">
                <p>
                    {{ __("This feature is not supported on mobile devices, such as iOS devices and Android devices." ) }}
                </p>
                <p>
                    {{ __("This feature is only supported on PC devices.") }}
                </p>
            </div>
            <p class="sentence">
                {{ __("In Cogni Chart, your playlists are stored in the storage space of the web browser.") }}
            </p>
            <p class="sentence">
                {{ __("Please export and import your playlist in the following cases.") }}
            </p>
            <ul class="sentence">
                <li>
                    {{ __("When reached to the capacity limit of the storage space of the browser.") }}
                </li>
                <li>
                    {{ __("When you want to share your playlist with multiple devices.") }}
                </li>
                <li>
                    {{ __("When you want to share your playlist with multiple browser.") }}
                </li>
            </ul>
            <p class="sentence">
                {{ __("About the way of exporting and importing, please look up followings.") }}
            </p>
            <ul>
                <li>
                    <a href="javascript:void(0);" class="swiper-pagination-link" data-fire-pagination="Go to slide 2">
                        {{ __("Export playlist") }}
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="swiper-pagination-link" data-fire-pagination="Go to slide 3">
                        {{ __("Import playlist") }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-sweet-period">
            <h3 class="heading">{{ __("Export playlist, import playlist") }}</h3>
            <h5 class="subheading">{{ __("Export playlist") }}</h5>
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
                        {{ __("Click Export button.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click Export button." data-original="{{ asset("png/click-export-button.png") }}">
                    </div>
                </li>
            </ol>
        </div>
        <div class="swiper-slide wakeup-board pl-4 pr-4 asset-sweet-period">
            <h3 class="heading">{{ __("Export playlist, import playlist") }}</h3>
            <h5 class="subheading">{{ __("Import playlist") }}</h5>
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
                        {{ __("Click ••• icon in the dialog header.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click ••• icon in the dialog header." data-original="{{ asset("png/click-•••-icon.png") }}">
                    </div>
                </li>
                <li>
                    <p class="sentence">
                        {{ __("Click \"import from file\" and choose playlist file.") }}
                    </p>
                    <div class="card bg-white image-box">
                        <img class="card-img statics-inview" src="{{ asset("png/statics-empty.png") }}" alt="Click import from file and choose playlist file." data-original="{{ asset("png/click-import-from-file.png") }}">
                    </div>
                </li>
            </ol>
        </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev swiper-button-preve-none exportplaylistimportplaylist-swiper-button-prev">
        <i class="fas fa-angle-left fa-2x"></i>
    </div>
    <div class="swiper-button-next swiper-button-next-none exportplaylistimportplaylist-swiper-button-next">
        <i class="fas fa-angle-right fa-2x"></i>
    </div>
</div>
