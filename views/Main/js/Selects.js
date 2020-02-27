"use strict";
function Selects() {

    this.sgUL = document.querySelector('#selectedGroup').children[1];
    this.lc = document.querySelector('#loadeding_cont');
    this.selectedElements = []; //здесь хранятся выделенные модели
    this.selectionMode = false;

}
Selects.prototype.checkSelectionMode = function() {
    this.selectionMode = document.querySelector('#selectMode').classList.contains('btnDefActive');

    var that = this;
    this.lc.addEventListener('click', function(event){
        //Достает из замыкания this, теперь === Select а не lc.
        that.selectBox(event);
    }, false);

    if ( this.selectionMode ) this.checkSelectedModels();
};
Selects.prototype.checkSelectedModels = function() {

    var that = this;
    $.ajax({
        url: "controllers/selectionController.php",
        type: "POST",
        data: {
            checkSelectedModels: 1
        },
        dataType: "json",
        success: function (data) {
            for (var key in data) {
                that.selectedElements.push(data[key]);
            }
            console.log(that.selectedElements);
        }
    })
};
Selects.prototype.toggleSelectionMode = function(self) {
    var activateClass = 1;  // 1 - мы включаем режим выделения. 2 - выключаем

    if ( this.selectionMode ) {
        if ( this.selectedElements.length ) {
            var conf = confirm('Есть выделенные элементы! Если отключить, все выделение сбросится. Продолжить?');
            if ( !conf ) return;
        }
        activateClass = 2;
    }

    var that = this;
    $.ajax({
        url: "controllers/selectionController.php",
        type: "POST",
        data: {
            active: activateClass
        },
        dataType: "json",
        success: function (data) {
            if ( data === 1 ) { // on
                that.selectionMode = true;
                document.querySelector('#selectedGroup').classList.remove('hidden');
            }
            if ( data === 2 ) { //off
                that.selectionMode = false;
                that.selectedElements = [];
                let li_arr = that.sgUL.querySelectorAll('li');
                for ( let i = 2; i < li_arr.length; i++ ) {
                    li_arr[i].remove();
                }
                document.querySelector('#selectedGroup').classList.add('hidden');
            }
            self.classList.toggle('btnDefActive');
            that.toggleBoxes();
            console.log(that.selectedElements);
        }
    })
};

Selects.prototype.toggleBoxes = function() {

    var selectBoxes = this.lc.querySelectorAll('.selectionCheck');
    var i;

    if ( this.selectionMode ) {
        for ( i = 0; i < selectBoxes.length; i++ ) {
            selectBoxes[i].classList.remove('hidden');
        }
    } else {
        for ( i = 0; i < selectBoxes.length; i++ ) {
            selectBoxes[i].children[0].children[0].className = "glyphicon glyphicon-unchecked";
            selectBoxes[i].children[1].checked = false;
            selectBoxes[i].classList.add('hidden');
        }
    }
};

Selects.prototype.selectBox = function(event) {

    var click = event.target;
    if ( !click.hasAttribute('checkBoxId') ) return;

    //console.log(click);
    
    var checked = 1;
    if ( !click.checked ) checked = 2;

    var that = this;
    $.ajax({
        url: "controllers/selectionController.php",
        type: "POST",
        data: {
            checkBox: checked,
            modelId: click.getAttribute('modelId'),
            modelName: click.getAttribute('modelName'),
            modelType: click.getAttribute('modelType')
        },
        dataType:"json",
        success:function(obj) {
			
			// var span = click.previousElementSibling.children[0];
			// span.classList.toggle('glyphicon-unchecked');
			// span.classList.toggle('glyphicon-check');
			
            var models = that.selectedElements;
            if ( checked === 1 ) { // добавляем
                models.push(obj);
                // добавляем в меню вверху
                var newLI = document.createElement('li');
                    newLI.setAttribute('data-id',obj.id);
                    newLI.innerHTML = '<a href="../ModelView/index.php?id=' + obj.id + '" >' + obj.name + '</a>';
                that.sgUL.appendChild(newLI);
				
				var span = click.previousElementSibling.children[0];
				span.classList.remove('glyphicon-unchecked');
				span.classList.add('glyphicon-check');
            }

            if ( checked === 2 ) { // удаляем
                let li_arr = that.sgUL.querySelectorAll('li');
                console.log(li_arr);

                for( var i = 0; i < models.length; i++ ) {
                    //console.log('obj.id=' + obj.id);
                    //console.log('models[i].id=' + models[i].id);
                    var j = i+2;
                    if ( obj.id == li_arr[j].getAttribute('data-id') ) li_arr[j].remove();
                    if ( models[i].id == obj.id ) models.splice(i, 1);
                }
				var span = click.previousElementSibling.children[0];
				span.classList.remove('glyphicon-check');
				span.classList.add('glyphicon-unchecked');
            }
            console.log(that.selectedElements);
        }
    })
};

var selects = new Selects();
selects.checkSelectionMode();
