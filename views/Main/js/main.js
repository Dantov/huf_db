"use strict";
function Main() {

    this.searchValue = '';
    this.collectionName = '';

    this.queryParams = {};

    this.parseQueryString();
    this.lightUpSomeButtons();
    this.modalStatusesInit();
}

/**
 * возьмем id из строки запроса, если он там есть
 */
Main.prototype.parseQueryString = function()
{
    let url = window.location.href;
    if ( ~url.indexOf('?') )
    {
        let params = url.split('?')[1].split('&');
        for (let i=0; i<params.length; i++)
        {
            let paramVal = params[i].split('=');
            this.queryParams[ paramVal[0] ] = paramVal[1];
        }
    }
};

Main.prototype.getQueryParam = function(param)
{
    let params = this.queryParams;
    for ( let key in params )
    {
        if ( key === param ) return params[key];
    }
};

Main.prototype.searchIn = function(num) {
	if ( num === 1 || num === 2 ) {
		console.log(num);
		
		$.ajax({
			url: _ROOT_ + "Views/Main/controllers/setSort.php", //путь к скрипту, который обрабатывает задачу
			type: 'POST',
			data: {//данные передаваемые в POST запросе
			   searchInNum: num
			},
			success:function(data) {
				let searchInBtn = document.getElementById('searchInBtn').firstElementChild;
					searchInBtn.innerHTML = data;
			}
		});
	}
};

Main.prototype.collectionSelect = function(self) {
	
	let collection_block = document.getElementById('collection_block');
	if ( collection_block.getAttribute('class')=='visible' )
	{
		collection_block.style.top = 20 + 'px';
		collection_block.classList.remove('visible');
		window.removeEventListener('click', hideCollBlock );
		return;
	} else {
		collection_block.classList.add('visible');
	}

	let a = this.getCoords(self);
	
	collection_block.style.top = (a.top - 15) + 'px';
		
	setTimeout(function(){
		window.addEventListener('click', hideCollBlock );
	},50);
		
	function hideCollBlock(event){
		if ( !event.target.hasAttribute('coll_block') ) {
			collection_block.style.top = 20 + 'px';
			collection_block.classList.remove('visible');
			window.removeEventListener('click', hideCollBlock );
		}
	}
};

Main.prototype.getCoords = function(elem) {

  let box = elem.getBoundingClientRect();

  let body = document.body;
  let docEl = document.documentElement;
  let scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
  let scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;
  let clientTop = docEl.clientTop || body.clientTop || 0;
  let clientLeft = docEl.clientLeft || body.clientLeft || 0;
  let top = box.top + scrollTop - clientTop;
  let left = box.left + scrollLeft - clientLeft;

  return {
    top: top,
    left: left
  };
  
};



/**
 * Ставит новую дату в рабочих центрах
 * @param self
 */
Main.prototype.changeStatusDate = function(self)
{
    let id = self.getAttribute('data-id');
    let oldDate = self.getAttribute('value');
    let newDate = self.value;

    let data = {
        id: id,
        newDate: newDate,
        oldDate: oldDate
    };

    $.ajax({
        url: _ROOT_ + "Views/Main/Controllers/changeStatusDate.php",
        type: "POST",
        data: data,
        dataType:"json",
        success:function(data)
        {
            debug(data.ok);
        },
        error: function ()
        {
            alert('Ошибка!');
        }
    });

    debug(data);
    //debug(_ROOT_);
};
Main.prototype.lightUpSomeButtons = function()
{
    // подсветим кнопки Send PDF Send Xlsx
    let expiredButon = document.getElementById('expiredButon');
    let sendXLS = document.getElementById('sendXLS');
    let sendPDF = document.getElementById('sendPDF');
    function farFasToggle(elem) {
        elem.classList.toggle('far');
        elem.classList.toggle('fas');
    }
    if ( expiredButon )
    {
        expiredButon.addEventListener('mouseover',function () {
            farFasToggle(this.children[0]);
        });
        expiredButon.addEventListener('mouseout',function () {
            farFasToggle(this.children[0]);
        });
    }
    if ( sendXLS )
    {
        sendXLS.addEventListener('mouseover',function () {
            farFasToggle(this.children[0]);
        });
        sendXLS.addEventListener('mouseout',function () {
            farFasToggle(this.children[0]);
        });
    }
    if ( sendPDF )
    {
        sendPDF.addEventListener('mouseover',function () {
            farFasToggle(this.children[0]);
        });
        sendPDF.addEventListener('mouseout',function () {
            farFasToggle(this.children[0]);
        });
    }
};

