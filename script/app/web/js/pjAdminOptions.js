var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			};
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0
		});
		
		function setInstall()
		{
			var clone = $('#pj_install_clone').text(),
				locale = $('#install_locale').val(),
				rel = $('#pj_preview_install').attr('rel');
			
			if(locale == '')
			{
				clone = clone.replace(/\{LOCALE\}/g, '');
			}else{
				clone = clone.replace(/\{LOCALE\}/g, '&locale=' + locale);
			}
			if(locale == '')
			{
				rel = rel.replace(/\{LOCALE\}/g, '');
			}else{
				rel = rel.replace(/\{LOCALE\}/g, '&locale=' + locale);
			}
			$('#install_code').val(clone);
			$('#pj_preview_install').attr('href', rel);
		}
		if($('#install_code').length > 0)
		{
			setInstall();
		}
		
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxPaypal").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxPaypal").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxAuthorize").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxAuthorize").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxBankAccount").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxBankAccount").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_confirmation']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientConfirmation").hide();
				break;
			case '0|1::1':
				$(".boxClientConfirmation").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_payment']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientPayment").hide();
				break;
			case '0|1::1':
				$(".boxClientPayment").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_email_cancel']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxClientCancel").hide();
				break;
			case '0|1::1':
				$(".boxClientCancel").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_confirmation']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminConfirmation").hide();
				break;
			case '0|1::1':
				$(".boxAdminConfirmation").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_payment']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminPayment").hide();
				break;
			case '0|1::1':
				$(".boxAdminPayment").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_admin_email_cancel']", function (e) {
			switch ($("option:selected", this).val()) {
			case '0|1::0':
				$(".boxAdminCancel").hide();
				break;
			case '0|1::1':
				$(".boxAdminCancel").show();
				break;
			}
		}).on("change", ".pj-install-config", function (e) {
			setInstall();
		});
		
		if ($('#frmNotification').length > 0) 
		{
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    width: 550,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			   ],
			   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
			 });
		}
	});
})(jQuery_1_8_2);