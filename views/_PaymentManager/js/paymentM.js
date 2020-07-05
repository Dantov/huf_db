"use strict";

function PaymentManager()
{

    this.init();
}

PaymentManager.prototype.init = function(button) 
{
    let pm = this;
    $('#paymentModal').on('show.bs.modal', function (e) {
        //debug(e);
        let button = e.relatedTarget;
        if ( button.getAttribute('data-priceID') === 'all' )
        {
            pm.getAllPriceData();
        } else {
            pm.getPriceData(button);    
        }
    });

    $('#paymentModal').on('hidden.bs.modal', function (e) {
        document.getElementById('paymentModal').querySelector('.modal-body').innerHTML = "";
    });

    debug('PaymentManager init ok!');
};

PaymentManager.prototype.getPriceData = function(button) 
{
    // Надо будет выбрать из БД
    //let that = this;
    /*
    $.ajax({
        url: "/options/widthControl",
        type: 'POST',
        data: {
            widthControl: opt
        },
        dataType:"json",
        success:function(data) {
            // if ( data.done === 1 ) console.log('показать во всю ширину');
            // if ( data.done === 2 ) console.log('Показать по центру');
            document.location.reload(true);
        }
    });
    */

    let inf = button.parentElement.parentElement.parentElement.parentElement;
    //debug(inf);
    let modal = document.getElementById('paymentModal').querySelector('.modal-body');

    let modalInfo = inf.querySelector('.modelInfo').cloneNode(true);
    let priceName_value = button.parentElement.querySelector('.priceName_value').cloneNode(true);
    let currentWorkerName = document.querySelector('.currentWorkerName').cloneNode(true);

    let br = document.createElement('br');
    let div = document.createElement('div');
        div.setAttribute('class','text-bold text-center');

    modal.appendChild(modalInfo);
    modal.appendChild(br);
    
    div.appendChild(priceName_value);
    modal.appendChild(div);
    modal.appendChild(br.cloneNode());

    modal.appendChild(currentWorkerName);
};

PaymentManager.prototype.getAllPriceData = function() 
{
    //let that = this;
    /*
    debug(opt,'opt');
    $.ajax({
        url: "/options/noticeControl",
        type: 'POST',
        data: {
            noticeActivate: opt
        },
        dataType:"json",
        success:function(data) {
            if ( data.done === 1 ) console.log('Уведомления активированы');
            if ( data.done === 2 ) console.log('Уведомления отключены');
        }
    });*/

    let modal = document.getElementById('paymentModal').querySelector('.modal-body');
    let modalTitle = document.getElementById('paymentModal').querySelector('.modal-title');

    let row = document.querySelector('.allmodels');
    let modelsInfo = row.querySelectorAll('.modelInfo');
    let pricesName_value = row.querySelectorAll('.priceName_value');

    let currentWorkerName = document.querySelector('.currentWorkerName').cloneNode(true);
    let br = document.createElement('br');

    modalTitle.innerHTML = "Оплатить все модели для " + currentWorkerName.innerHTML;

    for( let i = 0; i < pricesName_value.length; i++ ) 
    {
        modal.appendChild(pricesName_value[i].cloneNode(true));
        modal.appendChild(br.cloneNode());
    }
    modal.appendChild(br.cloneNode());
    modal.appendChild(currentWorkerName);

    //debug(modalsInfo,'modalsInfo');
    //debug(pricesName_value,'pricesName_value');
};

PaymentManager.prototype.changeBgImg = function() 
{
    /*
    console.log(this);
    if ( !this.checked ) {
        console.log('Ухожу');
        return;
    }

    //let src = this.previousElementSibling.getAttribute('src');
    let src = this.getAttribute('data-class');
    console.log(src);
    $.ajax({
        url: "/options/changeBgImg",
        type: 'POST',
        data: {
            srcBgImg: src
        },
        dataType:"json",
        success:function(data) {
            if ( data.done ) {
                //alert('Новый фон установлен!');
                document.querySelector('body').setAttribute('class',src);
                //document.location.reload(true);
            } else {
                alert('что-то пошло не так!');
            }
        }
    });
    */
};


let pm = new PaymentManager();


/*
let width_control = document.getElementById('width_control');
if ( width_control.checked ) {
    width_control.addEventListener('click',function(){
        options.widthControl(2);
    }, false);
} else {
    width_control.addEventListener('click',function(){
        options.widthControl(1);
    }, false);
}

let PN_control = document.getElementById('PN_control');
if ( PN_control.checked ) {
    PN_control.addEventListener('click',function(){
        options.noticeControl(2);
    }, false);
} else {
    PN_control.addEventListener('click',function(){
        options.noticeControl(1);
    }, false);
}

let bgImages = document.querySelectorAll('.bg_img');
for( let i = 0; i < bgImages.length; i++ ) {
    bgImages[i].addEventListener('change', options.changeBgImg, false);
}*/


