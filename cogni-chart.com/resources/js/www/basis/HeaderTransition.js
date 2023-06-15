import InView from '../chartrankingitem/InView';
import page from 'page'

export default class HeaderTransition
{

    constructor()
    {

        page('/chart/:chartname/:countryid', function (context) {
            this.getAndRefresh(context.pathname);
        }.bind(this));

        page('/chart/:chartname/:countryid/:charttermenddate', function (context) {
            this.getAndRefresh(context.pathname);
        }.bind(this));

        $(document).on(
            'click',
            '.chartListItem',
            function (event) {
                $('#chartList').button('loading');
                $('#chartTermList').button('loading');
                let baseUrl = $(event.currentTarget).attr('href');
                page(baseUrl);
                event.preventDefault();
            }
        );

        $(document).on(
            'click',
            '.chartTermListItem',
            function (event) {
                $('#chartList').button('loading');
                $('#chartTermList').button('loading');
                let baseUrl = $(event.currentTarget).attr('href');
                page(baseUrl);
                event.preventDefault();
            }
        );
    }

    getAndRefresh(baseUrl)
    {
        let params = {purpose: 'parts'};
        axios.get(baseUrl, {params})
        .then(response => {
            let partsObj = response.data;

            $('html > head > title').html(partsObj['titlePart']);
            $('html > head > meta[property="og:title"]').attr('content', partsObj['titlePart']);

            $('html > head > meta[name="description"]').attr('content', partsObj['descriptionPart']);
            $('html > head > meta[property="og:description"]').attr('content', partsObj['descriptionPart']);

            let canonical = $('html > head > link[rel="canonical"]');
            if (!partsObj['linkCanonicalPart']) {
                if (canonical.length > 0) {
                    canonical.remove();
                }
            } else {
                if (canonical.length > 0) {
                    canonical.attr('href', partsObj['linkCanonicalPart']);
                } else {
                    canonical = $("<link/>", {'rel':'canonical', 'href':partsObj['linkCanonicalPart']});
                    canonical.insertBefore('html > head > title');
                }
            }

            $('html > head > meta[property="og:url"]').attr('content', location.href);

            let noindex = $('html > head > meta[name="robots"][content="noindex"]');
            if (noindex.length > 0) {
                noindex.remove();
            }

            $('div#chartlistpart').html(partsObj['chartListPart']);
            $('div#charttermlistpart').html(partsObj['chartTermListPart']);
            $('article#wwwbody').html(partsObj['wwwBodyPart']);
            new InView();
            $('#chartList').button('reset');
            $('#chartTermList').button('reset');
        })
        .catch(error => {
            console.log(error);
            $('#chartList').button('reset');
            $('#chartTermList').button('reset');
        });
    }

}
