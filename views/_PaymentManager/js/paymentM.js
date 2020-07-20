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
        /*
        switch ( button.getAttribute('data-prices') )
        {
            case "allInModel": pm.getPricesData(button);    break;
            case     "single": pm.getPricesData(button);    break;
            case        "all": pm.getPricesAllData(button); break;
        }
        */
    });

    $('#paymentModal').on('hidden.bs.modal', function (e) {
        pm.modalBody.children[0].innerHTML = "";
        pm.modal.children[0].classList.remove("modal-lg");
        pm.priceIDs = [];
    });

    this.payButton.addEventListener('click', function () {
        pm.payPrices();
    },false);

    debug('PaymentManager init ok!');
};

PaymentManager.prototype.getPriceData = function(button) 
{
    let that = this;

    $.ajax({
        url: "/payment-manager/?getPrice=1",
        type: 'POST',
        data: {
            priceID: button.getAttribute('data-priceID'),
        },
        dataType:"json",
        success:function(modelPrice) {
            debug(modelPrice);

            /*
            if ( +modelPrice.status === 1 )
            {
                that.priceIDs.push(modelPrice.pID);
                let newModelRow = document.querySelector('.PM_protoModel').cloneNode(true);

                newModelRow.classList.remove('hidden','PM_protoModel');
                let vc = modelPrice.vendorCode ? " / " + modelPrice.vendorCode : "";
                newModelRow.querySelector('.panel-heading').innerHTML = modelPrice.number_3d + vc + " - " + modelPrice.modelType;
                newModelRow.querySelector('.panel-body').children[0].src = modelPrice.imgName;

                let ul = newModelRow.querySelector('.list-group');
                let span = document.createElement('span');
                    span.innerHTML = modelPrice.costName + ": <b>" + modelPrice.value + "грн.</b> - " + modelPrice.fio + " " + modelPrice.date;
                ul.children[0].appendChild(span);
                that.modalBody.appendChild(newModelRow);

            } else if ( modelPrice.error ) {
                switch ( modelPrice.error )
                {
                    case 321: alert("Ошибка выбора стоимости."); break;
                }
            }
            */
        }
    });
};
PaymentManager.prototype.getPricesData = function(button)
{
    let that = this;

    $.ajax({
        url: "/payment-manager/?getPrices",
        type: 'POST',
        data: {
            priceIDs: button.getAttribute('data-priceID'),
            posID: button.getAttribute('data-posID'),
        },
        dataType:"json",
        success:function(modelPrices) {
            if ( modelPrices.error )
            {
                debug(modelPrices.error.code);
                debug(modelPrices.error.message);
                return;
            }
            debug(modelPrices);

            let newModelRow = document.querySelector('.PM_protoModel').cloneNode(true);

            newModelRow.classList.remove('hidden','PM_protoModel');
            let vc = modelPrices[0].vendorCode ? " / " + modelPrices[0].vendorCode : "";
            newModelRow.querySelector('.panel-heading').innerHTML = modelPrices[0].number_3d + vc + " - " + modelPrices[0].modelType;
            newModelRow.querySelector('.panel-body').children[0].src = modelPrices[0].imgName;

            let tValue = 0;
            let ul = newModelRow.querySelector('.list-group');

            $.each(modelPrices, function (i, price) {
                that.priceIDs.push(price.pID);
                tValue += +price.value;

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
                let div = document.createElement('div');
                    div.classList.add('pt-1');
                    div.appendChild(accruedLabel);
                    div.appendChild(notPayedLabel);

                newLi.appendChild(span);
                newLi.appendChild(spanFIO);
                newLi.appendChild(div);

                ul.appendChild(newLi);
            });
            let footer = newModelRow.querySelector('.panel-footer');
                footer.innerHTML = "Всего: " + tValue + "грн.";

            let paidType = button.getAttribute('data-prices');
            let topText = "";
            switch (paidType)
            {
                case "allInModel": topText = "Оплатить всё в модели"; break;
                case     "single": topText = "Оплатить <i>" + modelPrices[0].costName + "</i>"; break;
                case        "all":  topText = "Оплатить всё"; break;
            }
            that.modal.querySelector('.modal-title').innerHTML = topText;
            that.modalBody.appendChild(newModelRow);
        },
        error:function (e) {
            debug(e);
        }
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
            debug(models);

            let totalValue = 0;
            $.each(models, function (i, model) {

                let newModelRow = document.querySelector('.PM_protoModel').cloneNode(true);

                newModelRow.classList.remove('hidden','PM_protoModel');
                let vc = model.vendorCode ? " / " + model.vendorCode : "";
                newModelRow.querySelector('.panel-heading').innerHTML = model.number_3d + vc + " - " + model.modelType;
                newModelRow.querySelector('.panel-body').children[0].src = model.imgName;

                let tMValue = 0;
                let ul = newModelRow.querySelector('.list-group');

                $.each(model.prices, function (i, price) {
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
                    let div = document.createElement('div');
                    div.classList.add('pt-1');
                    div.appendChild(accruedLabel);
                    div.appendChild(notPayedLabel);

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
                let lg = "col-lg-12";
                switch (paidType)
                {
                    case "allInModel": topText = "Оплатить всё в модели"; break;
                    case     "single": topText = "Оплатить <i>" + models[0].prices[0].costName + "</i>"; break;
                    case        "all":
                        topText = "Оплатить всё - <b>" + totalValue + "</b>грн.";
                        that.modal.children[0].classList.add("modal-lg");
                        lg = 'col-lg-6';
                        break;
                }
                that.modal.querySelector('.modal-title').innerHTML = topText;
                let col = document.createElement('div');
                    col.classList.add('col-sm-12', lg);
                    col.appendChild(newModelRow);

                that.modalBody.children[0].appendChild(col);

            });

        },
    });

};

PaymentManager.prototype.payPrices = function()
{
    let that = this;
    $.ajax({
        url: "/payment-manager/?payPrice=1",
        type: 'POST',
        data: {
            prices: that.priceIDs,
        },
        dataType:"json",
        success:function(data) {
            if ( +data.success === 610 ) {
                //notPayed paySuccess
                that.modal.querySelector('.notPayed').classList.add('hidden');
                that.modal.querySelector('.paySuccess').classList.remove('hidden');
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