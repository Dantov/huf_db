"use strict";

function ResultModal() {

    this.init();
}

ResultModal.prototype.init = function()
{
    debug('init');
    $('#modalResult').iziModal({
        title: 'Подготовка...',
        icon: 'glyphicon glyphicon-floppy-disk',
        transitionIn: 'comingIn',
        transitionOut: 'comingOut',
        overlayClose: false,
        closeButton: false,
        afterRender: function () {
            document.getElementById('modalResultContent').classList.remove('hidden');
        }
    });

    let that = this;
    // открылось
    $(document).on('opened', '#modalResult', that.onModalOpen.bind(null, that) );
    // Начало закрытия
    $(document).on('closing', '#modalResult', that.onModalClosing.bind(null, that) );
    // исчезло
    $(document).on('closed', '#modalResult', that.onModalClosed.bind(null, that) );
};


ResultModal.prototype.onModalOpen = function(that, event)
{
    console.log('Modal is Open');

    let modal = $('#modalResult');
    let modalButtonsBlock = document.getElementById('modalResult').querySelector('.modalButtonsBlock');
    let status = modalButtonsBlock.querySelector('.modalResultStatus');
    let back = modalButtonsBlock.querySelector('.modalProgressBack');
    let edit = modalButtonsBlock.querySelector('.modalResultEdit');
    let show = modalButtonsBlock.querySelector('.modalResultShow');

};
ResultModal.prototype.onModalClosing = function(main, event)
{
    console.log('Modal is closing');

};
ResultModal.prototype.onModalClosed = function(main, event)
{
    console.log('Modal is closed');

    let modal = $('#modalResult');
    let modalButtonsBlock = document.getElementById('modalResult').querySelector('.modalButtonsBlock');
    let status = document.querySelector('#modalResultStatus');
    let back = modalButtonsBlock.querySelector('.modalProgressBack');
    let edit = modalButtonsBlock.querySelector('.modalResultEdit');
    let show = modalButtonsBlock.querySelector('.modalResultShow');

    status.innerHTML = '';
    back.classList.add('hidden');
    edit.classList.add('hidden');
    show.classList.add('hidden');

    modal.iziModal('setTitle', '');
    modal.iziModal('setSubtitle', '');
    modal.iziModal('setIcon', 'glyphicon glyphicon-floppy-disk');

    progressModal.ProgressBar(-1);
};

let resModal = new ResultModal();
