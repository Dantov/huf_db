"use strict";

function PaymentManager()
{
    this.modal = document.querySelector("#paymentModal");
    this.modalBody = this.modal.querySelector(".modal-body");
    this.payButton = this.modal.querySelector(".payButton");

    this.priceIDs = [];

    this.init();
}

PaymentManager.prototype.init = function(button) 
{
    let pm = this;
    $('#paymentModal').on('show.bs.modal', function (e) {
        //debug(e);
        let button = e.relatedTarget;
        pm.getPricesAllData(button);
    });

    $('#paymentModal').on('hidden.bs.modal', function (e) {
        let f = pm.modalBody.querySelector('.columnFirst');
        let s = pm.modalBody.querySelector('.columnSecond');
        f.innerHTML = "";
        s.innerHTML = "";
        f.classList.remove('col-md-12', 'col-md-6');
        s.classList.remove('col-md-12', 'col-md-6');
        pm.modal.children[0].classList.remove("modal-lg");
        pm.priceIDs = [];
    });

    this.payButton.addEventListener('click', function () {
        pm.payPrices(this);
    },false);

    this.addCollapsesEvent();

    debug('PaymentManager init ok!');
};
/**
 * Накинем обработчик на панель, что бы открывалась не только лишь по клику на ссылке collapse
 */
PaymentManager.prototype.addCollapsesEvent = function()
{
    let allModels = document.getElementById('allModels');
    let panelHeadings = allModels.querySelectorAll('.panel-heading');

    $.each(panelHeadings, function (i, ph) {
        // при клике на панель, раскрыли панель и подсветили её
        ph.addEventListener('click',function (e) {
            let click = e.target;
            if ( click.classList.contains('modelHref') ) return;
            $(this.nextElementSibling).collapse('toggle');
            let panel = this.parentElement;

            panel.classList.toggle('panel-default');
            panel.classList.toggle('panel-info');
            panel.classList.toggle('panel-primary');
        });
        // Подсветили строку модели по mouse over
        ph.addEventListener('mouseover',function (e) {
            let click = e.target;
            if ( click.classList.contains('modelHref') ) return;

            let panel = this.parentElement;
            if ( !panel.classList.contains('panel-primary') )
            {
                panel.classList.toggle('panel-default');
                panel.classList.toggle('panel-info');
            }
        });
        // Убрали подсветку по mouse out
        ph.addEventListener('mouseout',function (e) {
            let click = e.target;
            if ( click.classList.contains('modelHref') ) return;

            let panel = this.parentElement;
            if ( !panel.classList.contains('panel-primary') )
            {
                panel.classList.toggle('panel-default');
                panel.classList.toggle('panel-info');
            }
        });
    });
};

