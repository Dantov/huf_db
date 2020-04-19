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

function getVCmenu( mType_name, targetEl ) {
	$.ajax({
		url: "controllers/num3dVC_controller.php", //путь к скрипту, который обрабатывает задачу
		type: "POST",
		data: { //данные передаваемые в POST запросе
		   modelType_quer: mType_name                    
		},
		dataType:"json",
		success:function(dataLi) {
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
		}
	}); // end ajax
}
// -----END  Обработчик кликов ----- //






//-----  Добавляем STL и Ai файлы  -------//
let stlSelect = document.getElementById('stlSelect');
let aiSelect = document.getElementById('aiSelect');

if ( aiSelect ) {
	aiSelect.addEventListener('click',selectFilesAi,false);
	let fileAi_input = document.getElementById('fileAi');
	fileAi_input.onchange = function() { // запускаем по событию change
		
			aiSelect.classList.toggle('hidden');
			let files = this.files;
			let numfiles = files.length;
			
			let spanWrapp = document.createElement('span');
				spanWrapp.setAttribute('id','spanWrappAi');
			
			for( let i = 0; i < numfiles; i++ ) {
				let size = ( (files[i].size / 1024) ).toFixed(2);
				let span = document.createElement('span');
					span.style.backgroundColor = '#fff';
					span.style.padding = '5px 8px 5px 8px';
					span.style.marginRight = '5px';
					span.style.borderRadius = '5px';
					span.innerHTML = files[i].name + ' - (' + size + 'кб)';
				spanWrapp.appendChild(span);
			}
			removeAi.parentElement.insertBefore(spanWrapp, removeAi);
			removeAi.classList.toggle('hidden');
		};
		
		let removeAi = document.getElementById('removeAi');
			removeAi.addEventListener('click', clearAi_Inpt, false);
			
		function clearAi_Inpt(){
			let spanWrapp = document.getElementById('spanWrappAi').remove();
			fileAi_input.value = null;
			this.classList.toggle('hidden');
			aiSelect.classList.toggle('hidden');
		}
		function selectFilesAi() {
			fileAi_input.click();
		}
}

if ( stlSelect ) {

	let fileSTL_input = document.getElementById('fileSTL');
    let removeStl = document.getElementById('removeStl');

	fileSTL_input.onchange = function() // запускаем по событию change
	{
		stlSelect.classList.toggle('hidden');
		let files = this.files;
		let numfiles = files.length;

		let spanWrapp = document.createElement('span');
			spanWrapp.setAttribute('id','spanWrappSTl');

		for( let i = 0; i < numfiles; i++ ) {
			let size = ( (files[i].size / 1024) / 1024 ).toFixed(2);
			let span = document.createElement('span');
				span.style.backgroundColor = '#fff';
				span.style.padding = '5px 8px 5px 8px';
				span.style.marginRight = '5px';
				span.style.borderRadius = '5px';
				span.innerHTML = files[i].name + ' - (' + size + 'mb)';
			spanWrapp.appendChild(span);
		}
		removeStl.parentElement.insertBefore(spanWrapp, removeStl);
		removeStl.classList.toggle('hidden');
	};

    stlSelect.addEventListener('click',selectFilesSTL,false);
	removeStl.addEventListener('click',clearStl_Inpt,false);
		
	function clearStl_Inpt()
	{
		let spanWrapp = document.getElementById('spanWrappSTl').remove();
		fileSTL_input.value = null;
		this.classList.toggle('hidden');
		stlSelect.classList.toggle('hidden');
	}
    function selectFilesSTL()
    {
        fileSTL_input.click();
    }

}
//----- END STL файлы -------//





/**
 *  Накидываем существующие
 */
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
	function collectionDropDown(button)
	{
	    let collectionBlockForm = document.getElementById('collectionBlockForm');
	    button.addEventListener('click',function (ev) {
	        ev.preventDefault();
	        ev.stopPropagation();

	        collectionBlockForm.classList.toggle('hidden');
	        collectionBlockForm.style.top = this.getBoundingClientRect().top - 170 + window.pageYOffset + 'px';

	        collectionDropDown.button = this;
	    },false);
	}
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
        inputs.forEach(function (elem) {
            if (elem.className === 'rowID') return;
            elem.setAttribute('value',-1);
        });
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
	            url: 'controllers/repairsPayment.php',
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
function dell_fromServ( id, imgname, isSTL, dellpos, element )
{

    let imgtoDell;
    if (element)
    {
        imgtoDell = element.parentElement.parentElement.parentElement;
    }
    let dellObj = {
        id: id || false,
        imgname: imgname || false,
        isSTL: isSTL || false, // если == 2 значит это Ai файл
        dellpos: dellpos || false,
        element: imgtoDell || false,
    };

    dellModal.modalDataInit(dellObj);

    $('#modalDelete').iziModal('open');

}
//----- END удаление с сервера картинок, стл, модели целиком -------//






