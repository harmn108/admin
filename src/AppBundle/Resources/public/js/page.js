function Page() {
    this.Init();
}

Page.prototype.Init = function () {
    var obj = this;

    obj.addPageForm = $("#add_page_form");
    obj.addPageModal = $("#add_page_modal");
    obj.updatePageForm = $("#update_page_form");
    obj.updatePageModal = $("#update_page_modal");
    obj.pagesListTable = $("#pages_list_table");
    obj.updateName = obj.updatePageForm.find('input.name');
    obj.updateRoute = obj.updatePageForm.find('input.route');
    obj.selectedPageId = obj.updatePageForm.find('input.page_id');

    $(document).on("click", "#pages_list_table .update_page", function(event) {
        var pageId = $(event.target).closest('tr').data('id');
        var page = obj.getPageById(pageId);

        obj.selectedPageId.val(pageId);

        if(typeof pageId !== 'undefined'){
            obj.updatePageForm.find('input').trigger('change');
            obj.updatePageModal.modal('show');
        }
    });

    obj.addPageForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addPageAction(formData);

        if(result){
            obj.pagesListTable.find('tbody').html(result.pages);
            obj.addPageModal.modal('hide');
            obj.addPageForm[0].reset();
        }
    });

    obj.updatePageForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        obj.updatePageData(formData);
    });
};


Page.prototype.getPageById = function (id) {
    var obj = this;
    var userInfo;

    $.ajax({
        method: "POST",
        url: 'get_page_by_id',
        dataType: "JSON",
        async: false,
        data: {
            id: id
        },
        success: function (result) {
            userInfo = result;
            obj.updateName.val(result.name);
            obj.updateRoute.val(result.route);
        }
    });

    return userInfo;
};

Page.prototype.updatePageData = function (data) {
    var obj = this;

    $.ajax({
        method: "POST",
        url: 'update_page',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            var updatedRow = obj.pagesListTable.find('tr[data-id="'+ data +'"]');
            updatedRow.find('.page_route').text(obj.updatePageForm.find('.name').val());
            updatedRow.find('.page_route').attr('href', obj.updatePageForm.find('.route').val());
            obj.updatePageForm[0].reset();
            obj.updatePageModal.modal('hide');
        }
    });
};

Page.prototype.addPageAction = function (data) {
    var result;

    $.ajax({
        method: "POST",
        url: 'add_page',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        }
    });

    return result;
};

$( document ).ready(function() {
    new Page();
});