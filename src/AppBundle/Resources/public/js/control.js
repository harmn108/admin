function Control() {
    this.Init();
}

Control.prototype.Init = function () {
    var obj = this;

    this.base_url = document.location.origin;
    obj.menu_item = $("a.control_menu_item");
    obj.update_user_modal = $("#control_update_user");
    obj.addUM = $("#control_add_user");
    obj.add_user_btn = $("#add_user_btn");

    obj.update_um_f_name = obj.update_user_modal.find('input.f_name');
    obj.update_um_l_name = obj.update_user_modal.find('input.l_name');
    obj.update_um_email = obj.update_user_modal.find('input.email');
    obj.update_um_userid = obj.update_user_modal.find('input.userid');
    obj.update_um_password = obj.update_user_modal.find('input.password');
    obj.update_um_cpassword = obj.update_user_modal.find('input.cpassword');

    obj.update_um_save = obj.update_user_modal.find('button.save');

    obj.userListTable = $("#user_list_table")

    obj.menu_item.on('click', function(event){
        event.preventDefault();
        obj.userId = $(this).data('userid');
        var user = obj.getUserById(obj.userId);

        if(typeof user.id !== 'undefined'){
            obj.fillUpdateUMFields(user);
            $('#update_um_form input').trigger('change');
            obj.update_user_modal.modal('show');
        }
    });

    $('#update_um_form').validator().on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.updateUser(formData);

        if(result){
            obj.update_user_modal.modal('hide');
        }
    });

    obj.add_user_btn.on('click', function(event){
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
};

Control.prototype.getUserById = function (id) {
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
    obj.update_um_userid.val(obj.userId);
};

Control.prototype.generateUpdateUserData = function () {
    var obj = this;
    var userData = {};

    userData.firstName = obj.update_um_f_name.val();
    userData.lastName = obj.update_um_l_name.val();
    userData.email = obj.update_um_email.val();
    userData.id = obj.update_um_userid.val();

    if(obj.update_um_password.val() !== ''){
        userData.password = obj.update_um_password.val();
        var cpassword = obj.update_um_cpassword.val();

        if(userData.password !== cpassword){
            alert('Passwords do not match');
            return false;
        }
    }

    return userData;
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
        }
    });

    return result;
};


$( document ).ready(function() {
    new Control();
});