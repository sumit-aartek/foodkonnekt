var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateUser = $("#frmCreateUser"),
			$frmUpdateUser = $("#frmUpdateUser"),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateUser.length > 0) {
			$frmCreateUser.validate({
				rules: {
					"merchant_name": {
						required: true,
						merchant_name: true,
						remote: "index.php?controller=pjAdminMerchant&action=pjActionCheckEmail"
					}
				},
				messages: {
					"merchant_name": {
						remote: myLabel.email_taken
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateUser.length > 0) {
			$frmUpdateUser.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminMerchant&action=pjActionCheckEmail&id=" + $frmUpdateUser.find("input[name='id']").val()
					}
				},
				messages: {
					"email": {
						remote: myLabel.email_taken
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
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
			return ['<span class="label-status user-role-', str, '">', str, '</span>'].join("");
		}
		
		function onBeforeShow (obj) {
			if (parseInt(obj.id, 10) === pjGrid.currentUserId || parseInt(obj.id, 10) === 1) {
				return false;
			}
			return true;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminMerchant&action=pjActionUpdate&id={:merchant_id}"},
				          {type: "delete", url: "index.php?controller=pjAdminMerchant&action=pjActionDeleteMerchant&id={:merchant_id}", beforeShow: onBeforeShow}
				          ],
				columns: [{text: myLabel.merchant_name, type: "text", sortable: true, editable: true},
				          {text: myLabel.merchant_address, type: "text", sortable: true, editable: true}
						  ],
				dataUrl: "index.php?controller=pjAdminMerchant&action=pjActionGetMerchant",
				dataType: "json",
				fields: ['merchant_name', 'merchant_address'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminMerchant&action=pjActionDeleteUserBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminMerchant&action=pjActionStatusUser", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminMerchant&action=pjActionExportUser", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminMerchant&action=pjActionSaveUser&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminMerchant&action=pjActionGetUser", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminMerchant&action=pjActionGetUser", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminMerchant&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminMerchant&action=pjActionGetUser");
			});
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
			$grid.datagrid("load", "index.php?controller=pjAdminMerchant&action=pjActionGetUser", "id", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);