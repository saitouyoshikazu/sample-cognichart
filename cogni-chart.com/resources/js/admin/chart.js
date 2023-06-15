$('.chartRow').click(
    function (event) {
        var chart_phase = $(event.currentTarget).data('chart_phase');
        var country_id = $(event.currentTarget).data('country_id');
        var chart_name = $(event.currentTarget).data('chart_name');
        $('<form/>', {action: '/chart/'+chart_phase, method: 'get'})
        .append($('<input/>', {type: 'hidden', name: 'country_id', value: country_id}))
        .append($('<input/>', {type: 'hidden', name: 'chart_name', value: chart_name}))
        .appendTo(document.body)
        .submit();
    }
);
