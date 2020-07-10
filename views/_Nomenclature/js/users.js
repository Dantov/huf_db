"use strict";

function UsersEditing()
{
    this.modal = document.querySelector('#userEditModal');
    this.editUser_ID = null;
    this.addUser = null;

    this.init();
}

UsersEditing.prototype.init = function(button)
{
    let that = this;

    $('#userEditModal').on('show.bs.modal', function (e) {

        that.editUser_ID = +e.relatedTarget.getAttribute('data-id');

        if ( that.editUser_ID )
        {
            that.showUserEditModal(e);
        } else {
            that.userAddModal();
        }

    });

    $('#userEditModal').on('hidden.bs.modal', function (e) {
        //let button = e.relatedTarget;
        that.hideUserEditModal();
    });

    $('#userOKModal').on('show.bs.modal', function (e) {
        let modal = e.target;
        modal.querySelector('.modal-body').innerHTML = "Данные внесены успешно";
    });

    this.editUserButtonInit();
    this.addWCList();

    this.userDellInit();

    debug('UserEdit init ok!');
};

UsersEditing.prototype.addWCList = function()
{
    let that = this;
    document.querySelector('.addWCList').addEventListener('click',function (event) {
        if ( !event.target.classList.contains('addWC') ) return;

        let a = event.target;
        let wcID = a.getAttribute('data-wcID');
        let wcName = a.innerHTML;

        let newWcLi = document.querySelector('.wcListProto').cloneNode(true);
            newWcLi.children[0].innerHTML = wcName;
            newWcLi.children[1].value = wcID;
            newWcLi.classList.remove('wcListProto','hidden');

        let addedWCLi = document.querySelector('.wcList').appendChild(newWcLi);
            addedWCLi.querySelector('.deleteWCListItem').addEventListener('click', function () {
                that.deleteWCListItem(this);
            });
    });
};

UsersEditing.prototype.deleteWCListItem = function( buttonDell )
{
    buttonDell.parentElement.remove();
};

UsersEditing.prototype.showUserEditModal = function(e)
{

    let submitUserData = this.modal.querySelector('.submitUserData');
        submitUserData.classList.remove('disabled');
        submitUserData.children[1].innerHTML = 'Изменить';
    this.modal.querySelector('#userEditModalLabel').innerHTML='Редактировать данные пользователя';

    if ( this.modal.querySelector('.deleteUser') )
    {
        this.modal.querySelector('.deleteUser').classList.remove('hidden');
    }


    let rowID = this.editUser_ID;
    let that = this;

    $.ajax({
        url: "/nomenclature/editUser",
        type: 'POST',
        data: {
            userShow: 1,
            showUserID: rowID,
        },
        dataType:"json",
        success:function(data) {
            debug(data);
            that.editUser_ID = data.id;

            let fio = [];
            if ( data.fullFio )
            {
                fio = data.fullFio.split(' ');
            } else {
                fio[0] = data.fio;
            }

            that.modal.querySelector('#userName').innerHTML = data.fio;
            that.modal.querySelector('#userFirstName').setAttribute('value', fio[0]);
            that.modal.querySelector('#userFirstName').value = fio[0];

            that.modal.querySelector('#userSecondName').setAttribute('value', fio[1] ? fio[1] : '');
            that.modal.querySelector('#userSecondName').value = fio[1] ? fio[1] : '';

            that.modal.querySelector('#userThirdName').setAttribute('value', fio[2] ? fio[2] : '');
            that.modal.querySelector('#userThirdName').value = fio[2] ? fio[2] : '';

            that.modal.querySelector('#userLog').setAttribute('value', data.login);
            that.modal.querySelector('#userLog').value = data.login;

            that.modal.querySelector('#userPass').setAttribute('value', data.pass);
            that.modal.querySelector('#userPass').value = data.pass;

            if ( data.location )
            {
                let wcListUL = document.querySelector('.wcList');
                let locations = data.location.split(',');
                $.each(workingCentersDB, function(name, wcList)
                {
                    $.each(wcList, function(iter, wc)
                    {
                        if ( locations.includes(iter) )
                        {
                            let newWcLi = document.querySelector('.wcListProto').cloneNode(true);
                            newWcLi.children[0].innerHTML = wc.name + ": " + wc.descr;
                            newWcLi.children[1].value = iter;
                            newWcLi.classList.remove('wcListProto','hidden');
                            let addedWCLi = wcListUL.appendChild(newWcLi);
                            addedWCLi.querySelector('.deleteWCListItem').addEventListener('click', function () {
                                that.deleteWCListItem(this);
                            });
                        }
                    });
                });
            }

            if ( data.access )
            {
                let acc = {
                    mt_admin: 1,
                    mt_moder: 122,
                    mt_modell: 2,
                    mt_modellHM: 5,
                    mt_oper: 3,
                    mt_prod: 4,
                    mt_tech: 7,
                    mt_pdo: 8,
                };
                let userMTProd = document.querySelector('#userMTProd');
                $.each(acc, function(name, num)
                {
                    if ( +num === +data.access )
                    {
                        $.each(userMTProd.options, function(i, option)
                        {
                            if ( option.value === name ) option.selected = true;
                        });
                    }
                });
            }

        }
    });
};
UsersEditing.prototype.editUserButtonInit = function()
{
    let that = this;
    this.modal.querySelector('.submitUserData').addEventListener('click', function() {

        let button = this;

        let editUser_form = new FormData( document.getElementById('editUser_form') );
        if ( that.addUser )
        {
            editUser_form.append('userAdd', '1');
        }  else {
            editUser_form.append('editUser_ID', that.editUser_ID);
            editUser_form.append('userEdit', '1');
        }

        $.ajax({
            url: "/nomenclature/editUser",
            type: 'POST',
            data: editUser_form,
            processData: false,
            contentType: false,
            beforeSend: function() {
                button.classList.add('disabled');
            },
            success:function(data) {
                data = JSON.parse(data);
                debug(data);

                $('#userEditModal').modal('hide');
                if ( data.success )
                {
                    if ( data.success != -1 ) $('#userOKModal').modal('show');

                } else if ( data.error ) {
                    let text = "";
                    switch ( data.error  )
                    {
                        case 345: text = "Поле Фамилия должно быть заполнено"; break;
                        case 348: text = "Поле Логин должно быть заполнено"; break;
                        case 349: text = "Поле Пароль должно быть заполнено"; break;
                        case 346: text = "Ошибка! Нет такого пользователя."; break;

                        case 347: text = "Ошибка! Попробуйте позже."; break;
                    }
                    alert(text);
                }
            }
        });
    });
};
UsersEditing.prototype.hideUserEditModal = function()
{
    debug('hideUserEditModal');
    this.editUser_ID = 0;
    this.addUser = false;
    document.querySelector('.wcList').innerHTML = '';
    this.modal.querySelector('.submitUserData').classList.remove('disabled');
    if ( this.modal.querySelector('.deleteUser') )
    {
        this.modal.querySelector('.deleteUser').classList.remove('disabled');
    }
};