PaymentManager.prototype.getPricesAllData = function(button)
{
    let that = this;

    $.ajax({
        url: "/payment-manager/?getPricesAll",
        type: 'POST',
        data: {
            priceIDs: button.getAttribute('data-priceID'),
            posID: button.getAttribute('data-posID'),
        },
        dataType:"json",
        success:function(models) {
            if ( models.error )
            {
                debug(models.error.code);
                debug(models.error.message);
                return;
            }

            let toFirstColumn = Math.floor(models.length / 2);

            let colFirst = that.modalBody.querySelector('.columnFirst');
            let colSecond = that.modalBody.querySelector('.columnSecond');

            let totalValue = 0;
            $.each(models, function (i, model) {

                let newModelRow = document.querySelector('.PM_protoModel').cloneNode(true);

                newModelRow.classList.remove('hidden','PM_protoModel');
                let vc = model.vendorCode ? " / " + model.vendorCode : "";
                newModelRow.querySelector('.panel-heading').children[0].innerHTML = model.number_3d + vc + " - " + model.modelType;
                newModelRow.querySelector('.panel-body').children[0].src = model.imgName;

                let modelPanelID = "modelPanel_" + i;
                let collapsePanelID = "collapsePanelID_" + i;
                let ph = newModelRow.querySelector('.panel-heading');
                    ph.id = modelPanelID;
                    ph.children[0].setAttribute('href','#' + collapsePanelID);
                    ph.children[0].setAttribute('aria-controls', collapsePanelID);
                    ph.nextElementSibling.setAttribute('id', collapsePanelID);
                    ph.nextElementSibling.setAttribute('aria-labelledby', modelPanelID);
                    ph.addEventListener('click',function (e) {
                        $(this.nextElementSibling).collapse('toggle');
                    });

                let tMValue = 0;
                let ul = newModelRow.querySelector('.list-group');

                $.each(model.prices, function (c, price) {
                    that.priceIDs.push(price.pID);
                    tMValue += +price.value;

                    let newLi = document.createElement('li');
                    newLi.classList.add('list-group-item');

                    let p3D = '';
                    if ( +price.is3dGrade === 1 ) p3D = "<u>3D Моделирование: </u>";
                    let span = document.createElement('span');
                    span.innerHTML = p3D + "<i>" + price.costName + ": </i><b>" + price.value + 'грн.</b>';
                    let spanFIO = document.createElement('span');
                    spanFIO.classList.add('pull-right', 'small');
                    spanFIO.innerHTML =  price.fio + " " + price.date;

                    let notPayedLabel = newModelRow.querySelector('.notPayed').cloneNode(true);
                    notPayedLabel.classList.remove('hidden');
                    notPayedLabel.classList.add('mr-1');

                    let accruedLabel = newModelRow.querySelector('.accrued').cloneNode(true);
                    accruedLabel.classList.remove('hidden');
                    accruedLabel.classList.add('mr-1');

                    let paySuccessLabel = newModelRow.querySelector('.paySuccess').cloneNode(true);
                    paySuccessLabel.classList.add('mr-1');

                    let div = document.createElement('div');
                    div.classList.add('pt-1');
                    div.appendChild(accruedLabel);
                    div.appendChild(notPayedLabel);
                    div.appendChild(paySuccessLabel);

                    newLi.appendChild(span);
                    newLi.appendChild(spanFIO);
                    newLi.appendChild(div);

                    ul.appendChild(newLi);
                });
                totalValue += tMValue;

                let footer = newModelRow.querySelector('.panel-footer');
                footer.innerHTML = "Всего: " + tMValue + "грн.";

                let paidType = button.getAttribute('data-prices');
                let topText = "";
                let md12 = "col-md-12";
                switch (paidType)
                {
                    case "allInModel":
                        topText = "Оплатить всё в модели";
                        $(newModelRow.querySelector('.collapse')).collapse('toggle');
                        break;
                    case     "single":
                        topText = "Оплатить <i>" + models[0].prices[0].costName + "</i>";
                        $(newModelRow.querySelector('.collapse')).collapse('toggle');
                        break;
                    case        "all":
                        topText = "Оплатить всё - <b>" + totalValue + "</b>грн.";
                        that.modal.children[0].classList.add("modal-lg");
                        md12 = "col-md-6";
                        break;
                }
                that.modal.querySelector('.modal-title').innerHTML = topText;
                if ( i <= toFirstColumn )
                {
                    colFirst.classList.add(md12);
                    colFirst.appendChild(newModelRow);
                } else {
                    colSecond.classList.add(md12);
                    colSecond.appendChild(newModelRow);
                }
            });
        },
    });

};

PaymentManager.prototype.payPrices = function(payButton)
{
    payButton.classList.add('disabled');
    cursorSet('wait');
    let notPayed = this.modalBody.querySelectorAll('.notPayed');
    $.each(notPayed, function (i, label) {
        label.classList.add('hidden');
    });

    let that = this;
    $.ajax({
        url: "/payment-manager/?payPrice",
        type: 'POST',
        data: {
            prices: that.priceIDs,
        },
        dataType:"json",
        success:function(data) {
            if ( data.success ) {

                setTimeout(function () {
                    let paySuccess = that.modalBody.querySelectorAll('.paySuccess');
                    $.each(paySuccess, function (i, label) {
                        label.classList.remove('hidden');
                    });
                    payButton.previousElementSibling.classList.add('hidden');
                }, 500);

                setTimeout(function () {
                    $('#paymentModal').modal('hide');

                    let paymentModalResult = document.getElementById('paymentModalResult');
                    let span = document.createElement('span');
                        span.innerHTML = data.success.message;
                    paymentModalResult.querySelector('.modal-title').appendChild(span);
                    $('#paymentModalResult').modal({
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $('#paymentModalResult').modal('show');
                    cursorRestore();
                }, 1000);

            } else if (data.error) {
                alert(data.error.message + " " + data.error.code);
            }
        },
        error: function (err) {
            alert(err);
        }
    });

};

let pm = new PaymentManager();