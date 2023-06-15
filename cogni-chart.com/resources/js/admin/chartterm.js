import Vue from 'vue'

$('.chartTermChartRow').click(
    function (event) {
        var chart_phase = $(event.currentTarget).data('chart_phase');
        var country_id = $(event.currentTarget).data('country_id');
        var chart_name = $(event.currentTarget).data('chart_name');
        $('<form/>', {action: '/chartterm/list', method: 'get'})
            .append($('<input/>', {type: 'hidden', name: 'chart_phase', value: chart_phase}))
            .append($('<input/>', {type: 'hidden', name: 'country_id', value: country_id}))
            .append($('<input/>', {type: 'hidden', name: 'chart_name', value: chart_name}))
            .appendTo(document.body)
            .submit();
    }
);
$('.chartTermRow').click(
    function (event) {
        var chart_phase = $(event.currentTarget).data('chart_phase');
        var country_id = $(event.currentTarget).data('country_id');
        var chart_name = $(event.currentTarget).data('chart_name');
        var chartterm_phase = $(event.currentTarget).data('chartterm_phase');
        var end_date = $(event.currentTarget).data('end_date');
        $('<form/>', {action: '/chartterm/get', method: 'get'})
            .append($('<input>', {type: 'hidden', name: 'chart_phase', value: chart_phase}))
            .append($('<input>', {type: 'hidden', name: 'country_id', value: country_id}))
            .append($('<input>', {type: 'hidden', name: 'chart_name', value: chart_name}))
            .append($('<input>', {type: 'hidden', name: 'chartterm_phase', value: chartterm_phase}))
            .append($('<input>', {type: 'hidden', name: 'end_date', value: end_date}))
            .appendTo(document.body)
            .submit();
    }
);

if ($('#resolvechartrankingitems').length) {
    new Vue({
        el: '#resolvechartrankingitems',
        methods: {
            sendResolve: async function (chartTermPhase, chartTermId, resolveUrl) {
                $('#resolvechartrankingitemsbutton').button('loading');
                let params = new URLSearchParams();
                params.append('chartterm_phase', chartTermPhase);
                params.append('chartterm_id', chartTermId);
                await axios.post(resolveUrl, params)
                .then(response => {
                    var result = response.data;
                    alert(result.message);
                })
                .catch(error => alert(error));
                $('#resolvechartrankingitemsbutton').button('reset');
            }
        }
    });
}