//--------- отображаем превью при наведении ----------//
addPrevImg( document.getElementById('topName'), 'top', 'right' );
addPrevImg( document.getElementById('dop_vc_table'), 'bottom', 'right' );
function addPrevImg( domEl, vert, horiz )
{
	if ( domEl == null ) return;

	let complects = domEl.querySelectorAll('a');
	
	let multMinTop = 10;
	let multMinLeft = 15;
	
	if ( vert == 'bottom' ) multMinTop = - 185;
	if ( horiz == 'right' ) multMinLeft = - 210;
	
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







$.each(document.querySelectorAll('.submitButton'), function (index, button) {
    button.addEventListener('click',function (event) {
        event.preventDefault();
        event.stopPropagation();
        
        if ( validateForm() ) submitForm();
    }, false);
});
//-------- ВАЛИДАЦИЯ ФОРМЫ ---------//
function validateForm() {
	
	if ( document.getElementById('collections_table') )
	{
		let collectionInputs = document.getElementById('collections_table').querySelectorAll('input');
		if ( !collectionInputs.length ) {
			alert( 'Нужно внести хоть одну коллекцию!' );
			return false;
	    }
	    let collValid = true;
	    $.each(collectionInputs, function (index, input) {
	        if ( !input.value )
			{
	            input.scrollIntoView();
	            alert( 'Выберите коллекцию!' );
	            return collValid=false;
			}
	    });
	    if ( !collValid ) return false;
	}


    if ( document.getElementById('author') && !document.getElementById('author').value )
    {
        alert( 'Нужно указать Автора!' );
        return false;
    }
    if ( document.getElementById('modeller3d') && !document.getElementById('modeller3d').value )
    {
        alert( 'Нужно указать 3D-моделлера!' );
        return false;
    }
    if ( document.getElementById('modelType') && !document.getElementById('modelType').value )
    {
        alert( 'Нужно указать Тип модели!' );
        return false;
    }

    let modelWeight = document.getElementById('modelWeight');
    if ( modelWeight )
    {
    	if ( document.getElementById('modelWeight') && !document.getElementById('modelWeight').value )
	    {
	        alert( 'Нужно указать Вес модели!' );
	        return false;
	    }
	    if ( modelWeight.value < 0 || modelWeight.value > 1000 )
		{
	        alert( 'Вес модели указан не верно!' );
	        return false;
		}
    }

	if ( document.getElementById('metals_table') )
	{
		let materials = document.getElementById('metals_table').querySelectorAll('tr');
		let matsArr = [];
		$.each(materials, function (i, tr) {
	    	if ( !tr.classList.contains('hidden') ) matsArr.push(tr);
	    });

	    if ( !matsArr.length ) {
	        alert( 'Нужно внести хоть один материал!' );
	        return false;
	    }
	    if ( matsArr[0] )
		{
			let inputs = matsArr[0].querySelectorAll('input');
			let matsValid = false;
	        $.each(inputs, function (index, input) {
	            if ( input.value ) return matsValid=true;
	        });
	        if ( !matsValid ) {
	            matsArr[0].scrollIntoView();
	            alert( 'Заполните строку материала!' );
	        	return false;
	        }
		}
	}
	
	if ( document.getElementById('picts') )
	{
		let images = document.getElementById('picts').querySelectorAll('.image_row');
	    let imgFor = document.getElementById('imgFor');
	    if ( !images.length ) {
	        imgFor.innerHTML = 'Нужно внести хоть одну картинку!';
	        imgFor.classList.remove('hidden');
	        imgFor.scrollIntoView();
	        alert( 'Нужно внести хоть одну картинку!' );
	        return false;
	    } else {
	        imgFor.classList.add('hidden');
		}
	}
	

	return true;
}


//-------- ОТПРАВКА ФОРМЫ ---------//
function submitForm() {
	
	let addform = document.getElementById('addform');
	//if ( !addform.checkValidity() ) return;


	let addedit = 'controllers/addEdit_handler.php';
	let formData = new FormData(addform);
		formData.append('userName',userName);
		formData.append('tabID',tabName);

	if ( handlerFiles )
	{
		handlerFiles.getFiles().forEach(function (file) {
	        formData.append('UploadImages[]',file);
	    });
	}
    
    let modal = $('#modalResult');
    let modalButtonsBlock = document.getElementById('modalResult').querySelector('.modalButtonsBlock');
    let status = document.querySelector('#modalResultStatus');
    let back = modalButtonsBlock.querySelector('.modalProgressBack');
    let edit = modalButtonsBlock.querySelector('.modalResultEdit');
    let show = modalButtonsBlock.querySelector('.modalResultShow');

    $('#modalResult').iziModal('open');
	let xhr;

    xhr = $.ajax({
		url: addedit,
		type: 'POST',
		//dataType: "html", //формат данных
		//dataType: "json",
		//data: $("#addform").serialize(),
		data: formData,
		processData: false,
		contentType: false,
		beforeSend: function()
		{
            debug(xhr);

			modal.iziModal('setTitle', 'Идёт отправление данных на сервер.');
			modal.iziModal('setHeaderColor', '#858172');

			//xhr.abort();
			//if ( xhr.readyState === 0 ) debug('aborted');
		},
		success:function(resp)
		{
			resp = JSON.parse(resp);
			debug(resp);

			modal.iziModal('setIcon', 'glyphicon glyphicon-floppy-saved');
            modal.iziModal('setHeaderColor', '#edaa16');
            modal.iziModal('setTitle', 'Сохранение прошло успешно!');
            let title = '';
			if ( resp.isEdit == true )
			{
				title = 'Данные модели <b>'+ resp.number_3d + ' - ' + resp.model_type+'</b> изменены!';
            } else {
                title = 'Новая модель <b>'+ resp.number_3d + ' - ' + resp.model_type+'</b> добавлена!';
			}

			status.innerHTML = title;

			back.href = '../Main/index.php';
            show.href = '../ModelView/index.php?id=' + resp.id;
            let href = _URL_ + '/views/AddEdit/index.php?id=' + resp.id + '&component=2';

            edit.onclick = function() {
            	document.location.href = href;
            };

            back.classList.remove('hidden');
            edit.classList.remove('hidden');
            show.classList.remove('hidden');

		},
		error: function(error) { // Данные не отправлены
			modal.iziModal('setTitle', 'Ошибка отправки! Попробуйте снова.');
			modal.iziModal('setHeaderColor', '#95ffb1');

            edit.classList.remove('hidden');
            
			debug(error);
		}
	});
}
//-------- END ОТПРАВКА ФОРМЫ ---------//




//-------- Side buttons ---------//
let addEditSideButtons = [];
window.addEventListener('load',function(){
	addEditSideButtons = document.getElementById('AddEditSideButtons').querySelectorAll('button');
},false);

function pageUp()
{
	//debug('Текущая прокрутка сверху: ' + window.pageYOffset);
	window.scrollTo(0,0);
}

function pageDown()
{
    let scrollHeight = Math.max(
        document.body.scrollHeight, document.documentElement.scrollHeight,
        document.body.offsetHeight, document.documentElement.offsetHeight,
        document.body.clientHeight, document.documentElement.clientHeight
    );
	window.scrollTo(0,scrollHeight);
}

window.addEventListener('scroll',function () {

    let scrollHeight = Math.max(
        document.body.scrollHeight, document.documentElement.scrollHeight,
        document.body.offsetHeight, document.documentElement.offsetHeight,
        document.body.clientHeight, document.documentElement.clientHeight
    );
    let windowHeight = document.documentElement.clientHeight;

    addEditSideButtons = document.getElementById('AddEditSideButtons').querySelectorAll('button');

    //верхняя
	if ( window.pageYOffset === 0 ) addEditSideButtons[0].classList.add('hidden');
    if ( window.pageYOffset !== 0 ) addEditSideButtons[0].classList.remove('hidden');

    // нижняя
    if ( Math.round(window.pageYOffset + windowHeight) === scrollHeight ) addEditSideButtons[2].classList.add('hidden');
    if ( Math.round(window.pageYOffset + windowHeight) !== scrollHeight ) addEditSideButtons[2].classList.remove('hidden');

});
//-------- END Side buttons ---------//



//-------- Statuses buttons ---------//
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

let workingCenters = document.getElementById('workingCenters');
let statusesInputs = workingCenters.querySelectorAll('input');
let panelNeedle;
statusesInputs.forEach(input => {

    if ( input.hasAttribute('checked') )
    {
        panelNeedle = input.parentElement.parentElement.parentElement.parentElement;
        panelNeedle.classList.remove('panel-info');
        panelNeedle.classList.add('panel-warning');

        panelNeedle.querySelector('button').click();
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

//-------- END Statuses buttons ---------//