UsersEditing.prototype.userAddModal = function()
{
    debug('userAddModal');
    this.editUser_ID = 0;

    this.modal.querySelector('#userName').innerHTML = "ФИО";
    this.modal.querySelector('#userFirstName').value = "";
    this.modal.querySelector('#userSecondName').value = '';
    this.modal.querySelector('#userThirdName').value = '';
    this.modal.querySelector('#userLog').value = "";
    this.modal.querySelector('#userPass').value = "";
    this.modal.querySelector('.wcList').innerHTML = '';

    this.modal.querySelector('.submitUserData').children[1].innerHTML = 'Добавить';
    this.modal.querySelector('#userEditModalLabel').innerHTML='Добавить Нового Пользователя';

    this.addUser = true;

    if ( this.modal.querySelector('.deleteUser') )
    {
        this.modal.querySelector('.deleteUser').classList.add('hidden');
    }
};
UsersEditing.prototype.userDellInit = function()
{

    if ( this.modal.querySelector('.deleteUser') )
    {
        let that = this;
        this.modal.querySelector('.deleteUser').addEventListener('click', function() {
            let button = this;
            if ( confirm('Удалить Пользователя безвозвратно?') )
            {
                $.ajax({
                    url: "/nomenclature/editUser",
                    type: 'POST',
                    data: {
                        userDell: 1,
                        userID: that.editUser_ID,
                    },
                    beforeSend: function() {
                        button.classList.add('disabled');
                        that.modal.querySelector('.submitUserData').classList.add('disabled');
                    },
                    success:function(data) {
                        data = JSON.parse(data);
                        debug(data);

                        $('#userEditModal').modal('hide');
                        let text = "";
                        if ( data.success )
                        {
                            text = "Пользователь Удален";
                            //if ( data.success != -1 ) $('#userOKModal').modal('show');

                        } else if ( data.error ) {

                            switch ( data.error  )
                            {
                                case 347: text = "Ошибка! Попробуйте позже."; break;
                            }
                        }
                        alert(text);
                        document.location.reload(true);
                    }
                });
            }

        });
    }

};


let usersEditing = new UsersEditing();