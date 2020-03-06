"use strict";

function ProgressModal()
{
    if ( !document.getElementById('modalProgress') ) return;
    this.init();

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
 * Иницаализация модального окна для прогресс бара
 * запускается при создании объекта Main
 * Collection to PDF
 */
ProgressModal.prototype.setProgressModal = function(docSwitch)
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
            doc.data.id = main.getQueryParam('id');
            break;
        case 'runner':
            doc.doc = 'PDF';
            doc.url = 'controllers/runner_pdf.php';
            doc.data.id = main.getQueryParam('id');
            break;
    }

    debug(doc);

    let modal = $('#modalProgress');
    modal.iziModal('setTitle', 'Идёт подготовка к созданию <b>'+ doc.doc +'</b> документа.');
    modal.iziModal('setHeaderColor', doc.headerColor);
};

ProgressModal.prototype.onModalOpen = function(that, event)
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
ProgressModal.prototype.onModalClosing = function(main, event)
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
ProgressModal.prototype.onModalClosed = function(main, event)
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

ProgressModal.prototype.init = function()
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
ProgressModal.prototype.getPDF = function(doc)
{
    this.setProgressModal(doc);
    $('#modalProgress').iziModal('open');
};
ProgressModal.prototype.sendXLS = function()
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

ProgressModal.prototype.sendPDF = function()
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

ProgressModal.prototype.ProgressBar = function(percent)
{
    let progBar = document.querySelector('.progress-bar');

    if ( percent === -1 ) percent = 0;

    progBar.setAttribute('aria-valuenow', percent);
    progBar.style.width = percent + "%";
    progBar.innerHTML = percent + "%";
};

ProgressModal.prototype.openPDF = function(filename) {
    window.open( _ROOT_ + 'Pdfs/' + filename );
};

let progressModal = new ProgressModal();
