(function ($, undefined) {
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined);
		
		if ($("#grid").length > 0 && datagrid) {
			
			function formatFile(val, obj) {
				return ['<a href="index.php?controller=pjBackup&action=pjActionDownload&id=', obj.id, '">', val, '</a>'].join("");
			}
			
			var gridOpts = {
				buttons: [{type: "delete", url: "index.php?controller=pjBackup&action=pjActionDelete&id={:id}"}],
				columns: [{text: "Date/Time", type: "text", sortable: true, editable: false, width: 150},
				          {text: "Type", type: "text", sortable: true, editable: false},
				          {text: "File", type: "text", sortable: true, editable: false, renderer: formatFile}
				          ],
				dataUrl: "index.php?controller=pjBackup&action=pjActionGet",
				dataType: "json",
				fields: ['created', 'type', 'id'],
				paginator: {
					actions: [
						{text: "Delete selected", url: "index.php?controller=pjBackup&action=pjActionDeleteBulk", render: true, confirmation: "Are you sure you want to delete selected backups?"}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: null,
				select: {
					field: "id",
					name: "record[]"
				}
			};
			
			var $grid = $("#grid").datagrid(gridOpts);
		}
		
	});
})(jQuery);