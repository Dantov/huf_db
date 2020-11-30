"use strict";

function Repairs()
{

    this.repairsBlock = document.querySelector('#repairsBlock');

    this.init();
}

Repairs.prototype.init = function()
{
    let that = this;

    // Накинем обработчики на добавление ремонтов
    $.each(this.repairsBlock.querySelectorAll('.addRepair'), function(i, button) {
        button.addEventListener('click', that.addRepair.bind(event, button) ,false);
    });

    // Накинем обработчики на удаление ремонтов
    $.each(this.repairsBlock.querySelectorAll('.removeRepair'), function(i, button) {
        button.addEventListener('click', that.removeRepair.bind(event, button) ,false);
    });
    // Накинем обработчики на удаление Оценок
    $.each(this.repairsBlock.querySelectorAll('.repDellGrade'), function(i, button) {
        button.addEventListener('click', that.removeGrade.bind(event, button) ,false);
    });

    debug('Repairs init ok!');
};


Repairs.prototype.addRepair = function(button, event)
{
    event.preventDefault();
    event.stopPropagation();

    let lastRepNum = 0, repairsCount = 0;
    let repairsBlock = document.getElementById('repairsBlock');

    let today = new Date();

    let dataRepair = this.getAttribute('data-repair');
    let newRepairs = document.getElementById('protoRepairs').cloneNode(true);

    switch (dataRepair) {
        case '3d':
            repairsCount = repairsBlock.querySelectorAll('.repairs3d');
            if ( repairsCount.length ) {
                lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
            }

            newRepairs.removeAttribute('id');
            newRepairs.classList.remove('hidden');
            newRepairs.classList.add('repairs3d');
            newRepairs.classList.add('panel-info');
            newRepairs.querySelector('.repairs_name').innerHTML = '3Д Ремонт №';
            newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
            newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
            newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[3d][num][]');
            newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[3d][id][]');
            newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[3d][description][]');
            newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[3d][which][]');
            newRepairs.querySelector('.repairs_which').setAttribute('value','0');
            newRepairs.querySelector('.repairCost').setAttribute('name','repairs[3d][cost][]');
            newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);
            break;
        case 'jeweler':
            repairsCount = repairsBlock.querySelectorAll('.repairsJew');
            if ( repairsCount.length ) {
                lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
            }

            newRepairs.removeAttribute('id');
            newRepairs.classList.remove('hidden');
            newRepairs.classList.add('repairsJew');
            newRepairs.classList.add('panel-success');
            newRepairs.querySelector('.repairs_name').innerHTML = 'Ремонт Модельера-доработчика №';
            newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
            newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
            newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[jew][num][]');
            newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[jew][id][]');
            newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[jew][description][]');
            newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[jew][which][]');
            newRepairs.querySelector('.repairs_which').setAttribute('value','1');
            newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);

            newRepairs.querySelector('.repairCost').setAttribute('name','repairs[jew][cost][]');
            newRepairs.querySelector('.repairsPayment').classList.remove('hidden');

            break;
    }

    repairsBlock.insertBefore(newRepairs, this);
};

Repairs.prototype.removeRepair = function(button, event)
{
    let toDell = button.parentElement.parentElement;
        toDell.classList.add('hidden');
        toDell.querySelector('.repairs_descr_done').innerHTML = '-1';
};
Repairs.prototype.removeGrade = function(button, event)
{
    let tBody = this.parentElement.parentElement.parentElement;
    this.parentElement.parentElement.remove();

    // скроем всю табл если нет строк
    let rows = tBody.getElementsByTagName('tr');
    if ( rows.length === 0 ) tBody.parentElement.classList.add('hidden');
};

let repairs = new Repairs();







