import { showPlayer, hidePlayer, cue, player } from '../player/player';
import InView from './InView';

export default class ChartRankingItem
{

    constructor()
    {
        $(window).on(
            'load',
            function (event) {

                new InView();

                $(document).on(
                    'click',
                    '.playpvbutton',
                    function (event) {
                        showPlayer();
                        var request = $(event.currentTarget);
                        var promotionVideoUrlValue = request.data('promotionvideourlvalue');
                        player.loadVideoById(promotionVideoUrlValue);
                    }
                );
            }
        );
    }

}
