var k=Object.defineProperty;var E=(e,o,t)=>o in e?k(e,o,{enumerable:!0,configurable:!0,writable:!0,value:t}):e[o]=t;var n=(e,o,t)=>(E(e,typeof o!="symbol"?o+"":o,t),t);import{C as u,W as v,V as R}from"./bcd39eff.js";import{O as G,M as w,a as f,T as $}from"./9f99010c.js";import{M as U,a as A,C as N,A as q,O as z}from"./99472363.js";import{M as W,S as V,O as L,a as J}from"./e1eaa995.js";import{M as j,B as O}from"./079d606a.js";import{n as m}from"../entry/index.js";const c=class{};let i=c;n(i,"Prefix",W.PluginPrefix+"wc_integration_"),n(i,"Enabled",c.Prefix+"enabled"),n(i,"SendVolumesOrderStatus",c.Prefix+"send_volumes_order_status"),n(i,"RegistrationSponsorField",c.Prefix+"registration_sponsor_field"),n(i,"RegistrationSponsorFieldStatus",c.Prefix+"registration_sponsor_field_status"),n(i,"VolumeProductFields",c.Prefix+"volume_product_fields"),n(i,"UseDocuments",c.Prefix+"use_documents"),n(i,"CreateOrderDoc",c.Prefix+"create_order_doc"),n(i,"UpdateOrderDoc",c.Prefix+"update_order_doc"),n(i,"VolumeChangeDoc",c.Prefix+"volume_change_doc"),n(i,"WalletOperationTypeIdToPayBonuses",c.Prefix+"wallet_operation_id_to_pay_bonuses"),n(i,"WalletOperationTypeIdToCancelPayBonuses",c.Prefix+"wallet_operation_id_to_cancel_pay_bonuses"),n(i,"MaxPercentForPayWithBonuses",c.Prefix+"max_percent_to_pay_bonuses"),n(i,"CurrencyWalletMatch",c.Prefix+"currency_wallet_match"),n(i,"RegistrationSponsorFieldAttribute",c.Prefix+"registration_sponsor_field_attribute");var K=Object.defineProperty,Q=Object.getOwnPropertyDescriptor,X=(e,o,t,s)=>{for(var a=s>1?void 0:s?Q(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&K(o,t,a),a};let b=class extends G{get sponsorFieldStatuses(){return[{label:"Disabled",value:"disabled"},{label:"Enabled",value:"enabled"},{label:"Required",value:"required"}]}async created(){this.setInternalOption()}changeHandler(){this.changeOption()}};b=X([u({})],b);var Y=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-select",{attrs:{items:e.sponsorFieldStatuses,"item-text":"label","item-value":"value",label:e.label,hint:e.hint},on:{change:e.changeHandler},model:{value:e.optionInternal.value.value,callback:function(s){e.$set(e.optionInternal.value,"value",s)},expression:"optionInternal.value.value"}})},Z=[];const I={};var ee=m(b,Y,Z,!1,te,null,null,null);function te(e){for(let o in I)this[o]=I[o]}var oe=function(){return ee.exports}(),ae=Object.defineProperty,se=Object.getOwnPropertyDescriptor,ne=(e,o,t,s)=>{for(var a=s>1?void 0:s?se(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&ae(o,t,a),a};let y=class extends f{constructor(){super(...arguments);n(this,"BooleanOption",O);n(this,"TextOption",$);n(this,"WCOptions",i)}mounted(){super.mounted()}};y=ne([u({components:{MTextOption:w,MSponsorFieldStatusOption:oe,MSponsorFieldOption:U,MOrderStatusOption:A,MBooleanOption:j}})],y);var re=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-container",[t("v-form",{model:{value:e.valid,callback:function(s){e.valid=s},expression:"valid"}},[t("m-boolean-option",{attrs:{option:e.BooleanOption,name:e.WCOptions.Enabled,label:e.$t("integrations.woocommerce.base.enabled")}}),t("m-order-status-option",{attrs:{option:e.TextOption,name:e.WCOptions.SendVolumesOrderStatus,label:e.$t("integrations.woocommerce.base.orderStatus")}}),t("m-sponsor-field-option",{attrs:{option:e.TextOption,name:e.WCOptions.RegistrationSponsorField,label:e.$t("integrations.woocommerce.base.SponsorProfileField")}}),t("m-sponsor-field-status-option",{attrs:{option:e.TextOption,name:e.WCOptions.RegistrationSponsorFieldStatus,label:e.$t("integrations.woocommerce.base.SponsorProfileFieldStatus")}}),t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.RegistrationSponsorFieldAttribute,label:e.$t("integrations.woocommerce.base.RegistrationSponsorFieldAttribute")}}),t("v-btn",{staticClass:"mt-5",attrs:{disabled:!e.valid,color:"success",loading:e.optionsStore.optionsLoading},on:{click:e.saveHandler}},[e._v(" "+e._s(e.$t("save"))+" ")])],1)],1)},ie=[];const S={};var le=m(y,re,ie,!1,ce,null,null,null);function ce(e){for(let o in S)this[o]=S[o]}var de=function(){return le.exports}(),ue=Object.defineProperty,me=Object.getOwnPropertyDescriptor,pe=(e,o,t,s)=>{for(var a=s>1?void 0:s?me(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&ue(o,t,a),a};let x=class extends f{constructor(){super(...arguments);n(this,"BooleanOption",O);n(this,"TextOption",$);n(this,"WCOptions",i);n(this,"useDocuments",!1)}mounted(){super.mounted()}enableChanged(o){this.useDocuments=o}};x=pe([u({components:{MTextOption:w,MBooleanOption:j}})],x);var ve=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-container",[t("m-boolean-option",{attrs:{option:e.BooleanOption,name:e.WCOptions.UseDocuments,label:e.$t("integrations.woocommerce.documents.useDocuments")},on:{change:e.enableChanged,setOption:e.enableChanged}}),e.useDocuments?[t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.CreateOrderDoc,label:e.$t("integrations.woocommerce.documents.createOrderDocument")}}),t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.UpdateOrderDoc,label:e.$t("integrations.woocommerce.documents.updateOrderDocument")}}),t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.VolumeChangeDoc,label:e.$t("integrations.woocommerce.documents.volumeChangeDocument")}})]:e._e(),t("v-btn",{staticClass:"mt-5",attrs:{color:"success",loading:e.optionsStore.optionsLoading},on:{click:e.saveHandler}},[e._v(" "+e._s(e.$t("save"))+" ")])],2)},_e=[];const F={};var he=m(x,ve,_e,!1,fe,null,null,null);function fe(e){for(let o in F)this[o]=F[o]}var ge=function(){return he.exports}();class T{constructor(){n(this,"name","");n(this,"volumeProperty","");n(this,"type","volume")}}var be=Object.defineProperty,ye=Object.getOwnPropertyDescriptor,g=(e,o,t,s)=>{for(var a=s>1?void 0:s?ye(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&be(o,t,a),a};let d=class extends R{constructor(){super(...arguments);n(this,"headers",[]);n(this,"propertyTypes",[]);n(this,"optionsStore",V.use(L));n(this,"fields",[]);n(this,"editedIndex",-1);n(this,"editedItem",new T);n(this,"defaultItem",new T);n(this,"dialog",!1);n(this,"dialogDelete",!1)}setFields(){this.fields=this.optionsStore.getOptionValue(i.VolumeProductFields)||[],this.headers=[{text:this.$t("integrations.woocommerce.productVolumeFields.headers.fieldName"),sortable:!0,value:"name"},{text:this.$t("integrations.woocommerce.productVolumeFields.headers.volumeProperty"),sortable:!0,value:"volumeProperty"},{text:this.$t("integrations.woocommerce.productVolumeFields.headers.type"),sortable:!0,value:"type"},{text:this.$t("integrations.woocommerce.productVolumeFields.headers.actions"),value:"actions",sortable:!1}],this.propertyTypes=[{label:this.$t("integrations.woocommerce.productVolumeFields.propertyTypes.status"),value:"status"},{label:this.$t("integrations.woocommerce.productVolumeFields.propertyTypes.volume"),value:"volume"}]}editItem(o){this.editedIndex=this.fields.indexOf(o),this.editedItem=Object.assign({},o),this.dialog=!0}deleteItem(o){this.editedIndex=this.fields.indexOf(o),this.editedItem=Object.assign({},o),this.dialogDelete=!0}deleteItemConfirm(){this.fields.splice(this.editedIndex,1),this.closeDelete()}close(){this.dialog=!1,this.$nextTick(()=>{this.editedItem=Object.assign({},this.defaultItem),this.editedIndex=-1})}closeDelete(){this.dialogDelete=!1,this.$nextTick(()=>{this.editedItem=Object.assign({},this.defaultItem),this.editedIndex=-1})}save(){this.editedIndex>-1?Object.assign(this.fields[this.editedIndex],this.editedItem):this.fields.push(this.editedItem),this.close()}async saveHandler(){this.optionsStore.setUnsavedValue(i.VolumeProductFields,this.fields),await this.optionsStore.updateOptions()}mounted(){this.optionsStore.optionsLoading||this.setFields()}get formTitle(){return this.editedIndex===-1?this.$t("integrations.woocommerce.productVolumeFields.formTitleNew").toString():this.$t("integrations.woocommerce.productVolumeFields.formTitleEdit").toString()}dialogWatch(o){o||this.close()}dialogDeleteWatch(o){o||this.closeDelete()}optionLoadingChanged(o){o||this.setFields()}};g([v("dialog")],d.prototype,"dialogWatch",1);g([v("dialogDelete")],d.prototype,"dialogDeleteWatch",1);g([v("optionsStore.optionsLoading")],d.prototype,"optionLoadingChanged",1);d=g([u({})],d);var xe=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-container",[t("v-data-table",{attrs:{headers:e.headers,items:e.fields,"sort-by":"calories"},scopedSlots:e._u([{key:"top",fn:function(){return[t("v-toolbar",{attrs:{flat:""}},[t("v-spacer"),t("v-dialog",{attrs:{"max-width":"500px"},scopedSlots:e._u([{key:"activator",fn:function(s){var a=s.on,r=s.attrs;return[t("v-btn",e._g(e._b({staticClass:"mb-2",attrs:{color:"primary",dark:""}},"v-btn",r,!1),a),[e._v(" "+e._s(e.$t("integrations.woocommerce.productVolumeFields.newItem"))+" ")])]}}]),model:{value:e.dialog,callback:function(s){e.dialog=s},expression:"dialog"}},[t("v-card",[t("v-card-title",[t("span",{staticClass:"text-h5"},[e._v(e._s(e.formTitle))])]),t("v-card-text",[t("v-container",[t("v-text-field",{attrs:{label:e.$t("integrations.woocommerce.productVolumeFields.headers.fieldName")},model:{value:e.editedItem.name,callback:function(s){e.$set(e.editedItem,"name",s)},expression:"editedItem.name"}}),t("v-text-field",{attrs:{label:e.$t("integrations.woocommerce.productVolumeFields.headers.volumeProperty")},model:{value:e.editedItem.volumeProperty,callback:function(s){e.$set(e.editedItem,"volumeProperty",s)},expression:"editedItem.volumeProperty"}}),t("v-select",{attrs:{items:e.propertyTypes,"item-text":"label","item-value":"value",label:e.$t("integrations.woocommerce.productVolumeFields.headers.type")},model:{value:e.editedItem.type,callback:function(s){e.$set(e.editedItem,"type",s)},expression:"editedItem.type"}})],1)],1),t("v-card-actions",[t("v-spacer"),t("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:e.close}},[e._v(" "+e._s(e.$t("integrations.woocommerce.productVolumeFields.modalCancel"))+" ")]),t("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:e.save}},[e._v(" "+e._s(e.$t("integrations.woocommerce.productVolumeFields.modalSave"))+" ")])],1)],1)],1),t("v-dialog",{attrs:{"max-width":"500px"},model:{value:e.dialogDelete,callback:function(s){e.dialogDelete=s},expression:"dialogDelete"}},[t("v-card",[t("v-card-title",{staticClass:"text-h5"},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.deletionConfirmHint")))]),t("v-card-actions",[t("v-spacer"),t("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:e.closeDelete}},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.deletionCancel")))]),t("v-btn",{attrs:{color:"blue darken-1",text:""},on:{click:e.deleteItemConfirm}},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.deletionConfirm")))]),t("v-spacer")],1)],1)],1)],1)]},proxy:!0},{key:"item.actions",fn:function(s){var a=s.item;return[t("v-btn",{attrs:{plain:""},on:{click:function(r){return e.editItem(a)}}},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.editItem")))]),t("v-btn",{attrs:{color:"error",plain:""},on:{click:function(r){return e.deleteItem(a)}}},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.deleteItem")))])]}}])}),t("v-btn",{staticClass:"mt-5",attrs:{color:"success",loading:e.optionsStore.optionsLoading},on:{click:e.saveHandler}},[e._v(" "+e._s(e.$t("save"))+" ")])],1)},we=[];const C={};var $e=m(d,xe,we,!1,Oe,null,null,null);function Oe(e){for(let o in C)this[o]=C[o]}var Pe=function(){return $e.exports}();class Ie{constructor(){n(this,"walletId",0);n(this,"walletName","");n(this,"currency","");n(this,"maxPercent",0)}}const P=class extends J{getWalletTypes(){return this.callHandler("get-wallet-types")}getCurrencies(){return this.callHandler("get-currencies")}};let p=P;n(p,"instance",new P(W.PluginPrefix+"e_wallet_coupon_admin"));var Se=Object.defineProperty,Fe=Object.getOwnPropertyDescriptor,H=(e,o,t,s)=>{for(var a=s>1?void 0:s?Fe(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&Se(o,t,a),a};let _=class extends f{constructor(){super(...arguments);n(this,"TextOption",$);n(this,"WCOptions",i);n(this,"headers",[]);n(this,"walletTypeMap",{});n(this,"walletTypes",[]);n(this,"walletTypesLoading",!0);n(this,"currencies",[]);n(this,"currencyMap",{});n(this,"currencyLoading",!0);n(this,"optionsStore",V.use(L));n(this,"matches",[]);n(this,"defaultItem",new Ie)}mounted(){this.optionsStore.optionsLoading||this.setFields(),super.mounted(),this.loadWalletTypes()}loadWalletTypes(){this.walletTypesLoading=!0,p.instance.getWalletTypes().then(o=>{this.walletTypes=o.map(t=>(t.title=`${t.title} (${t.currency_id})`,t)),this.walletTypeMap=q.createMap(this.walletTypes,"id")}).finally(()=>{this.walletTypesLoading=!1}),this.currencyLoading=!0,p.instance.getCurrencies().then(o=>{this.currencyMap=o;const t=Object.keys(o);this.currencies=[];for(let s=0;s<t.length;s++){const a=t[s];this.currencies.push({label:`${o[a]} (${a})`,value:a})}}).finally(()=>{this.currencyLoading=!1})}setFields(){this.matches=this.optionsStore.getOptionValue(i.CurrencyWalletMatch)||[],this.headers=[{text:this.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.headers.currency"),sortable:!0,value:"currency"},{text:this.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.headers.wallet"),sortable:!0,value:"walletId"}]}changeHandler(){this.optionsStore.setUnsavedValue(i.CurrencyWalletMatch,this.matches)}async saveOptionsHandler(){this.optionsStore.setUnsavedValue(i.CurrencyWalletMatch,this.matches),await this.optionsStore.updateOptions()}optionLoadingChanged(o){o||this.setFields()}};H([v("optionsStore.optionsLoading")],_.prototype,"optionLoadingChanged",1);_=H([u({components:{CrudTable:N,MTextOption:w}})],_);var Te=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-container",[t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.WalletOperationTypeIdToPayBonuses,label:e.$t("integrations.woocommerce.eWalletGateway.walletOperationTypeId")}}),t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.WalletOperationTypeIdToCancelPayBonuses,label:e.$t("integrations.woocommerce.eWalletGateway.walletOperationTypeIdToCancelPayment")}}),t("m-text-option",{attrs:{option:e.TextOption,name:e.WCOptions.MaxPercentForPayWithBonuses,label:e.$t("integrations.woocommerce.eWalletGateway.maxPercent"),"additional-options":{"append-icon":"mdi-percent-outline"}}}),t("crud-table",{attrs:{title:e.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.title"),headers:e.headers,items:e.matches,"default-item":e.defaultItem},on:{change:e.changeHandler},scopedSlots:e._u([{key:"editForm",fn:function(s){var a=s.editItem;return[t("v-autocomplete",{attrs:{items:e.currencies,"item-text":"label","item-value":"value",loading:e.walletTypesLoading,label:e.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.headers.currency")},model:{value:a.currency,callback:function(r){e.$set(a,"currency",r)},expression:"editItem.currency"}}),t("v-select",{attrs:{items:e.walletTypes,"item-text":"title","item-value":"id",loading:e.currencyLoading,label:e.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.headers.wallet")},model:{value:a.walletId,callback:function(r){e.$set(a,"walletId",r)},expression:"editItem.walletId"}}),t("v-text-field",{attrs:{type:"number",label:e.$t("integrations.woocommerce.eWalletGateway.currencyWalletMatch.headers.maxPercent")},model:{value:a.maxPercent,callback:function(r){e.$set(a,"maxPercent",r)},expression:"editItem.maxPercent"}})]}},e.currencyLoading?{key:"item.currency",fn:function(){return[t("v-skeleton-loader",{attrs:{type:"list-item"}})]},proxy:!0}:{key:"item.currency",fn:function(s){var a=s.item;return[e.currencyMap[a.currency]?t("span",[e._v(" "+e._s(e.currencyMap[a.currency])+" ("+e._s(a.currency)+") ")]):t("v-chip",{attrs:{color:"red"}},[e._v(" - ")])]}},e.walletTypesLoading?{key:"item.walletId",fn:function(){return[t("v-skeleton-loader",{attrs:{type:"list-item"}})]},proxy:!0}:{key:"item.walletId",fn:function(s){var a=s.item;return[e.walletTypeMap[a.walletId]?t("span",[e._v(" "+e._s(e.walletTypeMap[a.walletId].title)+" ")]):t("v-chip",{attrs:{color:"red",outlined:""}},[e._v(" - ")])]}}],null,!0)}),t("v-btn",{staticClass:"mt-5",attrs:{disabled:!e.valid,color:"success",loading:e.optionsStore.optionsLoading},on:{click:e.saveOptionsHandler}},[e._v(" "+e._s(e.$t("save"))+" ")])],1)},Ce=[];const D={};var De=m(_,Te,Ce,!1,Me,null,null,null);function Me(e){for(let o in D)this[o]=D[o]}var We=function(){return De.exports}(),Ve=Object.defineProperty,Le=Object.getOwnPropertyDescriptor,B=(e,o,t,s)=>{for(var a=s>1?void 0:s?Le(o,t):o,r=e.length-1,l;r>=0;r--)(l=e[r])&&(a=(s?l(o,t,a):l(a))||a);return s&&a&&Ve(o,t,a),a};let h=class extends f{constructor(){super(...arguments);n(this,"tabs",[]);n(this,"enabled",!1)}mounted(){super.mounted(),this.preloaded&&this.setEnabled()}optionLoadingChanged(o){super.optionLoadingChanged(o),o||this.setEnabled()}setEnabled(){const o=this.optionsStore.getOption(i.Enabled,O);this.enabled=o.value.value}};B([v("optionsStore.optionsLoading")],h.prototype,"optionLoadingChanged",1);h=B([u({components:{EWalletGateway:We,ProductVolumeFields:Pe,DocumentSettings:ge,BaseSettings:de,OptionsSkeletonLoader:z}})],h);var je=function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("v-container",[t("v-tabs",{attrs:{centered:""},model:{value:e.tabs,callback:function(s){e.tabs=s},expression:"tabs"}},[t("v-tab",[e._v(e._s(e.$t("integrations.woocommerce.base.tabHeader")))]),t("v-tab",{attrs:{disabled:!e.enabled}},[e._v(e._s(e.$t("integrations.woocommerce.productVolumeFields.tabHeader")))]),t("v-tab",{attrs:{disabled:!e.enabled}},[e._v(e._s(e.$t("integrations.woocommerce.documents.tabHeader")))]),t("v-tab",{attrs:{disabled:!e.enabled}},[e._v(e._s(e.$t("integrations.woocommerce.eWalletGateway.tabHeader")))])],1),t("v-tabs-items",{model:{value:e.tabs,callback:function(s){e.tabs=s},expression:"tabs"}},[t("v-tab-item",[t("v-card",{attrs:{flat:""}},[t("v-card-title",{staticClass:"text-h5"},[e._v(" "+e._s(e.$t("integrations.woocommerce.base.tabHeader"))+" ")]),t("v-card-text",[t("options-skeleton-loader",{attrs:{loading:!e.preloaded}},[t("base-settings")],1)],1)],1)],1),t("v-tab-item",[t("v-card",{attrs:{flat:""}},[t("v-card-title",{staticClass:"text-h5"},[e._v(" "+e._s(e.$t("integrations.woocommerce.productVolumeFields.tabHeader"))+" ")]),t("v-card-text",[t("options-skeleton-loader",{attrs:{loading:!e.preloaded}},[t("product-volume-fields")],1)],1)],1)],1),t("v-tab-item",[t("v-card",{attrs:{flat:""}},[t("v-card-title",{staticClass:"text-h5"},[e._v(" "+e._s(e.$t("integrations.woocommerce.documents.tabHeader"))+" ")]),t("v-card-text",[t("options-skeleton-loader",{attrs:{loading:!e.preloaded}},[t("document-settings")],1)],1)],1)],1),t("v-tab-item",[t("v-card",{attrs:{flat:""}},[t("v-card-title",{staticClass:"text-h5"},[e._v(" "+e._s(e.$t("integrations.woocommerce.eWalletGateway.tabHeader"))+" ")]),t("v-card-text",[t("options-skeleton-loader",{attrs:{loading:!e.preloaded}},[t("e-wallet-gateway")],1)],1)],1)],1)],1)],1)},He=[];const M={};var Be=m(h,je,He,!1,ke,null,null,null);function ke(e){for(let o in M)this[o]=M[o]}var ze=function(){return Be.exports}();export{ze as default};