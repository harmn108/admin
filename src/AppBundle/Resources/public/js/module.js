function Module() {
    this.Init();
}

Module.prototype.Init = function () {
    var obj = this;

    obj.addModuleForm = $("#add_module_form");
    obj.addModuleModal = $("#add_module_modal");
    obj.updateModuleForm = $("#update_module_form");
    obj.updateModuleModal = $("#update_module_modal");
    obj.modulesListTable = $("#modules_list_table");
    obj.updateName = obj.updateModuleForm.find('input.name');
    obj.updateRoute = obj.updateModuleForm.find('input.route');
    obj.selectedModuleId = obj.updateModuleForm.find('input.module_id');

    $(document).on("click", "#modules_list_table .update_module", function(event) {
        var moduleId = $(event.target).closest('tr').data('id');
        var module = obj.getModuleById(moduleId);

        obj.selectedModuleId.val(moduleId);

        if(typeof moduleId !== 'undefined'){
            obj.updateModuleForm.find('input').trigger('change');
            obj.updateModuleModal.modal('show');
        }
    });

    obj.addModuleForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addModuleAction(formData);

        if(result){
            obj.modulesListTable.find('tbody').html(result.modules);
            obj.addModuleModal.modal('hide');
            obj.addModuleForm[0].reset();
        }
    });

    obj.updateModuleForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        obj.updateModuleData(formData);
    });
};


Module.prototype.getModuleById = function (id) {
    var obj = this;
    var userInfo;

    $.ajax({
        method: "POST",
        url: 'get_module_by_id',
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

Module.prototype.updateModuleData = function (data) {
    var obj = this;

    $.ajax({
        method: "POST",
        url: 'update_module',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            var updatedRow = obj.modulesListTable.find('tr[data-id="'+ data +'"]');
            updatedRow.find('.module_route').text(obj.updateModuleForm.find('.name').val());
            updatedRow.find('.module_route').attr('href', obj.updateModuleForm.find('.route').val());
            obj.updateModuleForm[0].reset();
            obj.updateModuleModal.modal('hide');
        }
    });
};

Module.prototype.addModuleAction = function (data) {
    var result;

    $.ajax({
        method: "POST",
        url: 'add_module',
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
    new Module();
});