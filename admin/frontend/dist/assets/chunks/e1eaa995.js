var Dt=Object.defineProperty;var Rt=(a,r,o)=>r in a?Dt(a,r,{enumerable:!0,configurable:!0,writable:!0,value:o}):a[r]=o;var P=(a,r,o)=>(Rt(a,typeof r!="symbol"?r+"":r,o),o);import{e as X,f as Lt,r as K,g as Ct}from"./bcd39eff.js";/*! *****************************************************************************
Copyright (C) Microsoft. All rights reserved.
Licensed under the Apache License, Version 2.0 (the "License"); you may not use
this file except in compliance with the License. You may obtain a copy of the
License at http://www.apache.org/licenses/LICENSE-2.0

THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
MERCHANTABLITY OR NON-INFRINGEMENT.

See the Apache Version 2.0 License for specific language governing permissions
and limitations under the License.
***************************************************************************** */var tt;(function(a){(function(r){var o=typeof X=="object"?X:typeof self=="object"?self:typeof this=="object"?this:Function("return this;")(),d=h(a);typeof o.Reflect=="undefined"?o.Reflect=a:d=h(o.Reflect,d),r(d);function h(v,y){return function(m,j){typeof v[m]!="function"&&Object.defineProperty(v,m,{configurable:!0,writable:!0,value:j}),y&&y(m,j)}}})(function(r){var o=Object.prototype.hasOwnProperty,d=typeof Symbol=="function",h=d&&typeof Symbol.toPrimitive!="undefined"?Symbol.toPrimitive:"@@toPrimitive",v=d&&typeof Symbol.iterator!="undefined"?Symbol.iterator:"@@iterator",y=typeof Object.create=="function",m={__proto__:[]}instanceof Array,j=!y&&!m,g={create:y?function(){return C(Object.create(null))}:m?function(){return C({__proto__:null})}:function(){return C({})},has:j?function(t,e){return o.call(t,e)}:function(t,e){return e in t},get:j?function(t,e){return o.call(t,e)?t[e]:void 0}:function(t,e){return t[e]}},V=Object.getPrototypeOf(Function),U=typeof process=="object"&&process.env&&process.env.REFLECT_METADATA_USE_MAP_POLYFILL==="true",D=!U&&typeof Map=="function"&&typeof Map.prototype.entries=="function"?Map:xt(),st=!U&&typeof Set=="function"&&typeof Set.prototype.entries=="function"?Set:At(),ut=!U&&typeof WeakMap=="function"?WeakMap:Ut(),x=new ut;function ft(t,e,n,i){if(_(n)){if(!Z(t))throw new TypeError;if(!z(e))throw new TypeError;return Ot(t,e)}else{if(!Z(t))throw new TypeError;if(!w(e))throw new TypeError;if(!w(i)&&!_(i)&&!k(i))throw new TypeError;return k(i)&&(i=void 0),n=b(n),mt(t,e,n,i)}}r("decorate",ft);function ct(t,e){function n(i,s){if(!w(i))throw new TypeError;if(!_(s)&&!kt(s))throw new TypeError;N(t,e,i,s)}return n}r("metadata",ct);function lt(t,e,n,i){if(!w(n))throw new TypeError;return _(i)||(i=b(i)),N(t,e,n,i)}r("defineMetadata",lt);function pt(t,e,n){if(!w(e))throw new TypeError;return _(n)||(n=b(n)),B(t,e,n)}r("hasMetadata",pt);function ht(t,e,n){if(!w(e))throw new TypeError;return _(n)||(n=b(n)),R(t,e,n)}r("hasOwnMetadata",ht);function dt(t,e,n){if(!w(e))throw new TypeError;return _(n)||(n=b(n)),F(t,e,n)}r("getMetadata",dt);function vt(t,e,n){if(!w(e))throw new TypeError;return _(n)||(n=b(n)),H(t,e,n)}r("getOwnMetadata",vt);function yt(t,e){if(!w(t))throw new TypeError;return _(e)||(e=b(e)),$(t,e)}r("getMetadataKeys",yt);function _t(t,e){if(!w(t))throw new TypeError;return _(e)||(e=b(e)),q(t,e)}r("getOwnMetadataKeys",_t);function wt(t,e,n){if(!w(e))throw new TypeError;_(n)||(n=b(n));var i=T(e,n,!1);if(_(i)||!i.delete(t))return!1;if(i.size>0)return!0;var s=x.get(e);return s.delete(n),s.size>0||x.delete(e),!0}r("deleteMetadata",wt);function Ot(t,e){for(var n=t.length-1;n>=0;--n){var i=t[n],s=i(e);if(!_(s)&&!k(s)){if(!z(s))throw new TypeError;e=s}}return e}function mt(t,e,n,i){for(var s=t.length-1;s>=0;--s){var O=t[s],f=O(e,n,i);if(!_(f)&&!k(f)){if(!w(f))throw new TypeError;i=f}}return i}function T(t,e,n){var i=x.get(t);if(_(i)){if(!n)return;i=new D,x.set(t,i)}var s=i.get(e);if(_(s)){if(!n)return;s=new D,i.set(e,s)}return s}function B(t,e,n){var i=R(t,e,n);if(i)return!0;var s=L(e);return k(s)?!1:B(t,s,n)}function R(t,e,n){var i=T(e,n,!1);return _(i)?!1:Pt(i.has(t))}function F(t,e,n){var i=R(t,e,n);if(i)return H(t,e,n);var s=L(e);if(!k(s))return F(t,s,n)}function H(t,e,n){var i=T(e,n,!1);if(!_(i))return i.get(t)}function N(t,e,n,i){var s=T(n,i,!0);s.set(t,e)}function $(t,e){var n=q(t,e),i=L(t);if(i===null)return n;var s=$(i,e);if(s.length<=0)return n;if(n.length<=0)return s;for(var O=new st,f=[],c=0,u=n;c<u.length;c++){var l=u[c],p=O.has(l);p||(O.add(l),f.push(l))}for(var M=0,Q=s;M<Q.length;M++){var l=Q[M],p=O.has(l);p||(O.add(l),f.push(l))}return f}function q(t,e){var n=[],i=T(t,e,!1);if(_(i))return n;for(var s=i.keys(),O=St(s),f=0;;){var c=Et(O);if(!c)return n.length=f,n;var u=Tt(c);try{n[f]=u}catch(l){try{It(O)}finally{throw l}}f++}}function Y(t){if(t===null)return 1;switch(typeof t){case"undefined":return 0;case"boolean":return 2;case"string":return 3;case"symbol":return 4;case"number":return 5;case"object":return t===null?1:6;default:return 6}}function _(t){return t===void 0}function k(t){return t===null}function gt(t){return typeof t=="symbol"}function w(t){return typeof t=="object"?t!==null:typeof t=="function"}function bt(t,e){switch(Y(t)){case 0:return t;case 1:return t;case 2:return t;case 3:return t;case 4:return t;case 5:return t}var n=e===3?"string":e===5?"number":"default",i=J(t,h);if(i!==void 0){var s=i.call(t,n);if(w(s))throw new TypeError;return s}return Mt(t,n==="default"?"number":n)}function Mt(t,e){if(e==="string"){var n=t.toString;if(S(n)){var i=n.call(t);if(!w(i))return i}var s=t.valueOf;if(S(s)){var i=s.call(t);if(!w(i))return i}}else{var s=t.valueOf;if(S(s)){var i=s.call(t);if(!w(i))return i}var O=t.toString;if(S(O)){var i=O.call(t);if(!w(i))return i}}throw new TypeError}function Pt(t){return!!t}function jt(t){return""+t}function b(t){var e=bt(t,3);return gt(e)?e:jt(e)}function Z(t){return Array.isArray?Array.isArray(t):t instanceof Object?t instanceof Array:Object.prototype.toString.call(t)==="[object Array]"}function S(t){return typeof t=="function"}function z(t){return typeof t=="function"}function kt(t){switch(Y(t)){case 3:return!0;case 4:return!0;default:return!1}}function J(t,e){var n=t[e];if(n!=null){if(!S(n))throw new TypeError;return n}}function St(t){var e=J(t,v);if(!S(e))throw new TypeError;var n=e.call(t);if(!w(n))throw new TypeError;return n}function Tt(t){return t.value}function Et(t){var e=t.next();return e.done?!1:e}function It(t){var e=t.return;e&&e.call(t)}function L(t){var e=Object.getPrototypeOf(t);if(typeof t!="function"||t===V||e!==V)return e;var n=t.prototype,i=n&&Object.getPrototypeOf(n);if(i==null||i===Object.prototype)return e;var s=i.constructor;return typeof s!="function"||s===t?e:s}function xt(){var t={},e=[],n=function(){function f(c,u,l){this._index=0,this._keys=c,this._values=u,this._selector=l}return f.prototype["@@iterator"]=function(){return this},f.prototype[v]=function(){return this},f.prototype.next=function(){var c=this._index;if(c>=0&&c<this._keys.length){var u=this._selector(this._keys[c],this._values[c]);return c+1>=this._keys.length?(this._index=-1,this._keys=e,this._values=e):this._index++,{value:u,done:!1}}return{value:void 0,done:!0}},f.prototype.throw=function(c){throw this._index>=0&&(this._index=-1,this._keys=e,this._values=e),c},f.prototype.return=function(c){return this._index>=0&&(this._index=-1,this._keys=e,this._values=e),{value:c,done:!0}},f}();return function(){function f(){this._keys=[],this._values=[],this._cacheKey=t,this._cacheIndex=-2}return Object.defineProperty(f.prototype,"size",{get:function(){return this._keys.length},enumerable:!0,configurable:!0}),f.prototype.has=function(c){return this._find(c,!1)>=0},f.prototype.get=function(c){var u=this._find(c,!1);return u>=0?this._values[u]:void 0},f.prototype.set=function(c,u){var l=this._find(c,!0);return this._values[l]=u,this},f.prototype.delete=function(c){var u=this._find(c,!1);if(u>=0){for(var l=this._keys.length,p=u+1;p<l;p++)this._keys[p-1]=this._keys[p],this._values[p-1]=this._values[p];return this._keys.length--,this._values.length--,c===this._cacheKey&&(this._cacheKey=t,this._cacheIndex=-2),!0}return!1},f.prototype.clear=function(){this._keys.length=0,this._values.length=0,this._cacheKey=t,this._cacheIndex=-2},f.prototype.keys=function(){return new n(this._keys,this._values,i)},f.prototype.values=function(){return new n(this._keys,this._values,s)},f.prototype.entries=function(){return new n(this._keys,this._values,O)},f.prototype["@@iterator"]=function(){return this.entries()},f.prototype[v]=function(){return this.entries()},f.prototype._find=function(c,u){return this._cacheKey!==c&&(this._cacheIndex=this._keys.indexOf(this._cacheKey=c)),this._cacheIndex<0&&u&&(this._cacheIndex=this._keys.length,this._keys.push(c),this._values.push(void 0)),this._cacheIndex},f}();function i(f,c){return f}function s(f,c){return c}function O(f,c){return[f,c]}}function At(){return function(){function t(){this._map=new D}return Object.defineProperty(t.prototype,"size",{get:function(){return this._map.size},enumerable:!0,configurable:!0}),t.prototype.has=function(e){return this._map.has(e)},t.prototype.add=function(e){return this._map.set(e,e),this},t.prototype.delete=function(e){return this._map.delete(e)},t.prototype.clear=function(){this._map.clear()},t.prototype.keys=function(){return this._map.keys()},t.prototype.values=function(){return this._map.values()},t.prototype.entries=function(){return this._map.entries()},t.prototype["@@iterator"]=function(){return this.keys()},t.prototype[v]=function(){return this.keys()},t}()}function Ut(){var t=16,e=g.create(),n=i();return function(){function u(){this._key=i()}return u.prototype.has=function(l){var p=s(l,!1);return p!==void 0?g.has(p,this._key):!1},u.prototype.get=function(l){var p=s(l,!1);return p!==void 0?g.get(p,this._key):void 0},u.prototype.set=function(l,p){var M=s(l,!0);return M[this._key]=p,this},u.prototype.delete=function(l){var p=s(l,!1);return p!==void 0?delete p[this._key]:!1},u.prototype.clear=function(){this._key=i()},u}();function i(){var u;do u="@@WeakMap@@"+c();while(g.has(e,u));return e[u]=!0,u}function s(u,l){if(!o.call(u,n)){if(!l)return;Object.defineProperty(u,n,{value:g.create()})}return u[n]}function O(u,l){for(var p=0;p<l;++p)u[p]=Math.random()*255|0;return u}function f(u){return typeof Uint8Array=="function"?typeof crypto!="undefined"?crypto.getRandomValues(new Uint8Array(u)):typeof msCrypto!="undefined"?msCrypto.getRandomValues(new Uint8Array(u)):O(new Uint8Array(u),u):O(new Array(u),u)}function c(){var u=f(t);u[6]=u[6]&79|64,u[8]=u[8]&191|128;for(var l="",p=0;p<t;++p){var M=u[p];(p===4||p===6||p===8)&&(l+="-"),M<16&&(l+="0"),l+=M.toString(16).toLowerCase()}return l}}function C(t){return t.__=void 0,delete t.__,t}})})(tt||(tt={}));var Gt=Object.defineProperty,Wt=Object.defineProperties,Vt=Object.getOwnPropertyDescriptors,et=Object.getOwnPropertySymbols,Bt=Object.prototype.hasOwnProperty,Ft=Object.prototype.propertyIsEnumerable,nt=(a,r,o)=>r in a?Gt(a,r,{enumerable:!0,configurable:!0,writable:!0,value:o}):a[r]=o,Ht=(a,r)=>{for(var o in r||(r={}))Bt.call(r,o)&&nt(a,o,r[o]);if(et)for(var o of et(r))Ft.call(r,o)&&nt(a,o,r[o]);return a},Nt=(a,r)=>Wt(a,Vt(r)),I;(function(a){a.StoreOptions="pinia-store-decorators:StoreOptions"})(I||(I={}));function it(a){const r=[],o={},d={};Object.getOwnPropertyNames(a.prototype).map(v=>{const y=Object.getOwnPropertyDescriptor(a.prototype,v),m=y==null?void 0:y.get,j=y==null?void 0:y.set,g=y==null?void 0:y.value;m&&!j&&(o[v]=m),typeof g=="function"&&g!==a&&(d[v]=g)}),r.push({getters:o,actions:d});const h=Object.getPrototypeOf(a);if(h.name!==""){const v=Reflect.getMetadata(I.StoreOptions,h);v?r.push({getters:v.getters,actions:v.actions}):r.push(...it(h))}return r}function $t(a){let r=a[0];return a.shift(),a.map(o=>{r=at(r,o)}),r}function at(a,r){const o=[...new Set([...Object.keys(a),...Object.keys(r)])],d={};return o.map(h=>{var v;typeof a[h]=="object"&&typeof r[h]=="object"?d[h]=at(a[h],r[h]):d[h]=(v=r[h])!=null?v:a[h]}),d}function qt(a){const r=new a,o={};Object.getOwnPropertyNames(r).map(y=>{const m=Object.getOwnPropertyDescriptor(r,y);m&&(o[y]=m.value)});const{getters:d,actions:h}=$t(it(a).reverse()),v={id:"default id",state:()=>o,getters:d,actions:h};Reflect.defineMetadata(I.StoreOptions,v,a)}function Yt(a,r){const o=Reflect.getMetadata(I.StoreOptions,r);return Nt(Ht({},o),{id:a})}class ot{}P(ot,"PluginPrefix","mlmsoft_v3_");class Zt extends Lt{constructor(r,o=3e4){super(r,o),this.service.defaults.baseURL="/wp-admin/admin-ajax.php"}}const G=class extends Zt{constructor(){super(ot.PluginPrefix+"admin_ajax")}getAdminUsers(){return this.callHandler("get-admin-users")}};let A=G;P(A,"instance",new G);const W=class extends A{getOptions(){return this.callHandler("get-options")}updateOptions(r){return this.callHandler("update-options",r)}};let E=W;P(E,"instance",new W);var zt=Object.defineProperty,Jt=Object.getOwnPropertyDescriptor,Qt=(a,r,o,d)=>{for(var h=d>1?void 0:d?Jt(r,o):r,v=a.length-1,y;v>=0;v--)(y=a[v])&&(h=(d?y(r,o,h):y(h))||h);return d&&h&&zt(r,o,h),h};let rt=class{constructor(){P(this,"options",{});P(this,"unsavedOptions",{});P(this,"optionsLoading",!0)}async loadOptions(){this.optionsLoading=!0;const a=await E.instance.getOptions(),r=Object.keys(a);for(let o=0;o<r.length;o++){const d=r[o];this.options[d]=K(a[d])}this.optionsLoading=!1}async updateOptions(){if(!this.unsavedOptions||Object.keys(this.unsavedOptions).length===0)return;const a=Object.keys(this.unsavedOptions);for(let r=0;r<a.length;r++){const o=a[r];this.options[o]=K(this.unsavedOptions[o])}this.optionsLoading=!0,await E.instance.updateOptions(this.unsavedOptions),this.optionsLoading=!1}getOption(a,r,o){const d=this.options[a]===void 0?o:this.options[a].value,h=new r(a);return h.loadFromRaw(d),h}getOptionValue(a,r){return this.options[a]===void 0?r:this.options[a].value}setUnsavedValue(a,r){this.unsavedOptions[a]=r}setUnsavedOption(a){this.unsavedOptions[a.name]=a.getRaw()}clearUnsaved(){this.unsavedOptions={}}};rt=Qt([qt],rt);class Xt{static use(r){const o=r.name;return this.stores[o]||(this.stores[o]=Ct(Yt(r.name,r))),this.stores[o]()}}P(Xt,"stores",{});export{A,ot as M,rt as O,Xt as S,Zt as a,qt as b};
