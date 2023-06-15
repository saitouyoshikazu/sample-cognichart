export default class PrivacyPolicy
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

    showPrivacyPolicy()
    {
        $("#wwwbody").html($("#privacypolicydoc").html());
        $("#wwwbody .privacypolicy-wakeup-board").addClass("do-wakeup");
        document.title = "Privacy Policy";
        $('html > head > meta[property="og:title"]').attr('content', 'Privacy Policy');
    }

}
