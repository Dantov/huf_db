"use strict";

function DeleteModal() {

	this.dellObj = {};

    this.init();
}

DeleteModal.prototype.init = function()
{
    debug('init delete modal');
    $('#modalDelete').iziModal({
        title: '',
        headerColor: '#ff3f36',
        icon: 'glyphicon glyphicon-trash',
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
    //$(document).on('opening', '#modalDelete', that.onModalOpen.bind(null, that) );
    $(document).on('opening', '#modalDelete', function () {
		that.onModalOpen(that);
    } );
    // Начало закрытия
    $(document).on('closing', '#modalDelete', that.onModalClosing.bind(null, that) );
    // исчезло
    $(document).on('closed', '#modalDelete', that.onModalClosed.bind(null, that) );

    //обработчики на кнопки
    let buttons = document.getElementById('modalDeleteContent').querySelectorAll('a');
    let dell = buttons[1];
    let ok = buttons[2];

    dell.addEventListener('click', that.modalDeleteButton.bind(event,that) );

};


DeleteModal.prototype.onModalOpen = function(that)
{
    console.log('Dell Modal is Open');

    let modal = $('#modalDelete');
    let status = document.getElementById('modalDeleteContent').querySelector('#modalDeleteStatus');
    let buttons = document.getElementById('modalDeleteContent').querySelectorAll('a');
    let back = buttons[0];
    let dell = buttons[1];
    let ok = buttons[2];

	let num3d = document.querySelector('#num3d').value;
	let vendor_code = document.querySelector('#vendor_code').value;
	let modelType = document.querySelector('#modelType').value;
	let modelText = '<b>'+ num3d + ' / ' + vendor_code + ' - ' + modelType +'</b>';
	
	let dellData = that.dellObj;
	let titleText, img;
	if ( dellData.imgname )
	{
		if ( !dellData.isSTL )
		{
			titleText = 'Удалить картинку <b>' + dellData.imgname + '?</b>';
			img = document.createElement('img');
			img.src = _URL_ + '/Stock/' + num3d + '/' + dellData.id + '/images/' + dellData.imgname + '';
			img.height = 100;
		} else {
			img = document.createElement('p');
			img.innerHTML = dellData.imgname;
		}
		status.appendChild(img);
	}
	if ( dellData.isSTL == 1 )
		titleText = 'Удалить STL файлы позиции '+ modelText +'?'; 
	if ( dellData.isSTL == 2 )
		titleText = 'Удалить AI файл позиции '+ modelText +'?'; 
	if ( dellData.dellpos ) 
	{
		titleText = 'Удалить позицию '+ modelText +'?';
		modal.iziModal('setIcon', 'glyphicon glyphicon-floppy-remove');
	}

    modal.iziModal('setTitle', titleText);
    modal.iziModal('setSubtitle', 'Удаление происходит безвозвратно!');

    back.classList.remove('hidden');
    dell.classList.remove('hidden');

};
DeleteModal.prototype.onModalClosing = function(that, event)
{
    console.log('Modal is closing');

};
DeleteModal.prototype.onModalClosed = function(that, event)
{
    console.log('Modal is closed');

    let modal = $('#modalDelete');
    let buttons = document.getElementById('modalDeleteContent').querySelectorAll('a');
    let status = document.getElementById('modalDeleteContent').querySelector('#modalDeleteStatus');
    
	let back = buttons[0];
	let dell = buttons[1];
	let ok = buttons[2];

    status.innerHTML = '';
    back.classList.add('hidden');
    dell.classList.add('hidden');
    ok.classList.add('hidden');

    modal.iziModal('setTitle', '');
    modal.iziModal('setSubtitle', '');
    modal.iziModal('setHeaderColor', '#ff3f36');
    modal.iziModal('setIcon', 'glyphicon glyphicon-trash');
    
};

DeleteModal.prototype.modalDataInit = function(dellObj) 
{
	this.dellObj = dellObj;
};
DeleteModal.prototype.modalDeleteButton = function(that, event) {
	debug(that.dellObj,'dellObj');
	let dellData = that.dellObj;
	let imgElement;
	if ( dellData.element )
	{
		imgElement = dellData.element;
		delete dellData.element;
	}
		
	let modal = $('#modalDelete');
	let buttons = document.getElementById('modalDeleteContent').querySelectorAll('a');
	let status = document.getElementById('modalDeleteContent').querySelector('#modalDeleteStatus');

	let back = buttons[0];
	let dell = buttons[1];
	let ok = buttons[2];
	
	$.ajax({
		type: 'POST',
		url: 'controllers/delete.php',
		data: dellData,
		dataType:"json",
		success:function(response) {
            debug(response,'response');

			let imgname = response.imgname;
			let kartinka = response.kartinka;

			if ( response.dell ) { // удалили модель целиком
				imgname = response.dell; // здесь строка с именем модели
				kartinka = 'Модель ';
			}
			
			modal.iziModal('setTitle', kartinka + imgname +' удалена!');
			modal.iziModal('setSubtitle', '');
			modal.iziModal('setHeaderColor', '#2aabd2');
			modal.iziModal('setIcon', 'glyphicon glyphicon-ok');
			
			if (imgElement) imgElement.remove();

            ok.onclick = function() {
                document.location.reload(true);
            };
			ok.classList.remove('hidden');
			back.classList.add('hidden');
			dell.classList.add('hidden');
		}
	});
};

let dellModal = new DeleteModal();
