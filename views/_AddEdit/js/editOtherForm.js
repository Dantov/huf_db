"use strict"

Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
	this.parentElement.removeChild(this);
	};

// ----- Обработчик кликов на контейнер 25,02,18 ----- //
var container = document.querySelector('.container');
	container.addEventListener('click', function(event) {
		
	if ( !event.target.hasAttribute('elemToAdd') ) return;
	var click = event.target;
	
	var inputToAddSomethig = click.parentElement.parentElement.parentElement.previousElementSibling;
	var inputToAddSomethig_PrevVal = inputToAddSomethig.getAttribute('value');
	
	inputToAddSomethig.value = click.innerHTML;
	inputToAddSomethig.setAttribute('value', click.innerHTML );
	
	// для вставки швенз
	var tableVC_Id = click.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.getAttribute('id') || 'нет';
	
	if ( tableVC_Id == 'dop_vc_table' ) { //если таблица == доп. арт. то штопаемся со швензами
		var target = click.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
		if ( inputToAddSomethig_PrevVal == 'Швенза' ) {
			var cInpt = document.createElement('input');
			cInpt.setAttribute('type','text');
			cInpt.setAttribute('class','form-control');
			cInpt.setAttribute('name','num3d_vc_[]');
			target.innerHTML = '';
			target.appendChild(cInpt);
			return;
		}
		if ( click.innerHTML == 'Швенза' ) {
		
			function addSchwenzeInpt() {
				var new_schwenze = document.getElementById('proto_schwenze').cloneNode(true);
				new_schwenze.removeAttribute('id');
				new_schwenze.classList.toggle('hidden');
				return	new_schwenze;
			};
		
			target.children[0].remove();
			target.appendChild(addSchwenzeInpt());
			return;
		}
	}
	
});
// -----END  Обработчик кликов ----- //

//--------- отображаем превью при наведении ----------//
var topName = document.getElementById('topName').querySelectorAll('a');
var mouseX,mouseY;

for ( var i = 0; i < topName.length; i++ ) {
	
	topName[i].addEventListener('mouseover',function(event){

		var hover = event.target;
		var imageBoxPrev = document.getElementById('imageBoxPrev');
		
		var src = hover.getAttribute('imgtoshow');
		
		imageBoxPrev.setAttribute('src',src);
		imageBoxPrev.classList.remove('hidden');
		
	},false);
	
	topName[i].addEventListener('mouseout',function(event) {
		
		var imageBoxPrev = document.getElementById('imageBoxPrev');
		imageBoxPrev.classList.add('hidden');
		
	},false);
	
	topName[i].addEventListener('mousemove',function(event){
		mouseX = event.clientX;
		mouseY = event.clientY;
		//console.log('mouseX = ',mouseX,'; mouseY = ',mouseY);
		
		//var imageBoxPrev = document.getElementById('imageBoxPrev');
		
		imageBoxPrev.style.top = mouseY + 5 + 'px';
		imageBoxPrev.style.left = mouseX + 5 + 'px';
		
		var dfdfdf = imageBoxPrev.getBoundingClientRect();
		var wWidth = document.documentElement.clientWidth;
		
		if ( dfdfdf.right > wWidth ) {
			//console.log(dfdfdf.right);
			var tt = (dfdfdf.right - wWidth)+20;
			imageBoxPrev.style.left = mouseX + 5 - tt + 'px';
		}
		
	},false);
}
//---------END отображаем превью при наведении ----------//

// ----- ДОП АРТИКУЛЫ -------//
var dop_vc_rows_counter = document.getElementById('dop_vc_table');
if ( dop_vc_rows_counter ) dop_vc_rows_counter = dop_vc_rows_counter.querySelectorAll('.dop_vc_row').length;

function addLinksRow(self) {
	var dop_vc_table = document.getElementById('dop_vc_table');
	var newRow = document.getElementById('protoArticlRow').cloneNode(true);
	newRow.style.display = "table-row";
	newRow.removeAttribute('id');
	newRow.setAttribute('class','dop_vc_row');
	
	dop_vc_rows_counter++;
	newRow.children[0].innerHTML = dop_vc_rows_counter;
	dop_vc_table.insertBefore(newRow, dop_vc_table.lastElementChild);
};

// для удаления доп. артикулов
function deleteLinksRow(self) {

	self.parentElement.parentElement.remove();
	
	setNum();
	--dop_vc_rows_counter;
};

// для дублирования строк допов
function duplicateLinksRow(self) {
	
	var tocopy = self.parentElement.parentElement.cloneNode(true);
	self.parentElement.parentElement.after(tocopy); //вставляет после себя
	
	setNum();
	dop_vc_rows_counter++;
};
function setNum() {
	var tBody = document.getElementById('dop_vc_table');
	var dop_vc_row = tBody.querySelectorAll('.dop_vc_row');
	
	for ( var i = 0; i < dop_vc_row.length; i++ ) {
		var a = i;
		dop_vc_row[i].children[0].innerHTML = a+1;	
	}
}
// -----END ДОП АРТИКУЛЫ -------//

