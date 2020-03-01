"use strict"
cutLongNames( document.querySelector('.table_gems'), 12 );
cutLongNames( document.querySelector('.table_vc_links'), 12 );
function cutLongNames( table, numCut ) {
		
	var td_arr = table.getElementsByTagName('td');
	
	for ( var i = 0; i < td_arr.length; i++ ) {
		
		if ( table.classList.contains('table_vc_links') ) {
			if ( td_arr[i].firstElementChild ) continue;
		}
		
		var str = td_arr[i].innerHTML;
		if ( str.length > numCut ) {
			td_arr[i].setAttribute('realstr',str);
			td_arr[i].setAttribute('class','cutedTD');
			
			str = str.slice(0,numCut) + "...";
			
			td_arr[i].innerHTML = str;
			td_arr[i].addEventListener('mouseover',function(){
				this.setAttribute('cutstr',this.innerHTML);
				var coords = getCoords(this);
				var longTD = document.querySelector('#longTD');
					longTD.innerHTML = this.getAttribute('realstr');
					longTD.classList.remove('hidden');
					longTD.style.top = coords.top+'px';
					longTD.style.left = coords.left+'px';
					longTD.addEventListener('mouseout',function(){
						this.classList.add('hidden');
					},false);
					
			}, false);
		}
	}
};
//--------- прячем таблицы если в них нет данных ----------//
function hideTables( table ) {
	if ( !table.children[1].children[0] ) {
		table.style.display = "none";
		table.previousElementSibling.style.display = "none";
	}
}
hideTables( document.querySelector('.table_gems') );
hideTables( document.querySelector('.table_vc_links') );

//--------- Рисуем бордер слева или справа ----------//
var descr = document.getElementById('descr');
var images_block = document.getElementById('images_block');

var descrH = descr.offsetHeight;
var images_blockH = images_block.offsetHeight;

if ( descrH >= images_blockH ) {
	descr.classList.add('bordersMiddleLeft');
	images_block.classList.remove('bordersMiddleRight');
}
if ( descrH <= images_blockH ) {
	descr.classList.remove('bordersMiddleLeft');
	images_block.classList.add('bordersMiddleRight');
}



function statusChange() {
	var status_blackCover = document.getElementById('status_blackCover');
	var status_cover = document.getElementById('status_cover');
	var status_window = document.getElementById('status_window');
	var status_description = document.getElementById('status_description');
	
	status_blackCover.classList.toggle('blackCover');
	status_cover.classList.toggle('hidden');
	
	if ( status_description.children[0].value ) { // скрываем описание если в нем пусто
		status_description.classList.remove('hidden');
	}
	
	if ( status_window ) {
		status_window.addEventListener('click',function(event){
			
			if ( event.target.id == 'onRepaire' ) {
				status_description.classList.remove('hidden');
			}
			if ( event.target.id == 'wipM' || event.target.id == 'signalDone' ) {
				if ( status_description.children[0].value ) return;
				status_description.classList.add('hidden');
			}
	
		}, false);
	}
	
};
function close_status_window(rel){
	var status_blackCover = document.getElementById('status_blackCover');
	var status_cover = document.getElementById('status_cover');
	
	status_blackCover.classList.toggle('blackCover');
	status_cover.classList.toggle('hidden');
	
	if (rel) { // если нажала на ок перегрузим страницу, если на отмену то нет
		document.location.reload(true); // true говорит что б загружал с сервера а не с кеша
	}
};

//--------- отображаем превью при наведении ----------//

addPrevImg( document.getElementById('complects') );
addPrevImg( document.querySelector('.table_vc_links') );
function addPrevImg(domEl) {
	var complects = domEl.querySelectorAll('a');
	
	for ( var i = 0; i < complects.length; i++ ) {
	
		complects[i].addEventListener('mouseover',function(event){
			
			var mouseX = event.pageX;
			var mouseY = event.pageY;
			
			var hover = event.target;
			var imageBoxPrev = document.getElementById('imageBoxPrev');
				imageBoxPrev.style.top = 0 + 'px';
				imageBoxPrev.style.left = 0 + 'px';
			
			var src = hover.getAttribute('imgtoshow');
			
			imageBoxPrev.style.top = mouseY + 15 + 'px';
			imageBoxPrev.style.left = mouseX - 208 + 'px';
			imageBoxPrev.setAttribute('src',src);
			imageBoxPrev.classList.remove('hidden');
			
			/*
			var imgRect = imageBoxPrev.getBoundingClientRect();
			var docRect = document.querySelector('#content').getBoundingClientRect();
			
			if ( imgRect.right > docRect.right ) {
				var tt = docRect.right-208 + 'px'
				imageBoxPrev.style.left = mouseX - 208 + 'px';
			}
			*/
			
			
		},false);
		
		complects[i].addEventListener('mouseout',function(event) {
			
			var imageBoxPrev = document.getElementById('imageBoxPrev');
			imageBoxPrev.classList.add('hidden');
			
		},false);

	}
}

