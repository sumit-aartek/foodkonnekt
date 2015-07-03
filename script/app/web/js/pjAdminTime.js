var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmTimeCustom = $("#frmTimeCustom"),
			$frmDefaultTime = $("#frmDefaultTime"),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs");
		
		if ($frmDefaultTime.length > 0) {
			
			$.validator.addMethod('pMonGreaterThan',
			    function (value) {
					if($("#p_monday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_monday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_monday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_monday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_monday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			$.validator.addMethod('pTueGreaterThan',
			    function (value) {
					if($("#p_tuesday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_tuesday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_tuesday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_tuesday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_tuesday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			$.validator.addMethod('pWedGreaterThan',
			    function (value) {
					if($("#p_wednesday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_wednesday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_wednesday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_wednesday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_wednesday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('pThuGreaterThan',
			    function (value) {
					if($("#p_thursday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_thursday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_thursday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_thursday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_thursday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('pFriGreaterThan',
			    function (value) {
					if($("#p_friday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_friday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_friday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_friday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_friday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('pSatGreaterThan',
			    function (value) {
					if($("#p_saturday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_saturday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_saturday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_saturday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_saturday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('pSunGreaterThan',
			    function (value) {
					if($("#p_sunday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#p_sunday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#p_sunday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#p_sunday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#p_sunday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			
			$.validator.addMethod('dMonGreaterThan',
			    function (value) {
					if($("#d_monday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_monday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_monday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_monday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_monday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			$.validator.addMethod('dTueGreaterThan',
			    function (value) {
					if($("#d_tuesday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_tuesday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_tuesday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_tuesday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_tuesday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			$.validator.addMethod('dWedGreaterThan',
			    function (value) {
					if($("#d_wednesday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_wednesday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_wednesday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_wednesday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_wednesday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('dThuGreaterThan',
			    function (value) {
					if($("#d_thursday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_thursday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_thursday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_thursday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_thursday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('dFriGreaterThan',
			    function (value) {
					if($("#d_friday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_friday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_friday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_friday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_friday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('dSatGreaterThan',
			    function (value) {
					if($("#d_saturday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_saturday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_saturday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_saturday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_saturday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			$.validator.addMethod('dSunGreaterThan',
			    function (value) {
					if($("#d_sunday_dayoff").is(':checked'))
					{
						return true;
					}else{
						var  start_hour = parseInt($('#d_sunday_hour_from').val(), 10),
					     	 start_min =  parseInt($('#d_sunday_minute_from').val(), 10),
					     	 end_hour =  parseInt($('#d_sunday_hour_to').val(), 10),
					     	 end_min =  parseInt($('#d_sunday_minute_to').val(), 10);
						
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
			    }, myLabel.validate_time);
			
			$frmDefaultTime.validate({
				rules: {
					"p_monday_hour_to": {
						pMonGreaterThan: true
					},
					"p_tuesday_hour_to": {
						pTueGreaterThan: true
					},
					"p_wednesday_hour_to": {
						pWedGreaterThan: true
					},
					"p_thursday_hour_to": {
						pThuGreaterThan: true
					},
					"p_friday_hour_to": {
						pFriGreaterThan: true
					},
					"p_saturday_hour_to": {
						pSatGreaterThan: true
					},
					"p_sunday_hour_to": {
						pSunGreaterThan: true
					},
					
					"d_monday_hour_to": {
						dMonGreaterThan: true
					},
					"d_tuesday_hour_to": {
						dTueGreaterThan: true
					},
					"d_wednesday_hour_to": {
						dWedGreaterThan: true
					},
					"d_thursday_hour_to": {
						dThuGreaterThan: true
					},
					"d_friday_hour_to": {
						dFriGreaterThan: true
					},
					"d_saturday_hour_to": {
						dSatGreaterThan: true
					},
					"d_sunday_hour_to": {
						dSunGreaterThan: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmTimeCustom.length > 0) {
			
			$.validator.addMethod('greaterThan',
			    function (value) { 
					var  start_hour = parseInt($('#start_hour').val(), 10),
				     	 start_min =  parseInt($('#start_minute').val(), 10),
				     	 end_hour =  parseInt($('#end_hour').val(), 10),
				     	 end_min =  parseInt($('#end_minute').val(), 10);
					
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
			    }, myLabel.validate_time);
			$frmTimeCustom.validate({
				rules: {
					"end_hour": {
						greaterThan: true
					}
				},
				messages: {
					"end_hour": {
						greaterThan: myLabel.validate_time
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminTime&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminTime&action=pjActionDeleteDate&id={:id}"}
				          ],
				columns: [{text: myLabel.date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.type, type: "text", sortable: true, editable: false},
				          {text: myLabel.start_time, type: "text", sortable: true, editable: false},
				          {text: myLabel.end_time, type: "date", sortable: true, editable: false},
				          {text: myLabel.is_day_off, type: "text", sortable: true, editable: false}],
				dataUrl: "index.php?controller=pjAdminTime&action=pjActionGetDate" + pjGrid.queryString,
				dataType: "json",
				fields: ['date', 'type', 'start_time', 'end_time', 'is_dayoff'],
				paginator: {
					actions: [
							   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminTime&action=pjActionDeleteDateBulk", render: true, confirmation: myLabel.delete_confirmation}
							],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".working-day", function (e) {
			var checked = $(this).is(":checked"),
				$tr = $(this).closest("tr");
			$tr.find("select, input[type='text']").attr("disabled", checked);
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
		});
	});
})(jQuery_1_8_2);