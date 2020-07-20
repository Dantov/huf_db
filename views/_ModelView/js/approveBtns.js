"use strict";

function Approves()
{
    this.modal = document.querySelector('#approveModal');

    this.approveSketchBtn = document.querySelector('#approveSketchBtn');
    this.approve3DTechBtn = document.querySelector('#approve3DTechBtn');

    this.modalTitle = this.modal.querySelector('.modal-title');
    this.modalBody = this.modal.querySelector('.modal-body');
    this.approveSubmit = this.modal.querySelector('.approveSubmit');

    this.url = '/model-view/?approve=1';
    this.apprData = {
        id: main.getQueryParam('id'),
        approve: '',
    };
    this.init();
}

Approves.prototype.init = function()
{
    let that = this;

    $('#approveModal').on('show.bs.modal', function (e) {
        //debug(e.relatedTarget);
        that.showingModal(e.relatedTarget);
    });
    $('#approveModal').on('hide.bs.modal', function () {
        that.hideModal();
    });

    this.approveSubmit.addEventListener('click',function () {
        that.signModel(this);
    },false);

    debug('Approves init ok!');
};

Approves.prototype.hideModal = function()
{
    this.modalTitle.innerHTML = '';
    this.modalBody.innerHTML = "";
    this.approveSubmit.innerHTML = "";
    this.apprData.approve = '';
};

Approves.prototype.showingModal = function(btn)
{
    let idBtn = btn.id;
    let txt = "";
    switch ( idBtn )
    {
        case "approveSketchBtn":
            txt = '<i class="fas fa-magic"></i>';
            txt += " Худ. Совет - <b><i>" + wsUserData.fio + "</i></b>";

            this.modalTitle.innerHTML = '<i class="fas fa-magic"></i>';
            this.modalBody.innerHTML = "Утвердить этот эскиз в работу?";
            this.approveSubmit.innerHTML = '<i class="fas fa-magic"></i>' + " Утвердить";
            this.apprData.approve = 'approveSketch';
            break;
        case "approve3DTechBtn":
            txt = '<span class="glyphicon glyphicon-education" aria-hidden="true"></span>';
            txt += " Технолог - <b><i>" + wsUserData.fio + "</i></b>";

            this.modalTitle.innerHTML = txt;
            this.modalBody.innerHTML = "<b>Подписать эту 3Д модель?</b>";
            this.approveSubmit.innerHTML = '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>' + " Подписать";
            this.apprData.approve = 'signByTech';
            break;
    }
};

Approves.prototype.signModel = function(btn)
{
    let that = this;
    $.ajax({
        type: 'POST',
        url: that.url,
        data: that.apprData,
        dataType:"json",
        cache: false,
        beforeSend: function() {
            btn.classList.add('disabled');
        },
        success:function(response) {
            debug(response,'response');
            if ( response.done )
            {
                $('#approveModal').modal('hide');
                document.location.reload(true);
            } else if ( response.error )
            {
                alert("Что-то пошло не так! Попробуйте позже.");
            }
        },
        error:function (error) {
            alert("Ошибка, что-то пошло не так! Попробуйте позже.");
            debug(error);
        }
    });
};

let approves = new Approves();