//---------  ----------//
function getPDF(id,doc) {
	
	if ( doc == 'runner' ) var urll = 'controllers/runner_pdf.php?id='+id;
	if ( doc == 'passport' ) var urll = 'controllers/passport_pdf.php?id='+id;
	
	ProgressBar(0,id);
	// --- запуск пдф скрипта --- //
	var blackCover2 = document.getElementById('blackCover2');
		blackCover2.classList.add('blackCover');
	var pdf_result = document.getElementById('pdf_result');
		pdf_result.classList.toggle('hidethis');
	var a = document.createElement('a');
		a.setAttribute('class','btn btn-danger');
		a.setAttribute('type','button');
		a.setAttribute('href','index.php?id='+id);
		a.style.marginBottom = '5px';
		a.innerHTML = 'Отмена';
	var center = document.createElement('center');
		center.appendChild(a);
	document.getElementById("pdf_result").appendChild(center);
	
	$.ajax({
		url: urll,
		cache: false,
		success:function(data) {}
	});
	
}

function ProgressBar(persent,id,filename){
	//console.log(filename);
	if ( persent < 100 ) { //если задача не достигла 100% готовности, отправляем запрос на ее выполнение
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
					
				var progressBar = document.getElementById("progress-bar");
					progressBar.style.width = overalProgress + "%";
					progressBar.innerHTML = overalProgress + "%"; 
					
				
				//console.log(filename);
				
				// рекурсивно вызываем этуже функцию, она будет выполняться пока не выполнит 100%
				setTimeout(function(){
					ProgressBar( parseInt(overalProgress),id,filename );
				},250);
			}
		})
	} else {//если задача выполненна на 100%, то выводим информацию об этом.
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
			}
			
		var openA = document.createElement('a');
			openA.setAttribute('class','btn btn-success');
			openA.setAttribute('type','button');
			openA.setAttribute('onclick','openPDF("' + filename + '");');
			openA.style.marginBottom = '5px';
			openA.style.marginRight = '8px';
			openA.innerHTML = '<span class="glyphicon glyphicon-open-file"></span> Открыть';
			
		var download = document.createElement('a');
			download.setAttribute('class','btn btn-info');
			download.setAttribute('type','button');
			download.setAttribute('download','');
			download.setAttribute('href','../../Pdfs/' + filename );
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
}

function openPDF(filename){
	window.open( '../../Pdfs/'+filename );
}

// ========== LIKES ========== //
var btnlikes = document.querySelectorAll('.btnlikes');

if ( btnlikes[0] ) {
	btnlikes[0].addEventListener('click', likesDislikes, false);
	btnlikes[1].addEventListener('click', likesDislikes, false);
}

function likesDislikes() {
	var id = document.querySelector('.container').getAttribute('id').split('_');

	var obj = {
		likeDisl: 0,
		id: id[1]
	};
	var nums;

	if ( this.classList.contains("likeBtn") ) {
		obj.likeDisl = 1; // облайкали
		nums = document.getElementById("numLikes");
	} else if ( this.classList.contains("disLikeBtn") ) {
		obj.likeDisl = 2; //обдислайкали
		nums = document.getElementById("numDisLikes");
	}
	var that = this;
	$.ajax({
		type: 'POST',
		url: "controllers/likesController.php",
		data: obj,
		dataType:"json",
		success:function(data) {

			nums.innerHTML = data.done;

			btnlikes[0].removeEventListener('click', likesDislikes, false);
			btnlikes[1].removeEventListener('click', likesDislikes, false);

			btnlikes[0].classList.remove('btnlikes');
			btnlikes[0].classList.add('btnlikesoff');
			btnlikes[1].classList.remove('btnlikes');
			btnlikes[1].classList.add('btnlikesoff');

		}
	});

}