"use strict";
Node.prototype.remove = function() {  // - полифил для elem.remove(); document.getElementById('elem').remove();
    this.parentElement.removeChild(this);
};

const _HOSTNAME_ = _WORK_PLACE_ ? "192.168.0.245" : "127.0.0.1";
//const _HOSTNAME_ = "192.168.0.245";
const _URL_ = document.location.origin; // http://huf.db
const _DIR_ = document.location.href.split('/')[3]; // views
const _ROOT_ = _URL_ + '/'; //http://localhost/HUF_DB_Dev/

// debug(_URL_,'_URL_');
// debug(_DIR_,'_DIR_');
// debug(_ROOT_,'_ROOT_');

/**
 * @type {string}
 * _CONTROLLER_ - страница где находимся
 */
const _CONTROLLER_ = _DIR_;//document.location.href.split('/')[4]; // AddEdit/Main/ModelView

//debug(_CONTROLLER_,'_CONTROLLER_');
let approvedControllers = [
    'main',
    'model-view',
    'nomenclature',
];
const _PNSHOW_ = approvedControllers.includes(_CONTROLLER_);

let main = '';

// экземпляр класса pushNotice
let pushNotice;

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
function formatDate(date) {
    let dd = date.getDate();
    if (dd < 10) dd = '0' + dd;

    let mm = date.getMonth() + 1;
    if (mm < 10) mm = '0' + mm;

    let yy = date.getFullYear();

    return dd + '.' + mm + '.' + yy;
}
function isInteger(num) {
    return (num ^ 0) === num;
}
function copyInnerHTMLToClipboard(element)
{
    window.getSelection().removeAllRanges();
    let range = document.createRange();
    range.selectNode(element);
    window.getSelection().addRange(range);
    try {
        // Now that we've selected the anchor text, execute the copy command
        let successful = document.execCommand('copy');
        let msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copy: '+ element.innerHTML + ' - ' + msg);
        setTimeout(function() {
            window.getSelection().removeAllRanges();
        },300);
    } catch(err) {
        debug(err,'Oops, unable to copy');
    }
    // Remove the selections - NOTE: Should use
    // removeRange(range) when it is supported
}
function redirect(url) {
    if ( !url ) return;
    if ( typeof url === 'string' )
    {
        document.location.href = url;
    }
    return false;
}
function reload( full ) 
{
    document.location.reload( full );
}

function cursorSet(cursorStyle, elem) {
    /*
    auto        move           no-drop      col-resize
    all-scroll  pointer        not-allowed  row-resize
    crosshair   progress       e-resize     ne-resize
    default     text           n-resize     nw-resize
    help        vertical-text  s-resize     se-resize
    inherit     wait           w-resize     sw-resize
    */
    let cursorStyles = [];
    if (elem)
    {
        if (elem.style) elem.style.cursor = cursorStyle;
    } else if (document) {
        if (document.documentElement)
            if (document.documentElement.style)
                document.documentElement.style.cursor = cursorStyle;
    }
}
function cursorHide()
{
    document.documentElement.style.cursor = "none";
}
function cursorRestore(elem)
{
    let cursorStyles = [];
    if (elem)
    {
        if (elem.style) elem.style.cursor = "";
    } else if (document) {
        if (document.documentElement)
            if (document.documentElement.style)
                document.documentElement.style.cursor = "";
    }
}