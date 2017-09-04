function Website() {
    this.Init();
}

Website.prototype.Init = function () {
    var obj = this;

    $("#website_name_form").validator().on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        var result = obj.saveWebsiteName(formData);

        if(result && typeof result.websiteOptions.id !== 'undefined'){
            $(this).find('input.id').val(result.websiteOptions.id);
            Main.notify('success', 'Website name successfully saved');
            return true;
        }
        else{
            return false;
        }
    });
};


Website.prototype.saveWebsiteName = function (data) {
    var obj = this;
    var result = false;

    $.ajax({
        method: "POST",
        url: 'website_name',
        dataType: "JSON",
        async: false,
        data: data,
        success: function (data) {
            result = data;
        },
        error: function(data) {
            if(data && typeof data.responseJSON !== 'undefined'){
                Main.notify('danger', data.responseJSON);
            }
            else {
                Main.notify('danger', 'Something went wrong please try again later');
            }
        }
    });

    return result;
};

$( document ).ready(function() {
    new Website();
});