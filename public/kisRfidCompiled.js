!function(t){var e={};function n(o){if(e[o])return e[o].exports;var r=e[o]={i:o,l:!1,exports:{}};return t[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=t,n.c=e,n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:o})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)n.d(o,r,function(e){return t[e]}.bind(null,r));return o},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=11)}([function(t,e){function n(e,o){return t.exports=n=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t},n(e,o)}t.exports=n},function(t,e){function n(e){return t.exports=n=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},n(e)}t.exports=n},function(t,e){t.exports=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},function(t,e){function n(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}t.exports=function(t,e,o){return e&&n(t.prototype,e),o&&n(t,o),t}},function(t,e,n){var o=n(7),r=n(8);t.exports=function(t,e){return!e||"object"!==o(e)&&"function"!=typeof e?r(t):e}},function(t,e,n){var o=n(0);t.exports=function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&o(t,e)}},function(t,e,n){var o=n(1),r=n(0),u=n(9),i=n(10);function c(e){var n="function"==typeof Map?new Map:void 0;return t.exports=c=function(t){if(null===t||!u(t))return t;if("function"!=typeof t)throw new TypeError("Super expression must either be null or a function");if(void 0!==n){if(n.has(t))return n.get(t);n.set(t,e)}function e(){return i(t,arguments,o(this).constructor)}return e.prototype=Object.create(t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),r(e,t)},c(e)}t.exports=c},function(t,e){function n(t){return(n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function o(e){return"function"==typeof Symbol&&"symbol"===n(Symbol.iterator)?t.exports=o=function(t){return n(t)}:t.exports=o=function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":n(t)},o(e)}t.exports=o},function(t,e){t.exports=function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}},function(t,e){t.exports=function(t){return-1!==Function.toString.call(t).indexOf("[native code]")}},function(t,e,n){var o=n(0);function r(e,n,u){return!function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],function(){})),!0}catch(t){return!1}}()?t.exports=r=function(t,e,n){var r=[null];r.push.apply(r,e);var u=new(Function.bind.apply(t,r));return n&&o(u,n.prototype),u}:t.exports=r=Reflect.construct,r.apply(null,arguments)}t.exports=r},function(t,e,n){"use strict";n.r(e);var o=n(3),r=n.n(o),u=n(2),i=n.n(u),c=n(4),s=n.n(c),f=n(1),a=n.n(f),l=n(5),p=n.n(l),y=n(6),h=function(t){function e(){return i()(this,e),s()(this,a()(e).apply(this,arguments))}return p()(e,t),e}(n.n(y)()(Error)),b=new Uint8Array([2,110,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,230]),d=new Uint8Array([3,110,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,230]),v=[10,110,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,230],w=function(){function t(e){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:b,o=arguments.length>2&&void 0!==arguments[2]?arguments[2]:v,r=arguments.length>3&&void 0!==arguments[3]?arguments[3]:d;i()(this,t),this.url=e,this.request=n,this.stopRequest=r,this.responseTemplate=o,this.lastResponse=null,this.lastReject=null}return r()(t,[{key:"init",value:function(){var t=this;return new Promise(function(e,n){t.socket?e():(t.socket=new WebSocket(t.url),t.socket.onerror=function(t){console.log("ERROR:",t),n(new h("READER_ERROR"))},t.socket.binaryType="arraybuffer",t.socket.onopen=function(n){e(),t.socket&&(t.socket.onerror=function(t){throw new h(t)})},t.socket.onclose=function(){return t.socket=null})})}},{key:"stop",value:function(){var t=this;return this.lastReject&&(this.lastReject("Stopping"),this.lastReject=null),new Promise(function(e,n){if(!t.socket||1!==t.socket.readyState)throw new h("READER_NOT_CONNECTED");t.socket.send(t.stopRequest),setTimeout(function(){return e("stopped")},100)})}},{key:"read",value:function(){var t=this;return new Promise(function(e,n){if(!t.socket||1!==t.socket.readyState)throw new h("READER_NOT_CONNECTED");t.socket.onmessage=function(n){var o=n.data;e(t.__dataToRFID(o))},t.lastReject=n,t.socket.onerror=function(t){n(new h(t))},t.socket.send(t.request)})}},{key:"__read",value:function(t,e){var n=this;this.socket&&(this.socket.onmessage=function(e){var o=e.data;t(n.__dataToRFID(o))},this.socket.onerror=function(t){e(new h(t))},this.socket.send(this.request))}},{key:"__checkResponse",value:function(t){if(t.byteLength!==this.responseTemplate.length)return!1;for(var e=0;e<this.responseTemplate.length;e++)if(this.responseTemplate[e]>=0&&this.responseTemplate[e]!==t[e])return!1;return!0}},{key:"__dataToRFID",value:function(t){var e=new Uint8Array(t);if(!this.__checkResponse(e))throw new h("INVALID_RESPONSE");for(var n="",o=!1,r=18;r<=33;r++)!o&&e[r]>0&&(o=!0),n+=String.fromCharCode(e[r]);if(!o)throw new h("EMPTY_RESPONSE");return window.btoa(n)}},{key:"getLastResponse",value:function(){return this.lastResponse}},{key:"close",value:function(){this.socket&&this.socket.close(),this.socket=null}}]),t}();window.readOnce=function(t,e){var n=new w(t);n.init().then(function(){n.read().then(function(t){return e(t)}).then(function(){return n.close()}).catch(function(){return e(null)})})},window.KisRfidReader=w}]);