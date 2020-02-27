"use strict"
function Main() {
	
}

function searchIn(num) {
	if ( num === 1 || num === 2 ) {
		console.log(num);
		$.ajax({
			url: "../stan_admin/setSort.php", //путь к скрипту, который обрабатывает задачу
			type: 'POST',
			data: {//данные передаваемые в POST запросе
			   searchInNum: num,                   
			},
			success:function(data) {
				var searchInBtn = document.getElementById('searchInBtn').firstElementChild;
					searchInBtn.innerHTML = data;
			}
		})
	}
}
function collectionSelect(self) {
	
	var collection_block = document.getElementById('collection_block');
	if ( collection_block.getAttribute('class')=='visible' ) {
		collection_block.style.top = 20 + 'px';
		collection_block.classList.remove('visible');
		window.removeEventListener('click', hideCollBlock );
		return;
	} else {
		collection_block.classList.add('visible');
	}

	var a = getCoords(self);
	
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
function getCoords(elem) {
  // (1)
  var box = elem.getBoundingClientRect();

  var body = document.body;
  var docEl = document.documentElement;

  // (2)
  var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
  var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

  // (3)
  var clientTop = docEl.clientTop || body.clientTop || 0;
  var clientLeft = docEl.clientLeft || body.clientLeft || 0;

  // (4)
  var top = box.top + scrollTop - clientTop;
  var left = box.left + scrollLeft - clientLeft;

  return {
    top: top,
    left: left
  };
};
	
function sendPDF() {
	
	var collectionName = document.getElementById('collectionName');
	var c_name = collectionName.innerHTML;
	
	if ( c_name == 'Все Коллекции' ) { // уходим если выбраны все коллекции
		alert('Нужно выбрать какую нибудь коллекцию!');
		return;
	}
	ProgressBar(0);
	// --- запуск пдф скрипта --- //
	var blackCover = document.getElementById('blackCover');
		blackCover.classList.add('blackCover');
	var pdf_result = document.getElementById('pdf_result');
		pdf_result.classList.toggle('hidethis');
	var a = document.createElement('a');
		a.setAttribute('class','btn btn-danger');
		a.setAttribute('type','button');
		a.setAttribute('href','index_adm.php');
		a.style.marginBottom = '5px';
		a.innerHTML = 'Отмена';
	var center = document.createElement('center');
		center.appendChild(a);
	document.getElementById("pdf_result").appendChild(center);
	
	$.ajax({
		url: 'pdfExport_Controller.php',
		cache: false,
		success:function(data) {}
	});
	
}

function ProgressBar(persent,filename){
	if ( persent < 100 ) { //если задача не достигла 100% готовности, отправляем запрос на ее выполнение
		$.ajax({
			url: "progress_pdf.php", //путь к скрипту, который обрабатывает задачу
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
				//console.log('overalProgress = ',overalProgress);
				
				// рекурсивно вызываем этуже функцию, она будет выполняться пока не выполнит 100%
				setTimeout(function(){
					ProgressBar( parseInt(overalProgress), filename );
				},250);
			}
		})
	} else {//если задача выполненна на 100%, то выводим информацию об этом.
		console.log(filename);
		var back = document.createElement('a');
			back.setAttribute('class','btn btn-default');
			back.setAttribute('type','button');
			back.setAttribute('onclick','document.location.reload()' );
			back.style.marginBottom = '5px';
			back.style.marginRight = '8px';
			back.innerHTML = '<span class="glyphicon glyphicon-triangle-left"></span> Назад';
			
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
			download.setAttribute('href','../Pdfs/' + filename );
			download.style.marginBottom = '5px';
			download.innerHTML = '<span class="glyphicon glyphicon-save-file"></span> Загрузить';
			
		var center = document.getElementById("pdf_result").lastElementChild;
			center.lastElementChild.remove();
			center.appendChild(back);
			center.appendChild(openA);
			center.appendChild(download);
	}                           
}

function openPDF(filename){
	window.open( '../Pdfs/'+filename );
}