export default class TermsOfUse
{

    constructor()
    {
        $(document).on(
            "animationend",
            ".do-wakeup",
            function (event) {
                $(".do-wakeup").removeClass("do-wakeup");
            }
        );
    }

    showTermsOfUse()
    {
        $("#wwwbody").html($("#termsofusedoc").html());
        $("#wwwbody .termsofuse-wakeup-board").addClass("do-wakeup");
        document.title = "Terms of Use";
        $('html > head > meta[property="og:title"]').attr('content', 'Terms of Use');
    }

}
