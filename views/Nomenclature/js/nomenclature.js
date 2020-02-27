"use strict";

Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
	this.parentElement.removeChild(this);
};

document.getElementById('navnav').children[1].setAttribute('class','active');

var container = document.getElementById('container');

function addRow(self){
	var tbody = self.parentElement.parentElement.parentElement;
	var trPlus = self.parentElement.parentElement;
	var len = tbody.querySelectorAll('.collsRow').length;
	var coll = tbody.getAttribute('data-coll');
	
	var newRow = document.querySelector('.protoRow').cloneNode(true);
		newRow.classList.remove('protoRow');
		newRow.classList.add('collsRow');
		newRow.children[1].children[0].setAttribute('data-coll',coll);
		newRow.children[0].innerHTML = ++len;
		
	var elem = tbody.insertBefore(newRow, trPlus);
		elem.children[1].children[0].addEventListener('change', changeInpt, false);
}
applyEvents();
function applyEvents() {
	var inputs = container.querySelectorAll('.collsRow input');
	var a = container.querySelectorAll('.collsRow a');
	
	for( var i = 0; i < inputs.length; i++ ) {
		inputs[i].addEventListener('change', changeInpt, false);
		a[i].addEventListener('click', dellRow, false);
	}
}
function changeInpt() {
	
	var coll = this.getAttribute('data-coll');
	var id = this.getAttribute('data-id');
	var val = this.value;
	var that = this;
	
	console.log('coll=',coll,' val=',val,' id=',id);
	
	var obj = {
		coll : coll,
		id : id,
		val : val
	}
	
	$.ajax({
		url: "controllers/nom_handler.php", //путь к скрипту, который обрабатывает задачу
		type: 'POST',
		data: obj,
		dataType:"json",
		success:function(data) {  //функция обратного вызова, выполняется в случае успешной отработки скрипта
		
			var target = that.parentElement.previousElementSibling;
			if (data.status === 1) {	
				var span = document.createElement('span');
					span.setAttribute('class','glyphicon glyphicon-ok pull-right');
					span.setAttribute('title','Сохранено.');
				var elem = target.appendChild(span);
				setTimeout(function(){
					elem.remove();
				}, 2000);
				if ( data.add === 1 ) {
					var span = document.createElement('span');
						span.setAttribute('class','glyphicon glyphicon-trash');
						span.setAttribute('aria-hidden','true');
					var a = document.createElement('a');
						a.setAttribute('class','btn btn-sm btn-default');
						a.setAttribute('type','button');
						a.setAttribute('role','button');
						a.appendChild(span);
						a.addEventListener('click', dellRow, false);
					that.setAttribute('data-id',data.id);
					target = that.parentElement.nextElementSibling.nextElementSibling;
					that.parentElement.nextElementSibling.innerHTML = data.date;
					target.appendChild(a);
				}
			} else if (data.status === -1){
				console.log('data.coll=',data.coll);
				var objNames = {
					collections : 'Коллекция - "'+val+'" уже есть!',
					gems_names : 'Сырьё - "'+val+'" уже есть!',
					gems_cut : 'Огранка - "'+val+'" уже есть!',
					gems_color : 'Цвет - "'+val+'" уже есть!',
					gems_sizes : 'Размер - "'+val+'" уже есть!',
					author : 'Автор - "'+val+'" уже есть!',
					modeller3d : 'Модельер - "'+val+'" уже есть!',
					model_type : 'Тип модели - "'+val+'" уже есть!',
					vc_names : 'Доп. Артикул - "'+val+'" уже есть!'
				}
				var strAlert = '';
				for (var key in objNames) {
					if ( coll == key ) {
						strAlert = objNames[key];
						break;
					}
				}
				alert(strAlert);
			} else if (data.status === 0){
				alert('ошибка при внесении данных. Попробуйте снова.' );
			}
		}
	})
}
function dellRow(){
	//var table = this.parentElement.parentElement.getAtribute('data-coll');
	var input = this.parentElement.previousElementSibling.previousElementSibling.children[0];
	var coll = input.getAttribute('data-coll');
	var id = input.getAttribute('data-id');
	var val = input.value;
	var that = this;
	var str = '';

	console.log('coll=',coll,' val=',val,' id=',id);
	var objNames = {
		collections : 'Коллекция "' + val + '" удалена!;Удалить коллекцию - "' + val + '" ? Все модели принадлежащие к ней будут помечены тирэ.',
		gems_names : 'Сырьё "' +val+ '" удален!;Удалить сырьё - "' + val + '" ?',
		gems_cut : 'Огранка "' +val+ '" удалена!;Удалить огранку - "' + val + '" ?',
		gems_color : 'Цвет "' +val+ '" удален!;Удалить цвет - "' + val + '" ?',
		gems_sizes : 'Размер "' +val+ '" удален!;Удалить размер - "' + val + '" ?',
		author : 'Автор "' +val+ '" удален!;Удалить автора - "' + val + '" ?',
		modeller3d : 'Модельер "' +val+ '" удален!;Удалить модельера - "' + val + '" ?',
		model_type : 'Тип модели "' +val+ '" удален!;Удалить тип модели - "' + val + '" ?',
		vc_names : 'Доп. Артикул "' +val+ '" удален!;Удалить доп. Артикул - "' + val + '" ?'
	}

	var strAlert = '';
	for (var key in objNames) {
		if ( coll == key ) {
			strAlert = objNames[key];
			break;
		}
	}
	var mass = strAlert.split(';');
	var conf_str = mass[1];
	strAlert = mass[0];

	var objReqest = {
		coll : coll,
		id : id,
		val : val,
		dell : 1
	}
	var conf = confirm(conf_str);
	if ( conf ) {
		$.ajax({
			url: "controllers/nom_handler.php", //путь к скрипту, который обрабатывает задачу
			type: 'POST',
			data: objReqest,
			dataType:"json",
			success:function(data) {  //функция обратного вызова, выполняется в случае успешной отработки скрипта

				var target = that.parentElement.parentElement;
				if (data.dell === 1) {

					if ( data.count ) str = data.count + ' моделей остались без коллекции';
					target.remove();

					alert(strAlert + str );

				}
			}
		})
	}

}