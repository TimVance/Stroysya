/*
You can use this file with your scripts.
It will not be overwritten when you upgrade solution.
*/

$(function() {
    // Появление модалки
    $('.call-modal-master').click(function() {
        $('.modal-order-wrapper, .overlay').fadeIn();
        let masterId = parseInt($(this).attr("data-id"));
        $('.modal-order-wrapper').attr("data-master-id", masterId);
    });
    $(".close-modal-order, .overlay").click(function() {
        $('.modal-order-wrapper, .overlay').fadeOut();
    });
    $(document).on("submit", ".modal-order-wrapper form", function(e) {
        let id = $('.modal-order-wrapper').attr("data-master-id");
        $(this).find('input[name="PROPERTY[281][0]"]').val(id);
        return true;
    });
});