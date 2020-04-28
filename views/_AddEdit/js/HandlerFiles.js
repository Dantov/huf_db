function HandlerFiles( dropArea, button, filterTypes )
{
    this.dropArea = dropArea;
    this.button = button;

    this.fileTypes = Array.isArray(filterTypes) ? filterTypes : [];
    this.fileBuffer = []; //здесь хранятся загруженные файлы. это массив файлов, не FileList

    this.init();
}

HandlerFiles.prototype.init = function() {

    let self = this;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        self.dropArea.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        self.dropArea.addEventListener(eventName, function () {
            self.highlight(this);
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        self.dropArea.addEventListener(eventName, function () {
            self.unhighlight(this);
        }, false);
    });

    this.dropArea.addEventListener('drop', function(e) {
        let dt = e.dataTransfer;
        let files = dt.files; // FileList
        self.handleFiles(files);
    }, false);

    this.button.addEventListener('click', function() {

        let typesStr = self.fileTypes.join(',');
        let fileInput = document.createElement('input');

        fileInput.setAttribute('type','file');
        fileInput.setAttribute('multiple','');
        fileInput.setAttribute('accept',typesStr);

        fileInput.addEventListener('change',function () {
            self.handleFiles(this.files);
        });

        fileInput.click();
    }, false);

    debug('HandlerFiles Init(ok)');

};

HandlerFiles.prototype.highlight = function(dropArea)
{
    dropArea.classList.add('highlight');
};

HandlerFiles.prototype.unhighlight = function(dropArea)
{
    dropArea.classList.remove('highlight');
};

HandlerFiles.prototype.handleFiles = function(files) {

    let self = this;

    Array.prototype.push.apply( this.fileBuffer, files );
    files = [...files];
    files.forEach(function (file)
    {
        debug(file.type);
        if ( !self.fileTypes.includes(file.type) ) return;
        self.previewFile(file);
    });

    // удаляем файлы из Буффера, не соотвсетст. заданным типам
    for ( let i = 0; i < this.fileBuffer.length; i++ )
    {
        if ( !this.fileTypes.includes(this.fileBuffer[i].type) )
        {
            this.fileBuffer.splice(i, 1);
            --i;
        }
    }

    debug(this.fileBuffer);
};

/*
 * этот метод нужно реализовать
 */
HandlerFiles.prototype.previewFile = function(file) {

    let self = this;

    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = function() {

        let imgRow = document.getElementById('proto_image_row').cloneNode(true);
            imgRow.removeAttribute('id');
            imgRow.setAttribute('fileId',file.lastModified);
            imgRow.classList.add('image_row');
            imgRow.classList.remove('hidden');

        imgRow.querySelector('.img_dell').children[0].addEventListener('click',function () {
            self.removeImg(this);
        });
        imgRow.querySelector('select').addEventListener('change',function () {
            self.onSelect(this);
        });

        // в прототипе строки, в HTML, нет аттрибутов name
        // чтобы прототип строки не отправлялся вместе с формой, добавим его в JS
        let img_inputs = imgRow.querySelector('.img_inputs');
        img_inputs.children[0].setAttribute('name','image[id][]');
        img_inputs.children[0].setAttribute('value','');
        img_inputs.children[1].setAttribute('name','image[imgFor][]');

        let img = imgRow.getElementsByTagName('img');
            img[0].src = reader.result;

        document.getElementById('picts').insertBefore(imgRow, self.dropArea.parentElement);
    }
};
HandlerFiles.prototype.onSelect = function(self)
{
    let options = self.options;
    let optText;
    for ( let opt in options )
    {
        if ( options[opt].selected && options[opt].value === 27 ) return;
        if ( options[opt].selected ) optText = options[opt].text;
    }

    let selects = $("select[name*='image[imgFor][]']");
    for ( let key in selects )
    {
        let select = selects[key];
        if ( select === self ) continue;

        let options = select.options;

        // Поик 'Нет' в текущем селекте
        let no;
        for ( let opt in options )
        {
            if (  +options[opt].value === 27 )
            {
                no = options[opt];
                break;
            }
        }

        // Ищем совпадения по статусам картинок. Может быть только 1 уникальный
        for ( let opt in options )
        {
            if ( options[opt].selected && options[opt].text === optText )
            {
                options[opt].selected = false;
                no.selected = true;
                //debug(options[opt].text);
                break;
            }
        }
    }

    //debug(self.options);
    //debug(selects);
};

HandlerFiles.prototype.removeImg = function(self)
{

    let toDell = self.parentElement.parentElement.parentElement.parentElement;

    for ( let i = 0; i < this.fileBuffer.length; i++ )
    {
        if ( this.fileBuffer[i].lastModified === +toDell.getAttribute('fileId') )
        {
            toDell.remove();
            this.fileBuffer.splice(i, 1);
            break;
        }
    }
    debug(this.fileBuffer);
};
HandlerFiles.prototype.getFiles = function()
{
    return this.fileBuffer;
};

let handlerFiles = new HandlerFiles(
    document.getElementById('drop-area'),
    document.getElementById('addImageFiles'),
    ["image/jpeg", "image/png", "image/gif"]
);
