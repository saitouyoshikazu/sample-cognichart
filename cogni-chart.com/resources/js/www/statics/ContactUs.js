export default class ContactUs
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

    showContactUs()
    {
        $("#wwwbody").html($("#contactusdoc").html());
        $("#wwwbody .contactus-wakeup-board").addClass("do-wakeup");
        document.title = "Contact Us";
        $('html > head > meta[property="og:title"]').attr('content', 'Contact Us');
    }

}
