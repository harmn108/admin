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

        if(module && typeof module.name !== 'undefined' && typeof module.route !== 'undefined'){
            obj.updateName.val(module.name);
            obj.updateRoute.val(module.route);
        }

        if(typeof moduleId !== 'undefined'){
            obj.selectedModuleId.val(moduleId);
            obj.updateModuleForm.find('input').trigger('change');
            obj.updateModuleModal.modal('show');
        }
    });

    obj.addModuleForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addModuleAction(formData);

        if(result && typeof result.modules !== 'undefined'){
            obj.modulesListTable.find('tbody').html(result.modules);
            obj.addModuleModal.modal('hide');
            obj.addModuleForm[0].reset();
            Main.notify('success', 'Module successfully added');
        }
    });

    obj.updateModuleForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.updateModuleData(formData);

        if(result){
            var updatedRow = obj.modulesListTable.find('tr[data-id="'+ result +'"]');
            updatedRow.find('.module_route').text(obj.updateModuleForm.find('.name').val());
            updatedRow.find('.module_route').attr('href', obj.updateModuleForm.find('.route').val());
            obj.updateModuleForm[0].reset();
            obj.updateModuleModal.modal('hide');
            Main.notify('success', 'Module successfully updated');
        }
    });
};


Module.prototype.getModuleById = function (id) {
    var result = false;

    $.ajax({
        method: "POST",
        url: 'get_module_by_id',
        dataType: "JSON",
        async: false,
        data: {
            id: id
        },
        success: function (data) {
            result = data;
        },
        error: function(data) {
            if(data && typeof data.responseJSON !== 'undefined'){
                Main.notify('danger', data.responseJSON);
            } else {
                Main.notify('danger', 'Something went wrong please try again later');
            }
        }
    });

    return result;
};

Module.prototype.updateModuleData = function (data) {
    var result = false;
    $.ajax({
        method: "POST",
        url: 'update_module',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(data) {
            if(data && typeof data.responseJSON !== 'undefined'){
                Main.notify('danger', data.responseJSON);
            } else {
                Main.notify('danger', 'Something went wrong please try again later');
            }
        }
    });

    return result;
};

Module.prototype.addModuleAction = function (data) {
    var result = false;

    $.ajax({
        method: "POST",
        url: 'add_module',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(data) {
            if(data && typeof data.responseJSON !== 'undefined'){
                Main.notify('danger', data.responseJSON);
            } else {
                Main.notify('danger', 'Something went wrong please try again later');
            }
        }
    });

    return result;
};

$( document ).ready(function() {
    new Module();
});