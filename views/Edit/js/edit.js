"use strict";

//--------- отображаем превью при наведении ----------//
addPrevImg( document.getElementById('complects') );

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

            },false);

            complects[i].addEventListener('mouseout',function(event) {

            var imageBoxPrev = document.getElementById('imageBoxPrev');
            imageBoxPrev.classList.add('hidden');

            }, false);
	}
}




function submitForm() {

    var editform = document.getElementById('editform');

    var addedit = 'controllers/editHandler.php';
    var formData = new FormData(editform);

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
            //debug(resp);

            progressBar.style.width = 100 + '%';
            progressBar.innerHTML = 100 + '%';

            var strong =  document.createElement('strong');
                strong.innerHTML = "Статусы внесены успешно!";
            if ( resp.done == 1 )
            {
                strong.innerHTML = "Статусы внесены успешно!";
            } else {
                strong.innerHTML = "Ошибка!";
            }
            var h4 = document.createElement('h4');
                    h4.appendChild(strong);

            var a3 = document.createElement('a');
                    a3.setAttribute('class','btn btn-success');
                    a3.setAttribute('type','button');
                    a3.setAttribute('href','../Main/index.php');
                    a3.innerHTML = 'OK';

            var center = document.createElement('center');
                    center.appendChild(a3);

            progressStatus.innerHTML = 'Готово!';

            result.appendChild(h4);
            result.appendChild(center);
            
        },
        error: function() { // Данные не отправлены
            progressStatus.innerHTML = 'Ошибка отправки! Попробуйте снова.';
        }
    });

}



//-------- Side buttons ---------//
let addEditSideButtons = {};
window.onload = function f() {
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
