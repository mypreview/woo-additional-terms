"use strict";(function(wp,$,ajaxurl,l10n){"use strict";if(!wp){return}var dismiss={cache:function cache(){this.vars={};this.vars.dismiss=".notice[id^=\"woo-additional-terms-dismiss\"] [class*=\"notice-dismiss\"]";this.vars.rated="#woo-additional-terms-dismiss-rate .already-rated"},init:function init(){this.cache();this.bindEvents()},bindEvents:function bindEvents(){var _this=this;$(document.body).on("click",this.vars.dismiss,this.handleOnDismiss).on("click",this.vars.rated,function(event){return _this.handleOnDismiss(event,"rated")})},handleOnDismiss:function handleOnDismiss(event){var action=arguments.length>1&&arguments[1]!==undefined?arguments[1]:"";var $this=$(event.target);if(!$this.attr("href")){event.preventDefault()}$.ajax({type:"POST",url:ajaxurl,dataType:"json",data:{_ajax_nonce:l10n.dismiss_nonce,action:"woo_additional_terms_dismiss_".concat(action||$this.closest(".notice").data("action"))}}).always(function(){$this.closest("div.notice:visible").hide()})}};dismiss.init()})(window.wp,jQuery,ajaxurl,woo_additional_terms_params);