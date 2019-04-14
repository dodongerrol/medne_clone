var protocol = jQuery(location).attr('protocol');
var hostname = jQuery(location).attr('hostname');
var folderlocation = $(location).attr('pathname').split('/')[1];
window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/admin/';
window.base_loading_image = '<img src="http://medicloud.sg/medicloud_v2/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';

jQuery("document").ready(function(){
	
});