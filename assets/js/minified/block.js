"use strict";(function(wp,wc){"use strict";if(!wp||!wc){return}var el=wp.element.createElement;var registerBlockType=wp.blocks.registerBlockType;var useBlockProps=wp.blockEditor.useBlockProps;var CheckboxControl=wc.blocksCheckout.CheckboxControl;var getSetting=wc.wcSettings.getSetting;var __=wp.i18n.__;registerBlockType("mypreview/woo-additional-terms",{title:__("Additional Terms","woo-additional-terms"),description:__("Placeholder block for displaying additional terms checkbox.","woo-additional-terms"),icon:{foreground:"#ffffff",background:"#7f54b3",src:"text-page"},category:"woocommerce",parent:["woocommerce/checkout-fields-block"],attributes:{lock:{type:"object","default":{remove:true,move:true}},checkbox:{type:"boolean","default":false}},supports:{align:false,html:false,multiple:false,reusable:false},edit:function edit(){var blockProps=useBlockProps();var _getSetting=getSetting("_woo_additional_terms_data",""),notice=_getSetting.notice;return el("div",blockProps,el(CheckboxControl,{id:"_woo_additional_terms",label:notice,checked:false,disabled:true}))},save:function save(){return el("div",useBlockProps.save())}})})(window.wp,window.wc);