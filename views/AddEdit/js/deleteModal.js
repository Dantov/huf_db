"use strict";

function DeleteModal() {

    this.init();
}

DeleteModal.prototype.init = function()
{
    debug('init');
    $('#modalDelete').iziModal({
        title: '',
        transitionIn: 'comingIn',
        transitionOut: 'comingOut',
        overlayClose: false,
        closeButton: true,
        afterRender: function () {
            document.getElementById('modalDeleteContent').classList.remove('hidden');
        }
    });

    let that = this;
    // начало открытия
    $(document).on('opening', '#modalDelete', that.onModalOpen.bind(null, that) );
    // Начало закрытия
    $(document).on('closing', '#modalDelete', that.onModalClosing.bind(null, that) );
    // исчезло
    $(document).on('closed', '#modalDelete', that.onModalClosed.bind(null, that) );

    //обработчики на кнопки
};


DeleteModal.prototype.onModalOpen = function(that, event)
{
    console.log('Dell Modal is Open');

    let modal = $('#modalDelete');
    let modalButtonsBlock = document.getElementById('modalDelete').querySelectorAll('a');

    let back = modalButtonsBlock[0];
    let dell = modalButtonsBlock[1];
    let ok = modalButtonsBlock[2];

    modal.iziModal('setTitle', 'Удалить картинку?');
    modal.iziModal('setHeaderColor', '#ff3f36');

    back.classList.remove('hidden');
    dell.classList.remove('hidden');

};
DeleteModal.prototype.onModalClosing = function(main, event)
{
    console.log('Modal is closing');

};
DeleteModal.prototype.onModalClosed = function(main, event)
{
    console.log('Modal is closed');

    let modal = $('#modalDelete');
    let modalButtonsBlock = document.getElementById('modalDelete').querySelector('.modalButtonsBlock');
    let status = document.querySelector('#modalDeleteStatus');
    let back = modalButtonsBlock.querySelector('.modalProgressBack');
    let edit = modalButtonsBlock.querySelector('.modalDeleteEdit');
    let show = modalButtonsBlock.querySelector('.modalDeleteShow');

    status.innerHTML = '';
    back.classList.add('hidden');
    edit.classList.add('hidden');
    show.classList.add('hidden');

    modal.iziModal('setTitle', '');
    modal.iziModal('setSubtitle', '');

    progressModal.ProgressBar(-1);
};

let dellModal = new DeleteModal();
