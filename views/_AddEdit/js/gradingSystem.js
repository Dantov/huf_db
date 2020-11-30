"use strict";

function GradingSystem()
{
    this.modal = document.querySelector('#grade3DModal');
    this.modalRepair = document.querySelector('#grade3DRepair_Modal');

    //this.table_3Dmodeller = document.querySelector('#pricesData .modeller3D');
    this.table_modellerJewPrices = document.querySelector('#pricesData .modellerJewPrices');

    this.table = false;
    this.whichModal = null;


    this.init();
    this.deleteGS3DRow();
}

GradingSystem.prototype.init = function()
{
	let that = this;

	if ( this.modal )
	{
        this.modal.querySelector('.add3DGrade').addEventListener('change', function() {
            that.selectGrade3DChange(this);
        });
	}

    if ( this.modalRepair )
    {
        this.modalRepair.querySelector('.add3DRepairGrade').addEventListener('change', function() {
            that.selectGrade3DChange(this);
        });
    }

	if ( this.table_modellerJewPrices )
	{
        let addButton = document.querySelector('#pricesData .addModellerJewPrice');
        if (addButton)
		{
            addButton.addEventListener('click', function() {
                that.addModellerJewPrice();
                this.classList.add('hidden');
            });
		}
	}

    $('#grade3DRepair_Modal').on('show.bs.modal', function (e) {
        debug(e);
        that.table = e.relatedTarget.parentElement.parentElement.nextElementSibling.children[1];
        debug(that.table);
    });

    $('#grade3DModal').on('show.bs.modal', function (e) {
        that.table = e.relatedTarget.parentElement.nextElementSibling.children[1];
        //debug(e);
    });
    $('#grade3DModal').on('hidden.bs.modal', function (e) {

        //let button = e.relatedTarget;
        that.table = false;
    });

    debug('GradingSystem3D init ok!');
};

GradingSystem.prototype.addModellerJewPrice = function()
{

    let input_value = document.createElement('input');
		input_value.setAttribute('name', 'modellerJewPrice[value]');
		input_value.setAttribute('class', 'form-control');
		input_value.setAttribute('value', 0);
		input_value.setAttribute('type', 'number');
		input_value.value = 0;

    let newRow = document.querySelector('.gs_protoModJewRow').cloneNode(true);
    	newRow.removeAttribute('class');
    	newRow.children[1].innerHTML = "Доработка модели";
    	newRow.children[2].appendChild(input_value);

    let last = this.table_modellerJewPrices.querySelector('.t-total');

    this.table_modellerJewPrices.insertBefore(newRow,last);

};

GradingSystem.prototype.selectGrade3DChange = function(select)
{

    let gsID = +select.value;
    let whichNames = {
        add3DGrade:'ma3Dgs',
        add3DRepairGrade:'repairs[jew][prices]'
    };
    let name = '';
    $.each(whichNames, function(cl, n){
        if ( select.classList.contains(cl) ) {
            name = n;
            return null;
        }
    });

    // проверим если есть такая оценка
    let hasID = false;
    $.each(this.table.querySelectorAll('tr'), function(i, tr){
        let trID = +tr.getAttribute('data-gradeID');
        if ( trID === gsID ) {
            hasID = true;
            $('#grade3DRepair_Modal').modal('hide');
            $('#grade3DModal').modal('hide');
            return;
        }
    });
    if ( hasID ) return;

    let option = select.options[select.options.selectedIndex];
    let workName = option.getAttribute('data-workName');
    let price = option.getAttribute('data-points') * 100;
    let description = option.getAttribute('title');

    $('#grade3DModal').modal('hide');
    $('#grade3DRepair_Modal').modal('hide');

    // ID оценки из таблицы Grading_system
    let inputID = document.createElement('input');
    inputID.setAttribute('hidden', '');
    inputID.setAttribute('value', gsID);
    inputID.setAttribute('name', name + '[gs3Dids][]');
    inputID.classList.add('hidden');
    inputID.value = gsID;

    // ID оценки из таблицы model_prices
    let inputIDmp = document.createElement('input');
    inputIDmp.setAttribute('hidden', '');
    inputIDmp.setAttribute('value', '');
    inputIDmp.setAttribute('name', name + '[mp3DIds][]');
    inputIDmp.classList.add('hidden');
    inputIDmp.value = '';

    // Сама оценка
    let inputPoints = document.createElement('input');
    if ( +price.toFixed() !== 0 )
    {
        inputPoints.setAttribute('hidden', '');
        inputPoints.classList.add('hidden');
    }
    inputPoints.setAttribute('value', +price.toFixed());
    inputPoints.value = +price.toFixed();
    inputPoints.setAttribute('name', name + '[gs3Dpoints][]');
    inputPoints.classList.add('form-control');

    // Для Тултипа
    let div = document.createElement('div');
    div.classList.add('cursorPointer', 'lightUpGSRow');
    div.setAttribute('data-toggle','tooltip');
    div.setAttribute('data-placement','bottom');
    div.setAttribute('title',description);
    div.innerHTML = workName;

    let newRow = document.querySelector('.gs_proto3DRow').cloneNode(true);
    newRow.setAttribute('data-gradeID',gsID);
    newRow.removeAttribute('class');
    if ( +price.toFixed() !== 0 )
        newRow.children[2].innerHTML = +price.toFixed();

    let totalRow = gs.table.querySelector('.t-total');
    let insertedRow = gs.table.insertBefore(newRow, totalRow);
    insertedRow.children[1].appendChild(div);
    insertedRow.children[2].appendChild(inputPoints);
    insertedRow.children[3].appendChild(inputIDmp);
    insertedRow.children[3].appendChild(inputID);
    let dellButton = insertedRow.children[4].querySelector('.ma3DgsDell');
    this.setEventListener(dellButton,name);
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

};

/**
 * накинем обработчики на уже существующие кнопки удаления
 */
GradingSystem.prototype.deleteGS3DRow = function()
{
	let that = this;
	if ( this.table )
	{
        let dellButtons = this.table.querySelectorAll('.ma3DgsDell');
        $.each(dellButtons, function(i, button) {
            that.setEventListener(button);
        });
	}
};

/**
 * Создадим инпуты с айдишниками на удаление
 * @param button
 * @param name
 */
GradingSystem.prototype.setEventListener = function( button, name )
{
    if ( !name ) name = 'ma3Dgs';
	let that = this;
	button.addEventListener('click', function(event) {
		let id = button.parentElement.previousElementSibling.children[0].value;
		if ( !id ) return;
		let input = document.createElement('input');
			input.setAttribute('hidden', '');
			input.setAttribute('value', id);
			input.setAttribute('name', name + '[toDell][]');
			input.classList.add('hidden');

		let tTotal = that.table.querySelector('.t-total');
			tTotal.children[0].appendChild(input);
	}, false);
};

let gs = new GradingSystem();

