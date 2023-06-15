import Swiper from "swiper";

export default class ClickBankCircle01
{

    constructor()
    {
        new Swiper(
            '#ad-clickbank-circle01',
            {
                autoplay: {
                    delay: 5000,
                    stopOnLastSlide: false,
                    disableOnInteraction: false,
                    reverseDirection: false
                }
            }
        );
    }

}
