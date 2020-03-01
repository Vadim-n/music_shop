// _mutation
// http://dom.spec.whatwg.org/#mutation-method-macro
// function _mutation(nodes) { // eslint-disable-line no-unused-vars
//     if (!nodes.length) {
//         throw new Error('DOM Exception 8');
//     } else if (nodes.length === 1) {
//         return typeof nodes[0] === 'string' ? document.createTextNode(nodes[0]) : nodes[0];
//     } else {
//         var
//             fragment = document.createDocumentFragment(),
//             length = nodes.length,
//             index = -1,
//             node;
//
//         while (++index < length) {
//             node = nodes[index];
//
//             fragment.appendChild(typeof node === 'string' ? document.createTextNode(node) : node);
//         }
//
//         return fragment;
//     }
// }
//
// (function () {
//     if (!Element.prototype.append) {// Element.prototype.append
//         Document.prototype.append = Element.prototype.append = function append() {
//             this.appendChild(_mutation(arguments));
//         };
//     }
//
// // Element.prototype.before
//     if (!Element.prototype.before) {
//         Document.prototype.before = Element.prototype.before = function before() {
//             if (this.parentNode) {
//                 this.parentNode.insertBefore(_mutation(arguments), this);
//             }
//         };
//     }
// })();
//
// // from:https://github.com/jserz/js_piece/blob/master/DOM/ChildNode/remove()/remove().md
// // from:https://github.com/jserz/js_piece/blob/master/DOM/ChildNode/remove()/remove().md
// (function() {
// if (!Array.prototype.includes) {
//     Object.defineProperty(Array.prototype, 'includes', {
//         value: function(searchElement, fromIndex) {
//
//             if (this == null) {
//                 throw new TypeError('"this" is null or not defined');
//             }
//
//             // 1. Let O be ? ToObject(this value).
//             var o = Object(this);
//
//             // 2. Let len be ? ToLength(? Get(O, "length")).
//             var len = o.length >>> 0;
//
//             // 3. If len is 0, return false.
//             if (len === 0) {
//                 return false;
//             }
//
//             // 4. Let n be ? ToInteger(fromIndex).
//             //    (If fromIndex is undefined, this step produces the value 0.)
//             var n = fromIndex | 0;
//
//             // 5. If n ≥ 0, then
//             //  a. Let k be n.
//             // 6. Else n < 0,
//             //  a. Let k be len + n.
//             //  b. If k < 0, let k be 0.
//             var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
//
//             function sameValueZero(x, y) {
//                 return x === y || (typeof x === 'number' && typeof y === 'number' && isNaN(x) && isNaN(y));
//             }
//
//             // 7. Repeat, while k < len
//             while (k < len) {
//                 // a. Let elementK be the result of ? Get(O, ! ToString(k)).
//                 // b. If SameValueZero(searchElement, elementK) is true, return true.
//                 if (sameValueZero(o[k], searchElement)) {
//                     return true;
//                 }
//                 // c. Increase k by 1.
//                 k++;
//             }
//
//             // 8. Return false
//             return false;
//         }
//     });
// }
// })();
//
// (function (arr) {
//     arr.forEach(function (item) {
//         if (item.hasOwnProperty('remove')) {
//             return;
//         }
//         Object.defineProperty(item, 'remove', {
//             configurable: true,
//             enumerable: true,
//             writable: true,
//             value: function remove() {
//                 this.parentNode.removeChild(this);
//             }
//         });
//     });
// })([Element.prototype, CharacterData.prototype, DocumentType.prototype]);
//
// (function() {
//
// if (!Object.assign) {
//     Object.defineProperty(Object, 'assign', {
//         enumerable: false,
//         configurable: true,
//         writable: true,
//         value: function(target, firstSource) {
//             'use strict';
//             if (target === undefined || target === null) {
//                 throw new TypeError('Cannot convert first argument to object');
//             }
//
//             var to = Object(target);
//             for (var i = 1; i < arguments.length; i++) {
//                 var nextSource = arguments[i];
//                 if (nextSource === undefined || nextSource === null) {
//                     continue;
//                 }
//
//                 var keysArray = Object.keys(Object(nextSource));
//                 for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
//                     var nextKey = keysArray[nextIndex];
//                     var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
//                     if (desc !== undefined && desc.enumerable) {
//                         to[nextKey] = nextSource[nextKey];
//                     }
//                 }
//             }
//             return to;
//         }
//     });
// }
// })();
//
//
// (function() {
//
//     // проверяем поддержку
//     if (!Element.prototype.matches) {
//
//         // определяем свойство
//         Element.prototype.matches = Element.prototype.matchesSelector ||
//             Element.prototype.webkitMatchesSelector ||
//             Element.prototype.mozMatchesSelector ||
//             Element.prototype.msMatchesSelector;
//
//     }
//
//     if ('NodeList' in window && !NodeList.prototype.forEach) {
//         console.info('polyfill for IE11');
//         NodeList.prototype.forEach = function (callback, thisArg) {
//             thisArg = thisArg || window;
//             for (var i = 0; i < this.length; i++) {
//                 callback.call(thisArg, this[i], i, this);
//             }
//         };
//     }
//
// })();
//
// (function() {
//
//     // проверяем поддержку
//     if (!Element.prototype.closest) {
//
//         // реализуем
//         Element.prototype.closest = function(css) {
//             var node = this;
//
//             while (node) {
//                 if (node.matches(css)) return node;
//                 else node = node.parentElement;
//             }
//             return null;
//         };
//     }
//
// })();
//
// // _mutation
// // http://dom.spec.whatwg.org/#mutation-method-macro
// function _mutation(nodes) { // eslint-disable-line no-unused-vars
//     if (!nodes.length) {
//         throw new Error('DOM Exception 8');
//     } else if (nodes.length === 1) {
//         return typeof nodes[0] === 'string' ? document.createTextNode(nodes[0]) : nodes[0];
//     } else {
//         var
//             fragment = document.createDocumentFragment(),
//             length = nodes.length,
//             index = -1,
//             node;
//
//         while (++index < length) {
//             node = nodes[index];
//
//             fragment.appendChild(typeof node === 'string' ? document.createTextNode(node) : node);
//         }
//
//         return fragment;
//     }
// }
//
// (function () {
//    if (!Element.prototype.append) {// Element.prototype.append
//        Document.prototype.append = Element.prototype.append = function append() {
//            this.appendChild(_mutation(arguments));
//        };
//    }
//
// // Element.prototype.before
//     if (!Element.prototype.before) {
//         Document.prototype.before = Element.prototype.before = function before() {
//             if (this.parentNode) {
//                 this.parentNode.insertBefore(_mutation(arguments), this);
//             }
//         };
//     }
// })();

