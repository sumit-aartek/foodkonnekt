var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateLocation = $("#frmCreateLocation"),
			$frmUpdateLocation = $("#frmUpdateLocation"),
			$frmUpdatePrices = $("#frmUpdatePrices"),
			$frmGetDetails = $("#frmGetDetails"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			overlays = [];
		
		if ($frmCreateLocation.length > 0 && validate) {
			$frmCreateLocation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
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
			});
		}
		if ($frmUpdateLocation.length > 0 && validate) {
			$frmUpdateLocation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
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
			});
		}
		if ($frmCreateLocation.length > 0 || $frmUpdateLocation.length > 0) 
		{
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var name = $("#i18n_name_" + locale_array[i]),
						address = $("#i18n_address_" + locale_array[i]);
					name.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
					address.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
			
			var myGoogleMaps = null,
				myGoogleMapsMarker = null;
			
			function GoogleMaps() {
				this.map = null;
				this.drawingManager = null;
				this.init();
			}
			GoogleMaps.prototype = {
				init: function () {
					var self = this;
					self.map = new google.maps.Map(document.getElementById("fd_map_canvas"), {
						zoom: 8,
						center: new google.maps.LatLng(40.65, -73.95),
						mapTypeId: google.maps.MapTypeId.ROADMAP
					});
					return self;
				},
				addMarker: function (position) {
					if (myGoogleMapsMarker != null) {
						myGoogleMapsMarker.setMap(null);
					}
					myGoogleMapsMarker = new google.maps.Marker({
						map: this.map,
						position: position,
						icon: "app/web/img/backend/pin.png"
					});
					this.map.setCenter(position);
					$("#lat").val(position.lat());
					$("#lng").val(position.lng());
					return this;
				},
				draw: function () {
					var $el,
						self = this,
						tmp = {cnt: 0, type: ""},
						mapBounds = new google.maps.LatLngBounds();
					$(".coords").each(function (i, el) {
						$el = $(el);
						tmp.cnt += 1;
						switch ($el.data("type")) {
							case 'circle':
								var str = $el.val().replace(/\(|\)|\s+/g, ""),
									arr = str.split("|"),
									center = new google.maps.LatLng(arr[0].split(",")[0], arr[0].split(",")[1]);
	
								var circle = new google.maps.Circle({
									strokeColor: '#008000',
									strokeOpacity: 1,
									strokeWeight: 1,
									fillColor: '#008000',
									fillOpacity: 0.5,
									center: center,								
						            radius: parseFloat(arr[1]),
						            editable: true,
						            center_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'circle');
						            	};
						            }($el),
						            radius_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'circle');
						            	};
						            }($el)
								});
								circle.myObj = {
									"id": $el.data("id")
								};
								circle.setMap(self.map);
								mapBounds.extend(center);
								google.maps.event.addListener(circle, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this);
									selectedShape = this.myObj.id;
								});
								overlays.push(circle);
								tmp.type = "circle";
								break;
							case 'polygon':
								var path,
									str = $el.val().replace(/\(|\s+/g, ""),
									arr = str.split("),"),
									paths = [];
								arr[arr.length-1] = arr[arr.length-1].replace(")", "");
								for (var i = 0, len = arr.length; i < len; i++) {
									path = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
									paths.push(path);
									mapBounds.extend(path);
								}
								var polygon = new google.maps.Polygon({
									paths: paths,
									strokeColor: '#008000',
									strokeOpacity: 1,
									strokeWeight: 1,
									fillColor: '#008000',
									fillOpacity: 0.5,
						            editable: true
							    });
								polygon.myObj = {
									"id": $el.data("id")
								};
								polygon.setMap(self.map);
									
								google.maps.event.addListener(polygon, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this);
									selectedShape = this.myObj.id;
								});
								overlays.push(polygon);
								tmp.type = "plygon";
								break;
							case 'rectangle':
								var bound,
									str = $el.val().replace(/\(|\s+/g, ""),
									arr = str.split("),"), 
									bounds = [];
								for (var i = 0, len = arr.length; i < len; i++) {
									arr[i] = arr[i].replace(/\)/g, "");
									bound = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
									bounds.push(bound);
									mapBounds.extend(bound);
								}
								var rectangle = new google.maps.Rectangle({
									strokeColor: '#008000',
						            strokeOpacity: 1,
						            strokeWeight: 1,
						            fillColor: '#008000',
						            fillOpacity: 0.5,
						            bounds: new google.maps.LatLngBounds(bounds[0], bounds[1]),
						            editable: true,
						            bounds_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'rectangle');
						            	};
						            }($el)
								});
								
								rectangle.myObj = {
									"id": $el.data("id")
								};
								rectangle.setMap(self.map);
									
								google.maps.event.addListener(rectangle, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this);
									selectedShape = this.myObj.id;
								});
								overlays.push(rectangle);
								tmp.type = "rectangle";
								break;
						}
					});
					
					if (tmp.cnt === 1 && tmp.type === "circle") {
						this.map.setZoom(13);
					} else {
						this.map.fitBounds(mapBounds);
					}
				},
				drawing: function () {
					var self = this;
					this.drawingManager = new google.maps.drawing.DrawingManager({
						drawingMode: google.maps.drawing.OverlayType.POLYGON,
						drawingControl: true,
						drawingControlOptions: {
							position: google.maps.ControlPosition.TOP_CENTER,
							drawingModes: [
					            google.maps.drawing.OverlayType.CIRCLE,
					            google.maps.drawing.OverlayType.POLYGON,
					            google.maps.drawing.OverlayType.RECTANGLE
					        ]
						},
						circleOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						},
						polygonOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						},
						rectangleOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						}
					});
					this.drawingManager.setMap(this.map);
					
					google.maps.event.addListener(this.drawingManager, 'overlaycomplete', function(event) {
						var rand = Math.ceil(Math.random() * 999999),
							$frm = $(".frmLocation").eq(0);
						switch (event.type) {
							case google.maps.drawing.OverlayType.CIRCLE:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[circle][new_" + rand + "]",
									"class": "coords",
									"data-type": "circle",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'circle');
								break;
							case google.maps.drawing.OverlayType.POLYGON:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[polygon][new_" + rand + "]",
									"class": "coords",
									"data-type": "polygon",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'polygon');
								break;
							case google.maps.drawing.OverlayType.RECTANGLE:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[rectangle][new_" + rand + "]",
									"class": "coords",
									"data-type": "rectangle",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'rectangle');
								break;
						}
						
						event.overlay.myObj = {
							id: "new_" + rand
						};
						
						google.maps.event.addListener(event.overlay, "click", function () {
							self.removeFocus(overlays, this.myObj.id);
							self.setFocus(this);
							selectedShape = this.myObj.id;
						});
						
						overlays.push(event.overlay);
					});
				},
				update: function (obj, $el, type) {
					switch (type) {
						case "circle":
							$el.val(obj.getCenter().toString()+"|"+obj.getRadius());
							break;
						case "polygon":
							var str = [],
								paths = obj.getPaths();
							paths.getArray()[0].forEach(function (el, i) {
								str.push(el.toString());
							});
							$el.val(str.join(", "));
							break;
						case "rectangle":
							$el.val(obj.getBounds().toString());
							break;
					}
				},
				deleteShape: function (overlays) {
					if (overlays && overlays.length > 0) {
						for (var i = 0, len = overlays.length; i < len; i++) {
							if (overlays[i].myObj.id == selectedShape) {
								overlays[i].setMap(null);
								$(".btnDeleteShape").css('display', 'none');
								$(".coords[data-id='" + selectedShape + "']").remove();
								return true;
								break;
							}
						}
					}
					return false;
				},
				clearOverlays: function (overlays) {
					if (overlays && overlays.length > 0) {
						while (overlays[0]) {
							overlays.pop().setMap(null);
						}
					}
				},
				setFocus: function (overlay) {
					overlay.setOptions({
						strokeColor: '#1B7BDC',
						fillColor: '#4295E8'
					});
					$(".btnDeleteShape").css('display', 'inline-block');
				},
				removeFocus: function (overlays, exceptId) {
					if (overlays && overlays.length > 0) {
						for (var i = 0, len = overlays.length; i < len; i++) {
							if (overlays[i].myObj.id != exceptId) {
								overlays[i].setOptions({
									strokeColor: '#008000',
									fillColor: '#008000'
								});
							}
						}
					}
				}
			};
			if($frmCreateLocation.length > 0)
			{
				myGoogleMaps = new GoogleMaps();
				myGoogleMaps.drawing();
				google.maps.event.trigger(myGoogleMaps.map, 'resize');
				
				google.maps.event.addDomListener($(".btnDeleteShape").get(0), "click", function () {
					myGoogleMaps.deleteShape(overlays);
				});
			}
			if($frmUpdateLocation.length > 0)
			{
				var lat = $("#lat").val(),
					lng = $("#lng").val();
				
				if (myGoogleMaps == null) {
					myGoogleMaps = new GoogleMaps();
				}
				myGoogleMaps.addMarker(new google.maps.LatLng(lat, lng));
				if ($(".coords").length === 0) {
					if (lat !== undefined && lng !== undefined && lat.length > 0 && lng.length > 0) {
						myGoogleMaps.map.setCenter(new google.maps.LatLng(lat, lng));
					}
				} else {
					myGoogleMaps.draw();
				}
				myGoogleMaps.drawing();
				
				google.maps.event.addDomListener($(".btnDeleteShape").get(0), "click", function () {
					myGoogleMaps.deleteShape(overlays);
				});
			}
		}
		if ($frmUpdatePrices.length > 0 && validate) {
			$frmUpdatePrices.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmGetDetails.length > 0 && validate) {
			$frmGetDetails.validate({
				rules: {
					merchant_id: "required"
				},
				messages: {
					merchant_id: "Please fill merchant id!"
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
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminLocations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocation&id={:id}"},
						  {type: "getDetails", url: "{:id}"}
				          ],
				columns: [{text: myLabel.location_name, type: "text", sortable: true, editable: true, width: 240, editableWidth: 230},
				          {text: myLabel.address, type: "text", sortable: true, editable: true, width: 340, editableWidth: 330}],
				dataUrl: "index.php?controller=pjAdminLocations&action=pjActionGetLocation",
				dataType: "json",
				fields: ['name', 'address'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocationBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminLocations&action=pjActionSaveLocation&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminLocations&action=pjActionGetLocation", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btnGetCoords", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $frm = null;
			if($frmCreateLocation.length > 0)
			{
				$frm = $frmCreateLocation;
			}
			if($frmUpdateLocation.length > 0)
			{
				$frm = $frmUpdateLocation;
			}
			$('.pj-loader').css('display', 'block');
			$.post("index.php?controller=pjAdminLocations&action=pjActionGetCoords", $frm.serialize()).done(function (data) {
				$('.pj-loader').css('display', 'none');
				if (data.lat && data.lng) {
					$("#fd_get_coords_error").hide();
					if (myGoogleMaps == null) {
						myGoogleMaps = new GoogleMaps();
					}
					google.maps.event.trigger(myGoogleMaps.map, 'resize');
					myGoogleMaps.map.setCenter(new google.maps.LatLng(data.lat, data.lng));
					myGoogleMaps.addMarker(new google.maps.LatLng(data.lat, data.lng));
				} else {
					$("#fd_get_coords_error").show();
					$("#lat").val("");
					$("#lng").val("");
				}
			});
			return false;
		}).on("click", ".pj-add-price", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#tblClonePrices").find("tbody").clone();
			$($clone.html()).appendTo("#tblPrices tbody");
			return false;
		}).on("click", ".pj-remove-price", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).parent().parent().remove();
			return false;
		}).on("click", ".btnGetDetails", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $frm = $frmGetDetails;
			$('.pj-loader-1').css('display', 'block');
			$.post("index.php?controller=pjAdminLocations&action=getCloverAddress", $frm.serialize()).done(function (data) {
				$("#i18n_name_1").val(data.name);
				$("#i18n_address_1").val(data.address);
				$('.pj-loader-1').css('display', 'none');
			});			
			return false;
		}).on("click", ".pjCloverApi", function(e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $frm = $frmGetDetails;
			$('.pj-loader-2').css('display', 'block');
			$.post("index.php?controller=pjAdminLocations&action=pjActionGetCloverData", $frm.serialize()).done(function (data) {
				$($frm)[0].reset();
				$($frmCreateLocation)[0].reset();
				$('.pj-loader-2').css('display', 'none');				
			});
			return false;
		}).on("click", ".pj-table-icon-getDetails", function(e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var curl = this.href;
			var arr = curl.split('/');
			var strFine = arr[arr.length-1];
			$.get(["index.php?controller=pjAdminLocations&action=pjActionCloverUpdate"].join(""), {'id': strFine}).done(function(data){
				//do something here.
			});
		});
	});
})(jQuery_1_8_2);