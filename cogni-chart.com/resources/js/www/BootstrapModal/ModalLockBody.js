export default class ModalLockBody
{

    constructor()
    {
    }

    lockBody() {
        let current_scrollY = $(window).scrollTop();
        $('body').css('position', 'fixed');
        $('body').css('width', '100%');
        $('body').css('top', -1 * current_scrollY);
    }

    unlockBody() {
        let current_scrollY = -1 * parseInt($('body').css('top'));
        $('body').css('position', '');
        $('body').css('width', '');
        $('body').css('top', '');
        $(window).scrollTop(current_scrollY);
    }

}
