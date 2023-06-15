import HeaderTransition from './basis/HeaderTransition';
import FooterTransition from './basis/FooterTransition';
import Statics from './statics/Statics';
import HowToUse from './statics/HowToUse';
import PlaylistModalController from './playlist/PlaylistModalController';
import PlaylistItemsModalController from './playlist/PlaylistItemsModalController';
import './player/player';
import AppendAllSongsModalController from './chart/AppendAllSongsModalController';
import AppendToPlaylistModalController from './chart/AppendToPlaylistModalController';
import ChartRankingItem from './chartrankingitem/chartrankingitem';
import ClickBankCircle01 from './ad/clickbank/ClickBankCircle01';

new HeaderTransition();
new FooterTransition();
new Statics();
new HowToUse();
new PlaylistModalController();
new PlaylistItemsModalController();
new AppendAllSongsModalController();
new AppendToPlaylistModalController();
new ChartRankingItem();
new ClickBankCircle01();

// Loading button plugin (removed from BS4)
(
    function($) {
        $.fn.button = function(action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false);
            }
        };
    }(jQuery)
);
