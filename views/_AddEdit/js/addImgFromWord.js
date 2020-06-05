"use strict"

Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
	this.parentElement.removeChild(this);
	};

var picts = document.getElementById('picts');
var imgrowProto = document.querySelector('.image_row_proto');
var uplF_count = picts.querySelectorAll('.image_row').length || 0;
var add_bef_this = document.getElementById('add_bef_this');
//добавляем картинки

var fromWordDiv = document.getElementById('imgFromWord');

if ( fromWordDiv ) {
	
	var pathToimg = document.getElementById('pathToimg').getAttribute('value');
	var spansFromWord = fromWordDiv.getElementsByTagName('span');
	var imgL = spansFromWord.length;
	
	for ( var i = 0; i < imgL; i++ ) {
		
		var newImgrow = imgrowProto.cloneNode(true);
		newImgrow.classList.remove('image_row_proto');
		newImgrow.classList.add('image_row');
			
		var uploadInput = newImgrow.lastElementChild; // инпут с пуьем к ворд файлу
			uploadInput.setAttribute('value', '../../docx_temp/' + pathToimg + '/' + spansFromWord[i].innerHTML);

		var inputVis = newImgrow.querySelector('.vis');
		var inputNotVis = newImgrow.querySelector('.notVis');

		inputVis.setAttribute('value','Нет');
		inputNotVis.setAttribute('name', 'imgFor[]');
		inputNotVis.setAttribute('value', 0);

		/*
		var img_inputs = newImgrow.querySelector('.img_inputs');
		var _inputs = img_inputs.getElementsByTagName('input');
		var _labels = img_inputs.getElementsByTagName('label');

		for ( var ii = 0; ii < _inputs.length; ii++ ) {
			var id_inpt = _inputs[ii].getAttribute('id');
			var id_lab = _labels[ii].getAttribute('for');
			_inputs[ii].setAttribute('id',id_inpt + uplF_count);
			_inputs[ii].setAttribute('value', uplF_count);
			_labels[ii].setAttribute('for',id_lab + uplF_count);
		}
		*/
		var imgPrewiev = newImgrow.children[1].children[0].children[0].children[0]; //<img>
		var srcPrew = '../../docx_temp/' + pathToimg + '/' + spansFromWord[i].innerHTML;
		imgPrewiev.setAttribute('src', srcPrew);

		// вставляем картинку только после всех изменений
		picts.insertBefore(newImgrow, add_bef_this);
		uplF_count++;
	}
	
	
}

var stonesFromWord = document.getElementById('stonesFromWord').innerHTML;
if ( stonesFromWord ) {
	var objectGemsParsed = parseGemString(stonesFromWord);
	var addGemRowClick = document.getElementById('addGem');

	for ( var ig = 0; ig < objectGemsParsed.diametrs.length; ig++ ) {
		addGemRowClick.click();
		var gemsRow12 = document.getElementById('gems_table').getElementsByTagName('tr');
		var inputs_gem = gemsRow12[ig].getElementsByTagName('input');

		inputs_gem[0].setAttribute('value', objectGemsParsed.diametrs[ig]); 
		inputs_gem[1].setAttribute('value', objectGemsParsed.counts[ig]);
		inputs_gem[2].setAttribute('value', 'Круг');
		inputs_gem[3].setAttribute('value', objectGemsParsed.cutNamesEtc[ig]);
		inputs_gem[4].setAttribute('value', 'Белый');
	}
	
	function parseGemString(testStr) {

		var splatBySHt = testStr.split('шт');
		var splitByMM = testStr.split('мм');
		
		var object = {
			diametrs: [],   // содержит диаметры
			counts: [],     // содержит кол-во камней
			cutNamesEtc: [] // содержит сырье, названия, цвета и т.д.
		}
		
		// выделяем размер камня и кол-во штук
		for ( var i = 0; i < splitByMM.length-1; i++ ) {
			
			// trim " " убираем все пробелы
			var trimMM = splitByMM[i].split('');
			var trimSHt = splatBySHt[i].split('');
			
			//console.log(trimMM);
			for ( var tt = 0; tt < trimMM.length; tt++ ) {
				if ( trimMM[tt] == ' ' ) {
					trimMM.splice(tt, 1);
					tt--;
				}
				if ( trimMM[tt] == ',' ) {
					trimMM.splice(tt, 1, '.');
				}
			}
			trimMM = trimMM.join('');
			//console.log(trimMM);
			
			//console.log(trimSHt);
			for ( var tt = 0; tt < trimSHt.length; tt++ ) {
				if ( trimSHt[tt] == ' ' ) {
					trimSHt.splice(tt, 1);
					tt--;
				}
				if ( trimSHt[tt] == ',' ) {
					trimSHt.splice(tt, 1, '.');
				}
			}
			
			trimSHt = trimSHt.join('');
			//console.log(trimSHt);
			// end trim
			
			var probCount = trimSHt.slice(-3); // для штук берем 3 числа с конца
			var probNumber = trimMM.slice(-5); // для мм берем 5 чисел с конца
			
			var counts = [];
			var numbers = [];
			var firstNumber = false;
			
			//выделяем размер камня
			for ( var j = 0; j < probNumber.length; j++ ) {
				// проверяем от первого встретившегося числа
				if ( isNumeric(probNumber[j]) && firstNumber === false ) firstNumber = true;
				
				if ( firstNumber ) {
					if ( isNumeric( probNumber[j] ) || probNumber[j] === '.' || probNumber[j] === 'х' || probNumber[j] === 'x' || probNumber[j] === '*' ) {
						numbers.push(probNumber[j]);
					}
				}
			}
			
			// выделяем количество
			for ( var j = 0; j < probCount.length; j++ ) {
				if ( isNumeric( probCount[j] ) ) counts.push(probCount[j]);
			}

			object.diametrs.push( numbers.join('') );
			object.counts.push( +counts.join('') );

		}
		
		// выделяем огранку сырье цвет
		var substrnum = 0;
		var foundPos = false;
		
		for ( var i = 0; i < splatBySHt.length-1; i++ ) {
			
			foundPos = testStr.indexOf('шт', substrnum);

			//console.log( 'foundPos = ', foundPos );
			
			var subbstr = testStr.substring(foundPos);
			//console.log( 'subbstr = ', subbstr );
			
			for ( var k = 0; k < subbstr.length; k++ ) {
				if ( isNumeric(subbstr[k]) || k == (subbstr.length-1) ) {
					
					substrnum = k + foundPos;
					
					//console.log( 'нашли в подстроке число на позиции = ', k );
					
					var str123 = subbstr.substring(2, k );
					var str123arr = str123.split('');
					for ( var tt = 0; tt < str123arr.length; tt++ ) {
						if ( str123arr[tt] == ' ' || str123arr[tt] == '.' || str123arr[tt] == 'Ø' ) {
							str123arr.splice(tt, 1);
							tt--;
						}
					}
					object.cutNamesEtc.push( str123arr.join('') );
					break;
				}
			}
			
		}
		return object;
	} // end str parse
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}




