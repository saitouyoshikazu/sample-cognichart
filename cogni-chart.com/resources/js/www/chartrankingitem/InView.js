export default class InView
{

    constructor()
    {
        $('.pv-thumbnail').each(function () {
            $(this).one('inview', function (event, isInview) {
                if (isInview) {
                    var srcValue = $(this).data('original');
                    $(this).attr('src', srcValue);
                }
            })
        });
    }

}