window.musicShop = {
    email_regular:/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    validatorErrors:{},
    addError: function(vapp, field, message) {
        let obj = _.find(vapp.$validator.errors.items, function(o) { return o.field === field; });
        if (obj === undefined) {
            vapp.errors.add(field, message);
        }
    },
    validate: function (vapp) {
        document.querySelectorAll('.is-invalid').forEach(function (err_elem) {
            if (err_elem.classList) {
                err_elem.classList.remove('is-invalid');
            }
        });

        document.querySelectorAll('.has-error').forEach(function (err_elem) {
            if (err_elem.classList) {
                err_elem.classList.remove('has-error');
            }
        });

        document.querySelectorAll('.error-message').forEach(function (err_elem) {
            if (err_elem) {
                err_elem.remove();
            }
        });

        if (!vapp.$validator.errors.items.length) {
            return true;
        }
        else {
            vapp.$validator.errors.items.forEach(function (err_elem, j, k) {
                var elem = document.getElementsByName(err_elem.field)[0];
                var group = elem.closest('.form-group');
                var input_group = elem.closest('.input-group');

                group = (group === null) ? elem.parentElement : group;
                group.classList.add('has-error');
                // if (elem.classList.contains('v-select')) {
                //     elem.setAttribute('v-error',true);
                // }

                elem.classList.add('is-invalid');
                var errorElem = musicShop.generateErrorElem(err_elem.rule, err_elem.msg);
                musicShop.insertAfter(errorElem, input_group ? input_group : elem);
            });
            return false;

        }
    },
    insertAfter: function(elem, refElem) {
        return refElem.parentNode.insertBefore(elem, refElem.nextSibling);
    },
    generateErrorElem: function(rule, message) {
        var elem = document.createElement('div');
        elem.innerHTML = message;
        elem.classList.add('error-message');
        elem.classList.add('invalid-feedback');
        elem.classList.add('error-'+rule);
        return elem;
    },
    bootboxAlert: function (msg, title){
        bootbox.alert({
            size: 'small',
            title: title,
            message: msg,
            closeButton: false
        });
    },

    errorDialogFromResponse: function (response) {
        this.errorDialog('(Error: ' + response.status + ')');
    },

    errorDialog: function (message) {
        var buttons = {
                cancel: {
                label: 'Закрыть',
                className: 'btn-default'
            }
        };
        this.bootboxDialog(
          '<span style="color:red">Похоже, возникла проблема.</span>',
          'Мы сожалеем, но сервер не смог выполнить ваш запрос.<br/>Пожалуйста, повторите попытку позже.<br/>'+message, buttons);
    },
    bootboxAlertCallback: function (msg, f){
        bootbox.alert({
            size: 'small',
            message: msg,
            callback: f
        });
    },
    bootboxDialog(title, message, buttons) {
        bootbox.dialog({
            title: title,
            message: message,
            buttons: buttons,
        });
    },
    explode: function(delimiter, string) {
        var emptyArray = { 0: '' };
        if ( arguments.length != 2
            || typeof arguments[0] == 'undefined'
            || typeof arguments[1] == 'undefined' )
        {
            return null;
        }
        if ( delimiter === ''
            || delimiter === false
            || delimiter === null )
        {
            return false;
        }
        if ( typeof delimiter == 'function'
            || typeof delimiter == 'object'
            || typeof string == 'function'
            || typeof string == 'object' )
        {
            return emptyArray;
        }
        if ( delimiter === true ) {
            delimiter = '1';
        }
        return string.toString().split ( delimiter.toString() );
    },
    isEmpty: function(val) {
        return (val ===  null || val === '' || val === undefined || val === 0 || val === '0' || val === '.00' || val === '0.00');
    },
    isCanBeInteger: function(n){
        return (n - Math.round(n)) === 0;
    },
    isCanBeFloat: function(n){
        return Number(n) == n;
    },
    roundPlus: function(x, n) { //x - The number, n - The number of decimals after a comma
        if(isNaN(x) || isNaN(n)) return 0;
        var m = Math.pow(10,n);
        return Math.round(x*m)/m;
    },
    ukNumber: function(num, d) {
        var fractionalPart = '.';
        var delimiter = ',';
        d = (d === undefined)?0:d;

        if (num === undefined || num === null || isNaN(num)) {
                return '0';
        }
        return musicShop.roundPlus(num,d).toFixed(d).toString().replace(',','.').replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    },
    getParameterByName: function(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    parseErrors: function (respond) {
        console.log(respond);
        if (respond.data.status === 'error') {
            switch (respond.data.code) {
                case 101: window.location.replace(respond.data.redirect);
                break;
            }

            for (var i in respond.data.fields) {
                var elem = document.getElementsByName(i)[0];
                if (elem !== undefined) {
                    if (!elem.classList.contains('is-invalid')) {
                        elem.classList.add('is-invalid');
                        var errorElem = musicShop.generateErrorElem('custom', respond.data.fields[i]);
                        musicShop.insertAfter(errorElem, elem);
                    }
                }
            }
        }
    },
    getObjectVal: function (obj,is, value) {
        if (is === undefined)
            return undefined;
        if (typeof is == 'string')
            return this.getObjectVal(obj,is.split('.'), value);
        else if (is.length==1 && value!==undefined)
            return obj[is[0]] = value;
        else if (is.length==0)
            return obj;
        else if (!this.isEmpty(obj[is[0]]))
            return this.getObjectVal(obj[is[0]],is.slice(1), value);
        else
            return undefined;
    },
    moneyFormat: function(num, noCurrency, precision) {
        noCurrency = this.isEmpty(noCurrency) ? false : noCurrency;
        precision = precision === undefined ? 2 : precision;
        if (this.isCanBeNumber(num)) {
            var currency = noCurrency ? '': '₽\xa0';
            var fractionalPart = ',';
            var delimiter = ' ';
            if (num === undefined || num === null || isNaN(num)) {
                return '0'+fractionalPart+'00'+currency;
            }

            var val = this.roundPlus(num, precision).toFixed(precision).toString().replace('.',fractionalPart).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1"+delimiter);

            var arr = val.split(delimiter);
            if (arr.length === 2) {
                if (arr[1].length == 1 && parseInt(arr[1]) !== 0 && parseInt(arr[1]) < 10) {
                    val = val+'0';
                }
            }
            if (val.indexOf("-") >= 0 ) {
                val = val.replace('-','');
                return '-'+val+currency;
            }
            return val+currency;
        }
        else {
            return num;
        }
    },
    isCanBeNumber: function(n){
        return Number(n) == n;
    },
    floatToPercents: function (num) {
        if (this.isCanBeNumber(num)) {
            var fractionalPart = ',';
            var delimiter = ' ';
            if (num === undefined || num === null || isNaN(num)) {
                return '0'+fractionalPart+'0';
            }
            var val = (this.roundPlus(num,1)).toFixed(1).toString().replace('.',fractionalPart).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1"+delimiter);
            if (val.indexOf("-") >= 0 ) {
                val = val.replace('-','');
                return '-'+val;
            }
            return val;
        }
        else {
            return num;
        }
    },
    formatLongDateTime: function (date) {
        moment.lang('ru');
        return moment(date).format('LLL');
    },
    formatLongDate: function (date) {
        moment.lang('ru');
        return moment(date).format('LL');
    },
    formatDate: function (date) {
        moment.lang('ru');
        return moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY');
    },
    formatDateToBase: function (date) {
        moment.lang('ru');
        return moment(date, 'DD.MM.YYYY').format('YYYY-MM-DD');
    },
    formatDateTime: function (date) {
        moment.lang('ru');
        return moment(date).format('DD.MM.YYYY hh:mm');
    },
    formatPhone: function (telephone) {
        var tel = telephone.split('');
        return '+7' + ' (' + tel[0] + tel[1] + tel[2] + ') ' + tel[3] + tel[4] + tel[5] + '-' + tel[6] + tel[7] + '-' + tel[8] + tel[9];
    },
    debounceWithId: function (func, wait, immediate) {
    var timeouts = {};
    return function () {
            var context = this, args = arguments;
            var later = function () {
                timeouts[this.id] = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeouts[this.id];
            clearTimeout(timeouts[this.id]);
            timeouts[this.id] = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    },
    roundPrice50KopCeil: function (price) {
        price *= 100;
        price = Math.ceil(price / 50) * 50;
        price /= 100;
        return price;
    }
};
