"use strict";

Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
	this.parentElement.removeChild(this);
	};

//                       !!!!!функции для открытвания больших картинок!!!!!

var images_block = document.getElementById('images_block');
var popup = document.getElementById('popup');
var images_block =  document.getElementById('images_block');
var img_arr = images_block.getElementsByTagName('img');
var num = null;
var tid; // айдишник для интервала

images_block.addEventListener("click", function(event) {
	if ( !(event.target.getAttribute('class') == 'img-responsive image') ) return;
	blackCover_toggle();
	var click = event.target;
	var wrapper_img = document.getElementById('wrapper_img');
	var img_src = click.getAttribute('src'); // взяли имя картинки
	var img_count = document.getElementById('img_count');

	var name   = document.getElementById('num3d').innerHTML;
	var articl = document.getElementById('articl').innerHTML;
	var mtype  = document.getElementById('modelType').innerHTML;
	
	var model_name = document.getElementById('model_name');
	
	num = event.target.getAttribute('num');
	var num1 = num;
	
	wrapper_img.children[0].setAttribute('src', img_src);
	
	
	img_count.innerHTML = ++num1 + " / " + img_arr.length;
	model_name.innerHTML = name + " / " + articl + " &#8212; " + mtype;
	
	tid = setInterval(function(){
	  wrapp_center();
    }, 50);

});
// закрытие
popup.addEventListener("click", function(event) {
	var click = event.target;
	
	if ( click.getAttribute('class') == 'blackCover' || click.getAttribute('id') == 'close_popup' ) {
		var img = this.children[3].children[0]; // картинка которая сейчас открыта
	    img.removeAttribute('style');
		blackCover_toggle();
		clearInterval(tid);
		return;
	}
});

function prev_next_Img(self) {
	var arrow = self.getAttribute('id');
	var loadSrc = '../picts/loading.gif';
	var img = popup.children[3].children[0]; // картинка которая сейчас открыта
		img.removeAttribute('style');
	var l = img_arr.length;
	if ( arrow == 'left-bracket' ) {
		++num;
		if ( num > l-1 ) num = 0;
	} 
	if ( arrow == 'right-bracket' ) {
		--num;
		if ( num < 0 ) num = l-1;
	}
	var newimgsrc = img_arr[num].getAttribute('src');
	img.setAttribute('src', newimgsrc);
	popup.querySelector('#img_count').innerHTML = num+1 + " / " + img_arr.length;
};

if (popup.addEventListener) {
  if ('onwheel' in document) {
    // IE9+, FF17+, Ch31+
    popup.addEventListener("wheel", onWheel);
  } else if ('onmousewheel' in document) {
    // устаревший вариант события
    popup.addEventListener("mousewheel", onWheel);
  } else {
    // Firefox < 17
    popup.addEventListener("MozMousePixelScroll", onWheel);
  }
} else { // IE8-
  popup.attachEvent("onmousewheel", onWheel);
};
function onWheel() {
	var img = popup.children[3].children[0]; // картинка которая сейчас открыта
	img.removeAttribute('style');
    blackCover_toggle();
    clearInterval(tid);
};

function wrapp_center() { // центрируем большую картинку и стрелки
		var screen = document.documentElement;
		
		var thisimg = wrapper_img.children[0];
		var img_Width = wrapper_img.offsetWidth;
		var img_Height = wrapper_img.offsetHeight;
		//alert( a + ' ' + b );
		
		if ( screen.clientHeight <= img_Height ) {
			thisimg.style.height = screen.clientHeight + 'px';
			//thisimg.setAttribute('height', screen.clientHeight + 'px');
		} else {
			thisimg.style.height = '';
			//thisimg.removeAttribute('height');
		}

		img_Height = wrapper_img.offsetHeight; 
		
		wrapper_img.style.marginLeft = '50%';//Math.round( img_Width / 2) + 'px';
		//wrapper_img.style.marginTop = '50%';// Math.round( img_Height / 2 ) + 'px';
		
		var computedStyle = getComputedStyle(wrapper_img);
		
		var wrapper_img_marginLeft = computedStyle.marginLeft;
		var pxidL = wrapper_img_marginLeft.indexOf('px');
		wrapper_img_marginLeft = +wrapper_img_marginLeft.slice(0,pxidL);
		//alert( wrapper_img_marginLeft );
		var imgMargLeft = wrapper_img_marginLeft - Math.round( img_Width / 2);
		//alert( imgMargLeft );
	    wrapper_img.style.marginLeft = imgMargLeft + 'px';
		/*
		var wrapper_img_marginTop = computedStyle.marginTop;
		var pxidT = wrapper_img_marginTop.indexOf('px');
		wrapper_img_marginTop = +wrapper_img_marginTop.slice(0,pxidT);
		//alert( wrapper_img_marginTop );
		var imgMargTop = wrapper_img_marginTop - Math.round( img_Height / 2);
		alert( imgMargTop );*/
	    wrapper_img.style.top = Math.round( screen.clientHeight / 2 - Math.round( img_Height / 2) ) + 'px';
		

	    // установка стрелок
		var arrows = document.getElementById('arrows');

	    arrows.style.top = Math.round(screen.clientHeight / 2 - 41) + "px";
		
	};
	
function blackCover_toggle() {
    var popup = document.getElementById('popup');
	var blackCover = document.getElementById('blackCover');
	
	popup.classList.toggle('hidden_popup');
	blackCover.classList.toggle('blackCover');
};

//                              !!!!! конец функции для картинок!!!!!

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
}