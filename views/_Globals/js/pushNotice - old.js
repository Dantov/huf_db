"use strict";

function PushNotice(closeAll, pushNoticeWrapp)
{
    this.closeAllPN = closeAll ? closeAll : document.querySelector('.closeAll');
    this.pushNoticeWrapp = pushNoticeWrapp ? pushNoticeWrapp : document.getElementById('pushNoticeWrapp');
    this.showedNotice = showedNotice; //здесь хранятся показанные уведомления
}

PushNotice.prototype.closeAllNotices = function() {
    // проверка, что бы поставить обработчик только раз.( пытался каждый раз ставить при checkNotice )
	if ( !this.closeAllPN.classList.contains('hidden') ) return;
	
	this.closeAllPN.classList.remove('hidden');
	let that = this;
	
    this.closeAllPN.children[0].onclick = function()
    {
        $.ajax({
            url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
            type: 'POST',
            data: {
                closeAllPN: 1,
				closeById: that.showedNotice,
            },
            dataType:"json",
            success:function(data) {

				let pushNotices = document.querySelectorAll('.pushNotice');
                if ( !data.done ) return console.log('Ошибка закрытия.' + data.done);
				
                for ( let i = 0; i < pushNotices.length; i++ ) {
                    pushNotices[i].remove();
                }
                that.closeAllPN.classList.add('hidden');
            }
        });
    };
	
};

PushNotice.prototype.closeNotice = function(newNotice, id) {

    //ajax запрос на поставку ип адреса в таблицу
    $.ajax({
        url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
        type: 'POST',
        data: {//данные передаваемые в POST запросе
            closeNotice: id
        },
        dataType:"json",
        success:function(data) {

            // здесь ничего нет потому что
            // showedNotice всеравно стирается пи каждой перезагрузке стр

        }

    });
};

PushNotice.prototype.addNotice = function(notice)
{
    // если оно есть в массиве, значит уже показано - уходим.
    if ( this.showedNotice.indexOf(notice.not_id) !== -1 ) return;

    let addStr, statusStr, abt, newNotice;
	
    let url = _ROOT_ + "Views/ModelView/index.php?id=" + notice.pos_id;

    newNotice = document.querySelector('.pushNotice_proto').cloneNode(true);
    newNotice.classList.remove('hidden');
    newNotice.classList.add('pushNotice');


    if ( typeof notice.status === 'object' )
    {
        let statSpan = '<span class="glyphicon glyphicon-'+ notice.status.glyphi +'"></span>';
        statusStr = '<div class="' + notice.status.name_en + ' pn_status pull-left" title="' + notice.status.title + '">'+ statSpan +'</div>';
    }

    if ( +notice['addEdit'] === 1 ) abt = "Добавлена новая";
    if ( +notice['addEdit'] === 2 ) abt = "Изменена";
    if ( +notice['addEdit'] === 3 ) abt = "Удалена";

    addStr = '<p style="text-align: center"><span class="pull-left">' + notice.fio + "</span><b>" + abt + " модель!</b></p>";
    let divblock = '<div>' +
            '<table width="100%" border="0">' +
                '<body>' +
                    '<tr>' +
                        '<td align="center"><img src="' + notice.img_src +'"/></td>' +
                        '<td align="center"><b>' + notice.number_3d + '/'+ notice.vendor_code + ' - ' + notice.model_type + '</b></td>' +
                    '</tr>' +
                '</body>' +
            '</table>' +
        '</div>';
    newNotice.children[1].innerHTML = addStr + statusStr + divblock;


    // переходим на модель при клике и ставим IP
    if ( +notice['addEdit'] !== 3 )
    {
        newNotice.children[1].addEventListener('click', function() {
            let id = notice['not_id'];

            $.ajax({
                url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
                type: 'POST',
                data: {
                    closeNotice: id
                },
                dataType:"json",
                success:function(data) {

                    if ( data['done'] )
                    {
                        console.log(data['done'],id);
                        document.location.href= url;
                    }
                },
                error:function(error) {
                    console.log(error);
                }
            });
        }, false);
    }


    // при закрытии ставим IP
    let that = this;
    newNotice.firstElementChild.addEventListener('click', function() {

        this.parentElement.addEventListener('transitionend', function () {
            this.remove();
        });

        that.closeNotice(newNotice, notice.not_id);
        this.parentElement.classList.add('closedPN');

        let len = that.pushNoticeWrapp.querySelectorAll('.pushNotice').length;
        //console.log('!!!!!!!!!!!!!!!len=',len);
        if ( (len-1) < 3 ) that.closeAllPN.classList.add('hidden');

    }, false );


    this.pushNoticeWrapp.insertBefore(newNotice, this.pushNoticeWrapp.children[1]);
    // плавное появление, нужна задержка, иначе ставит класс а потом добавляет
    setTimeout(function()
    {
        newNotice.classList.remove('pushNotice_proto');
        that.showedNotice.push(notice.not_id); //добавли в массив как показанное уведомление чтоб не показывать снова

        if ( that.showedNotice.length > 2 ) that.closeAllNotices();
    }, 20);
};

PushNotice.prototype.checkNotice = function() {

    let that = this;

    $.ajax({
        url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
        type: 'POST',
        data: {},
        dataType:"json",
        success:function(data) {

            console.log('data = ', data);
            for ( let i = 0; i < data.length; i++ )
            {
                that.addNotice(data[i]);
            }
			
            if ( data.length > 2 )
            {
				// если нотайсов больше 3х - запускаем функцию closeAllNotices. она выводит крестик вверху и вешает обработчик на него
                that.closeAllNotices();
            }
        }
    })
};

//let pushNotice;
window.onload = function()
{
    if ( !_PNSHOW_ ) return;
    if ( !pushNotice )
    {
        pushNotice = new PushNotice( document.querySelector('.closeAll'), document.getElementById('pushNoticeWrapp') );
    }
    pushNotice.checkNotice();
};
