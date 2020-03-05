"use strict";

function ResultModal() {

    this.init();
}

ResultModal.prototype.init = function()
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

ResultModal.prototype.onModalOpen = function(that, event)
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

    });
};
ResultModal.prototype.onModalClosing = function(main, event)
{
    console.log('Modal is closing');
    debug(main.xhr);

    let doc = main.doc;
    debug(doc);
    if ( !doc.fileName ) return;

};
ResultModal.prototype.onModalClosed = function(main, event)
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