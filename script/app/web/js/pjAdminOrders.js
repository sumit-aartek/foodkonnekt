var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			$frmCreateOrder = $('#frmCreateOrder'),
			$frmUpdateOrder = $('#frmUpdateOrder'),
			$dialogReminderEmail = $("#dialogReminderEmail"),
			dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			chosen = ($.fn.chosen !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tabs = ($.fn.tabs !== undefined),
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
		if (chosen) {
			$("#c_country").chosen();
			$("#d_country_id").chosen();
			if($frmCreateOrder.length > 0)
			{
				$("#client_id").chosen();
			}
		}		
		if ($frmCreateOrder.length > 0 || $frmUpdateOrder.length > 0) 
		{
			$frmCreateOrder.validate({
				rules: {
					"p_dt":{
						required: function(){
							if($('#type').val() == 'pickup')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"p_location_id":{
						required: function(){
							if($('#type').val() == 'pickup')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_dt":{
						required: function(){
							if($('#type').val() == 'delivery')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_location_id":{
						required: function(){
							if($('#type').val() == 'delivery')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_address_1":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_address_1').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_address_2":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_address_2').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_city":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_city').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_state":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_state').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_zip":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_zip').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_country_id":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_country_id').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"d_notes":{
						required: function(){
							if($('#type').val() == 'delivery' && $('#d_notes').hasClass('fdRequired'))
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'd_dt' || element.attr('name') == 'p_dt')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function(form){
					var valid = true;
					$('#fdOrderList').find("tbody.main-body > tr.fdLine").each(function() {
						var index = $(this).attr('data-index'),
							$product = $('#fdProduct_' + index),
							$price = $('#fdPrice_' + index);
						
						if($product.val() == '')
						{
							$product.addClass('fdError');
							valid = false;
						}else{
							$product.removeClass('fdError');
						}
						if($price.val() == '')
						{
							$price.addClass('fdError');
							valid = false;
						}else{
							$price.removeClass('fdError');
						}
					});
					if(valid == true)
					{
						form.submit();
					}else{
						if ($tabs.length > 0 && tabs) 
						{
							$tabs.tabs(tOpt).tabs("option", "active", 0);
						}
					}
				}
			});
			$frmUpdateOrder.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'd_dt' || element.attr('name') == 'p_dt')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					var valid = true;
					$('#fdOrderList').find("tbody.main-body > tr.fdLine").each(function() {
						var index = $(this).attr('data-index'),
							$product = $('#fdProduct_' + index),
							$price = $('#fdPrice_' + index);
						
						if($product.val() == '')
						{
							$product.addClass('fdError');
							valid = false;
						}else{
							$product.removeClass('fdError');
						}
						if($price.val() == '')
						{
							$price.addClass('fdError');
							valid = false;
						}else{
							$price.removeClass('fdError');
						}
					});
					if(valid == true)
					{
						form.submit();
					}
				}
			});
			
			$('#fdOrderList').find(".pj-field-count").spinner({
				min: 1,
				stop: function(e, ui){
					if($('#fdOrderList').find("tbody.main-body > tr").length > 0)
					{
						calPrice();
					}
				}
			});
			if($('#fdOrderList').find("tbody.main-body > tr").length > 0)
			{
				calPrice();
				$('#fdOrderList').show();
			}
		}
		function formatType(val, obj) {			
			if(val == 'pickup')
			{
				return '<span class="label-status fd-type-pickup">' + myLabel.pickup + '</span>';
			}else{
				return '<span class="label-status fd-type-delivery">' + myLabel.delivery + '</span>';
			}
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminOrders&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrder&id={:id}"}
						 ],
				columns: [
				          {text: myLabel.name, type: "text", sortable: false, width:200},
				          {text: myLabel.date_time, type: "text", sortable: false, editable: false, width:140},
				          {text: myLabel.total, type: "text", sortable: false, editable: false, width:70},
				          {text: myLabel.type, type: "text", sortable: false, editable: false, width: 70, renderer: formatType},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminOrders&action=pjActionGetOrder" + pjGrid.queryString,
				dataType: "json",
				fields: ['client_name', 'datetime', 'total', 'type', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrderBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminOrders&action=pjActionExportOrder", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminOrders&action=pjActionSaveOrder&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
				
		$(document).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onSelect: function (dateText, inst) {
					
				}
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("focusin", ".datetimepick", function (e) {
			var $this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5
			};
			$(this).datetimepicker($.extend(o, custom));
			
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".pj-button-detailed").trigger("click");
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}
		}).on("click", "#btnAddProduct", function (e) {
			var index = Math.ceil(Math.random() * 999999),
				$clone = $("#boxProductClone").find("tbody").html();
			$clone = $clone.replace(/\{INDEX\}/g, 'new_' + index);
			$('#fdOrderList').find("tbody.main-body").append($clone);
			$('#fdOrderList').find(".pj-field-count").spinner({
				min: 1,
				stop: function(e, ui){
					if($('#fdOrderList').find("tbody.main-body > tr").length > 0)
					{
						calPrice();
					}
				}
			});
			$('#fdOrderList').show();
		}).on("click", ".pj-remove-product", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".fdLine").remove();
			if($('#fdOrderList').find("tbody.main-body > tr").length == 0)
			{
				$('#fdOrderList').hide();
			}else{
				calPrice();
			}
			return false;
		}).on("click", ".pj-add-extra", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				product_id = $("option:selected", $this.parentsUntil(".opBox").find("select[name^='product_id']")).val();
			if(product_id != '')
			{
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetExtras", {
					product_id: product_id,
					index: $this.attr("data-index")
				}).done(function (data) {
					$(data).appendTo($this.siblings(".pj-extra-table").find("tbody"));
					$this.siblings(".pj-extra-table").find(".pj-field-count").spinner({
						min: 1,
						stop: function(e, ui){
							if($('#fdOrderList').find("tbody.main-body > tr").length > 0)
							{
								calPrice();
							}
						}
					});
				});
			}
			return false;
		}).on("click", ".pj-remove-extra", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).parent().parent().remove();
			calPrice();
			return false;
		}).on("change", ".fdProduct", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				index = $this.attr("data-index");
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetPrices", {
				product_id: $this.val(),
				index: index
			}).done(function (data) {
				$("#fdPriceTD_" + index).html(data);
				$('#fdExtraTable_' + index).find("tbody").html("");
				calPrice();
			});
			return false;
		}).on("change", "#type", function (e) {
			switch ($("option:selected", this).val()) {
			case 'pickup':
				$(".delivery").hide();
				$(".pickup").show();
				break;
			case 'delivery':
				$(".delivery").show();
				$(".pickup").hide();
				break;
			default:
				$(".delivery, .pickup").hide();
			}
		}).on("click", "#btnCalc", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if($('#fdOrderList').find("tbody.main-body > tr").length > 0)
			{
				$.post("index.php?controller=pjAdminOrders&action=pjActionGetTotal", $(this).closest("form").serialize()).done(function (data) {
					$("#price").val(data.price);
					$("#price_delivery").val(data.delivery);
					$("#discount").val(data.discount);
					$("#subtotal").val(data.subtotal);
					$("#tax").val(data.tax);
					$("#total").val(data.total);
				});
			}
		}).on("change", "#client_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetClient&id=" + $(this).val()).done(function (data) {
				$('#c_title').val(data.c_title);
				$('#c_email').val(data.c_email);
				$('#c_name').val(data.c_name);
				$('#c_phone').val(data.c_phone);
				$('#c_address_2').val(data.c_address_2);
				$('#c_address_1').val(data.c_address_1);
				$('#c_city').val(data.c_city);
				$('#c_state').val(data.c_state);
				$('#c_zip').val(data.c_zip);
				$('#c_country').val(data.c_country).trigger("liszt:updated");
			});
		}).on("change", ".fdSize", function (e) {
			calPrice();
		}).on("change", ".fdExtra", function (e) {
			calPrice();		
		}).on("change", ".pj-field-count", function (e) {
			calPrice();
		}).on("click", "#btnEmail", function (e) {
			if ($dialogReminderEmail.length > 0 && dialog) {
				$dialogReminderEmail.data("id", $(this).data("id")).dialog("open");
			}
		});
		
		if ($dialogReminderEmail.length > 0 && dialog) {
			$dialogReminderEmail.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 660,
				open: function () {
					$dialogReminderEmail.html("");
					$.get("index.php?controller=pjAdminOrders&action=pjActionReminderEmail", {
						"id": $dialogReminderEmail.data("id")
					}).done(function (data) {
						$dialogReminderEmail.html(data);
						validator = $dialogReminderEmail.find("form").validate({
							errorPlacement: function (error, element) {
								error.insertAfter(element.parent());
							},
							errorClass: "error_clean"
						});
						$dialogReminderEmail.dialog("option", "position", "center");
					});
				},
				close: function () {
					fdApp.enableButtons.call(null, $dialogReminderEmail);
				},
				buttons: (function () {
					var buttons = {};
					buttons[fdApp.locale.button.send] = function () {
						if (validator.form()) {
							fdApp.disableButtons.call(null, $dialogReminderEmail);
							$.post("index.php?controller=pjAdminOrders&action=pjActionReminderEmail", $dialogReminderEmail.find("form").serialize()).done(function (data) {
								if (data.status == "OK") {
									$dialogReminderEmail.dialog("close");
									noty({text: data.text, type: "success"});
								} else {
									noty({text: data.text, type: "error"});
									fdApp.enableButtons.call(null, $dialogReminderEmail);
								}
							});
						}
					};
					buttons[fdApp.locale.button.cancel] = function () {
						$dialogReminderEmail.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		function calPrice()
		{			
			$('#fdOrderList').find("tbody.main-body > tr.fdLine").each(function() {
				var total = 0,
					total_format = '',
					index = $(this).attr('data-index'),
					product = $('#fdProduct_' + index).val(),
					price_element = $('#fdPrice_' + index),
					product_qty = parseInt($('#fdProductQty_' + index).val(), 10),
					price = 0;
				
				var element_type = price_element.attr('data-type');
				if(element_type == 'input')
				{
					price = parseFloat(price_element.val());
				}else{
					price = parseFloat($('option:selected', price_element).attr('data-price'), 10);
				}
				if(price > 0 && product_qty > 0)
				{
					total += parseFloat(price) * product_qty;
					$('.fdExtra_' + index).each(function() {
						var extra_index = $(this).attr('data-index'),
							extra = $(this).val(),
							extra_qty = parseInt($('#fdExtraQty_' + extra_index).val(), 10);
						
						if(extra != '' && extra_qty > 0)
						{
							var extra_price = parseFloat($('option:selected', this).attr('data-price'), 10);
							if(extra_price > 0)
							{
								total += extra_price * extra_qty;
							}
						}
					});
				}
				total_format = formatCurrency(total, myLabel.currency, '');
				$('#fdTotalPrice_' + index).html(total_format);
			});
			
		}
		
		function formatCurrency(price, currency, separator)
		{
			var format = '---';
			switch (currency)
			{
				case 'USD':
					format = "$" + separator + price.toFixed(2);
					break;
				case 'GBP':
					format = "&pound;" + separator  + price.toFixed(2);
					break;
				case 'EUR':
					format = "&euro;" + separator  + price.toFixed(2);
					break;
				case 'JPY':
					format = "&yen;" + separator  + price.toFixed(2);
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price.toFixed(2) + separator  + currency;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = currency + separator  + price.toFixed(2);
					break;
				default:
					format = price.toFixed(2) + separator  + currency;
					break;
			}
			return format;
		}
	});
})(jQuery_1_8_2);