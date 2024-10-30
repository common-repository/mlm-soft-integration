/**
 * MLMSoft Integration
 * Interface JS functions
 * @package MLMSoft Integration
 */
/*jslint browser: true*/
/*global jQuery, console, ace, MLMSoftAceEditorAdmin*/

(function($) {
	"use strict";

	if ('undefined' === typeof ace){
			return;
	}

	if ('undefined' === typeof MLMSoftAceEditorAdmin){
			return;
	}
	
	var api = {
		parseBool: function(b){return !(/^(false|0)$/i).test(b) && !!b;},
		editor: false,
		get: {
			param: function(param){
				param = param || null;
				if ( null === param ) {
					return MLMSoftAceEditorAdmin.data;
				}
				if ( 'undefined' !== typeof MLMSoftAceEditorAdmin.data[param] ) { 
					return MLMSoftAceEditorAdmin.data[param];
				}
				return null;
			}			
		},
		attachListeners: function(){
			$( api.get.param('checkoutCssFormSelector') ).on('submit', function() {
				document.getElementById( api.get.param('aceEditorContentID') ).value = api.editor.getValue();
			});
		},
		start: function(){
			api.editor = ace.edit(api.get.param('aceEditorCssID'), {tabSize: 2});
			api.editor.session.setMode('ace/mode/css');		
			api.editor.setTheme('ace/theme/monokai');
			api.attachListeners();
		}
	}

	MLMSoftAceEditorAdmin = $.extend({}, MLMSoftAceEditorAdmin, api);
	MLMSoftAceEditorAdmin.start();
})(jQuery);