// ----- РЕМОНТЫ -------//
/*
if ( document.getElementById('repairsBlock') )
{
    let addRepairs = document.getElementById('repairsBlock').querySelectorAll('.addRepairs');
    $.each(addRepairs, function(i, button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let lastRepNum = 0, repairsCount = 0;
            let repairsBlock = document.getElementById('repairsBlock');

            let today = new Date();

            let dataRepair = this.getAttribute('data-repair');
            let newRepairs = document.getElementById('protoRepairs').cloneNode(true);

            switch (dataRepair) {
                case '3d':
                    repairsCount = repairsBlock.querySelectorAll('.repairs3d');
                    if ( repairsCount.length ) {
                        lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
                    }

                    newRepairs.removeAttribute('id');
                    newRepairs.classList.remove('hidden');
                    newRepairs.classList.add('repairs3d');
                    newRepairs.classList.add('panel-info');
                    newRepairs.querySelector('.repairs_name').innerHTML = '3Д Ремонт №';
                    newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
                    newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
                    newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[3d][num][]');
                    newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[3d][id][]');
                    newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[3d][description][]');
                    newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[3d][which][]');
                    newRepairs.querySelector('.repairs_which').setAttribute('value','0');
                    newRepairs.querySelector('.repairCost').setAttribute('name','repairs[3d][cost][]');
                    newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);
                    break;
                case 'jeweler':
                    repairsCount = repairsBlock.querySelectorAll('.repairsJew');
                    if ( repairsCount.length ) {
                        lastRepNum = +repairsCount[repairsCount.length-1].querySelector('.repairs_number').innerHTML;
                    }

                    newRepairs.removeAttribute('id');
                    newRepairs.classList.remove('hidden');
                    newRepairs.classList.add('repairsJew');
                    newRepairs.classList.add('panel-success');
                    newRepairs.querySelector('.repairs_name').innerHTML = 'Ремонт Модельера-доработчика №';
                    newRepairs.querySelector('.repairs_number').innerHTML = lastRepNum + 1;
                    newRepairs.querySelector('.repairs_num').setAttribute('value', lastRepNum + 1);
                    newRepairs.querySelector('.repairs_num').setAttribute('name','repairs[jew][num][]');
                    newRepairs.querySelector('.repairs_id').setAttribute('name','repairs[jew][id][]');
                    newRepairs.querySelector('.repairs_descr').setAttribute('name','repairs[jew][description][]');
                    newRepairs.querySelector('.repairs_which').setAttribute('name','repairs[jew][which][]');
                    newRepairs.querySelector('.repairs_which').setAttribute('value','1');
                    newRepairs.querySelector('.repairs_date').innerHTML = formatDate(today);

                    newRepairs.querySelector('.repairCost').setAttribute('name','repairs[jew][cost][]');
                    newRepairs.querySelector('.repairsPayment').classList.remove('hidden');

                    break;
            }

            repairsBlock.insertBefore(newRepairs, this);
        });
    });
/*
    function initPaidModal() {

        $('#modalPaid').iziModal({
            title: 'Пометить ремонт оплаченным?',
            headerColor: '#56a66e',
            icon: 'far fa-credit-card',
            transitionIn: 'comingIn',
            transitionOut: 'comingOut',
            overlayClose: false,
            closeButton: true,
            afterRender: function () {
                document.getElementById('modalPaidContent').classList.remove('hidden');
            }
        });

        // начало открытия
        $(document).on('opening', '#modalDelete', function () {

        } );
        // Начало закрытия
        $(document).on('closing', '#modalDelete', function () {

        });
        // исчезло
        $(document).on('closed', '#modalDelete', function () {

        } );

        //обработчики на кнопки
        let buttons = document.getElementById('modalPaidContent').querySelectorAll('a');
        let cancel = buttons[0];
        let ok = buttons[1];
        let paid = buttons[2];

        cancel.classList.remove('hidden');
        paid.classList.remove('hidden');

        paid.addEventListener('click', function () {

            let data = paidRepair.data;
            if ( data.paid !== 1 ) return;
            debug(data);

            $.ajax({
                type: 'POST',
                url: '/add-edit/rp',//'controllers/repairsPayment.php',
                data: data,
                dataType:"json",
                success:function(response) {
                    debug(response,'response');

                    if (response.error)
                    {
                        debug(response.error);
                        return;
                    }
                    if (response.done !== true)
                    {
                        alert('Ошибка на стороне сервера! Попробуйте позже.');
                        debug(response.done);
                        return;
                    }
                    let modal = $('#modalPaid');

                    modal.iziModal('setTitle', 'Ремонт отмечен оплаченным.');
                    modal.iziModal('setSubtitle', '');
                    modal.iziModal('setHeaderColor', '#66d246');
                    modal.iziModal('setIcon', 'glyphicon glyphicon-ok');

                    ok.onclick = function() {
                        document.location.reload(true);
                    };
                    cancel.classList.add('hidden');
                    paid.classList.add('hidden');
                    ok.classList.remove('hidden');
                },
                error:function(e){
                    debug(e,'Paid repair error');
                }
            });

        } );

        debug('Paid modal init');
    }(initPaidModal());
    */
//}

/*
function paidRepair(self) {
    event.preventDefault();
    event.stopPropagation();

    let repair = self.parentElement.parentElement.parentElement;
    let repairID = repair.querySelector('.repairs_id').value;
    let cost = repair.querySelector('.repairCost').value;
    let repairName = repair.querySelector('.repairs_name').innerHTML + repair.querySelector('.repairs_number').innerHTML + ' от ' + repair.querySelector('.repairs_date').innerHTML;
    let repairText = repair.querySelector('.repairs_descr').value;
    let repairCost = repair.querySelector('.repairCost').value;

    let data = {
        paid: 1,
        cost: cost,
        repairID: repairID,
    };
    let modal = $('#modalPaid');

    modal.iziModal('setSubtitle', 'Это действие будет невозможно отменить!');
    modal[0].querySelector('#modalPaidStatus').innerHTML = '<b>'+repairName + '</b><br>' + repairText + '<br><b>Стоимость: ' + repairCost + '</b>';
    $('#modalPaid').iziModal('open');

    paidRepair.data = data;
}
*/
// ----- END РЕМОНТЫ -------//