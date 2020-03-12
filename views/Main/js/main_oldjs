"use strict";
function Main() {

    this.searchValue = '';
    this.collectionName = '';
    this.setProgressModal();

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
		})
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

Main.prototype.sendXLS = function() {

    let blackCover = document.getElementById('blackCover');
    blackCover.classList.add('blackCover');
    let pdf_result = document.getElementById('pdf_result');
    pdf_result.classList.toggle('hidethis');

    let fileName;

    $.ajax({
        type:'GET',
        url: 'controllers/workingCenters_xls.php',
        data: {
            excel:1,
			getXlsx:1,
        },
        dataType:'json',
        beforeSend: function() {

            pdf_result.children[0].style.textAlign = 'center';
            pdf_result.children[0].innerHTML = '<p>Идет создание Excel документа...</p>';
            pdf_result.children[1].remove();

            //получим имя файла ещё одним запросом
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
                    fileName  = data.fileName;
                    debug(data.fileName);
                }
            });

		},
        success:function()
		{
			let int = setInterval(function () {
				if ( fileName )
				{
					drawSheet();
					clearInterval(int);
				}
			},500);

			function drawSheet()
			{
                pdf_result.children[0].innerHTML = "<p>Создание Excel документа завершено!</p>";
                pdf_result.children[0].innerHTML += "Заберите файл <i>'" + fileName + ".xlsx'</i> в загрузках вашего браузера.";

                let a = document.createElement('a');
                a.setAttribute('class','btn btn-success');
                a.setAttribute('type','button');
                a.setAttribute('href','index.php');
                a.style.marginTop = '10px';
                a.innerHTML = 'OK';

                let center = document.createElement('center');
                center.appendChild(a);
                pdf_result.appendChild(center);
			}
        }
    }).done(function(data)
	{
        let int = setInterval(function () {
            if ( fileName )
            {
                download();
                clearInterval(int);
            }
        },500);

		function download() {
            let $a = $("<a>");
            $a.attr("href",data);
            $("body").append($a);
            $a.attr("download", fileName + ".xlsx");
            $a[0].click();
            $a.remove();
        }
    });
};


/**
 * Collection to PDF
 */
