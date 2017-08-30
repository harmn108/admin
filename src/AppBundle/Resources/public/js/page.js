function Page() {
    this.Init();
}

Page.prototype.Init = function () {
    var obj = this;

    obj.addPage = $("#add_page");
    obj.addPageModal = $("#add_page_modal");
    obj.pagesListTable = $("#pages_list_table");

    // obj.update_um_f_name = obj.update_user_modal.find('input.f_name');
    // obj.update_um_l_name = obj.update_user_modal.find('input.l_name');
    // obj.update_um_email = obj.update_user_modal.find('input.email');
    // obj.update_um_userid = obj.update_user_modal.find('input.userid');
    // obj.update_um_password = obj.update_user_modal.find('input.password');
    // obj.update_um_cpassword = obj.update_user_modal.find('input.cpassword');
    //
    // obj.update_um_save = obj.update_user_modal.find('button.save');
    //
    // obj.menu_item.on('click', function(event){
    //     event.preventDefault();
    //     obj.userId = $(this).data('userid');
    //     var user = obj.getUserById(obj.userId);
    //
    //     if(typeof user.id !== 'undefined'){
    //         obj.fillUpdateUMFields(user);
    //         $('#update_um_form input').trigger('change');
    //         obj.update_user_modal.modal('show');
    //     }
    // });

    obj.addPage.validator().on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        var result = obj.addPageAction(formData);

        if(result){
            obj.pagesListTable.find('tbody').html(result.pages);
            obj.addPageModal.modal('hide');
            obj.addPage[0].reset();
        }
    });
};

//
// Page.prototype.getPageById = function (id) {
//     var userInfo;
//     $.ajax({
//         method: "POST",
//         url: 'get_user_by_id',
//         dataType: "JSON",
//         async: false,
//         data: {
//             id: id
//         },
//         success: function (result) {
//             userInfo = result;
//         }
//     });
//
//     return userInfo;
// };

// Page.prototype.fillUpdateUMFields = function (user) {
//     var obj = this;
//     // console.log(user);
//     obj.update_um_f_name.val(user.firstName);
//     obj.update_um_l_name.val(user.lastName);
//     obj.update_um_email.val(user.email);
//     obj.update_um_userid.val(obj.userId);
// };
//
// Page.prototype.generateUpdateUserData = function () {
//     var obj = this;
//     var userData = {};
//
//     userData.firstName = obj.update_um_f_name.val();
//     userData.lastName = obj.update_um_l_name.val();
//     userData.email = obj.update_um_email.val();
//     userData.id = obj.update_um_userid.val();
//
//     if(obj.update_um_password.val() !== ''){
//         userData.password = obj.update_um_password.val();
//         var cpassword = obj.update_um_cpassword.val();
//
//         if(userData.password !== cpassword){
//             alert('Passwords do not match');
//             return false;
//         }
//     }
//
//     return userData;
// };

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