$(function() {

    // Hande Category Click
    $(".sections-list_item").click(function() {
        $(this).toggleClass("selected");
        sendAjax();
    });

    // Collect Categoryes
    function collectSelectCats() {
        let categoryes = $(".sections-list_item.selected");
        let categoryId = [];
        categoryes.each(function() {
            categoryId.push($(this).data("category-id"));
        });
        return categoryId;
    }

    function sendAjax() {
        let list = $(".services-list__wrapper");
        let cats = collectSelectCats;
        let prop = {
            cats: cats,
            ajax: true,
            component: "services"
        };
        let url = '/bitrix/components/dlay/main.masters.list/templates/page/ajax.php';
        list.addClass("load");
        $.ajax({
            method: "post",
            url: url,
            data: prop,
            dataType: 'html',
            success: function (res) {
                $(".services-list").html(res);
                list.removeClass("load");
            }
        });
    }

});