Main.prototype.setProgressModal = function()
{
    let modalProgress = document.getElementById('modalProgress');
    if ( !modalProgress ) return;

    let that = this;
    let modal = $('#modalProgress');
    let xhr;
    let pdfFileName = '';

    /*
    transitionIn	- comingIn, bounceInDown, bounceInUp, fadeInDown, fadeInUp, fadeInLeft, fadeInRight, flipInX.
    transitionOut	- comingOut, bounceOutDown, bounceOutUp, fadeOutDown, fadeOutUp, , fadeOutLeft, fadeOutRight, flipOutX.
    */

    modal.iziModal({
        headerColor: '#1d82a6',
        transitionIn: 'comingIn',
        transitionOut: 'comingOut',
        afterRender: function () {
            document.getElementById('modalProgressContent').classList.remove('hidden');
        }
    });

    //начало открываться
    $(document).on('opening', '#modalProgress', function () {
        modal.iziModal('setTitle', 'Идёт подготовка к созданию <b>PDF</b> документа.');
    });

    // открылось
    $(document).on('opened', '#modalProgress', function () {

        let modalButtonsBlock = modalProgress.querySelector('.modalButtonsBlock');
        let cancel = modalButtonsBlock.querySelector('.modalProgressCancel');
        let download = modalButtonsBlock.querySelector('.modalProgressDownload');
        let open = modalButtonsBlock.querySelector('.modalProgressOpen');
        let docStr = that.searchValue ? 'Найдено ' + that.searchValue : that.collectionName;

        xhr = $.ajax({
            url: 'controllers/pdfExport_Controller.php',
            method: 'POST',
            cache: false,
            data: {
                userName: userName,
                tabID: tabName,
            },
            beforeSend: function() {
                debug(cancel);
                cancel.classList.remove('hidden');

                modal.iziModal('setTitle', 'Идёт создание <b>PDF</b> документа: <b>' + docStr + '</b>');
            },
            success:function(fileName) {
                debug(fileName);
                pdfFileName = fileName;
                modal.iziModal('setTitle', 'Создание <b>PDF</b> документа: <b>' + docStr + '</b> завершено!');

                cancel.classList.add('hidden');
                download.classList.remove('hidden');
                open.classList.remove('hidden');

                open.addEventListener('click',function () {
                    main.openPDF(fileName);
                });
                download.setAttribute('href', _ROOT_ + 'Pdfs/' + fileName );
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
    $(document).on('closed', '#modalProgress', function () {
        console.log('Modal is closed');

        let modalButtonsBlock = modalProgress.querySelector('.modalButtonsBlock');
        modalButtonsBlock.querySelector('.modalProgressCancel').classList.add('hidden');
        modalButtonsBlock.querySelector('.modalProgressDownload').classList.add('hidden');
        modalButtonsBlock.querySelector('.modalProgressOpen').classList.add('hidden');

        modal.iziModal('setTitle', '');
        main.ProgressBarPDF(-1);

        if ( !pdfFileName ) return;
        $.ajax({
            url: "../AddEdit/controllers/delete.php",
            type: "POST",
            data: {
                isPDF: 1,
                pdfname: pdfFileName,
            },
            dataType:"json",
            success:function(data) {
                if ( data.success ) console.log('delete complete'); //document.location.reload();
            }
        })
    });

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

    $('#modalProgress').iziModal('open');
};
Main.prototype.ProgressBarPDF = function( percent )
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


// старый ProgressBar
Main.prototype.ProgressBar = function(persent,filename)
{
	if ( persent < 100 ) { //если задача не достигла 100% готовности, отправляем запрос на ее выполнение
		var that = this;
		$.ajax({
			url: "../Glob_Controllers/progress_pdf.php", //путь к скрипту, который обрабатывает задачу
			data: {//данные передаваемые в POST запросе
			   //difficult_task:"difficult_task",                                   
			},
			dataType:"json",
			success:function(data) {  //функция обратного вызова, выполняется в случае успехной отработки скрипта
				
				var stat = data.status;
				var overalProgress = data.overalProgress || 0;
				filename = data.filename;
				var progressStatus = document.getElementById("progressStatus");
					progressStatus.innerHTML = stat;
					
				var progressBarDOM = document.getElementById("progress-bar");
					progressBarDOM.style.width = overalProgress + "%";
					progressBarDOM.innerHTML = overalProgress + "%";
				//console.log('overalProgress = ',overalProgress);
				
				// рекурсивно вызываем этуже функцию, она будет выполняться пока не выполнит 100%
				setTimeout(function(){
					that.ProgressBar( parseInt(overalProgress), filename );
				},250);
			}
		})
	} else {//если задача выполненна на 100%, то выводим информацию об этом.
		console.log(filename);
		var back = document.createElement('a');
			back.setAttribute('class','btn btn-default');
			back.setAttribute('type','button');
			back.style.marginBottom = '5px';
			back.style.marginRight = '8px';
			back.innerHTML = '<span class="glyphicon glyphicon-triangle-left"></span> Назад';
			back.onclick = function()
			{
				// удаляем pdf file 
				$.ajax({
					url: "../AddEdit/controllers/delete.php",
					type: "POST",
					data: {
						isPDF: 1,
						pdfname: filename,
					},
					dataType:"json",
					success:function(data) {
						console.log('imhere');
						if ( data.success ) document.location.reload();
					}
				})
			};
			
		var openA = document.createElement('a');
			openA.setAttribute('class','btn btn-success');
			openA.setAttribute('type','button');
			openA.setAttribute('onclick','main.openPDF("' + filename + '");');
			openA.style.marginBottom = '5px';
			openA.style.marginRight = '8px';
			openA.innerHTML = '<span class="glyphicon glyphicon-open-file"></span> Открыть';
			
		var download = document.createElement('a');
			download.setAttribute('class','btn btn-info');
			download.setAttribute('type','button');
			download.setAttribute('download','');
			download.setAttribute('href', _ROOT_ + 'Pdfs/' + filename );
			download.style.marginBottom = '5px';
			download.innerHTML = '<span class="glyphicon glyphicon-save-file"></span> Загрузить';
			
		var center = document.getElementById("pdf_result").lastElementChild;
			center.lastElementChild.remove();
			center.appendChild(back);
			center.appendChild(openA);
			center.appendChild(download);
		
		// удаляем прогресс бар после завершения
		$.ajax({
			url: "../Glob_Controllers/progress_pdf.php",
			type: "POST",
			data: {
			   killProgressBar: 1,                                   
			},
			dataType:"json",
			success:function(data) {
				//console.log('progress bar killed ',data.killed);
			}
		})
	}
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

if ( main !== 'object' ) main = new Main();

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
// END Send PDF Send Xlsx


//modals

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



