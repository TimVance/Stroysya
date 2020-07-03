/*
You can use this file with your scripts.
It will not be overwritten when you upgrade solution.
*/

$(function() {
    // Появление модалки заявок
    $(document).on('click', '.call-modal-master', function() {
        $('.modal-order-wrapper, .overlay').fadeIn();
        let masterId = parseInt($(this).attr("data-id"));
        $(".modal-order-wrapper form").find('input[name="PROPERTY[281][0]"]').val(masterId);
    });
    $(".close-modal-order, .overlay").click(function() {
        $('.modal-order-wrapper, .overlay').fadeOut();
    });

    // Появление модалки вызова замерщика
    $(document).on('click', '.call-master-button', function() {
        $('.modal-call-wrapper, .overlay').fadeIn();
    });
    $(".close-modal-order, .overlay").click(function() {
        $('.modal-call-wrapper, .overlay').fadeOut();
    });
});