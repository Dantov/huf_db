"use strict";

cutLongNames( document.querySelector('.table_gems'), 21 );
cutLongNames( document.querySelector('.table_vc_links'), 21 );
function cutLongNames( table, numCut ) {
	if ( !table ) return;
	let td_arr = table.getElementsByTagName('td');
	
	for ( let i = 0; i < td_arr.length; i++ ) {
		
		if ( table.classList.contains('table_vc_links') ) {
			if ( td_arr[i].firstElementChild ) continue;
		}
		
		let str = td_arr[i].innerHTML;
		if ( str.length > numCut ) {
			td_arr[i].setAttribute('realstr',str);
			td_arr[i].setAttribute('class','cutedTD');
			
			str = str.slice(0,numCut) + "...";
			
			td_arr[i].innerHTML = str;
			td_arr[i].addEventListener('mouseover',function(){
				this.setAttribute('cutstr',this.innerHTML);
				let coords = main.getCoords(this);
				let longTD = document.querySelector('#longTD');
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
}


//--------- Рисуем бордер слева или справа ----------//
let descr = document.getElementById('descr');

let descrH = descr.offsetHeight;
let images_blockH = images_block.offsetHeight;

if ( descrH >= images_blockH ) {
	descr.classList.add('bordersMiddleLeft');
	images_block.classList.remove('bordersMiddleRight');
}
if ( descrH <= images_blockH ) {
	descr.classList.remove('bordersMiddleLeft');
	images_block.classList.add('bordersMiddleRight');
}

window.addEventListener('load', function () {
    let dPHeight = document.querySelector('.descriptionPanel').offsetHeight;
    document.querySelector('.mainImage').style.height = dPHeight + 'px';

    document.querySelectorAll('.imageSmall').forEach( image => {
       image.style.height = image.offsetWidth + 'px';
	} );

});




//--------- отображаем превью при наведении ----------//
addPrevImg( document.getElementById('complects') );
addPrevImg( document.querySelector('.table_vc_links') );

function addPrevImg(domEl) 
{
    if ( !domEl ) return;
	let complects = domEl.querySelectorAll('a');
	
	for ( let i = 0; i < complects.length; i++ ) {
	
		complects[i].addEventListener('mouseover',function(event){
			
			let mouseX = event.pageX;
			let mouseY = event.pageY;
			
			let hover = event.target;
			let imageBoxPrev = document.getElementById('imageBoxPrev');
				imageBoxPrev.style.top = 0 + 'px';
				imageBoxPrev.style.left = 0 + 'px';
			
			let src = hover.getAttribute('imgtoshow');

			imageBoxPrev.style.left = mouseX - 215 + 'px';

            imageBoxPrev.setAttribute('src',src);
            imageBoxPrev.onload = function(){
                imageBoxPrev.classList.remove('hidden');

                if ( domEl.classList.contains('table_vc_links') ){
                    imageBoxPrev.style.top = mouseY - 15 - imageBoxPrev.height + 'px';
				} else {
                    imageBoxPrev.style.top = mouseY + 15 + 'px';
				}

			};

		},false);
		
		complects[i].addEventListener('mouseout',function(event) {
			
			let imageBoxPrev = document.getElementById('imageBoxPrev');
			imageBoxPrev.classList.add('hidden');
            imageBoxPrev.removeAttribute('src');
			
		},false);

	}
}



let butt3D = document.getElementById('butt3D');
if ( butt3D )
{
    butt3D.onclick = function() {
        let formData = new FormData( document.getElementById('extractForm') );
        $.ajax({
            url: "/model-view/extract-zip",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
			//dataType: "JSON", // не работает с new FormData
            success:function(resp) {
                resp = JSON.parse(resp);
                if ( resp.done === false )
                {
                    debug(resp.errMessage);
                	return;
                }

                let body = document.getElementById("body");
                let formToDell = document.getElementById("dellStlForm");

                let names = resp.names;

                for ( let i = 0; i < names.length; i++ )
                {
                    let input = document.createElement('input');
						input.setAttribute('type', 'hidden');
						input.setAttribute('name', 'dell_name[]');
						input.setAttribute('value', resp.zip_path + names[i]); //'../../Stock/'+resp['zip_path'] + names[i]
                    formToDell.appendChild(input);
                }

                let three = document.createElement('script');
                three.setAttribute('src','/web/js_lib/three.min.js');
                body.appendChild(three);
                $( three ).on('load',function()
                {
                    let scripts = {
                        script2: '/web/js_lib/OrbitControls.js',
                        //script3: '/web/js_lib/TrackballControls.js',
                        script4: '/web/js_lib/TransformControls.js',
                        script5: '/web/js_lib/STLLoader.js',
                        script6: '/Views/_ModelView/js/view3D.js?ver='+new Date().getMilliseconds(),
                    };
                    for( let src in scripts )
                    {
                        let script = document.createElement('script');
                        script.setAttribute('src',scripts[src]);
                        body.appendChild(script);
                    }

                    body.classList.add('body');
                });
            },
            error:function (err) {
				debug('Error sending ajax');
				debug(err);
            }
        })
    };
}











// ========== LIKES ========== //
let btnlikes = document.querySelectorAll('.btnlikes');
if ( btnlikes[0] ) {
	btnlikes[0].addEventListener('click', likesDislikes, false);
	btnlikes[1].addEventListener('click', likesDislikes, false);
}
function likesDislikes() {
	let id = document.querySelector('.container').getAttribute('id').split('_');

	let obj = {
		likeDisl: 0,
		id: id[1]
	};
	let nums;

	if ( this.classList.contains("likeBtn") ) {
		obj.likeDisl = 1; // облайкали
		nums = document.getElementById("numLikes");
	} else if ( this.classList.contains("disLikeBtn") ) {
		obj.likeDisl = 2; //обдислайкали
		nums = document.getElementById("numDisLikes");
	}
	let that = this;
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