//-------- ОТПРАВКА ФОРМЫ ---------//
$(function(){
  $('#addform').on('submit', function(e){
	
	// перед отправкой проверка на покрытие и метериал
	var material = document.getElementById('material');
	var covering = document.getElementById('covering');
	if ( material ) {
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
	}
	
	var progressStatus = document.getElementById('progressStatus');
		progressStatus.innerHTML = 'Отправляю данные...';
	var blackCover = document.getElementById('blackCover');
		blackCover.classList.add('blackCover');
	var saved_form_result = document.getElementById('saved_form_result');
		saved_form_result.classList.toggle('hidethis');
	
    e.preventDefault();
    var $that = $(this);
	var fData = $that.serialize(); // сериализируем данные
	var thisId = document.getElementById('thisId').value;
    setTimeout(function(){
		 $.ajax({
		  url: 'editOtherForm_Controller.php', // путь к обработчику берем из атрибута action
		  type: $that.attr('method'), // метод передачи - берем из атрибута method
		  data: {form_data: fData},
		  dataType: 'json',
		  success: function(data){
			var progressBar = document.getElementById('progress-bar');
				progressBar.style.width = '100%';
				progressBar.innerHTML = '100%';
				
			var progressStatus = document.getElementById('progressStatus');
				progressStatus.innerHTML = data.done;
			
			var a = document.createElement('a');
				a.setAttribute('class','btn btn-primary');
				a.setAttribute('type','button');
				a.setAttribute('href','show_pos_adm.php?id=' + thisId );
				a.style.marginLeft = '20px';
				a.innerHTML = 'Просмотр';
			
			var a2 = document.createElement('a');
				a2.setAttribute('class','btn btn-default');
				a2.setAttribute('type','button');
				a2.setAttribute('href','editOtherForm.php?id=' + thisId );
				a2.style.marginLeft = '20px';
				a2.innerHTML = 'Редактировать';
			
			var a3 = document.createElement('a');
				a3.setAttribute('class','btn btn-success');
				a3.setAttribute('type','button');
				a3.setAttribute('href','index_adm.php');
				a3.innerHTML = 'В Базу';
			
			var center = document.createElement('center');
				center.appendChild(a3);
				center.appendChild(a2);
				center.appendChild(a);
				
			var result = document.getElementById('saved_form_result');
				result.appendChild(center);
			
			console.log(data);
		  }
		});
	}, 500);
   
  });
  
});

/*
function submitForm(self) {
	
	try {
		var addform = document.getElementById('addform');
		var fData = addform.serialize(); // сериализируем данные
		//var size_range = document.getElementById('size_range');
		//var print_cost = document.getElementById('print_cost');
		
		$.ajax({
				url: "editOtherForm_Controller.php", //путь к скрипту, который обрабатывает задачу
				type: "POST",
				data: { //данные передаваемые в POST запросе
					form_data: fData
				},
				dataType:"json",
				success:function(data) {  //функция обратного вызова, выполняется в случае успехной отработки скрипта
					
					var stat = data.status;
					var overalProgress = data.overalProgress || 0;
					
					var progressStatus = document.getElementById("progressStatus");
						progressStatus.innerHTML = stat;
						
					var progressBar = document.getElementById("progress-bar");
						progressBar.style.width = overalProgress + "%";
						progressBar.innerHTML = overalProgress + "%"; 
					
					
					console.log(data);

				}
		});
	} catch (e) {
		console.log('Ошибка ' + e.name + ":" + e.message + "\n" + e.stack);
	}
	
	
	$$f({
        formid:'addform',
        url: addedit,
        onstart:function () { 
			var progressStatus = document.getElementById('progressStatus');
				progressStatus.innerHTML = 'Отправляю данные...';
			var blackCover = document.getElementById('blackCover');
				blackCover.classList.add('blackCover');
			var saved_form_result = document.getElementById('saved_form_result');
				saved_form_result.classList.toggle('hidethis');
        },
		onsend:function () {  //действие по окончании загрузки файла, похоже на то что это onsuccess
        },
		error: function() {
			var progressStatus = document.getElementById('progressStatus');
				progressStatus.innerHTML = 'Ошибка отправки! Попробуйте снова.';
        }
    });
	
};
*/
function hidemodal() {
	
	var fromSaveH4 = document.getElementById('fromSaveH4');
	if (fromSaveH4) fromSaveH4.remove();
		
	var fromSaveCanter = document.getElementById('fromSaveCanter');
	if (fromSaveCanter) fromSaveCanter.remove();
	
	var progressStatus = document.getElementById('progressStatus');
		progressStatus.innerHTML = '';
	var progressBar = document.getElementById('progress-bar');
		progressBar.innerHTML = 0;
		progressBar.style.width = 0 + '%';
		
	var saved_form_result = document.getElementById('saved_form_result');
		saved_form_result.classList.toggle('hidethis');
		
	var blackCover = document.getElementById('blackCover');
		blackCover.classList.remove('blackCover');
		blackCover.children[0].classList.add('hidden');
}
//-------- END ОТПРАВКА ФОРМЫ ---------//









