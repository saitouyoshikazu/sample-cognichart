require('./adminuser');
require('./chart');
require('./chartterm');
require('./chartrankingitem');
require('./artist');
require('./music');

$('.mouseOverFocus').on({
    'mouseover': function (event) {
        $(this).addClass('rowFocus');
    },
    'mouseout': function (event) {
        $(this).removeClass('rowFocus');
    }
});

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
