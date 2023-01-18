"use strict";function _defineProperty(obj,key,value){if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true})}else{obj[key]=value}return obj}function _slicedToArray(arr,i){return _arrayWithHoles(arr)||_iterableToArrayLimit(arr,i)||_unsupportedIterableToArray(arr,i)||_nonIterableRest()}function _nonIterableRest(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function _unsupportedIterableToArray(o,minLen){if(!o)return;if(typeof o==="string")return _arrayLikeToArray(o,minLen);var n=Object.prototype.toString.call(o).slice(8,-1);if(n==="Object"&&o.constructor)n=o.constructor.name;if(n==="Map"||n==="Set")return Array.from(o);if(n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return _arrayLikeToArray(o,minLen)}function _arrayLikeToArray(arr,len){if(len==null||len>arr.length)len=arr.length;for(var i=0,arr2=new Array(len);i<len;i++){arr2[i]=arr[i]}return arr2}function _iterableToArrayLimit(arr,i){var _i=arr==null?null:typeof Symbol!=="undefined"&&arr[Symbol.iterator]||arr["@@iterator"];if(_i==null)return;var _arr=[];var _n=true;var _d=false;var _s,_e;try{for(_i=_i.call(arr);!(_n=(_s=_i.next()).done);_n=true){_arr.push(_s.value);if(i&&_arr.length===i)break}}catch(err){_d=true;_e=err}finally{try{if(!_n&&_i["return"]!=null)_i["return"]()}finally{if(_d)throw _e}}return _arr}function _arrayWithHoles(arr){if(Array.isArray(arr))return arr}(function(wp,wc){"use strict";if(!wp||!wc){return}var el=wp.element.createElement;var withInstanceId=wp.compose.withInstanceId;var _wp$data=wp.data,useDispatch=_wp$data.useDispatch,useSelect=_wp$data.useSelect;var _wp$element=wp.element,useEffect=_wp$element.useEffect,useState=_wp$element.useState;var __=wp.i18n.__;var getSetting=wc.wcSettings.getSetting;var _wc$blocksCheckout=wc.blocksCheckout,CheckboxControl=_wc$blocksCheckout.CheckboxControl,registerCheckoutBlock=_wc$blocksCheckout.registerCheckoutBlock;registerCheckoutBlock({metadata:{name:"mypreview/woo-additional-terms",parent:["woocommerce/checkout-fields-block"]},component:withInstanceId(function(_ref){var instanceId=_ref.instanceId,checkoutExtensionData=_ref.checkoutExtensionData;var _getSetting=getSetting("_woo_additional_terms_data",""),content=_getSetting.content,notice=_getSetting.notice;if(!notice){return null}var validationErrorId="_woo_additional_terms_data_".concat(instanceId);var setExtensionData=checkoutExtensionData.setExtensionData;var _useState=useState(false),_useState2=_slicedToArray(_useState,2),checked=_useState2[0],setChecked=_useState2[1];var _useDispatch=useDispatch("wc/store/validation"),setValidationErrors=_useDispatch.setValidationErrors,clearValidationError=_useDispatch.clearValidationError;var error=useSelect(function(select){return select("wc/store/validation").getValidationError(validationErrorId)});var hasError=!!(error!==null&&error!==void 0&&error.message&&!(error!==null&&error!==void 0&&error.hidden));useEffect(function(){setExtensionData("_woo_additional_terms","wat_checkbox",checked);if(checked){clearValidationError(validationErrorId)}else{setValidationErrors(_defineProperty({},validationErrorId,{message:__("Please read and accept the additional terms and conditions to proceed with your order.","woo-additional-terms"),hidden:true}))}return function(){clearValidationError(validationErrorId)}},[setExtensionData,checked,validationErrorId,clearValidationError,setValidationErrors]);return el("div",{className:"woocommerce-terms-and-conditions-wrapper woo-additional-terms"},el("div",{dangerouslySetInnerHTML:{__html:content}}),el(CheckboxControl,{checked:checked,hasError:hasError,id:"_woo_additional_terms",name:"_woo_additional_terms",onChange:function onChange(){return setChecked(function(value){return!value})}},el("span",{dangerouslySetInnerHTML:{__html:notice}})))})})})(window.wp,window.wc);