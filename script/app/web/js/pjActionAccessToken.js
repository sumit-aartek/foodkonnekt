var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";        
        
		var token = window.location.hash.substr(14);
        document.getElementById('token_key').value = token;        
		document.cloverFrom.submit();
		
	});
})(jQuery_1_8_2);