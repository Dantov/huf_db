"use strict";

function PushNotice()
{
    this.showedNotice = showedNotice; //здесь хранятся показанные уведомления
    this.noticesBadgeStatus = true; // все нотайсы показаны по умолчанию

    iziToast.settings({
    	titleSize: 12,
		titleLineHeight: 14,
		messageSize: 12,
		messageLineHeight: 12,
		imageWidth: 75,
		position: 'topRight',
		timeout: 60000,
		maxWidth: 350,
        zindex: 998,
        target: '#pushNoticeWrapp',
	});
	
}

PushNotice.prototype.closeAllNotices = function() {
    // проверка, что бы поставить обработчик только раз.( пытался каждый раз ставить при checkNotice )
	//if ( !this.closeAllPN.classList.contains('hidden') ) return;
	
	//this.closeAllPN.classList.remove('hidden');
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

PushNotice.prototype.closeNotice = function(id) 
{
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
    let that = this;
	
    let url = _ROOT_ + "Views/ModelView/index.php?id=" + notice.pos_id;


    /*
    if ( typeof notice.status === 'object' )
    {
        let statSpan = '<span class="glyphicon glyphicon-'+ notice.status.glyphi +'"></span>';
        statusStr = '<div class="' + notice.status.name_en + ' pn_status pull-left" title="' + notice.status.title + '">'+ statSpan +'</div>';
    }
    */

    if ( +notice['addEdit'] === 1 ) abt = "Добавлена новая";
    if ( +notice['addEdit'] === 2 ) abt = "Изменена";
    if ( +notice['addEdit'] === 3 ) abt = "Удалена";

    iziToast.show({
		id: notice.not_id,
		title: notice.number_3d +'/'+ notice.vendor_code + ' - ' + notice.model_type,
		message: abt + ' модель!',
		image: notice.img_src,
		icon: 'glyphicon glyphicon-'+ notice.status.glyphi,
		iconColor: '',
	});
	
	newNotice = document.getElementById(notice.not_id);

    // переходим на модель при клике и ставим IP
    if ( +notice['addEdit'] !== 3 )
    {
    	newNotice.children[0].addEventListener('click',function() {

            that.closeNotice(notice.not_id);
            document.location.href= url;
            /*
			$.ajax({
				url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
				type: 'POST',
				data: {
					closeNotice: notice.not_id
				},
				dataType:"json",
				success:function(data) {

					if ( data['done'] ) {
						console.log(data['done'],id);
						document.location.href= url;
					}
				},
				error:function(error) {
					console.log(error);
				}
			});
			*/
		});
		
		/*
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
        }, false);*/
    }

    // при закрытии ставим IP
    newNotice.querySelector('.iziToast-close').addEventListener('click', function() {

        that.closeNotice(notice.not_id);
    }, false );
	this.showedNotice.push(notice.not_id); //добавли в массив как показанное уведомление чтоб не показывать снова
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
            if ( typeof data !== 'object' ) return;
            for ( let i = 0; i < data.length; i++ )
            {
                that.addNotice(data[i]);
            }
			
            if ( data.length > 2 )
            {
				// если нотайсов больше 3х - запускаем функцию closeAllNotices. она выводит крестик вверху и вешает обработчик на него
                //that.closeAllNotices();
            }
        }
    })
};
PushNotice.prototype.noticesBadgeToggle = function() {
	let that = this;
	let noticesBadge = document.getElementById('noticesBadge');
	noticesBadge.querySelector('.noticeShow').addEventListener('click',function(){
		
	});
	noticesBadge.querySelector('.noticeHide').addEventListener('click',function(){

	});
	noticesBadge.querySelector('.noticeCloseAll').addEventListener('click',function(){

	});
	
};
//let pushNotice;
window.onload = function()
{
    if ( !_PNSHOW_ ) return;
    if ( !pushNotice )
    {
       // pushNotice = new PushNotice( document.querySelector('.closeAll'), document.getElementById('pushNoticeWrapp') );
        pushNotice = new PushNotice();
    }
    pushNotice.checkNotice();
};
