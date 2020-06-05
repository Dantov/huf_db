"use strict";

// ----- Обработчик кликов на контейнер 25,02,18 ----- //
document.querySelector('.content').addEventListener('click', function(event) {
		let click = event.target;

		if ( !click.hasAttribute('elemToAdd') ) return;
		if ( click.hasAttribute('coll') ) return;
		if ( click.hasAttribute('VCTelem') ) {
			let target = click.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;

			//запускает код на поиск соответствий в колл. "Детали", и формирует меню Арт./Ном.3D справа
			getVCmenu(click.innerHTML, target);
		}

		// всиавим номер для инрута картинки
		if ( click.hasAttribute('data-imgFor') )
		{
			let target = click.parentElement.parentElement.parentElement.parentElement.firstElementChild;
			//console.log(target);

			let oldInput = click.parentElement.parentElement.parentElement.parentElement.children[1];
			let allVisInpts = picts.querySelectorAll('.vis');
			// проверим на такой же выбранный в других картинках
			for( let i = 0; i < allVisInpts.length; i++ )
			{
				if ( allVisInpts[i] == oldInput ) continue;
				if ( allVisInpts[i].value == click.innerHTML )
				{
					allVisInpts[i].value = 'Нет';
					allVisInpts[i].previousElementSibling.value = 0;
				}
			}

			target.setAttribute('value', click.getAttribute('data-imgFor') );
		}


		let inputToAddSomethig = click.parentElement.parentElement.parentElement.previousElementSibling;
            inputToAddSomethig.value = click.innerHTML;
            inputToAddSomethig.setAttribute('value', click.innerHTML );
	});

function getVCmenu( mType_name, targetEl ) 
{
	$.ajax({
		url: "/add-edit/num3dVC",
		type: "POST",
		data: {
		   modelsTypeRequest: mType_name,
		},
		dataType:"json",
		success:function(dataLi) {
			//debug(dataLi);
			let num3dVC_new = document.getElementById('num3dVC_proto').cloneNode(true);
				num3dVC_new.removeAttribute('id');
				num3dVC_new.classList.remove('hidden');
				num3dVC_new.children[0].setAttribute('name','num3d_vc_[]');
			let ul = num3dVC_new.querySelector('.dropdown-menu');
			for( let i = 0; i < dataLi.length; i++ ){
				ul.innerHTML += dataLi[i];
			}
			addPrevImg( num3dVC_new, 'bottom', 'feft' ); // насаживаем события на показ картинки при наведении
			targetEl.children[0].remove();
			targetEl.appendChild(num3dVC_new);
			dataLi = null;
			//console.log('dataLi = ', typeof dataLi);
		},
		error:function(error){
			debug(error,"getVCmenu error");
		}
	});
}
// -----END  Обработчик кликов ----- //




/*
//-----  Добавляем STL и Ai файлы  -------//
let aiSelect = document.getElementById('aiSelect');
if ( aiSelect ) {
    let fileAi_input = document.getElementById('fileAi');
    let removeAi = document.getElementById('removeAi');

	aiSelect.addEventListener('click',function () {
        fileAi_input.click();
    },false);

    removeAi.addEventListener('click', function () {
        document.getElementById('spanWrappAi').remove();
        fileAi_input.value = null;
        this.classList.toggle('hidden');
        aiSelect.classList.toggle('hidden');
    }, false);

	fileAi_input.onchange = function() {
		aiSelect.classList.toggle('hidden');
		let files = this.files;

		let spanWrapper = document.createElement('span');
        spanWrapper.setAttribute('id','spanWrappAi');

		for( let i = 0; i < files.length; i++ ) {
			let size = ( (files[i].size / 1024) ).toFixed(2);
			let span = document.createElement('span');
				span.style.backgroundColor = '#fff';
				span.style.padding = '5px 8px 5px 8px';
				span.style.marginRight = '5px';
				span.style.borderRadius = '5px';
				span.innerHTML = files[i].name + ' - (' + size + 'кб)';
            spanWrapper.appendChild(span);
		}
		removeAi.parentElement.insertBefore(spanWrapper, removeAi);
		removeAi.classList.toggle('hidden');
	};

}
let stlSelect = document.getElementById('stlSelect');
if ( stlSelect ) {

	let fileSTL_input = document.getElementById('fileSTL');
    let removeStl = document.getElementById('removeStl');

    stlSelect.addEventListener('click',function () {
        fileSTL_input.click();
    },false);

    removeStl.addEventListener('click',function () {
        document.getElementById('spanWrappSTl').remove();
        fileSTL_input.value = null;
        this.classList.toggle('hidden');
        stlSelect.classList.toggle('hidden');
    },false);

	fileSTL_input.onchange = function()
	{
		stlSelect.classList.toggle('hidden');
		let files = this.files;

		let spanWrapper = document.createElement('span');
        spanWrapper.setAttribute('id','spanWrappSTl');

		for( let i = 0; i < files.length; i++ ) {
			let size = ( (files[i].size / 1024) / 1024 ).toFixed(2);
			let span = document.createElement('span');
				span.style.backgroundColor = '#fff';
				span.style.padding = '5px 8px 5px 8px';
				span.style.marginRight = '5px';
				span.style.borderRadius = '5px';
				span.innerHTML = files[i].name + ' - (' + size + 'mb)';
            spanWrapper.appendChild(span);
		}
		removeStl.parentElement.insertBefore(spanWrapper, removeStl);
		removeStl.classList.toggle('hidden');
	};

}
//----- END STL файлы -------//
*/




