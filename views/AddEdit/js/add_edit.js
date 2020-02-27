"use strict";

Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
	this.parentElement.removeChild(this);
	};

// ----- Обработчик кликов на контейнер 25,02,18 ----- //
document.querySelector('.container').addEventListener('click', function(event) {
		let click = event.target;
		if ( !click.hasAttribute('elemToAdd') ) return;
		if ( click.hasAttribute('VCTelem') ) {
			let target = click.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;

			//запускает код на поиск соответствий в колл. "Детали", и формирует меню Арт./Ном.3D справа
			getVCmenu(click.innerHTML, target);
		}

		// всиавим номер для инрута картинки
		if ( click.hasAttribute('data-imgFor') ) {
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

        //если выбираем коллекцию "зол накладки", отобразим Ai блок
		if ( click.hasAttribute('coll') )
		{
			let aIBlock = document.querySelector('.AIBlock');
			let aIBlockHR = document.querySelector('.AIBlockHR');
			if ( click.hasAttribute('aiblock') )
			{
				aIBlock.classList.remove('hidden');
				aIBlockHR.classList.remove('hidden');
				console.log('show ai block');
			} else {
				aIBlock.classList.add('hidden');
				aIBlockHR.classList.add('hidden');
				console.log('hide ai block');
			}

            //если выбираем коллекцию то в value попадет её ID

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
		success:function(dataLi) { //функция обратного вызова, выполняется в случае успешной отработки скрипта
			var num3dVC_new = document.getElementById('num3dVC_proto').cloneNode(true);
				num3dVC_new.removeAttribute('id');
				num3dVC_new.classList.remove('hidden');
				num3dVC_new.children[0].setAttribute('name','num3d_vc_[]');
			var ul = num3dVC_new.querySelector('.dropdown-menu');
			for( var i = 0; i < dataLi.length; i++ ){
				ul.innerHTML += dataLi[i];
			}
			addPrevImg( num3dVC_new, 'bottom', 'feft' ); // насаживаем события на показ картинки при наведении
			//addPrevToDopVC(num3dVC_new); // насаживаем события на показ картинки при наведении
			targetEl.children[0].remove();
			targetEl.appendChild(num3dVC_new);
			dataLi = null;
			//console.log('dataLi = ', typeof dataLi);
		}
	}); // end ajax
}
// -----END  Обработчик кликов ----- //

//----- Загрузка Превьюшек  -------//
var add_img = document.getElementById('add_img');
var picts = document.getElementById('picts');
var imgrowProto = document.querySelector('.image_row_proto');
if ( picts ) var uplF_count = picts.querySelectorAll('.image_row').length || 0;
if ( add_img ) add_img.addEventListener('click', function(){ 
	
	var add_bef_this = document.getElementById('add_bef_this');
	
	var newImgrow = imgrowProto.cloneNode(true);
	var uploadInput = newImgrow.children[0];
		uploadInput.click();
	
	//функции предпросмотра
	uploadInput.onchange = function() { // запускаем по событию change
        preview(this.files[0]);
    };
	function preview(file) {
		
		//вставляем новые	
			var reader = new FileReader();

			reader.addEventListener("load", function(event) {
				
				newImgrow.classList.remove('image_row_proto');
				newImgrow.classList.add('image_row');

				//var img_inputs = newImgrow.querySelector('.img_inputs');
				var inputVis = newImgrow.querySelector('.vis');
				var inputNotVis = newImgrow.querySelector('.notVis');
				//var _labels = img_inputs.getElementsByTagName('label');

				inputVis.setAttribute('value','Нет');
				inputNotVis.setAttribute('name', 'imgFor[]');
				inputNotVis.setAttribute('value', 0);

				/*
				for ( var ii = 0; ii < _inputs.length; ii++ ) {
					var id_inpt = _inputs[ii].getAttribute('id');
					var id_lab = _labels[ii].getAttribute('for');
					_inputs[ii].setAttribute('id',id_inpt + uplF_count);
					_inputs[ii].setAttribute('value', uplF_count);
					_labels[ii].setAttribute('for',id_lab + uplF_count);
				}
				*/
				var imgPrewiev = newImgrow.children[1].children[0].children[0].children[0];
				var srcPrew = event.target.result;
				imgPrewiev.setAttribute('src', srcPrew);

				// вставляем картинку только после всех изменений
				picts.insertBefore(newImgrow, add_bef_this);
				uplF_count++;
			});

		reader.readAsDataURL(file);

	}
	
});
//----- END Загрузка Превьюшек  -------//

//-----  удаление превьюшек  -------//
function dellImgPrew(self){
	
	let todell = self.parentElement.parentElement.parentElement.parentElement;
	todell.remove();
	uplF_count--;
}
//----- END удаление превьюшек  -------//

//----- удаление с сервера картинок, стл, модели целиком -------//
function dell_fromServ( id, imgname, isSTL, dellpos ) {
	
	id = id || false;
	imgname = imgname || false;
	isSTL = isSTL || false; // если == 2 значит это Ai файл
	dellpos = dellpos || false;
	
	var postObj = {
		id: id,
		imgname: imgname
	}
	if ( isSTL == 1 ) postObj.isSTL = 1;
	if ( isSTL == 2 ) postObj.isSTL = 2;
	if ( dellpos ) postObj.dellpos = 1;
	
	var body = document.querySelector('body');
	var blackCover2 = document.createElement('div');
		blackCover2.setAttribute('id','blackCover2');
		blackCover2.setAttribute('class','blackCover');
		
	var saved_form_result = document.getElementById('saved_form_result');
	
	var abortBtn = document.createElement('a');
		abortBtn.setAttribute('class','btn btn-default');
		abortBtn.setAttribute('type','button');
		abortBtn.innerHTML = 'Отменить';
		abortBtn.onclick = function () {
			var saved_form_result = document.querySelector('#saved_form_result');
			var blackCover2 = document.querySelector('#blackCover2');
			saved_form_result.classList.remove('hidethis');
			saved_form_result.innerHTML = '';
			blackCover2.remove();
		};
	
	var deleteBtn = document.createElement('a');
		deleteBtn.setAttribute('class','btn btn-danger');
		deleteBtn.setAttribute('type','button');
		deleteBtn.style.marginLeft = '20px';
		deleteBtn.innerHTML = 'Удалить';
		deleteBtn.onclick = function () {
			
			var result = document.querySelector('#saved_form_result');
				result.classList.remove('hidethis');
			
			var loading_img = document.createElement('img');
				loading_img.setAttribute('class','blackCover_loading');
				loading_img.setAttribute('src','../../picts/loading.gif');
				
			var blackCover2 = document.querySelector('#blackCover2');
				blackCover2.appendChild(loading_img); // добавляем гифку
				
			$.ajax({
				type: 'POST',
				url: 'controllers/delete.php', //путь к скрипту, который обрабатывает задачу
				data: postObj,
				dataType:"json",
				success:function(data) {  //функция обратного вызова, выполняется в случае успешной отработки скрипта
					
					var id = data.id;
					var imgname = data.imgname;
					var kartinka = data.kartinka;
					var dell = data.dell;
					
					var href = 'index.php?id='+id+'&component=2';
					if ( dell ) {
						href = '../Main/index.php';
						imgname = dell;
						kartinka = 'Модель ';
					}
					
					blackCover2.children[0].remove(); // удаляем гифку
					result.classList.add('hidethis');
					var a = document.createElement('a');
						a.setAttribute('class','btn btn-primary');
						a.setAttribute('type','button');
						a.setAttribute('href',href);
						a.innerHTML = 'ОК';
					var strong = document.createElement('strong');
						strong.innerHTML = imgname;
					var h4 	   = document.createElement('h4');
						h4.innerHTML = kartinka + strong.outerHTML + ' удалена!' + '<br />';
					var center = document.createElement('center');
						center.appendChild(h4);
						center.appendChild(a);

					result.innerHTML = '';
					result.appendChild(center);
					
				}
			});
		};
		
	var center = document.createElement('center');	
		center.appendChild(abortBtn);
		center.appendChild(deleteBtn);
	saved_form_result.innerHTML = '<center><h4>Удалить картинку - <b>' + imgname + '?</b></h4></center>';
	
	if ( isSTL == 1 ) {
		saved_form_result.innerHTML = '<center><h4>Удалить STL файл - <b>' + imgname + '?</b></h4></center>';
	}
	if ( isSTL == 2 ) {
		saved_form_result.innerHTML = '<center><h4>Удалить файл накладки - <b>' + imgname + '?</b></h4></center>';
	}
	if ( dellpos ) {
		var num3d = document.querySelector('#num3d').value;
		var vendor_code = document.querySelector('#vendor_code').value;
		var modelType = document.querySelector('#modelType').value;
		
		saved_form_result.innerHTML = '<center><h4>Удалить модель - <b>' + num3d + ' / ' + vendor_code + ' - ' + modelType + ' безвозвратно?</b></h4></center>';
	}
	saved_form_result.appendChild(center);
	saved_form_result.classList.add('hidethis');
	
	body.appendChild(blackCover2);
};
//----- END удаление с сервера картинок, стл, модели целиком -------//






//-----  Добавляем STL и Ai файлы  -------//
var stlSelect = document.getElementById('stlSelect');
var aiSelect = document.getElementById('aiSelect');


if ( aiSelect ) {
	aiSelect.addEventListener('click',selectFilesAi,false);
	var fileAi_input = document.getElementById('fileAi');
	fileAi_input.onchange = function() { // запускаем по событию change
		
			aiSelect.classList.toggle('hidden');
			var files = this.files;
			var numfiles = files.length;
			
			var spanWrapp = document.createElement('span');
				spanWrapp.setAttribute('id','spanWrappAi');
			
			for( var i = 0; i < numfiles; i++ ) {
				var size = ( (files[i].size / 1024) ).toFixed(2);
				var span = document.createElement('span');
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
		
		var removeAi = document.getElementById('removeAi');
			removeAi.addEventListener('click', clearAi_Inpt, false);
			
		function clearAi_Inpt(){
			var spanWrapp = document.getElementById('spanWrappAi').remove();
			fileAi_input.value = null;
			this.classList.toggle('hidden');
			aiSelect.classList.toggle('hidden');
		}
		function selectFilesAi() {
			fileAi_input.click();
		}
}


if ( stlSelect ) {

	var fileSTL_input = document.getElementById('fileSTL');
    var removeStl = document.getElementById('removeStl');

	fileSTL_input.onchange = function() // запускаем по событию change
	{
		stlSelect.classList.toggle('hidden');
		var files = this.files;
		var numfiles = files.length;

		var spanWrapp = document.createElement('span');
			spanWrapp.setAttribute('id','spanWrappSTl');

		for( var i = 0; i < numfiles; i++ ) {
			var size = ( (files[i].size / 1024) / 1024 ).toFixed(2);
			var span = document.createElement('span');
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
		var spanWrapp = document.getElementById('spanWrappSTl').remove();
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






//----- Добавляем WORD data -------//
var docxFile = document.getElementById('docxFile');
if ( docxFile ) {
	docxFile.addEventListener('click',selectDocx,false);

	var docxFileInpt = document.getElementById('docxFileInpt');
		docxFileInpt.onchange = function() { // запускаем по событию change
			//var submitDocxFile = document.getElementById('submitDocxFile');
				//submitDocxFile.click();
			$$f({
				formid:'docxFileForm',
				url: 'controllers/wordParser.php',
				onstart:function () {
					var progressStatus = document.getElementById('progressStatus');
						progressStatus.innerHTML = 'Сканирование документа MSWord...';
					//  var blackCover = document.getElementById('blackCover');
					//	blackCover.classList.add('blackCover');
					var saved_form_result = document.getElementById('saved_form_result');
						saved_form_result.classList.toggle('hidethis');
				},
				onsend:function () {  //действие по окончании загрузки файла
					//document.location.reload(true);
				},
				error: function() {
					alert ("Ошибка отправки! Попробуйте снова.");
				}
			});
		}
	function selectDocx(){
		docxFileInpt.click();
	}
}
//----- END WORD data -------//



// ----- КАМНИ Dop VC -------//
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
    }

	let counter = tBody.getElementsByTagName('tr').length;
	let newRow = document.getElementById(protoRow).cloneNode(true);
		newRow.style.display = "table-row";
		newRow.removeAttribute('id');
		newRow.children[0].innerHTML = ++counter;
		
	tBody.appendChild(newRow);
	if ( tBody.parentElement.classList.contains('hidden') ) tBody.parentElement.classList.remove('hidden');
}

function duplicateRow( self ) {
	let tBody = self.parentElement.parentElement.parentElement;
	let tocopy = self.parentElement.parentElement.cloneNode(true);
	tBody.insertBefore(tocopy, self.parentElement.parentElement.nextElementSibling);
	setNum(tBody);
}
function deleteRow( self )
{
    let tBody = self.parentElement.parentElement.parentElement;
	self.parentElement.parentElement.remove();
	setNum(tBody);

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

// для доп вставки элементов addElemMore
let gems_table = document.getElementById('gems_table');
if ( gems_table )
{
    gems_table.addEventListener('click', function(event) {

        if ( !event.target.hasAttribute('addElemMore') ) return;

        event.stopPropagation(); // прекращаем обработку других событий под этим кликом
        var click = event.target;
        var elemtoAdd = click.previousElementSibling.innerHTML;

        var inputToAddSomethig = click.parentElement.parentElement.parentElement.previousElementSibling;

        var inputToAddSomethig_PrevVal = inputToAddSomethig.getAttribute('value');
        var coma = '';
        if ( inputToAddSomethig_PrevVal ) coma = ', ';
        var newValue = inputToAddSomethig_PrevVal + coma + elemtoAdd;

        inputToAddSomethig.value = newValue;
        inputToAddSomethig.setAttribute('value', newValue );


    });
}


// ----- РЕМОНТЫ -------//
function addRepairs(self) {
	var lastRepNum = 0;
	
	var repairsBlock = document.getElementById('repairsBlock');
	var repairsCount = repairsBlock.querySelectorAll('.repairs');
	if ( repairsCount.length ) {
		
		lastRepNum = repairsCount[repairsCount.length-1].querySelector('.repairs_num').getAttribute('value');
	}
	
	var today = new Date();
	
	var newRepairs = document.getElementById('protoRepairs').cloneNode(true);
		newRepairs.removeAttribute('id');
		newRepairs.classList.remove('hidden');
		newRepairs.classList.add('repairs');
		newRepairs.querySelector('.repairs_number').innerHTML = +lastRepNum+1;
		newRepairs.querySelector('.repairs_num').setAttribute('value', +lastRepNum+1);
		newRepairs.querySelector('.repairs_num').setAttribute('name','repairs_num[]');
		newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs_descr[]');
		newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);
		
	repairsBlock.insertBefore(newRepairs, self.parentElement);

}
function removeRepairs(self) {
	var todell = self.parentElement.parentElement;
		todell.classList.remove('repairs');
		todell.classList.add('hidden');
		todell.querySelector('.repairs_descr').innerHTML = -1;
}
function formatDate(date) {
	var dd = date.getDate();
	if (dd < 10) dd = '0' + dd;

	var mm = date.getMonth() + 1;
	if (mm < 10) mm = '0' + mm;

	var yy = date.getFullYear();

	return dd + '.' + mm + '.' + yy;
}
// ----- END РЕМОНТЫ -------//

//--------- отображаем превью при наведении ----------//
addPrevImg( document.getElementById('topName'), 'top', 'right' );
addPrevImg( document.getElementById('dop_vc_table'), 'bottom', 'right' );

function addPrevImg( domEl, vert, horiz )
{
	if ( domEl == null ) return;

	var complects = domEl.querySelectorAll('a');
	
	var multMinTop = 10;
	var multMinLeft = 15;
	
	if ( vert == 'bottom' ) multMinTop = - 185;
	if ( horiz == 'right' ) multMinLeft = - 210;
	
	for ( var i = 0; i < complects.length; i++ ) {
		if ( !complects[i].hasAttribute('imgtoshow') ) continue;
		complects[i].addEventListener('mouseover',function(event){
			
			var mouseX = event.pageX;
			var mouseY = event.pageY;
			
			var hover = event.target;
			var imageBoxPrev = document.getElementById('imageBoxPrev');
				imageBoxPrev.style.top = 0 + 'px';
				imageBoxPrev.style.left = 0 + 'px';
			
			var src = hover.getAttribute('imgtoshow');
			
			imageBoxPrev.style.top = mouseY + multMinTop + 'px';
			imageBoxPrev.style.left = mouseX + multMinLeft + 'px';
			imageBoxPrev.setAttribute('src',src);
			imageBoxPrev.classList.remove('hidden');

		},false);
		
		complects[i].addEventListener('mouseout',function(event) {
			
			var imageBoxPrev = document.getElementById('imageBoxPrev');
			imageBoxPrev.classList.add('hidden');
			
		},false);

	}
}
//---------END отображаем превью при наведении ----------//

//-------- ОТПРАВКА ФОРМЫ ---------//
function submitForm() {
	
	var addform = document.getElementById('addform');
	if ( !addform.checkValidity() ) return;

	/*
	// перед отправкой проверка на покрытие и метериал
	var material = document.getElementById('material');
	var covering = document.getElementById('covering');
	var inputsMaterial = material.getElementsByTagName('input');
	var inputCovering = covering.getElementsByTagName('input');

	if ( inputsMaterial[0].checked ) {
		for ( var i = 1; i < inputsMaterial.length; i++ ) {
			if ( inputsMaterial[i].checked ) {
				inputsMaterial[i].checked = false;
			}
		}
	}
	if ( inputCovering[0].checked && inputCovering[3].checked ) {

		inputCovering[5].checked = false;
		inputCovering[6].checked = false;
		inputCovering[7].setAttribute('value',"");
	}
	if ( !inputCovering[0].checked  ) {
		for ( var i = 3; i < inputCovering.length; i++ ) {
			
			if ( inputCovering[i].checked ) {
				inputCovering[i].checked = false;
			}
		}
		inputCovering[7].setAttribute('value',"");
	}
	*/

	var addedit = 'controllers/addEdit_handler.php';
        var formData = new FormData(addform);
        
        var result = document.getElementById('saved_form_result');
        var progressBar = result.querySelector('#progress-bar');
        var progressStatus = result.querySelector('#progressStatus');
        
        $.ajax({
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
                progressStatus.innerHTML = 'Отправляю данные...';
                
                var blackCover = document.getElementById('blackCover');
                    blackCover.classList.add('blackCover');
                var saved_form_result = document.getElementById('saved_form_result');
                    saved_form_result.classList.toggle('hidethis');
            },
            success:function(resp) 
            {
                resp = JSON.parse(resp);
                debug(resp);
                progressBar.style.width = 100 + '%';
				progressBar.innerHTML = 100 + '%';
                
                var strong =  document.createElement('strong');
                    strong.innerHTML = resp.number_3d + ' - ' + resp.model_type;
			
                var h4 = document.createElement('h4');
                        h4.innerHTML = resp.lastMess + ": ";
                        h4.appendChild(strong);

                var a = document.createElement('a');
                        a.setAttribute('class','btn btn-primary');
                        a.setAttribute('type','button');
                        a.setAttribute('href','../ModelView/index.php?id=' + resp.id);
                        a.style.marginLeft = '20px';
                        a.innerHTML = 'Просмотр';

                var a2 = document.createElement('a');
                        a2.setAttribute('class','btn btn-default');
                        a2.setAttribute('type','button');
                        a2.setAttribute('href','index.php?id=' + resp.id + '&component=2');
                        a2.style.marginLeft = '20px';
                        a2.innerHTML = 'Редактировать';

                var a3 = document.createElement('a');
                        a3.setAttribute('class','btn btn-success');
                        a3.setAttribute('type','button');
                        a3.setAttribute('href','../Main/index.php');
                        a3.innerHTML = 'В Базу';

                var center = document.createElement('center');
                        center.appendChild(a3);
                        center.appendChild(a2);
                        center.appendChild(a);

                progressStatus.innerHTML = 'Сохранение прошло успешно!';

                result.appendChild(h4);
                result.appendChild(center);
            },
            error: function(error) { // Данные не отправлены
                progressStatus.innerHTML = 'Ошибка отправки! Попробуйте снова.';
                debug(error);
            }
        });

}
//-------- END ОТПРАВКА ФОРМЫ ---------//


//-------- Side buttons ---------//
let addEditSideButtons = [];
window.onload = function f()
{
    addEditSideButtons = document.getElementById('AddEditSideButtons').querySelectorAll('button');
};

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