Main.prototype.modalStatusesInit = function()
{
    if ( document.getElementById('modalStatuses') )
    {
        $("#modalStatuses").iziModal({
            width: "95%",
            afterRender: function() {
                document.getElementById('modalContent').classList.remove('hidden');
                let statusesChevrons = document.querySelectorAll('.statusesChevron');
                statusesChevrons.forEach(button => {
                    button.addEventListener('click', function () {

                        if ( button.getAttribute('data-status') == 0 )
                        {
                            button.setAttribute('data-status','1')
                        } else {
                            button.setAttribute('data-status','0');
                        }
                        button.classList.toggle('btn-info');
                        button.classList.toggle('btn-primary');
                        button.children[0].classList.toggle('glyphicon-menu-down');
                        button.children[0].classList.toggle('glyphicon-menu-left');
                        let statArea = this.parentElement.parentElement.children[1];
                        statArea.classList.toggle('statusesPanelBodyHidden');
                        statArea.classList.toggle('statusesPanelBodyVisible');
                    }, false);
                });

                let currentSelectedStatus = document.getElementById('currentSelectedStatus').innerHTML;
                let modalStatuses = document.getElementById('modalStatuses');
                let statusesItems = modalStatuses.querySelectorAll('.wc-status-item');
                let panelNeedle;
                statusesItems.forEach(a => {
                    if ( a.children[1].innerHTML == currentSelectedStatus )
                    {
                        a.classList.add('active');
                        panelNeedle = a.parentElement.parentElement.parentElement;
                        panelNeedle.classList.remove('panel-info');
                        panelNeedle.classList.add('panel-primary');
                        panelNeedle.querySelector('button').click();
                        return;
                    }
                });

                let openAll = document.querySelector('#openAll');
                let closeAll = document.querySelector('#closeAll');
                openAll.addEventListener('click', function () {
                    statusesChevrons.forEach(button => {
                        if ( button.getAttribute('data-status') == 1 ) return;
                        button.click();
                    });

                    this.classList.add('hidden');
                    closeAll.classList.remove('hidden');
                }, false);
                closeAll.addEventListener('click', function () {
                    statusesChevrons.forEach(button => {
                        if ( button.getAttribute('data-status') == 0 ) return;
                        button.click();
                    });

                    this.classList.add('hidden');
                    openAll.classList.remove('hidden');
                }, false);
            },
        });
    }
};

if ( main !== 'object' ) main = new Main();
/*
let title = '0008750-кольцо / 700254';
let message = 'Добавлена новая модель!';
    message += '<br>';
    message += 'Быков В.А.';
    
iziToast.show({
	id: 100,
	title: title,
	titleSize: 12,
	titleLineHeight: 14,
	message: message,
	messageSize: 12,
	messageLineHeight: 12,
	image: _ROOT_ + 'Stock/default.jpg',
	imageWidth: 75,
	icon: 'fas fa-gem',
	iconColor: '',
	position: 'topRight',
	timeout: 60000,
	maxWidth: 350,
});
iziToast.show({
	id: 200,
	title: '0008750-серьги',
	message: 'модель изменена!',
	image: _ROOT_ + 'Stock/default.jpg',
	imageWidth: 80,
	position: 'topRight',
	timeout: 60000,
	buttons: [
		['<a href="#" class="btn btn-success">Перейти</a>',
			function (instance, toast) {
				document.location.href = _ROOT_+'views/ModelView/index.php?id=2060';
			},
			true
		], // true to focus
	],
	maxWidth: 350,
});

document.getElementById('100').children[0].addEventListener('click',function(){
	document.location.href = _ROOT_+'views/ModelView/index.php?id=2058';
});
document.getElementById('100').children[1].addEventListener('click',function() {
	document.location.href = _ROOT_+'views/ModelView/index.php?id=2058';
});
*/