
//-------- ВАЛИДАЦИЯ ФОРМЫ ---------//
function validateForm() {

    if ( document.getElementById('collections_table') )
    {
        let collectionInputs = document.getElementById('collections_table').querySelectorAll('input');
        if ( !collectionInputs.length ) {
            alert( 'Нужно внести хоть одну коллекцию!' );
            return false;
        }
        let collValid = true;
        $.each(collectionInputs, function (index, input) {
            if ( !input.value )
            {
                input.scrollIntoView();
                alert( 'Выберите коллекцию!' );
                return collValid=false;
            }
        });
        if ( !collValid ) return false;
    }


    if ( document.getElementById('author') && !document.getElementById('author').value )
    {
        alert( 'Нужно указать Автора!' );
        return false;
    }
    if ( document.getElementById('modeller3d') && !document.getElementById('modeller3d').value )
    {
        alert( 'Нужно указать 3D-моделлера!' );
        return false;
    }
    if ( document.getElementById('modelType') && !document.getElementById('modelType').value )
    {
        alert( 'Нужно указать Тип модели!' );
        return false;
    }

    let modelWeight = document.getElementById('modelWeight');
    if ( modelWeight )
    {
        if ( document.getElementById('modelWeight') && !document.getElementById('modelWeight').value )
        {
            alert( 'Нужно указать Вес модели!' );
            return false;
        }
        if ( modelWeight.value < 0 || modelWeight.value > 1000 )
        {
            alert( 'Вес модели указан не верно!' );
            return false;
        }
    }

    if ( document.getElementById('metals_table') )
    {
        let materials = document.getElementById('metals_table').querySelectorAll('tr');
        let matsArr = [];
        $.each(materials, function (i, tr) {
            if ( !tr.classList.contains('hidden') ) matsArr.push(tr);
        });

        if ( !matsArr.length ) {
            alert( 'Нужно внести хоть один материал!' );
            return false;
        }
        if ( matsArr[0] )
        {
            let inputs = matsArr[0].querySelectorAll('input');
            let matsValid = false;
            $.each(inputs, function (index, input) {
                if ( input.value ) return matsValid=true;
            });
            if ( !matsValid ) {
                matsArr[0].scrollIntoView();
                alert( 'Заполните строку материала!' );
                return false;
            }
        }
    }

    if ( document.getElementById('picts') )
    {
        let images = document.getElementById('picts').querySelectorAll('.image_row');
        let imgFor = document.getElementById('imgFor');
        if ( !images.length ) {
            imgFor.innerHTML = 'Нужно внести хоть одну картинку!';
            imgFor.classList.remove('hidden');
            imgFor.scrollIntoView();
            alert( 'Нужно внести хоть одну картинку!' );
            return false;
        } else {
            imgFor.classList.add('hidden');
        }
    }


    return true;
}

//-------- ОТПРАВКА ФОРМЫ ---------//
function submitForm() {
    if ( !validateForm() ) return null;

    let formData = new FormData( document.getElementById('addform') );
    formData.append('userName',userName);
    formData.append('tabID',tabName);

    if ( handlerFiles )
    {
        $.each(handlerFiles.getFiles(), function (i, file) {
            formData.append('UploadImages[]',file);
        });
    }

    let modal = $('#modalResult');
    let modalButtonsBlock = document.getElementById('modalResult').querySelector('.modalButtonsBlock');
    let status = document.querySelector('#modalResultStatus');
    let back = modalButtonsBlock.querySelector('.modalProgressBack');
    let edit = modalButtonsBlock.querySelector('.modalResultEdit');
    let show = modalButtonsBlock.querySelector('.modalResultShow');

    $('#modalResult').iziModal('open');
    let xhr;

    xhr = $.ajax({
        url: '/add-edit/formdata',
        type: 'POST',
        //dataType: "html", //формат данных
        //dataType: "json", // не работает с new FormData object
        //data: $("#addform").serialize(),
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function()
        {
            debug(xhr);

            modal.iziModal('setTitle', 'Идёт отправление данных на сервер.');
            modal.iziModal('setHeaderColor', '#858172');

            //xhr.abort();
            //if ( xhr.readyState === 0 ) debug('aborted');
        },
        success:function(resp)
        {
            resp = JSON.parse(resp);
            debug(resp);

            modal.iziModal('setIcon', 'glyphicon glyphicon-floppy-saved');
            modal.iziModal('setHeaderColor', '#edaa16');
            modal.iziModal('setTitle', 'Сохранение прошло успешно!');
            let title = '';
            if ( resp.isEdit == true )
            {
                title = 'Данные модели <b>'+ resp.number_3d + ' - ' + resp.model_type+'</b> изменены!';
            } else {
                title = 'Новая модель <b>'+ resp.number_3d + ' - ' + resp.model_type+'</b> добавлена!';
            }

            status.innerHTML = title;

            back.href = '/main/';
            show.href = '/model-view/?id=' + resp.id;
            let href  = '/add-edit/?id=' + resp.id + '&component=2';

            edit.onclick = function() {
                document.location.href = href;
            };

            back.classList.remove('hidden');
            edit.classList.remove('hidden');
            show.classList.remove('hidden');

        },
        error: function(error) { // Данные не отправлены
            modal.iziModal('setTitle', 'Ошибка отправки! Попробуйте снова.');
            modal.iziModal('setHeaderColor', '#95ffb1');

            edit.classList.remove('hidden');

            debug(error);
        }
    });
}
//-------- END ОТПРАВКА ФОРМЫ ---------//