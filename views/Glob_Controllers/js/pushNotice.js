"use strict";

function PushNotice()
{
    this.noticesBadgeStatus = true; // все нотайсы показаны по умолчанию
    this.pushNoticeBadge = document.querySelector('.pushNoticeBadge');

    // только что полученные уведомления от checkNotice - полные данные
    // массив объектов
    this.incomingNotices = [];

    //здесь хранятся ID показанных уведомлений
    //delete localStorage.showedNotice;
    this.showedNotice = [];

    if ( localStorage.getItem('showedNotice') )
    {
        this.showedNotice = JSON.parse( localStorage.getItem('showedNotice') );
    }

    this.pushNoticeBadgeInc();
    this.noticesBadgeToggle();

    let that = this;
    iziToast.settings({
    	titleSize: 12,
		titleLineHeight: 14,
		messageSize: 12,
		messageLineHeight: 12,
		imageWidth: 75,
		position: 'topRight',
		timeout: 5000,
		maxWidth: 350,
        zindex: 998,
        target: '#pushNoticeWrapp',
        onClosing: function(instance, toast, closedBy) {
            if ( closedBy === 'timeout' )
            {
                // когда нажали на Спрятать что бы второй раз не вносить показанные в массив
                if ( that.showedNotice.includes(toast.id) ) return;

                that.showedNotice.push(toast.id);
                localStorage.setItem('showedNotice', JSON.stringify(that.showedNotice));
                that.pushNoticeBadgeInc();
                console.info('closedBy: ' + closedBy); // tells if it was closed by 'drag' or 'button'
            }

            if ( closedBy === 'button' || closedBy === 'drag' )
            {
                that.closeNotice(toast.id);

            }


            debug( JSON.parse( localStorage.getItem('showedNotice') ) );
        }
	});


	
}
PushNotice.prototype.pushNoticeBadgeInc = function() {
    this.pushNoticeBadge.innerHTML = this.showedNotice.length;
};

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

/**
 * Закрытие по крестику для каждого тоста
 * @param id
 */
PushNotice.prototype.closeNotice = function(id) 
{
    //ajax запрос на поставку ип адреса в таблицу
    let that = this;
    $.ajax({
        url: _ROOT_ + "Views/Glob_Controllers/pushNoticeController.php",
        type: 'POST',
        data: {
            closeNotice: id
        },
        dataType:"json",
        success:function(data) {
            that.pushNoticeBadgeInc();
            let objects = that.incomingNotices;

            for ( let i = 0; i < objects.length; i++) {
                if (objects[i].not_id == id) {
                    that.incomingNotices.splice(i, 1);
                    break;
                }
            }

            that.showedNotice.forEach((item, i) => {
                if (item === id) {
                    that.showedNotice.splice(i, 1);
                    localStorage.setItem('showedNotice', JSON.stringify(that.showedNotice));
                    return false;
                }
            });
        }
    });
};

PushNotice.prototype.addNotice = function(notice)
{
    let found = false;
    for (let object of this.incomingNotices) {
        if (object.not_id === notice.not_id) {
            found = true;
            break;
        }
    }
    if ( !found ) {
        this.incomingNotices.push(notice);
        debug(this.incomingNotices,'incomingNotices');
    }



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
	//this.showedNotice.push(notice.not_id); //добавли в массив как показанное уведомление чтоб не показывать снова
    //this.pushNoticeBadgeInc();
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

            that.incomingNotices = data;

            for ( let i = 0; i < data.length; i++ )
            {
                // если оно есть в массиве, значит уже показано - уходим.
                if ( that.showedNotice.includes(data[i].not_id) ) continue;
                that.addNotice(data[i]);
            }
			
            // if ( data.length > 2 )
            // {
				// // если нотайсов больше 3х - запускаем функцию closeAllNotices. она выводит крестик вверху и вешает обработчик на него
            //     //that.closeAllNotices();
            // }
        }
    });
};
PushNotice.prototype.noticesBadgeToggle = function() {
	let that = this;
	let noticesBadge = document.getElementById('noticesBadge');

	noticesBadge.querySelector('.noticeShow').addEventListener('click',function(){
		debug(that.incomingNotices,'показываем');

        for ( let i = 0; i < that.incomingNotices.length; i++ )
        {
            // если оно есть в массиве, значит уже показано - уходим.
            //if ( that.showedNotice.includes(data[i].not_id) ) continue;
            that.addNotice(that.incomingNotices[i]);
        }

	});
	noticesBadge.querySelector('.noticeHide').addEventListener('click',function(){

	    let pushNoticeWrapp = document.getElementById('pushNoticeWrapp');
	    let showedToasts = pushNoticeWrapp.querySelectorAll('.iziToast');
	    
        for ( let i = 0; i < showedToasts.length; i++ )
        {
            iziToast.hide({}, showedToasts[i]);
        }

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
