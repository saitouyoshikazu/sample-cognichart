$('.adminUserRow').click(
    function (event) {
        var id = $(event.currentTarget).data('id');
        $('<form/>', {action: '/adminuser', method: 'get'})
        .append($('<input/>', {type: 'hidden', name: 'adminuser_id', value: id}))
        .appendTo(document.body)
        .submit();
    }
);
