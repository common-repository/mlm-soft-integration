var A=Object.defineProperty;var I=(e,t,a)=>t in e?A(e,t,{enumerable:!0,configurable:!0,writable:!0,value:a}):e[t]=a;var l=(e,t,a)=>(I(e,typeof t!="symbol"?t+"":t,a),a);import{V as i,a as C,F as B,L,C as v,P as u,E as M,n as f,W as O,b as q,c as T,d as E,R as D,e as R,i as j,f as U,g as F,h as H}from"../chunks/c0f3c2e3.js";const V=function(){const t=document.createElement("link").relList;if(t&&t.supports&&t.supports("modulepreload"))return;for(const n of document.querySelectorAll('link[rel="modulepreload"]'))s(n);new MutationObserver(n=>{for(const r of n)if(r.type==="childList")for(const o of r.addedNodes)o.tagName==="LINK"&&o.rel==="modulepreload"&&s(o)}).observe(document,{childList:!0,subtree:!0});function a(n){const r={};return n.integrity&&(r.integrity=n.integrity),n.referrerpolicy&&(r.referrerPolicy=n.referrerpolicy),n.crossorigin==="use-credentials"?r.credentials="include":n.crossorigin==="anonymous"?r.credentials="omit":r.credentials="same-origin",r}function s(n){if(n.ep)return;n.ep=!0;const r=a(n);fetch(n.href,r)}};V();const G="Page not found",z="Balance",K="Payment from bonus wallet",J="Please select the wallet",Q="Maximum amount to be paid from the wallet",X="Enter the amount",Y="Pay",Z="Field required",k="The amount must be greater than",ee="The amount must be less than",te="Wallet balance exceeded",ae="No wallets available for payment",ne="required",se="optional";var re={pageNotFound:G,walletBalance:z,paymentFromBonusWallet:K,selectWallet:J,maxAmountToPayFromWallet:Q,enterAmount:X,payButton:Y,fieldRequired:Z,amountMustBeGreaterThan:k,amountMustBeLessThan:ee,walletBalanceExceeded:te,noWalletsAvailable:ae,required:ne,optional:se};const le="\u0421\u0442\u0440\u0430\u043D\u0438\u0446\u0430 \u043D\u0435 \u043D\u0430\u0439\u0434\u0435\u043D\u0430",oe="\u0411\u0430\u043B\u0430\u043D\u0441",ie="\u041E\u043F\u043B\u0430\u0442\u0430 \u0441 \u0431\u043E\u043D\u0443\u0441\u043D\u043E\u0433\u043E \u0441\u0447\u0435\u0442\u0430",ce="\u0412\u044B\u0431\u0435\u0440\u0438\u0442\u0435 \u043A\u043E\u0448\u0435\u043B\u0435\u043A",ue="\u041C\u0430\u043A\u0441\u0438\u043C\u0430\u043B\u044C\u043D\u0430\u044F \u0441\u0443\u043C\u043C\u0430 \u0434\u043B\u044F \u043E\u043F\u043B\u0430\u0442\u044B \u0441 \u043A\u043E\u0448\u0435\u043B\u044C\u043A\u0430",me="\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0441\u0443\u043C\u043C\u0443",de="\u041E\u043F\u043B\u0430\u0442\u0438\u0442\u044C",pe="\u041F\u043E\u043B\u0435 \u044F\u0432\u043B\u044F\u0435\u0442\u0441\u044F \u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u044B\u043C",he="\u0421\u0443\u043C\u043C\u0430 \u0434\u043E\u043B\u0436\u043D\u0430 \u0431\u044B\u0442\u044C \u0431\u043E\u043B\u044C\u0448\u0435",ve="\u0421\u0443\u043C\u043C\u0430 \u0434\u043E\u043B\u0436\u043D\u0430 \u0431\u044B\u0442\u044C \u043C\u0435\u043D\u044C\u0448\u0435",fe="\u0411\u0430\u043B\u0430\u043D\u0441 \u043A\u043E\u0448\u0435\u043B\u044C\u043A\u0430 \u043F\u0440\u0435\u0432\u044B\u0448\u0435\u043D",_e="\u041D\u0435\u0442 \u043A\u043E\u0448\u0435\u043B\u044C\u043A\u043E\u0432 \u0434\u043E\u0441\u0442\u0443\u043F\u043D\u044B\u0445 \u0434\u043B\u044F \u043E\u043F\u043B\u0430\u0442\u044B",ge="\u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u043E",ye="\u043D\u0435\u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u043E";var we={pageNotFound:le,walletBalance:oe,paymentFromBonusWallet:ie,selectWallet:ce,maxAmountToPayFromWallet:ue,enterAmount:me,payButton:de,fieldRequired:pe,amountMustBeGreaterThan:he,amountMustBeLessThan:ve,walletBalanceExceeded:fe,noWalletsAvailable:_e,required:ge,optional:ye};i.use(C);const xe={en_US:re,ru_RU:we},We=B.load(L),$e=new C({fallbackLocale:"en_US",messages:xe,locale:We.locale});class h{static machineFormat(t){return t?(t=this.cleanNumber(t),t=t.padStart(this.precision+1,"0"),t=t.substring(0,t.length-this.precision)+"."+t.substring(t.length-this.precision,t.length),isNaN(Number(t))&&(t="0")):t="0",this.precision===0&&(t=this.cleanNumber(t)),Number(t)}static cleanNumber(t){let a="";if(t){let s=!1;const n=t.toString().split("");for(let r=0;r<n.length;r++)this.isInteger(n[r])&&(s?a=a+n[r]:n[r]!=="0"&&(a=a+n[r],s=!0))}return a}static isInteger(t){let a=!1;return Number.isInteger(parseInt(t))&&(a=!0),a}static formatNumber(t){return Number(t).toLocaleString(this.locale,{maximumFractionDigits:this.precision,minimumFractionDigits:this.precision})}}l(h,"precision",2),l(h,"locale",navigator.language?navigator.language:"en-US");var be=Object.defineProperty,Se=Object.getOwnPropertyDescriptor,_=(e,t,a,s)=>{for(var n=s>1?void 0:s?Se(t,a):t,r=e.length-1,o;r>=0;r--)(o=e[r])&&(n=(s?o(t,a,n):o(n))||n);return s&&n&&be(t,a,n),n};let d=class extends i{constructor(){super(...arguments);l(this,"wallet");l(this,"active");l(this,"locale",navigator.language?navigator.language:"en-US")}toggle(){}formatNumber(t){return h.formatNumber(t)}};_([u({required:!0})],d.prototype,"wallet",2);_([u({required:!1,default:!1})],d.prototype,"active",2);_([M("click")],d.prototype,"toggle",1);d=_([v({})],d);var Pe=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("v-card",{staticClass:"mx-auto",attrs:{outlined:"",color:e.active?"green accent-1":""},on:{click:e.toggle}},[a("v-list-item",{attrs:{"three-line":""}},[a("v-list-item-content",[a("v-list-item-title",{staticClass:"text-h5 mb-1"},[e._v(" "+e._s(e.wallet.title)+" ")]),a("v-list-item-subtitle",[a("div",{staticClass:"ml-6"},[a("div",{staticClass:"ml-auto text-right"},[a("div",{staticClass:"body-3 grey--text font-weight-light"},[e._v(e._s(e.$t("walletBalance")))]),a("h3",{staticClass:"display-2 font-weight-light text--primary"},[e._v(e._s(e.formatNumber(e.wallet.balance))+" "),a("small",[e._v(e._s(e.wallet.currency_id))])])])])])],1)],1)],1)},Ce=[];const $={};var Be=f(d,Pe,Ce,!1,Fe,null,null,null);function Fe(e){for(let t in $)this[t]=$[t]}var Ne=function(){return Be.exports}();class Ae extends O{constructor(){super("e-wallet-api")}}const W=class extends Ae{getPaymentInfo(){return this.callHandler("get-payment-info")}payWithBonuses(t,a){return this.callHandler("pay-with-bonuses",{amount:t,walletId:a})}};let p=W;l(p,"instance",new W);class Ie extends q{constructor(){super(...arguments);l(this,"maxAmount")}}var Le=Object.defineProperty,Me=Object.getOwnPropertyDescriptor,Oe=(e,t,a,s)=>{for(var n=s>1?void 0:s?Me(t,a):t,r=e.length-1,o;r>=0;r--)(o=e[r])&&(n=(s?o(t,a,n):o(n))||n);return s&&n&&Le(t,a,n),n};let g=class extends i{constructor(){super(...arguments);l(this,"amount",0);l(this,"maxAmount",0);l(this,"currency","USD");l(this,"valid",!0);l(this,"locale",navigator.language?navigator.language:"en-US");l(this,"options",{locale:this.locale,prefix:"",suffix:"",length:11,precision:2});l(this,"error","");l(this,"wallets",[]);l(this,"selectedWalletIndex",0);l(this,"preloading",!1);l(this,"payLoading",!1);l(this,"rules",[t=>!!t||this.translate("fieldRequired").toString(),t=>this.machineFormat(t)>0||this.translate("amountMustBeGreaterThan")+" 0",t=>{const a=this.machineFormat(t);return a&&a<=this.getSelectedWallet().balance||this.translate("walletBalanceExceeded").toString()},t=>{const a=this.machineFormat(t),s=this.getMaxAmount();return s?a&&a<=s||this.translate("amountMustBeLessThan").toString()+" "+this.formatNumber(s):!0}])}async created(){this.preloading=!0;try{const t=await p.instance.getPaymentInfo();this.wallets=t.wallets||[],this.maxAmount=t.maxAmount,this.currency=t.currency}catch(t){console.error(t)}finally{this.preloading=!1}}selectWalletHandler(t,a){this.amount>a.balance&&(this.amount=a.balance||0),t()}async payHandler(){this.payLoading=!0;try{const t=this.getSelectedWallet();await p.instance.payWithBonuses(this.amount,t.id)&&T.updateCheckout()}catch(t){this.error=t}this.payLoading=!1}getSelectedWallet(){return this.wallets[this.selectedWalletIndex]?this.wallets[this.selectedWalletIndex]:new Ie}machineFormat(t){return h.machineFormat(t)}formatNumber(t){return h.formatNumber(t)}getMaxAmount(){const t=this.getSelectedWallet().maxAmount||0;return t>0?Math.min(t,this.maxAmount):this.maxAmount}translate(t){return this.$t(t).toString()}};g=Oe([v({components:{WalletCard:Ne}})],g);var qe=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("v-card",{attrs:{outlined:"",loading:e.preloading}},[a("v-card-title",[e._v(" "+e._s(e.$t("paymentFromBonusWallet"))+" ")]),a("v-card-subtitle",{directives:[{name:"show",rawName:"v-show",value:e.wallets.length>1,expression:"wallets.length > 1"}]},[e._v(" "+e._s(e.$t("selectWallet"))+" ")]),a("v-card-subtitle",[a("v-alert",{directives:[{name:"show",rawName:"v-show",value:e.error,expression:"error"}],attrs:{color:"red",dark:"",border:"top",transition:"scale-transition"}},[e._v(" "+e._s(e.error)+" ")])],1),a("v-card-text",[a("v-alert",{directives:[{name:"show",rawName:"v-show",value:!e.preloading&&e.wallets.length===0,expression:"!preloading && wallets.length === 0"}],attrs:{color:"orange",dark:"",border:"top",transition:"scale-transition"}},[e._v(" "+e._s(e.$t("noWalletsAvailable"))+" ")])],1),a("v-item-group",{attrs:{mandatory:""},model:{value:e.selectedWalletIndex,callback:function(s){e.selectedWalletIndex=s},expression:"selectedWalletIndex"}},[a("v-row",{staticStyle:{padding:"15px"}},e._l(e.wallets,function(s,n){return a("v-col",{key:n,attrs:{cols:"12",md:"12"}},[a("v-item",{scopedSlots:e._u([{key:"default",fn:function(r){var o=r.active,N=r.toggle;return[a("wallet-card",{attrs:{wallet:s,active:o},on:{click:function(nt){return e.selectWalletHandler(N,s)}}})]}}],null,!0)})],1)}),1)],1),e.getMaxAmount()?a("v-card-text",[e._v(" "+e._s(e.$t("maxAmountToPayFromWallet"))+": "+e._s(e.formatNumber(e.getMaxAmount()))+" "+e._s(e.currency)+" ")]):e._e(),a("v-form",{model:{value:e.valid,callback:function(s){e.valid=s},expression:"valid"}},[a("v-card-text",[a("div",[a("vuetify-number",{key:"amount-to-pay"+e.selectedWalletIndex,ref:"vuetifyNumber",attrs:{label:e.$t("enterAmount"),valueWhenIsEmpty:"0",rules:e.rules,options:e.options,disabled:e.preloading||e.wallets.length===0},model:{value:e.amount,callback:function(s){e.amount=s},expression:"amount"}})],1)]),a("v-card-actions",[a("v-btn",{attrs:{color:"primary",loading:e.payLoading,disabled:!e.valid||e.preloading||e.wallets.length===0},on:{click:e.payHandler}},[e._v(" "+e._s(e.$t("payButton"))+" ")])],1)],1)],1)},Te=[];const b={};var Ee=f(g,qe,Te,!1,De,null,null,null);function De(e){for(let t in b)this[t]=b[t]}var Re=function(){return Ee.exports}(),je=Object.defineProperty,Ue=Object.getOwnPropertyDescriptor,He=(e,t,a,s)=>{for(var n=s>1?void 0:s?Ue(t,a):t,r=e.length-1,o;r>=0;r--)(o=e[r])&&(n=(s?o(t,a,n):o(n))||n);return s&&n&&je(t,a,n),n};let y=class extends i{};y=He([v({components:{PaymentFromWalletCard:Re}})],y);var Ve=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("v-app",{attrs:{id:"e-wallet-payment-app"}},[a("v-main",{attrs:{app:""}},[a("v-container",{attrs:{fluid:""}},[a("payment-from-wallet-card")],1)],1)],1)},Ge=[];const S={};var ze=f(y,Ve,Ge,!1,Ke,null,null,null);function Ke(e){for(let t in S)this[t]=S[t]}var Je=function(){return ze.exports}();class x{constructor(){l(this,"warehouses")}getAlias(){return"posWarehouses"}getDefaultValue(){const t=new x;t.warehouses=[];const a=new E;a.title="Russian federation",a.id="RU";const s=new D;s.title="\u041C\u043E\u0441\u043A\u0432\u0430",s.id=1;for(let n=0;n<10;n++){const r=new R;r.id=n+1,r.title="Warehouse "+(n+1),r.country=a,r.countryId=a.id,r.region=s,r.regionId=s.id,t.warehouses.push(r)}return t}}var Qe=Object.defineProperty,Xe=Object.getOwnPropertyDescriptor,m=(e,t,a,s)=>{for(var n=s>1?void 0:s?Xe(t,a):t,r=e.length-1,o;r>=0;r--)(o=e[r])&&(n=(s?o(t,a,n):o(n))||n);return s&&n&&Qe(t,a,n),n};let c=class extends i{constructor(){super(...arguments);l(this,"componentLabel");l(this,"componentId");l(this,"componentClass");l(this,"componentCity");l(this,"inputName");l(this,"required");l(this,"warehouses",[]);l(this,"selectedWarehouse",0);l(this,"search","")}created(){this.warehouses=B.load(x).warehouses}itemText(t){let a=t.title;return t.country&&(a+=", "+t.country.title),t.region&&(a+=", "+t.region.title),a}get filteredWarehouses(){return this.warehouses.map(t=>{const a=t;return a.titleSearch=this.getSearchData(t.title),t.country&&(a.countrySearch=this.getSearchData(t.country.title),a.regionSearch=this.getSearchData(t.region.title)),a})}get lowerSearch(){return this.search?this.search.toLowerCase():""}getSearchData(t){const s=t.toLowerCase().indexOf(this.lowerSearch);return s==-1?{start:t,search:"",end:""}:{start:t.substring(0,s),search:t.substring(s,s+this.lowerSearch.length),end:t.substring(s+this.lowerSearch.length)}}};m([u({})],c.prototype,"componentLabel",2);m([u({})],c.prototype,"componentId",2);m([u({})],c.prototype,"componentClass",2);m([u({})],c.prototype,"componentCity",2);m([u({})],c.prototype,"inputName",2);m([u({default:!1})],c.prototype,"required",2);c=m([v({})],c);var Ye=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("v-app",{attrs:{id:"pos-warehouse-select"}},[a("v-main",{attrs:{app:""}},[a("p",{class:"form-row "+e.componentClass,attrs:{id:e.componentId+"_field","data-priority":"80"}},[a("label",{attrs:{for:e.componentId+"_select"}},[e._v(" "+e._s(e.componentLabel)+"\xA0 "),e.required?a("abbr",{staticClass:"required",attrs:{title:e.$t("required")}},[e._v("*")]):a("span",{staticClass:"optional"},[e._v("("+e._s(e.$t("optional"))+")")])]),a("v-autocomplete",{attrs:{id:e.componentId+"_select",outlined:"","single-line":"","hide-no-data":"","hide-details":"","search-input":e.search,items:e.filteredWarehouses,"item-text":e.itemText,"item-value":"id"},on:{"update:searchInput":function(s){e.search=s},"update:search-input":function(s){e.search=s}},scopedSlots:e._u([{key:"item",fn:function(s){var n=s.item;return[a("v-list-item-content",[a("v-list-item-title",{attrs:{title:n.title}},[a("span",[e._v(e._s(n.titleSearch.start))]),a("span",{staticClass:"v-list-item__mask"},[e._v(e._s(n.titleSearch.search))]),a("span",[e._v(e._s(n.titleSearch.end))])]),n.country?a("v-list-item-subtitle",[a("span",[e._v(e._s(n.countrySearch.start))]),a("span",{staticClass:"v-list-item__mask"},[e._v(e._s(n.countrySearch.search))]),a("span",[e._v(e._s(n.countrySearch.end))]),n.region?a("span",[e._v(", "),a("span",[e._v(e._s(n.regionSearch.start))]),a("span",{staticClass:"v-list-item__mask"},[e._v(e._s(n.regionSearch.search))]),a("span",[e._v(e._s(n.regionSearch.end))])]):e._e()]):e._e()],1)]}}]),model:{value:e.selectedWarehouse,callback:function(s){e.selectedWarehouse=s},expression:"selectedWarehouse"}}),a("input",{directives:[{name:"model",rawName:"v-model",value:e.selectedWarehouse,expression:"selectedWarehouse"}],staticClass:"input-text",attrs:{name:e.inputName,id:e.componentId,type:"hidden"},domProps:{value:e.selectedWarehouse},on:{input:function(s){s.target.composing||(e.selectedWarehouse=s.target.value)}}})],1)])],1)},Ze=[];const P={};var ke=f(c,Ye,Ze,!1,et,null,null,null);function et(e){for(let t in P)this[t]=P[t]}var tt=function(){return ke.exports}();class w{}l(w,"Components",[{name:"e-wallet-payment",type:Je,dependencies:[j]},{name:"pos-warehouse-select",type:tt}]);i.use(U);i.use(F);i.use(H);const at=new F({});for(let e=0;e<w.Components.length;e++){const t=w.Components[e];if(t.dependencies)for(let s=0;s<t.dependencies.length;s++)i.use(t.dependencies[s]);let a={};i.customElement(t.name,{props:t.type.options.props,i18n:$e,vuetify:at,setup(s){a=s},render:s=>s(t.type,{props:a})})}