function Access() {
    this.Init();
}

Access.prototype.Init = function () {
    var obj = this;

    obj.addRoleModal = $("#add_role_modal");
    obj.addRoleForm = $("#add_role_form");

    obj.addRoleForm.on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addRole(formData);

        if(result){
            obj.addRoleModal.modal('hide');
            location.reload();
        }
    });
};

Access.prototype.addRole = function (data) {
    var result;

    $.ajax({
        method: "POST",
        url: 'add_role',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(result) {
            // add alert
        }
    });

    return result;
};


$( document ).ready(function() {
    new Access();
});