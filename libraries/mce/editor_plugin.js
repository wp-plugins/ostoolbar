// JavaScript Document
(function() {
	//alert 'goober';
	tinymce.create('tinymce.plugins.ostoolbar_plugin_function', {
		init : function(ed, url) {	
		
			// Register command to be executed.
			ed.addCommand('ostoolbar_plugin_command', function() {
				window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, "[ostoolbar]");

			});

			// Register button that will be displayed on wordpress rich editor
			ed.addButton('ostoolbar_plugin_button', 
				{title:'Tutorials', cmd:'ostoolbar_plugin_command', image:url+'/icon-tutorials.png'});},

				getInfo : function() {
					return {
						longname : 'Show tutorials',
						author : 'Nguyen Trung Thanh',
						authorurl : 'http://www.OSTraining.com',
						infourl : 'http://www.OSTraining.com',
						version : tinymce.majorVersion + "." + tinymce.minorVersion
					};
				}
			});

			// Register plugin
			tinymce.PluginManager.add('ostoolbar_plugin', tinymce.plugins.ostoolbar_plugin_function);
})();