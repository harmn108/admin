function Main() {
    this.Init();
}

Main.prototype.Init = function () {
    var obj = this;
};


Main.prototype.notify = function (type, message) {
    var obj = this;

    if($.isArray(message)){
        message = message.join('<br>')
    }

    $.notify({
        // options
        message: message
    },{
        // settings
        element: 'body',
        type: type,
        z_index: 10000
    });
};


$( document ).ready(function() {
    Main = new Main();
});