/**
 *  Накидываем существующие
 */
 function collectionDropDown(button)
{
    let collectionBlockForm = document.getElementById('collectionBlockForm');
    button.addEventListener('click',function (ev) {
        ev.preventDefault();
        ev.stopPropagation();

        collectionBlockForm.classList.toggle('hidden');
        collectionBlockForm.style.top = (this.getBoundingClientRect().top - 330) + window.pageYOffset + 'px';

        collectionDropDown.button = this;
    },false);
}
if ( document.getElementById('collections_table') )
{
	let dropDowns = document.getElementById('collections_table').querySelectorAll('.collectionDropDown');
	$.each(dropDowns, function(i, button){
		collectionDropDown(button);
	});

	/**
	 *  открывает Блок коллекций
	 */
	document.getElementById('collectionBlockForm').addEventListener('click', function(event) {
	    let click = event.target;
	    if ( !click.hasAttribute('coll') ) return;

	    let input = collectionDropDown.button.parentElement.previousElementSibling;

	    input.value = click.innerHTML;
	    input.setAttribute('value', click.innerHTML );

	    this.classList.add('hidden');
	},false);

	document.getElementById('content').addEventListener('click',function (event) {
		if ( event.target.getAttribute('id') === 'collectionBlockForm' ) return;
	    if ( !document.getElementById('collectionBlockForm').classList.contains('hidden') )
	        document.getElementById('collectionBlockForm').classList.add('hidden');
	},false);
}





// ----- КАМНИ Dop VC Материалы-------//
if ( document.getElementById('addGem') )
{
    document.getElementById('addGem').addEventListener('click', function(event){
        event.preventDefault();
        addRow(this);
    }, false );

}
if ( document.getElementById('addVC') )
{
    document.getElementById('addVC').addEventListener('click', function (event) {
        event.preventDefault();
        addRow(this);
    }, false);
}
if ( document.getElementById('addCollection') )
{
    document.getElementById('addCollection').addEventListener('click', function (event) {
        event.preventDefault();
        addRow(this);
    }, false);
}
if ( document.getElementById('addMats') ) 
{
	document.getElementById('addMats').addEventListener('click', function(event){
	    event.preventDefault();
	    addRowNew(this);
	}, false );
}

function addRowNew( self ) 
{
	let table = self.parentElement.nextElementSibling.children[1];
	let protoRow = '';

	let newRow = document.getElementById('protoMaterialsRow').cloneNode(true);
		newRow.removeAttribute('id');
		newRow.classList.remove('hidden','protoRow');

	table.appendChild(newRow);
	if ( table.parentElement.classList.contains('hidden') ) table.parentElement.classList.remove('hidden');
}

