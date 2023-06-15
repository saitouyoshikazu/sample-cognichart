export default class Statics
{

    constructor() {
        $(document).on(
            "inview",
            ".statics-inview",
            function (event, isInview) {
                if (isInview) {
                    var srcValue = $(this).data('original');
                    $(this).attr('src', srcValue);
                }
            }
        );
    }

}
