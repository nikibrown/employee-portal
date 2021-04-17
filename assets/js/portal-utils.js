/**
 * portal-utils.js
 *
 * @version 1.2
 * @date 22-Jul-2016
 * @package Wordpress Employee Portal
 */
jQuery(document).ready(function($){

	$(document.body).on('click', '.js-portal-opener', function(event){
		if($(this).data('content') !== undefined && $(this).data('content').length > 0) {
			var w = window.open('', 'newwindow', config="height=600, width=800, toolbar=no, menubar=no");
			var html = $(this).data('content');
			w.document.writeln(html);
		}
	});
});