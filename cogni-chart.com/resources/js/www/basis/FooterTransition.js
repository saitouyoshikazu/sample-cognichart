import page from 'page';
import HowToUse from "../statics/HowToUse";
import PrivacyPolicy from "../statics/PrivacyPolicy";
import TermsOfUse from "../statics/TermsOfUse";
import ContactUs from "../statics/ContactUs";

export default class FooterTransition
{

    constructor()
    {
        page('/howtouse', function (context) {
            (new HowToUse()).showHowToUse();
        });

        page('/privacypolicy', function (context) {
            (new PrivacyPolicy()).showPrivacyPolicy();
        });

        page('/termsofuse', function (context) {
            (new TermsOfUse()).showTermsOfUse();
        });

        page('/contactus', function (context) {
            (new ContactUs()).showContactUs();
        });

        $('#howtouse').on(
            'click',
            function (event) {
                this.showEventPage(event);
            }.bind(this)
        );

        $('#privacypolicy').on(
            'click',
            function (event) {
                this.showEventPage(event);
            }.bind(this)
        );

        $('#termsofuse').on(
            'click',
            function (event) {
                this.showEventPage(event);
            }.bind(this)
        );

        $('#contactus').on(
            'click',
            function (event) {
                this.showEventPage(event);
            }.bind(this)
        );
    }

    showEventPage(event)
    {
        let href = $(event.target).attr('href');
        page(href);

        $('html > head > meta[name="description"]').attr('content', 'YouTube songs of singles chart. You can listen to musics without an account.');
        $('html > head > meta[property="og:description"]').attr('content', 'YouTube songs of singles chart. You can listen to musics without an account.');

        let canonical = $('html > head > link[rel="canonical"]');
        if (canonical.length > 0) {
            canonical.remove();
        }

        $('html > head > meta[property="og:url"]').attr('content', location.href);

        let noindex = $('html > head > meta[name="robots"][content="noindex"]');
        if (noindex.length == 0) {
            noindex = $('<meta/>', {'name':'robots', 'content':'noindex'});
            noindex.insertAfter('html > head > title');
        }

        event.preventDefault();
    }

}
