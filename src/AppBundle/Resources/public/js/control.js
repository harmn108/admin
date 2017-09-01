function Control() {
    this.Init();
}

Control.prototype.Init = function () {
    var obj = this;

    obj.menu_item = $("a.control_menu_item");
    obj.updateUserModal = $("#control_update_user");
    obj.addUM = $("#control_add_user");
    obj.addUserBtn = $("#add_user_btn");

    obj.update_um_f_name = obj.updateUserModal.find('input.f_name');
    obj.update_um_l_name = obj.updateUserModal.find('input.l_name');
    obj.update_um_email = obj.updateUserModal.find('input.email');
    obj.update_um_userid = obj.updateUserModal.find('input.userid');
    obj.update_um_password = obj.updateUserModal.find('input.password');
    obj.update_um_cpassword = obj.updateUserModal.find('input.cpassword');

    obj.update_um_save = obj.updateUserModal.find('button.save');

    obj.userListTable = $("#user_list_table")

    obj.menu_item.on('click', function(event){
        event.preventDefault();
        obj.userId = $(this).data('userid');
        var user = obj.getUserById(obj.userId);

        if(typeof user.id !== 'undefined'){
            obj.fillUpdateUMFields(user);
            $('#update_um_form input').trigger('change');
            obj.updateUserModal.modal('show');
        }
    });

    $('#update_um_form').validator().on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.updateUser(formData);

        if(result){
            if(obj.userListTable.length){
                obj.userListTable.find('tbody').html(result.users);
            }
            obj.updateUserModal.modal('hide');
        }
    });

    obj.addUserBtn.on('click', function(event){
        event.preventDefault();
        var roleId = $(this).data('roleid');
        $("#add_um_form")[0].reset();

        obj.addUM.find('input.roleId').val(roleId);
        obj.addUM.modal('show');
    });

    $('#add_um_form').validator().on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addUser(formData);

        if(result){
            obj.userListTable.find('tbody').html(result.users);
            obj.addUM.modal('hide');
        }
    });

    $(document).on("click","#user_list_table .update_user",function() {
        var userId = $(this).closest('tr').data('id');

        var user = obj.getUserById(userId);

        if(typeof user.id !== 'undefined'){
            obj.fillUpdateUMFields(user);
            $('#update_um_form').find('input').trigger('change');
            obj.updateUserModal.modal('show');
        }
    });
};

Control.prototype.getUserById = function (id) {
    var obj = this;
    var userInfo;
    $.ajax({
        method: "POST",
        url: 'get_user_by_id',
        dataType: "JSON",
        async: false,
        data: {
            id: id
        },
        success: function (result) {
            userInfo = result;
        },
        error: function(result) {
            bootbox.alert({
                size: "small",
                title: "Error Alert",
                message: result.responseJSON.join('<br>')
            });
        }
    });

    return userInfo;
};

Control.prototype.fillUpdateUMFields = function (user) {
    var obj = this;
    // console.log(user);
    obj.update_um_f_name.val(user.firstName);
    obj.update_um_l_name.val(user.lastName);
    obj.update_um_email.val(user.email);
    obj.update_um_userid.val(user.id);
};

Control.prototype.updateUser = function (data) {
    var result;

    $.ajax({
        method: "POST",
        url: 'update_user',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(result, error_text) {
            bootbox.alert({
                size: "small",
                title: "Error Alert",
                message: result.responseJSON.join('<br>')
            });
        }
    });

    return result;
};

Control.prototype.addUser = function (data) {
    var result;

    $.ajax({
        method: "POST",
        url: 'add_user',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(result) {
            bootbox.alert({
                size: "small",
                title: "Error Alert",
                message: result.responseJSON.join('<br>')
            });
        }
    });

    return result;
};


$( document ).ready(function() {
    new Control();
});