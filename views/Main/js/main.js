"use strict";
function Main() {

    this.searchValue = '';
    this.collectionName = '';

    this.lightUpSomeButtons();
    this.modalStatusesInit();

}

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
    let modalProgress = document.getElementById('modalProgress');
    if ( !modalProgress ) return;

    let that = this;
    let modal = $('#modalProgress');
    let xhr;
    let forDocument = {
        doc: '',
        fileName:'',
        url:'',
        headerColor:'',
        type:'',
        data:{
            userName: userName,
            tabID: tabName,
        },
        method:'',
    };

    switch ( docSwitch )
    {
        case 'pdf':
            forDocument.doc = 'PDF';
            forDocument.url = 'controllers/pdfExport_Controller.php';
            forDocument.headerColor = '#1d82a6';
            forDocument.method = 'POST';
            break;
        case 'xls':
            forDocument.doc = 'Excel';
            forDocument.url = 'controllers/workingCenters_xls.php';
            forDocument.headerColor = '#00a623';
            forDocument.method = 'GET';
            forDocument.data.excel = 1;
            forDocument.data.getXlsx = 1;
            break;
    }

    debug(forDocument);

    modal.iziModal({
        title: 'Идёт подготовка к созданию <b>'+ forDocument.doc +'</b> документа.',
        headerColor: forDocument.headerColor,
        transitionIn: 'comingIn',
        transitionOut: 'comingOut',
        overlayClose: false,
        afterRender: function () {
            document.getElementById('modalProgressContent').classList.remove('hidden');
        }
    });

    // открылось
    $(document).on('opened', '#modalProgress', function () {

        let modalButtonsBlock = modalProgress.querySelector('.modalButtonsBlock');
        let cancel = modalButtonsBlock.querySelector('.modalProgressCancel');
        let download = modalButtonsBlock.querySelector('.modalProgressDownload');
        let open = modalButtonsBlock.querySelector('.modalProgressOpen');
        let ok = modalButtonsBlock.querySelector('.modalProgressOK');
        let docStr = that.searchValue ? 'Найдено ' + that.searchValue : that.collectionName;

        xhr = $.ajax({
            url: forDocument.url,
            method: forDocument.method,
            cache: false,
            dataType:'json',
            data: forDocument.data,
            beforeSend: function() {
                debug('Внешний');
                cancel.classList.remove('hidden');
                modal.iziModal('setTitle', 'Идёт создание <b>'+forDocument.doc+'</b> документа: <b>' + docStr + '</b>');

                if ( docSwitch === 'xls' )
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
                        beforeSend: function() {
                            debug('Внутренний');
                        },
                        success:function(data) {
                            forDocument.fileName = data.fileName;
                            debug(data.fileName);
                        }
                    });
                }

            },
            success:function(fileName)
            {
                modal.iziModal('setTitle', 'Создание <b>'+forDocument.doc+'</b> документа: <b>' + docStr + '</b> завершено!');

                if ( docSwitch === 'xls' )
                {
                    let int = setInterval(function () {
                        if ( forDocument.fileName )
                        {
                            modal.iziModal('setSubtitle', "Заберите файл <b><i>'" + forDocument.fileName + ".xlsx'</i></b> в загрузках вашего браузера.");
                            ok.classList.remove('hidden');
                            cancel.classList.add('hidden');
                            clearInterval(int);
                        }
                    },500);

                } else {
                    debug(fileName);

                    cancel.classList.add('hidden');
                    download.classList.remove('hidden');
                    open.classList.remove('hidden');

                    open.addEventListener('click',function () {
                        main.openPDF(fileName);
                    });
                    download.setAttribute('href', _ROOT_ + 'Pdfs/' + fileName );
                }
            }
        }).done(function(data)
        {
            if ( docSwitch === 'xls' )
            {
                let int = setInterval(function () {
                    if ( forDocument.fileName )
                    {
                        download();
                        clearInterval(int);
                    }
                },500);

                function download() {
                    let $a = $("<a>");
                    $a.attr("href",data);
                    $("body").append($a);
                    $a.attr("download", forDocument.fileName + ".xlsx");
                    $a[0].click();
                    $a.remove();
                }
            }
        });
        console.log('Modal is Open');
    });

    // Начало закрытия
    $(document).on('closing', '#modalProgress', function () {
        console.log('Modal is closing');
        if ( xhr.readyState !== 4 ) xhr.abort();
    });
    // исчезло
    $(document).on('closed', '#modalProgress', function ()
    {
        console.log('Modal is closed');

        let modalButtonsBlock = modalProgress.querySelector('.modalButtonsBlock');
        modalButtonsBlock.querySelector('.modalProgressCancel').classList.add('hidden');
        modalButtonsBlock.querySelector('.modalProgressDownload').classList.add('hidden');
        modalButtonsBlock.querySelector('.modalProgressOpen').classList.add('hidden');
        modalButtonsBlock.querySelector('.modalProgressOK').classList.add('hidden');

        modal.iziModal('setTitle', '');
        main.ProgressBar(-1);

        if ( !forDocument.fileName ) return;
        if ( docSwitch === 'pdf' )
        {
            $.ajax({
                url: "../AddEdit/controllers/delete.php",
                type: "POST",
                data: {
                    isPDF: 1,
                    pdfname: forDocument.fileName,
                },
                dataType:"json",
                success:function(data) {
                    if ( data.success ) console.log('delete complete'); //document.location.reload();
                }
            });
        }
    });

};
Main.prototype.getPassportRunner = function()
{

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