function addRow( self )
{
	let tBody = self.parentElement.nextElementSibling.children[1];
	let protoRow, tBodyId = tBody.getAttribute('id');

	switch (tBodyId)
    {
        case "gems_table":
            protoRow = 'protoGemRow';
            break;
        case "dop_vc_table":
            protoRow = 'protoArticlRow';
            break;
        case "collections_table":
            protoRow = 'protoCollectionRow';
            break;
        case "metals_table":
            protoRow = 'protoMaterialsRow';
            break;
    }

	let counter = tBody.getElementsByTagName('tr').length;
	let newRow = document.getElementById(protoRow).cloneNode(true);
		newRow.style.display = "table-row";
		newRow.removeAttribute('id');
		newRow.children[0].innerHTML = ++counter;
	if ( protoRow === 'protoCollectionRow' )
	{
        collectionDropDown(newRow.querySelector('.collectionDropDown'));
	}
		
	tBody.appendChild(newRow);
	if ( tBody.parentElement.classList.contains('hidden') ) tBody.parentElement.classList.remove('hidden');
}
function duplicateRowNew( self ) {
	let tBody = self.parentElement.parentElement.parentElement;
	let tocopy = self.parentElement.parentElement.cloneNode(true);
		tocopy.querySelector('.rowID').removeAttribute('value');
	tBody.insertBefore(tocopy, self.parentElement.parentElement.nextElementSibling);
}
function duplicateRow( self ) {
	let tBody = self.parentElement.parentElement.parentElement;
	let tocopy = self.parentElement.parentElement.cloneNode(true);
	tBody.insertBefore(tocopy, self.parentElement.parentElement.nextElementSibling);
	setNum(tBody);
}
function deleteRowNew( self ) {
	let row = self.parentElement.parentElement;
	if ( row.querySelector('.rowID').hasAttribute('value') )
	{
        let inputs = row.querySelectorAll('input');
        $.each(inputs, function(i, elem)
        {
            if (elem.className === 'rowID') return;
            elem.setAttribute('value',-1);
        });
        /*
        inputs.forEach(function (elem) {
            if (elem.className === 'rowID') return;
            elem.setAttribute('value',-1);
        });
        */
        row.setAttribute('class','hidden');
	} else {
        row.remove();
	}
}
function deleteRow( self )
{
    let tBody = self.parentElement.parentElement.parentElement;
	self.parentElement.parentElement.remove();
	//setNum(tBody);

	// скроем всю табл если нет строк
    let rows = tBody.getElementsByTagName('tr');
	if ( rows.length === 0 ) tBody.parentElement.classList.add('hidden');
}
function setNum(table) {
	let rows = table.getElementsByTagName('tr');
	for ( let i = 0; i < rows.length; i++ ) {
		rows[i].children[0].innerHTML = i+1;
	}
}
// -----END КАМНИ ДОП АРТИКУЛЫ-------//

// для доп вставки элементов addElemMore +
let gems_table = document.getElementById('gems_table');
if ( gems_table )
{
    gems_table.addEventListener('click', function(event) {

        if ( !event.target.hasAttribute('addElemMore') ) return;

        event.stopPropagation(); // прекращаем обработку других событий под этим кликом
        let click = event.target;
        let elemtoAdd = click.previousElementSibling.innerHTML;

        let inputToAddSomethig = click.parentElement.parentElement.parentElement.previousElementSibling;

        let inputToAddSomethig_PrevVal = inputToAddSomethig.getAttribute('value');
        let coma = '';
        if ( inputToAddSomethig_PrevVal ) coma = ', ';
        let newValue = inputToAddSomethig_PrevVal + coma + elemtoAdd;

        inputToAddSomethig.value = newValue;
        inputToAddSomethig.setAttribute('value', newValue );


    });
}


// ----- Описания -------//
let addNote = document.querySelector('.addNote');
if ( addNote )
{
    let modelNotes = document.querySelector('.modelNotes');
    let modelNotesDIV = modelNotes.querySelectorAll('.model-note');
    let lastNoteNum = 0;
    if ( modelNotesDIV.length )
	{
        lastNoteNum = modelNotesDIV[modelNotesDIV.length-1].querySelector('.note_num').getAttribute('value');
	}

    addNote.addEventListener('click', function(event) {
        event.preventDefault();

        let newNote = document.querySelector('.proto-note').cloneNode(true);
			newNote.classList.remove('proto-note', 'hidden');
			newNote.querySelector('.note-num').innerHTML = ++lastNoteNum;
			newNote.querySelector('.note_num').setAttribute('value', lastNoteNum);

        modelNotes.appendChild(newNote);
    });
}
function removeNote(self)
{
    let notePanel = self.parentElement.parentElement;
    let noteID = notePanel.querySelector('.note_id').getAttribute('value');

    notePanel.classList.add('hidden');
    notePanel.classList.remove('model-note');
    if ( noteID  )
	{
        notePanel.querySelector('textarea').innerHTML = -1;
	} else {
        notePanel.remove();
	}
}


