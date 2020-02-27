"use strict";

function Options(){
    this.loc = document.location.origin;
}

Options.prototype.noticeControl = function(opt) {

    //var that = this;
    $.ajax({
        url: _ROOT_ + "Views/Options/controllers/opt_handler.php",
        type: 'POST',
        data: {
            noticeActivate: opt
        },
        dataType:"json",
        success:function(data) {
            if ( data.done == 1 ) console.log('Уведомления активированы');
            if ( data.done == 2 ) console.log('Уведомления отключены');
        }
    });
};

Options.prototype.changeBgImg = function() {
    console.log(this);
    if ( !this.checked ) {
        console.log('Ухожу');
        return;
    }

    //var src = this.previousElementSibling.getAttribute('src');
    var src = this.getAttribute('data-class');
    console.log(src);
    $.ajax({
        url: _ROOT_ + "Views/Options/controllers/opt_handler.php",
        type: 'POST',
        data: {
            srcBgImg: src
        },
        dataType:"json",
        success:function(data) {
            if ( data.done ) {
                //alert('Новый фон установлен!');
                var ddd = document.querySelector('body').setAttribute('class',src);
                //document.location.reload(true);
            } else {
                alert('что-то пошло не так!');
            }
        }
    });
};


var options = new Options();

var PN_control = document.getElementById('PN_control');
if ( PN_control.checked ) {
    PN_control.addEventListener('click',function(){
        options.noticeControl(2);
        //console.log('посылаю 2 checked=',this.checked);
    }, false);
} else {
    PN_control.addEventListener('click',function(){
        options.noticeControl(1);
        //console.log('посылаю 1 checked=',this.checked);
    }, false);
}

var bgImages = document.querySelectorAll('.bg_img');
for( var i = 0; i < bgImages.length; i++ ) {
    bgImages[i].addEventListener('change', options.changeBgImg, false);
}


