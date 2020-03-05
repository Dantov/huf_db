"use strict";
function Main() {

    this.searchValue = '';
    this.collectionName = '';

    this.queryParams = {};


    this.parseQueryString();
    this.lightUpSomeButtons();
    this.modalStatusesInit();
    this.progressModalInit();

    this.doc = {
        doc: 'PDF',
        fileName:'',
        url:'',
        headerColor:'#1d82a6',
        type:'',
        data:{
            userName: userName,
            tabID: tabName,
        },
        method:'GET',
        xhr: '',
        switch:'',
    };
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
 * Иницаализация модального окна для прогресс бара
 * запускается при создании объекта Main
 * Collection to PDF
 */
Main.prototype.setProgressModal = function(docSwitch)
{
    let doc = this.doc;
    doc.switch = docSwitch;

    switch ( docSwitch )
    {
        case 'pdf':
            doc.doc = 'PDF';
            doc.url = 'controllers/pdfExport_Controller.php';
            doc.method = 'POST';
            break;
        case 'xls':
            doc.doc = 'Excel';
            doc.url = 'controllers/workingCenters_xls.php';
            doc.headerColor = '#00a623';
            doc.method = 'GET';
            doc.data.excel = 1;
            doc.data.getXlsx = 1;
            break;
        case 'passport':
            doc.doc = 'PDF';
            doc.url = 'controllers/passport_pdf.php';
            doc.data.id = this.getQueryParam('id');
            break;
        case 'runner':
            doc.doc = 'PDF';
            doc.url = 'controllers/runner_pdf.php';
            doc.data.id = this.getQueryParam('id');
            break;
    }

    debug(doc);

    let modal = $('#modalProgress');
    modal.iziModal('setTitle', 'Идёт подготовка к созданию <b>'+ doc.doc +'</b> документа.');
    modal.iziModal('setHeaderColor', doc.headerColor);
};

Main.prototype.onModalOpen = function(that, event)
{
    console.log('Modal is Open');

    let modalButtonsBlock = document.getElementById('modalProgress').querySelector('.modalButtonsBlock');
    let cancel = modalButtonsBlock.querySelector('.modalProgressCancel');
    let download = modalButtonsBlock.querySelector('.modalProgressDownload');
    let open = modalButtonsBlock.querySelector('.modalProgressOpen');
    let ok = modalButtonsBlock.querySelector('.modalProgressOK');
    let modal = $('#modalProgress');

    let docStr = that.searchValue ? 'Найдено ' + that.searchValue : that.collectionName;
    let doc = that.doc;

    if ( doc.switch === 'passport' ) docStr = 'Пасспорт';
    if ( doc.switch === 'runner' ) docStr = 'Бегунок';

    that.xhr = $.ajax({
        url: doc.url,
        method: doc.method,
        cache: false,
        dataType:'json',
        data: doc.data,
        beforeSend: function() {
            cancel.classList.remove('hidden');
            modal.iziModal('setTitle', 'Идёт создание <b>'+doc.doc+'</b> документа: <b>' + docStr + '</b>');

            if ( doc.switch === 'xls' )
            {
                // вторым запросом добудем имя файла
                $.ajax({
                    type:'GET',
                    url: 'controllers/workingCenters_xls.php',
                    data: {
                        excel:1,
                        getFileName:1
                    },
                    cache: false,
                    dataType:'json',
                    success:function(data) {
                        doc.fileName = data.fileName;
                        debug(data.fileName);
                    }
                });
            }
        },
        success:function(fileName)
        {
            if ( doc.switch === 'passport' || doc.switch === 'runner' ) doc.fileName = docStr = fileName;
            modal.iziModal('setTitle', 'Создание <b>'+doc.doc+'</b> документа: <b>' + docStr + '</b> завершено!');

            if ( doc.switch === 'xls' )
            {
                let int = setInterval(function () {
                    if ( doc.fileName )
                    {
                        modal.iziModal('setSubtitle', "Заберите файл <b><i>'" + doc.fileName + ".xlsx'</i></b> в загрузках вашего браузера.");
                        ok.classList.remove('hidden');
                        cancel.classList.add('hidden');
                        clearInterval(int);
                    }
                },500);

            } else {
                debug(fileName);
                doc.fileName = fileName;

                cancel.classList.add('hidden');
                download.classList.remove('hidden');
                open.classList.remove('hidden');

                open.addEventListener('click',function () {
                    that.openPDF(fileName);
                });
                download.setAttribute('href', _ROOT_ + 'Pdfs/' + fileName );
            }
        }
    }).done(function(data)
    {
        if ( doc.switch === 'xls' )
        {
            let int = setInterval(function () {
                if ( doc.fileName )
                {
                    let $a = $("<a>");
                    $a.attr("href",data);
                    $("body").append($a);
                    $a.attr("download", doc.fileName + ".xlsx");
                    $a[0].click();
                    $a.remove();

                    clearInterval(int);
                }
            },500);
        }
    });
};
Main.prototype.onModalClosing = function(main, event)
{
    console.log('Modal is closing');
    debug(main.xhr);
    if ( main.xhr.readyState !== 4 ) {
        main.xhr.abort();
        console.log('abort');
        return;
    }
    let doc = main.doc;
    debug(doc);
    if ( !doc.fileName ) return;
    if ( doc.switch === 'pdf' || doc.switch === 'passport' || doc.switch === 'runner' )
    {
        $.ajax({
            url: "../AddEdit/controllers/delete.php",
            type: "POST",
            data: {
                isPDF: 1,
                pdfname: doc.fileName,
            },
            dataType:"json",
            success:function(data) {
                if ( data.success ) console.log('delete complete');
            }
        });
    }
};
Main.prototype.onModalClosed = function(main, event)
{
    console.log('Modal is closed');

    let modalButtonsBlock = document.getElementById('modalProgress').querySelector('.modalButtonsBlock');
    modalButtonsBlock.querySelector('.modalProgressCancel').classList.add('hidden');
    modalButtonsBlock.querySelector('.modalProgressDownload').classList.add('hidden');
    modalButtonsBlock.querySelector('.modalProgressOpen').classList.add('hidden');
    modalButtonsBlock.querySelector('.modalProgressOK').classList.add('hidden');

    let modal = $('#modalProgress');

    modal.iziModal('setTitle', '');
    modal.iziModal('setSubtitle', '');

    main.ProgressBar(-1);
};

Main.prototype.progressModalInit = function()
{
    $('#modalProgress').iziModal({
        title: '',
        transitionIn: 'comingIn',
        transitionOut: 'comingOut',
        overlayClose: false,
        afterRender: function () {
            document.getElementById('modalProgressContent').classList.remove('hidden');
        }
    });

    let that = this;
    // открылось
    $(document).on('opened', '#modalProgress', that.onModalOpen.bind(null, that) );
    // Начало закрытия
    $(document).on('closing', '#modalProgress', that.onModalClosing.bind(null, that) );
    // исчезло
    $(document).on('closed', '#modalProgress', that.onModalClosed.bind(null, that) );
};
Main.prototype.getPDF = function(doc)
{
    this.setProgressModal(doc);
    $('#modalProgress').iziModal('open');
};
Main.prototype.sendXLS = function()
{
    let collectionName = document.getElementById('collectionName');
    let c_name = collectionName.innerHTML;
    let topSearchInpt = document.querySelector('.topSearchInpt').getAttribute('value');

    this.searchValue = topSearchInpt;
    this.collectionName = c_name;

    if ( c_name === 'Все Коллекции' && topSearchInpt === '' ) { // уходим если выбраны все коллекции
        alert('Нужно выбрать какую нибудь коллекцию!');
        return;
    }

    this.setProgressModal('xls');
    $('#modalProgress').iziModal('open');
};

Main.prototype.sendPDF = function()
{
    let collectionName = document.getElementById('collectionName');
    let c_name = collectionName.innerHTML;
    let topSearchInpt = document.querySelector('.topSearchInpt').getAttribute('value');

    this.searchValue = topSearchInpt;
    this.collectionName = c_name;

    debug(this.searchValue);
    debug(this.collectionName);

    if ( c_name === 'Все Коллекции' && topSearchInpt === '' ) { // уходим если выбраны все коллекции
        alert('Нужно выбрать какую нибудь коллекцию!');
        return;
    }

    this.setProgressModal('pdf');
    $('#modalProgress').iziModal('open');
};

Main.prototype.ProgressBar = function(percent)
{
    let progBar = document.querySelector('#modalProgressContent').children[0].children[0];

    if ( percent === -1 ) percent = 0;

    progBar.setAttribute('aria-valuenow', percent);
    progBar.style.width = percent + "%";
    progBar.innerHTML = percent + "%";
};

Main.prototype.openPDF = function(filename) {
    window.open( _ROOT_ + 'Pdfs/' + filename );
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