// ----- РЕМОНТЫ -------//
if ( document.getElementById('repairsBlock') )
{
	let addRepairs = document.getElementById('repairsBlock').querySelectorAll('.addRepairs');
	$.each(addRepairs, function(i, button) {
		button.addEventListener('click', function (event) {
	        event.preventDefault();
	        event.stopPropagation();

	        let lastRepNum = 0, repairsCount = 0;
	        let repairsBlock = document.getElementById('repairsBlock');

	        let today = new Date();

	        let dataRepair = this.getAttribute('data-repair');
	        let newRepairs = document.getElementById('protoRepairs').cloneNode(true);

	        switch (dataRepair) {
	            case '3d':
	                repairsCount = repairsBlock.querySelectorAll('.repairs3d');
	                if ( repairsCount.length ) {
	                    lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
	                }

	                newRepairs.removeAttribute('id');
	                newRepairs.classList.remove('hidden');
	                newRepairs.classList.add('repairs3d');
	                newRepairs.classList.add('panel-info');
	                newRepairs.querySelector('.repairs_name').innerHTML = '3Д Ремонт №';
	                newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
	                newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
	                newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[3d][num][]');
	                newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[3d][id][]');
	                newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[3d][description][]');
	                newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[3d][which][]');
	                newRepairs.querySelector('.repairs_which').setAttribute('value','0');
	                newRepairs.querySelector('.repairCost').setAttribute('name','repairs[3d][cost][]');
	                newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);
	                break;
	            case 'jeweler':
	                repairsCount = repairsBlock.querySelectorAll('.repairsJew');
	                if ( repairsCount.length ) {
	                    lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
	                }

	                newRepairs.removeAttribute('id');
	                newRepairs.classList.remove('hidden');
	                newRepairs.classList.add('repairsJew');
	                newRepairs.classList.add('panel-success');
	                newRepairs.querySelector('.repairs_name').innerHTML = 'Ремонт Модельера-доработчика №';
	                newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
	                newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
	                newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[jew][num][]');
	                newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[jew][id][]');
	                newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[jew][description][]');
	                newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[jew][which][]');
	                newRepairs.querySelector('.repairs_which').setAttribute('value','1');
	                newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);

	                newRepairs.querySelector('.repairCost').setAttribute('name','repairs[jew][cost][]');
	                newRepairs.querySelector('.repairsPayment').classList.remove('hidden');

	                break;
	        }

	        repairsBlock.insertBefore(newRepairs, this);
	    });
	});

	function initPaidModal() {

	    $('#modalPaid').iziModal({
	        title: 'Пометить ремонт оплаченным?',
	        headerColor: '#56a66e',
	        icon: 'far fa-credit-card',
	        transitionIn: 'comingIn',
	        transitionOut: 'comingOut',
	        overlayClose: false,
	        closeButton: true,
	        afterRender: function () {
	            document.getElementById('modalPaidContent').classList.remove('hidden');
	        }
	    });

	    // начало открытия
	    $(document).on('opening', '#modalDelete', function () {

	    } );
	    // Начало закрытия
	    $(document).on('closing', '#modalDelete', function () {

	    });
	    // исчезло
	    $(document).on('closed', '#modalDelete', function () {

	    } );

	    //обработчики на кнопки
	    let buttons = document.getElementById('modalPaidContent').querySelectorAll('a');
	    let cancel = buttons[0];
	    let ok = buttons[1];
	    let paid = buttons[2];

	    cancel.classList.remove('hidden');
	    paid.classList.remove('hidden');

	    paid.addEventListener('click', function () {

	    	let data = paidRepair.data;
	        if ( data.paid !== 1 ) return;
	    	debug(data);

	        $.ajax({
	            type: 'POST',
	            url: '/add-edit/rp',//'controllers/repairsPayment.php',
	            data: data,
	            dataType:"json",
	            success:function(response) {
	                debug(response,'response');

	                if (response.error)
					{
						debug(response.error);
						return;
					}
					if (response.done !== true)
					{
						alert('Ошибка на стороне сервера! Попробуйте позже.');
	                    debug(response.done);
						return;
					}
	                let modal = $('#modalPaid');

	                modal.iziModal('setTitle', 'Ремонт отмечен оплаченным.');
	                modal.iziModal('setSubtitle', '');
	                modal.iziModal('setHeaderColor', '#66d246');
	                modal.iziModal('setIcon', 'glyphicon glyphicon-ok');

	                ok.onclick = function() {
	                    document.location.reload(true);
	                };
	                cancel.classList.add('hidden');
	                paid.classList.add('hidden');
	                ok.classList.remove('hidden');
	            },
	            error:function(e){
	            	debug(e,'Paid repair error');
	            }
	        });

	    } );

		debug('Paid modal init');
	}(initPaidModal());
}
function paidRepair(self) {
    event.preventDefault();
    event.stopPropagation();

	let repair = self.parentElement.parentElement.parentElement;
    let repairID = repair.querySelector('.repairs_id').value;
    let cost = repair.querySelector('.repairCost').value;
    let repairName = repair.querySelector('.repairs_name').innerHTML + repair.querySelector('.repairs_number').innerHTML + ' от ' + repair.querySelector('.repairs_date').innerHTML;
    let repairText = repair.querySelector('.repairs_descr').value;
    let repairCost = repair.querySelector('.repairCost').value;

    let data = {
    	paid: 1,
    	cost: cost,
        repairID: repairID,
    };
    let modal = $('#modalPaid');

    modal.iziModal('setSubtitle', 'Это действие будет невозможно отменить!');
    modal[0].querySelector('#modalPaidStatus').innerHTML = '<b>'+repairName + '</b><br>' + repairText + '<br><b>Стоимость: ' + repairCost + '</b>';
    $('#modalPaid').iziModal('open');

    paidRepair.data = data;
}
function removeRepairs(self) {
	let toDell = self.parentElement.parentElement;
    toDell.classList.remove('repairs');
    toDell.classList.add('hidden');
    toDell.querySelector('.repairs_descr').innerHTML = -1;
}
// ----- END РЕМОНТЫ -------//





