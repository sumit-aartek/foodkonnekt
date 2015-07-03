var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateVoucher = $("#frmCreateVoucher"),
			$frmUpdateVoucher = $("#frmUpdateVoucher"),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		function validateTime()
		{
			var start_hour = parseInt($('#r_hour_from').val(), 10),
	     		start_min =  parseInt($('#r_minute_from').val(), 10),
	     		end_hour =  parseInt($('#r_hour_to').val(), 10),
	     		end_min =  parseInt($('#r_minute_to').val(), 10);
			
			if(end_hour < start_hour)
			{
				return false;
			}else if(end_hour == start_hour){
				if(end_min <= start_min)
				{
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}
		function validateFixedTime()
		{
			var start_hour = parseInt($('#f_hour_from').val(), 10),
	     		start_min =  parseInt($('#f_minute_from').val(), 10),
	     		end_hour =  parseInt($('#f_hour_to').val(), 10),
	     		end_min =  parseInt($('#f_minute_to').val(), 10);
			
			if(end_hour < start_hour)
			{
				return false;
			}else if(end_hour == start_hour){
				if(end_min <= start_min)
				{
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}
			
		if ($frmCreateVoucher.length > 0) {
							
			$frmCreateVoucher.validate({
				rules: {
					"code": {
						required: true,
						remote: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode"
					}, 
					"f_date" :{
						required: function(){
							if($("#valid").val() == 'fixed'){
								return true;
							}else{
								return false;
							}
						}
					},
					"p_date_from" :{
						required: function(){
							if($("#valid").val() == 'period'){
								return true;
							}else{
								return false;
							}
						}
					},
					"p_date_to" :{
						required: function(){
							if($("#valid").val() == 'period'){
								return true;
							}else{
								return false;
							}
						}
					}
				},
				messages: {
					"code": {
						remote: myLabel.code_exist
					},
					"f_date" : {
						required: myLabel.field_required
					},
					"p_date_from" : {
						required: myLabel.field_required
					},
					"p_date_to" : {
						required: myLabel.field_required
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'p_date_from' || element.attr('name') == 'p_date_to')
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
				submitHandler: function(){
					if($("#valid").val() == 'period' && $("#p_date_from").val() != '' && $("#p_date_to").val() != ''){
						$.ajax({
							url: "index.php?controller=pjAdminVouchers&action=pjActionCheckDate",
							type: "post",
							dataType: 'html',
							data: {
								p_date_from: function() {
									return $( "#p_date_from" ).val();
								},
								p_hour_from: function() {
									return $( "#p_hour_from" ).val();
								},
								p_minute_from: function() {
									return $( "#p_minute_from" ).val();
								},
								p_date_to: function() {
									return $( "#p_date_to" ).val();
								},
								p_hour_to: function() {
									return $( "#p_hour_to" ).val();
								},
								p_minute_to: function() {
									return $( "#p_minute_to" ).val();
								}
							},
							success: function(data){
								if(data == 'true')
								{
									$frmCreateVoucher.off('submit');
									$('#validate_datetime').parent().css('display', 'none');
									$frmCreateVoucher.submit();
								}else{
									$('#validate_datetime').css('display', 'block');
									$('#validate_datetime').parent().css('display', 'block');
								}
							}
						});
						return false;
					}else if($("#valid").val() == 'fixed'){
						if(validateFixedTime() == true)
						{
							$frmCreateVoucher.off('submit');
							$('#validate_fixedtime').parent().css('display', 'none');
							$frmCreateVoucher.submit();
						}else{
							$('#validate_fixedtime').css('display', 'block');
							$('#validate_fixedtime').parent().css('display', 'block');
						}
						return false;
					}else if($("#valid").val() == 'recurring'){
						if(validateTime() == true)
						{
							$frmCreateVoucher.off('submit');
							$('#validate_time').parent().css('display', 'none');
							$frmCreateVoucher.submit();
						}else{
							$('#validate_time').css('display', 'block');
							$('#validate_time').parent().css('display', 'block');
						}
						return false;
					}
				}
			});
		}
		if ($frmUpdateVoucher.length > 0) {
			$frmUpdateVoucher.validate({
				rules: {
					"code": {
						required: true,
						remote: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode&id=" + $frmUpdateVoucher.find("input[name='id']").val()
					},
					"f_date" :{
						required: function(){
							if($("#valid").val() == 'fixed'){
								return true;
							}else{
								return false;
							}
						}
					},
					"p_date_from" :{
						required: function(){
							if($("#valid").val() == 'period'){
								return true;
							}else{
								return false;
							}
						}
					},
					"p_date_to" :{
						required: function(){
							if($("#valid").val() == 'period'){
								return true;
							}else{
								return false;
							}
						}
					}
				},
				messages: {
					"code": {
						remote: myLabel.code_exist
					},
					"f_date" : {
						required: myLabel.field_required
					},
					"p_date_from" : {
						required: myLabel.field_required
					},
					"p_date_to" : {
						required: myLabel.field_required
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'p_date_from' || element.attr('name') == 'p_date_to')
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
				submitHandler: function(){
					if($("#valid").val() == 'period' && $("#p_date_from").val() != '' && $("#p_date_to").val() != ''){
						$.ajax({
							url: "index.php?controller=pjAdminVouchers&action=pjActionCheckDate",
							type: "post",
							dataType: 'html',
							data: {
								p_date_from: function() {
									return $( "#p_date_from" ).val();
								},
								p_hour_from: function() {
									return $( "#p_hour_from" ).val();
								},
								p_minute_from: function() {
									return $( "#p_minute_from" ).val();
								},
								p_date_to: function() {
									return $( "#p_date_to" ).val();
								},
								p_hour_to: function() {
									return $( "#p_hour_to" ).val();
								},
								p_minute_to: function() {
									return $( "#p_minute_to" ).val();
								}
							},
							success: function(data){
								if(data == 'true')
								{
									$frmUpdateVoucher.off('submit');
									$('#validate_datetime').parent().css('display', 'none');
									$frmUpdateVoucher.submit();
								}else{
									$('#validate_datetime').css('display', 'block');
									$('#validate_datetime').parent().css('display', 'block');
								}
							}
						});
						
						return false;
					}else if($("#valid").val() == 'fixed'){
						if(validateFixedTime() == true)
						{
							$frmUpdateVoucher.off('submit');
							$('#validate_fixedtime').parent().css('display', 'none');
							$frmUpdateVoucher.submit();
						}else{
							$('#validate_fixedtime').css('display', 'block');
							$('#validate_fixedtime').parent().css('display', 'block');
						}
						
						return false;
					}else if($("#valid").val() == 'recurring'){
						if(validateTime() == true)
						{
							$frmUpdateVoucher.off('submit');
							$('#validate_time').parent().css('display', 'none');
							$frmUpdateVoucher.submit();
						}else{
							$('#validate_time').css('display', 'block');
							$('#validate_time').parent().css('display', 'block');
						}
						
						return false;
					}
				}
			});
		}
		
		function formatDefault (str, obj) {
			if (obj.role_id == 3) {
				return '<a href="#" class="pj-status-icon pj-status-' + (str == 'F' ? '0' : '1') + '" style="cursor: ' +  (str == 'F' ? 'pointer' : 'default') + '"></a>';
			} else {
				return '<a href="#" class="pj-status-icon pj-status-1" style="cursor: default"></a>';
			}
		}
		function formatRole (str) {
			return ['<span class="label-status voucher-role-', str, '">', str, '</span>'].join("");
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminVouchers&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucher&id={:id}"}
				          ],
				columns: [{text: myLabel.code, type: "text", sortable: true, editable: true, width: 200},
				          {text: myLabel.discount, type: "text", sortable: false, editable: false, width: 100},
				          {text: myLabel.date_time_valid, type: "text", sortable: false, editable: false}],
				dataUrl: "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher",
				dataType: "json",
				fields: ['code', 'discount', 'datetime_valid'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucherBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminVouchers&action=pjActionExportVoucher", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVouchers&action=pjActionSaveVoucher&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "ASC", content.page, content.rowCount);
			return false;
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev")
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on("click", "#valid", function (e) {
			var val = $(this).val(),
				valid_box = $('#valid_' + val);
			$('.valid-box').css('display', 'none');
			valid_box.css('display', 'block');
		}).on("click", "#type", function (e) {
			var sign = $('option:selected', this).attr('data-sign');
			$('#icon_type').html(sign);
		}).on("change", "#itemCatId", function (e) {
			var $cid = $('option:selected', this).val();
			$.ajax({
				url: 'index.php?controller=pjAdminVouchers&action=pjActionItem',
				type: 'POST',
				data: {'cid':$cid},
				dataType: 'html',
				success:function(data){
					$('#product_id').html(data);
				},
				error:function(e) {
					console.log('Error Occured!'+ e.getMessage());
				}
			});			
		});
	});
})(jQuery_1_8_2);