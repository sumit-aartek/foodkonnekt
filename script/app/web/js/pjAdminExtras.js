var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateExtra = $("#frmCreateExtra"),
			$frmUpdateExtra = $("#frmUpdateExtra"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateExtra.length > 0 && validate) {
			$frmCreateExtra.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmUpdateExtra.length > 0 && validate) {
			$frmUpdateExtra.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmCreateExtra.length > 0 || $frmUpdateExtra.length > 0) 
		{
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var element = $("#i18n_name_" + locale_array[i]);
					element.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminExtras&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtra&id={:id}"}
				          ],
				columns: [{text: myLabel.extra_name, type: "text", sortable: true, editable: true, width: 380, editableWidth: 360},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width: 80},
				          {text: myLabel.products_used, type: "text", sortable: true, editable: false, width: 130, align: 'center'}],
				dataUrl: "index.php?controller=pjAdminExtras&action=pjActionGetExtra",
				dataType: "json",
				fields: ['name', 'price', 'products'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminExtras&action=pjActionDeleteExtraBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminExtras&action=pjActionSaveExtra&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminExtras&action=pjActionGetExtra", "name", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);