//-----  удаление превьюшек  -------//
function dellImgPrew(self){

    let todell = self.parentElement.parentElement.parentElement.parentElement;
    todell.remove();
    uplF_count--;
}
//----- удаление с сервера картинок, стл, модели целиком -------//
function dell_fromServ( id, fileName, fileType, dellPos, element )
{
    let imageDOMElement;
    if (element)
    {
        imageDOMElement = element.parentElement.parentElement.parentElement;
    }
    let dellObj = {
        id: id || 0,
        fileName: fileName || '',
        fileType: fileType || '',
        dellPosition: dellPos || '',
        element: imageDOMElement || '',
		deleteFile: fileName ? 1 : '',
    };

    dellModal.modalDataInit(dellObj);

    $('#modalDelete').iziModal('open');

}
//----- END удаление с сервера картинок, стл, модели целиком -------//






//--------- отображаем превью при наведении ----------//
addPrevImg( document.querySelector('#topName'), 'top', 'right' );
addPrevImg( document.getElementById('dop_vc_table'), 'bottom', 'right' );
function addPrevImg( domEl, vert, horiz )
{
	if ( domEl == null ) return;

	let complects = domEl.querySelectorAll('a');

	let multMinTop = 10;
	let multMinLeft = 15;
	
	if ( vert === 'bottom' ) multMinTop = - 185;
	if ( horiz === 'right' ) multMinLeft = - 210;
	
	for ( let i = 0; i < complects.length; i++ ) {
		if ( !complects[i].hasAttribute('imgtoshow') ) continue;
		complects[i].addEventListener('mouseover',function(event){

			let mouseX = event.pageX;
			let mouseY = event.pageY;
			
			let hover = event.target;
			let imageBoxPrev = document.getElementById('imageBoxPrev');
				imageBoxPrev.style.top = 0 + 'px';
				imageBoxPrev.style.left = 0 + 'px';
			
			let src = hover.getAttribute('imgtoshow');
			
			imageBoxPrev.style.top = mouseY + multMinTop + 'px';
			imageBoxPrev.style.left = mouseX + multMinLeft + 'px';
			imageBoxPrev.setAttribute('src',src);
			imageBoxPrev.classList.remove('hidden');

		},false);
		
		complects[i].addEventListener('mouseout',function(event) {
			
			let imageBoxPrev = document.getElementById('imageBoxPrev');
			imageBoxPrev.classList.add('hidden');
			
		},false);

	}
}
//---------END отображаем превью при наведении ----------//