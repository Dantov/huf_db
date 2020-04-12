"use strict";
Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
    this.parentElement.removeChild(this);
};

const _HOSTNAME_ = "127.0.0.1";
//const _HOSTNAME_ = "192.168.0.245";
const _URL_ = document.location.origin; // http://huf.db
const _DIR_ = document.location.href.split('/')[3]; // views
const _ROOT_ = _URL_ + '/'; //http://localhost/HUF_DB_Dev/

/**
 * @type {string}
 * _CONTROLLER_ - страница где находимся
 */
const _CONTROLLER_ = document.location.href.split('/')[4]; // AddEdit/Main/ModelView
let approvedControllers = [
    'Main',
    'ModelView',
    'Nomenclature',
];
const _PNSHOW_ = approvedControllers.includes(_CONTROLLER_);

let main = '';

// экземпляр класса pushNotice
let pushNotice;
//здесь хранятся показанные уведомления
// общие для всех оюъектов PushNotice
//let showedNotice = [];

// debug(_CONTROLLER_, "_CONTROLLER_");
// debug(_URL_, "_URL_");
// debug(_DIR_, "_DIR_");
// debug(_ROOT_, "_ROOT_");

function debug(arr, str)
{
    if ( str )
    {
        if ( typeof arr === 'object' ) {
            console.info(str + ': ');
            console.log(arr);
        } else {
            console.log(str + ': ' + arr);
        }

    } else {
        console.log(arr);
    }
}

