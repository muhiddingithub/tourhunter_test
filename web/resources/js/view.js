$(document).off('click', '.transaction-button'
).on('click', '.transaction-button', function (e) {
    e.preventDefault();
    send($(this).prop('href'));
});
var send = function (_url, _data) {
    $.ajax({
        url: _url,
        type: 'json',
        dataType: 'json',
        data: _data,
        success: function (res) {
            if (res.status == 'success') {
                window.location.reload();
            }
            else {
                $('#mainModal').html(res.content).modal();
                $('#mainModal').off('submit', '#tr-from'
                ).on('submit', '#tr-from', function (e) {
                    e.preventDefault();
                    send(_url, $(this).serialize());
                })
            }
        }
    })
}