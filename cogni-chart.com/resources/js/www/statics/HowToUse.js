import Swiper from "swiper";

export default class HowToUse
{

    constructor()
    {
        let self = this;

        self.beforeRenders = [];
        self.beforeRenders.howtocreateplaylist = function () {
            self.beforeRenderHowToCreatePlaylist();
        };
        self.beforeRenders.howtoplayplaylist = function () {
            self.beforeRenderHowToPlayPlaylist();
        };
        self.beforeRenders.howtoeditplaylists = function () {
            self.beforeRenderHowToEditPlaylists();
        };
        self.beforeRenders.howtoeditmusics = function() {
            self.beforeRenderHowToEditMusics();
        };
        self.beforeRenders.exportplaylistimportplaylist = function() {
            self.beforeRenderExportPlaylistImportPlaylist();
        };

        $(function () {
            $(document).on(
                "click",
                ".menu-swiper-container [data-wakeup-target]",
                function (event) {
                    $(".menu-swiper-container").find(".swiper-slide").each(
                        function () {
                            $(this).removeClass("howtouse-menu-active");
                        }
                    );
                    $(event.currentTarget).parents(".swiper-slide").addClass("howtouse-menu-active");
                    let baseDomSelector = $(event.currentTarget).attr("data-wakeup-target");
                    let baseDom = $(baseDomSelector);
                    let baseDomHtml = baseDom.html();
                    let callbackIndex = baseDomSelector.substr(1);

                    $("#wakeup-board-wrapper").html(baseDomHtml);
                    if (self.beforeRenders[callbackIndex]) {
                        self.beforeRenders[callbackIndex]();
                    }
                    $("#wakeup-board-wrapper").children().addClass("do-wakeup");
                }
            );

            $(document).on(
                "animationend",
                ".do-wakeup",
                function (event) {
                    $(".do-wakeup").removeClass("do-wakeup");
                }
            );

            $(document).on(
                "click",
                "a[data-fire-pagination]",
                function (event) {
                    let targetPagination = $(event.currentTarget).attr("data-fire-pagination");
                    $(".swiper-pagination [aria-label=\"" + targetPagination + "\"]").trigger("click");
                    event.preventDefault();
                }
            );

        });
    }

    showHowToUse()
    {
        $('#wwwbody').html($('#howtousedoc').html());
        $('#wwwbody').find(".tmp-menu-swiper-container").addClass("menu-swiper-container");
        self.howToUseMenuSwiper = new Swiper(
            ".menu-swiper-container",
            {
                effect: 'slide',
                slidesPerView: 3,
                centeredSlides: true,
                spaceBetween: 10,
                loop: false,
                allowTouchMove: true,
                navigation: {
                    prevEl: '.menu-swiper-button-prev',
                    nextEl: '.menu-swiper-button-next'
                }
            }
        );

        self.howToUseMenuSwiper.on(
            "slideChange",
            function () {
                let selectedSlideIndex = this.realIndex;
                let selectedSlide = this.slides[selectedSlideIndex];
                let clicker = $(selectedSlide).find("[data-wakeup-target]");
                if (clicker) {
                    clicker.trigger("click");
                }
            }
        );
        $('.menu-swiper-container [data-wakeup-target="#howtocreateplaylist"]').trigger('click');
        document.title = "How to Use";
        $('html > head > meta[property="og:title"]').attr('content', 'How to Use');
    }

    beforeRenderHowToCreatePlaylist()
    {
        $("#wakeup-board-wrapper .swiper-container").addClass("howtocreateplaylist-swiper-container");
        new Swiper(
            '.howtocreateplaylist-swiper-container',
            {
                effect: 'flip',
                speed: 750,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    prevEl: '.howtocreateplaylist-swiper-button-prev',
                    nextEl: '.howtocreateplaylist-swiper-button-next'
                }
            }
        );
    }

    beforeRenderHowToPlayPlaylist()
    {
        $("#wakeup-board-wrapper .swiper-container").addClass("howtoplayplaylist-swiper-container");
        new Swiper(
            '.howtoplayplaylist-swiper-container',
            {
                effect: 'flip',
                speed: 750,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    prevEl: '.howtoplayplaylist-swiper-button-prev',
                    nextEl: '.howtoplayplaylist-swiper-button-next'
                }
            }
        );
    }

    beforeRenderHowToEditPlaylists()
    {
        $("#wakeup-board-wrapper .swiper-container").addClass("howtoeditplaylists-swiper-container");
        new Swiper(
            '.howtoeditplaylists-swiper-container',
            {
                effect: 'flip',
                speed: 750,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    prevEl: '.howtoeditplaylists-swiper-button-prev',
                    nextEl: '.howtoeditplaylists-swiper-button-next'
                }
            }
        );
    }

    beforeRenderHowToEditMusics()
    {
        $("#wakeup-board-wrapper .swiper-container").addClass("howtoeditmusics-swiper-container");
        new Swiper(
            '.howtoeditmusics-swiper-container',
            {
                effect: 'flip',
                speed: 750,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    prevEl: '.howtoeditmusics-swiper-button-prev',
                    nextEl: '.howtoeditmusics-swiper-button-next'
                }
            }
        );
    }

    beforeRenderExportPlaylistImportPlaylist()
    {
        $("#wakeup-board-wrapper .swiper-container").addClass("exportplaylistimportplaylist-swiper-container");
        new Swiper(
            '.exportplaylistimportplaylist-swiper-container',
            {
                effect: 'flip',
                speed: 750,
                loop: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    prevEl: '.exportplaylistimportplaylist-swiper-button-prev',
                    nextEl: '.exportplaylistimportplaylist-swiper-button-next'
                }
            }
        );
    }

}
