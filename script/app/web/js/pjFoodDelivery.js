/*!
 * Food Delivery v2.0
 * http://www.phpjabbers.com/food-delivery-script/
 * 
 * Copyright 2014, StivaSoft Ltd.
 * 
 */
(function (window, undefined){
	"use strict";
	var document = window.document,
		myPickupMap = null,
		myPickupMarker = [],
		myPickupMarkers = [],
		myPickupOverlays = [],
		myPickupBounds = null,
		myPickupInfoWindow = false,
		myDeliveryMap = null,
		myDeliveryMarkers = [],
		myDeliveryOverlays = [],
		myDeliveryBounds = null,
		routes = [
		          	{pattern: /^#!\/loadMain$/, eventName: "loadMain"},
		          	{pattern: /^#!\/loadOptions$/, eventName: "loadOptions"},
		          	{pattern: /^#!\/loadTypes$/, eventName: "loadTypes"},
		          	{pattern: /^#!\/loadLogin$/, eventName: "loadLogin"},
		          	{pattern: /^#!\/loadForgot$/, eventName: "loadForgot"},
		          	{pattern: /^#!\/loadCheckout$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/loadPreview$/, eventName: "loadPreview"}
		         ],
		myTmp = {
			cnt: 0, 
			type: ""
		};
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadMain");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function FoodDelivery(opts) {
		if (!(this instanceof FoodDelivery)) {
			return new FoodDelivery(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	FoodDelivery.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	FoodDelivery.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	FoodDelivery.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.$responsive = null;			
			this.responsive = null;
			this.category_id = null; 
			this.opts = {};
			this.type = 'pickup';
			this.location_id = null;
			this.datesOff = [];
			this.datesOn = [];
			this.daysOff = [];
			this.deliveryDate = null;
			this.pickupDate = null;
			this.coord_id = null;
			this.myDeliveryOverlays = null;
			this.myDeliveryMap = null;
			this.cart = '';
			
			return this;
		},
		_daysOff: function (date) {
			var self = this,
				isDateOff = self._datesOff(date),
				isDateOn = self._datesOn(date);
			if (isDateOff[0] && !isDateOn[0]) {
				for (var i = 0, len = self.daysOff.length; i < len; i++) {
					if (self.daysOff[i] === date.getDay()) {
						return [false, 'bcal-past'];
					}
				}
				return [true, ''];
			} else {
				return isDateOff[0] ? isDateOff: isDateOn;
			}
		},
		_datesOff: function (date) {
			var d, i, len, dt,
				self = this;
			for (i = 0, len = self.datesOff.length; i < len; i++) {
				dt = self.datesOff[i].split("-");
				d = new Date(parseInt(dt[0], 10), parseInt(dt[1], 10) - 1, parseInt(dt[2], 10));
				if (d.getTime() === date.getTime()) {
					return [false, 'bcal-past'];
				}
			}
			return [true, ''];
		},
		_datesOn: function (date) {
			var d, i, len, dt,
				self = this;
			for (i = 0, len = self.datesOn.length; i < len; i++) {
				dt = self.datesOn[i].split("-");
				d = new Date(parseInt(dt[0], 10), parseInt(dt[1], 10) - 1, parseInt(dt[2], 10));
				if (d.getTime() === date.getTime()) {
					return [true, ''];
				}
			}
			return [false, 'bcal-past'];
		},
		setDays: function (location_id, type) {
			var self = this;
			self.daysOff = [];
			self.datesOff = [];
			self.datesOn = [];
			if (self.opts.daysOff[location_id] && self.opts.daysOff[location_id][type]) 
			{
				self.daysOff = self.opts.daysOff[location_id][type];
			}
			if (self.opts.datesOff[location_id] && self.opts.datesOff[location_id][type]) 
			{
				self.datesOff = self.opts.datesOff[location_id][type];
			}			
			if (self.opts.datesOn[location_id] && self.opts.datesOn[location_id][type]) 
			{
				self.datesOn = self.opts.datesOn[location_id][type];
			}
			return self;
		},
		disableButtons: function () {
			this.$container.find(".fdButton").each(function (i, el) {
				pjQ.$(el).addClass("fdButtonDisabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".fdButton").removeClass("fdButtonDisabled");
		},
		bindResponsive: function(){
			var self = this;
			
			self.$responsive.removeClass('w1024 w980-1024 w860-979 w700-859 w600-699 w599').addClass(function(){
				var cls = '', 
					width = self.$responsive.width();
				
				if(width >= 1024)
				{
					cls = 'w1024';
				}else if(width >=980 && width < 1024){
					cls = 'w980-1024';
				}else if(width >=860 && width < 980){
					cls = 'w860-979';
				}else if(width >=700 && width < 860){
					cls = 'w700-859';
				}else if(width >=600 && width < 700){
					cls = 'w600-699';
				}else if(width < 599){
					cls = 'w599';
				}
				return cls;
			});
			self.$responsive.removeClass('w750 w520-749 w520').addClass(function(){
				var cls = '', 
					width = self.$responsive.width();
				
				if(width >= 750)
				{
					cls = 'w750';
				}else if(width >=520 && width < 750){
					cls = 'w520-749';
				}else if(width < 520){
					cls = 'w520';
				}
				return cls;
			});
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("fdContainer_" + self.opts.index);
			this.responsive = document.getElementById("fdResponsive_" + self.opts.index);
			
			self.$container = pjQ.$(self.container);
			self.$responsive = pjQ.$(self.responsive);
			
			new ResizeSensor(self.$responsive, function(){
				self.bindResponsive();
		    });
			
			self.bindResponsive();
			
			this.$container.on("click.fd", ".fdSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.opts.locale = locale;
				pjQ.$(this).addClass("fdLocaleFocus").parent().parent().find("a.fdSelectorLocale").not(this).removeClass("pcLocaleFocus");
				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), {
					"locale_id": locale
				}).done(function (data) {
					if (!hashBang("#!/loadMain")) {
						pjQ.$(window).trigger("loadMain");
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("click.fd", ".fdPrev", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var leftPos = pjQ.$('.fdCategoryList').scrollLeft();
				pjQ.$(".fdCategoryList").animate({scrollLeft: leftPos - 100}, 500);
				return false;
			}).on("click.fd", ".fdNext", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var total_width = 0,
				    leftPos = pjQ.$('.fdCategoryList').scrollLeft();
				pjQ.$("#fdCateInner_" + self.opts.index).find(".fdCateItem").each(function (order, item) {
		    		total_width += pjQ.$(item).outerWidth() + 12;
				});
		    	pjQ.$("#fdCateInner_" + self.opts.index).width(total_width);
				pjQ.$(".fdCategoryList").animate({scrollLeft: leftPos + 100}, 500);
				return false;
			}).on("click.fd", ".fdProductTitle", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var product_id = pjQ.$(this).attr('data-id'),
					$fdProduct =  pjQ.$('#fdProductBox_' + product_id),
					$fdSize = pjQ.$('#fdSelectSize_' + product_id);
				if($fdProduct.hasClass('fdProductBoxSelected'))
				{
					$fdProduct.removeClass('fdProductBoxSelected');
				}else{
					$fdProduct.addClass('fdProductBoxSelected');
				}
				if($fdSize.length > 0 && $fdSize.val() == '')
				{
					pjQ.$(pjQ.$('#fdProductOrder_' + product_id)).addClass("fdButtonDisabled");
				}
				return false;
			}).on("change.fd", ".fdSelectSize", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var product_id = pjQ.$(this).attr('data-id'),
					$fdProduct =  pjQ.$('#fdProductBox_' + product_id);
				if(pjQ.$(this).val() != '')
				{
					if(!$fdProduct.hasClass('fdProductBoxSelected'))
					{
						$fdProduct.addClass('fdProductBoxSelected');
					}
					pjQ.$(pjQ.$('#fdProductOrder_' + product_id)).removeClass("fdButtonDisabled");
				}else{
					$fdProduct.removeClass('fdProductBoxSelected');
					pjQ.$(pjQ.$('#fdProductOrder_' + product_id)).addClass("fdButtonDisabled");
				}
				return false;
			}).on("click.fd", ".fdImage", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var product_id = pjQ.$(this).attr('data-id'),
					$fdProduct =  pjQ.$('#fdProductBox_' + product_id);
				if($fdProduct.hasClass('fdProductBoxSelected'))
				{
					$fdProduct.removeClass('fdProductBoxSelected');
				}else{
					$fdProduct.addClass('fdProductBoxSelected');
				}
				return false;
			}).on("click.fd", ".fdCategoryNode", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var category_id = parseInt(pjQ.$(this).attr('data-id'), 10);
				var lc_id = parseInt(pjQ.$(this).attr('data-lc'), 10);				
				self.loadCategories.call(self, category_id, lc_id);
				return false;
			}).on("click.fd", ".fdAddExtra", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var index = pjQ.$(this).attr('data-index');
				pjQ.$('#fdQty_' + index).val(1);
				pjQ.$(this).parent().addClass('fdExtraBoxSelected');
				return false;
			}).on("click.fd", ".fdOperator", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var index = pjQ.$(this).attr('data-index'),
					sign = pjQ.$(this).attr('data-sign'),
					product_id = pjQ.$(this).attr('data-product'),
					value = parseInt(pjQ.$('#fdQty_' + index).val(), 10);
				if(sign == '+')
				{
					value++;
				}else{
					if(value > 0)
					{
						value--;
					}
				}
				pjQ.$('#fdQty_' + index).val(value);				
				if(value == 0)
				{
					pjQ.$(this).parent().parent().parent().removeClass('fdExtraBoxSelected');
				}
				return false;
			}).on("keypress.fd", ".fdQtyInput", function (e) {
				if(e.which !=8 && isNaN(String.fromCharCode(e.which)))
				{
					e.preventDefault();
				}
			}).on("keyup.fd", ".fdQtyInput", function (e) {
				if(parseInt(pjQ.$(this).val(),10) == 0)
				{
					pjQ.$(this).parent().parent().parent().removeClass('fdExtraBoxSelected');
				}else if(pjQ.$(this).val() == ''){
					pjQ.$(this).val(0);
					pjQ.$(this).parent().parent().parent().removeClass('fdExtraBoxSelected');
				}
					
			}).on("focus.fd", ".fdQtyInput", function (e) {
				pjQ.$(this).select();
			}).on("click.fd", ".fdProductOrder", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $frm = pjQ.$(this).closest("form"),
					addable = true,
					product_id = pjQ.$(this).attr('data-id'),
					$size = pjQ.$('#fdSelectSize_' + product_id);
				if($size.length > 0 && $size.val() == '')
				{
					addable = false;
				}
				if(addable == true)
				{
					pjQ.$('.fdLoader').css('display', 'block');
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionAddProduct"].join(""), $frm.serialize()).done(function (data) {
						self.cart = '';
						self.loadCart.apply(self, ['', function(){
							pjQ.$('.fdLoader').css('display', 'none');
						}]);
					});
				}
				return false;
			}).on("click.fd", ".fdCartItemRemove", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var hash = pjQ.$(this).attr('data-hash'),
					extra_id = pjQ.$(this).attr('data-extra');
				
				self.removeItem.apply(self, [hash, extra_id]);
				return false;
			}).on("click.fd", ".fdButtonCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var logged = pjQ.$(this).attr('data-logged');
				pjQ.$('.fdLoader').css('display', 'block');
				if(logged == 'no')
				{
					hashBang("#!/loadLogin");
				}else{
					hashBang("#!/loadTypes");
				}
				return false;
			}).on("click.fd change.fd", "input[name='type']", function (e) {
				var $this = pjQ.$(this);
				switch ($this.filter(":checked").val()) {
					case 'pickup':
						pjQ.$(".fdPickup").show();
						pjQ.$(".fdDelivery").hide();
						self.type = 'pickup';
						if (myPickupMap !== null) {
							google.maps.event.trigger(myPickupMap.map, "resize");
						}
						break;
					case 'delivery':
						pjQ.$(".fdDelivery").show();
						if (myDeliveryMap !== null) {
							myDeliveryMap.map.fitBounds(myDeliveryBounds);
							if (myTmp.cnt === 1 && myTmp.type === "circle") {
								myDeliveryMap.map.setZoom(13);
							}
							google.maps.event.trigger(myDeliveryMap.map, "resize");
						}
						pjQ.$(".fdPickup").hide();
						break;
				}
				pjQ.$(".fdErrorBox").hide();
				pjQ.$(".fdType").removeClass("fdTypeFocus");
				pjQ.$("label[for='"+ $this.attr("id") +"']").addClass("fdTypeFocus");
			}).on("change.fd", "select[name='p_location_id']", function (e) {
				var location_id = pjQ.$("option:selected", this).val();
				pjQ.$('.fdLoader').css('display', 'block');
				self.location_id = location_id;
				self.getLocation.apply(self, [location_id, true, function(e){
					self.getWTime.apply(self, [pjQ.$("#fd_p_date_" + self.opts.index).val(), location_id, 'pickup', function (data) {
						pjQ.$(".fdPickupTime").html(data);
					}]);
				}]);
				self.setDays.apply(self, [location_id, 'pickup']);
				self.pickupDate.opts.onBeforeShowDay = function (date) 
				{
					return self._daysOff.apply(self, [date]);
				};
				self.pickupDate.refresh();
				
			}).on("change.fd", "select[name='d_location_id']", function (e) {
				
				var d_location_id = pjQ.$("option:selected", this).val(),
					d_location_name = pjQ.$("option:selected", this).text();
				
				if(d_location_id != '')
				{
					pjQ.$(".fdDeliveryNote")
						.html(self.opts.messages[11].replace('{LOCATION}', d_location_name))
						.show();
				}
				self.setDays.apply(self, [d_location_id, 'delivery']);
				self.deliveryDate.opts.onBeforeShowDay = function (date) {
					return self._daysOff.apply(self, [date]);
				};
				self.deliveryDate.refresh();
				
				if(self.myDeliveryOverlays != null && self.myDeliveryMap != null)
				{
					var shape_focus = null;
					for (var i = 0, len = self.myDeliveryOverlays.length; i < len; i++) {
						self.myDeliveryMap.removeFocus(self.myDeliveryOverlays, self.myDeliveryOverlays[i].myObj.id);
						if (self.myDeliveryOverlays[i].myObj.location_id == d_location_id) {
							shape_focus = self.myDeliveryOverlays[i];
						}
					}
					if(shape_focus != null)
					{
						self.myDeliveryMap.setFocus(shape_focus);
					}
				}
				
				pjQ.$('.fdLoader').css('display', 'block');
				self.getWTime.apply(self, [pjQ.$("#fd_d_date_" + self.opts.index).val(), d_location_id, 'delivery', function (data) {
					pjQ.$(".fdDeliveryTime").html(data);
				}]);
				
			}).on("click.fd", ".fdButtonGetCategories", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadMain");
				return false;
			}).on("click.fd", ".fdButtonPostPrice", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('fdButtonDisabled'))
				{
					if (!self.validateTypes.call(self)) {
						return false;
					}
					var $frm = pjQ.$('#fdMain_' + self.opts.index).find("form");
					pjQ.$('.fdLoader').css('display', 'block');
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetTypes"].join(""), $frm.serialize()).done(function (data) {
						hashBang("#!/loadCheckout");
					});
				}
				return false;
			}).on("click.fd", ".fdButtonGetTypes", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				//hashBang("#!/loadTypes");
				hashBang("#!/loadLogin");
				return false;
			}).on("click.fd", ".fdButtonLogin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!self.validateLogin.call(self)) {
					return false;
				}
				var $frm = pjQ.$('#fdLoginForm_' + self.opts.index);
				pjQ.$('.fdLoader').css('display', 'block');
				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckLogin"].join(""), $frm.serialize()).done(function (data) {
					if(data.code != '200')
					{
						pjQ.$('#fdLoginMessage_' + self.opts.index).html(self.opts.login_messages[parseInt(data.code, 10)]);
						pjQ.$('#fdLoginMessage_' + self.opts.index).css('display', 'block');
						pjQ.$('.fdLoader').css('display', 'none');
					}else{
						//hashBang("#!/loadTypes");
						hashBang('#!/loadCheckout');
					}
				});
				return false;
			}).on("click.fd", ".fdContinue", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadTypes");
				return false;
			}).on("change.fd", "#fdPaymentMethod_" + self.opts.index, function (e) {
				if(pjQ.$(this).val() == 'creditcard')
				{
					pjQ.$('#fdCCData_' + self.opts.index).show();
				}else{
					pjQ.$('#fdCCData_' + self.opts.index).hide();
				}
			}).on("click.fd", "#fdBtnTerms_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $terms = pjQ.$("#fdTermContainer_" + self.opts.index);
				if($terms.is(':visible')){
					$terms.css('display', 'none');
				}else{
					$terms.css('display', 'block');
				}
				return false;
			}).on("click.fd", ".fdButtonApply", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('fdButtonDisabled'))
				{
					var voucher_code = pjQ.$('#fdVoucherCode_' + self.opts.index).val();
					if(voucher_code != '')
					{
						pjQ.$('.fdLoader').css('display', 'block');
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionAddPromo"].join(""), {voucher_code: voucher_code}).done(function (data) {
							if (!data.code) {
								return;
							}
							switch (data.code) {
								case 200:
									self.cart = 'total';
									self.loadCart.apply(self, ['', function(){
										pjQ.$('.fdLoader').css('display', 'none');
									}]);
									break;
								default:
									pjQ.$('#fdVoucherMessage_' + self.opts.index).html(data.text).parent().show();
									pjQ.$('.fdLoader').css('display', 'none');
									break;
							}
						});
					}
				}
				return false;
			}).on("click.fd", ".fdButtonGetLogin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadLogin");
				return false;
			}).on("click.fd", ".fdButtonGetPreview", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.validateDetails.call(self)
				return false;
			}).on("click.fd", ".fdButtonGetCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('fdButtonDisabled'))
				{
					pjQ.$('.fdLoader').css('display', 'block');
					hashBang("#!/loadCheckout");
				}
				return false;
			}).on("click.fd", ".fdButtonConfirm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('fdButtonDisabled'))
				{
					self.disableButtons.call(self);
					var $msg_container = pjQ.$('#fdOrderMessage_' + self.opts.index);
					$msg_container.html(self.opts.messages[12]);
					$msg_container.parent().css('display', 'block');
					
					var params = {
						"location_id": pjQ.$(this).attr('data-location'),
						"payment": pjQ.$(this).attr('data-pay'),
						"price" : pjQ.$(this).attr('data-amt'),
						"tax" : pjQ.$(this).attr('data-tax')
					};
					
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionPostCloverData"].join(""), params).done(function (data){
						//Do something
					});
					
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveOrder"].join("")).done(function (data) {
						if (!data.code) {
							return;
						}
						switch (parseInt(data.code, 10)) {
							case 100:
								$msg_container.html(self.opts.messages[4]);
								self.enableButtons.call(self);
								break;
							case 200:
							case 201:
								if(data.path == 'cash')
								{
									self.getPaymentForm(data);
								} else {								
									window.location.href = data.path;
								}
								break;
						}
					});
				}
				return false;
			}).on("click.fd", ".fdStartOver", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/loadMain");
				return false;
			}).on("click.fd", ".fdChangePersonal", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdPersonalField ').prop("disabled", false);
				return false;
			}).on("click.fd", ".fdChangeAddress", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdAddrField').prop("disabled", false);
				pjQ.$('#fdPreviousAddresses_' + self.opts.index).show();
				return false;
			}).on("change.fd", "#fdPreviousAddr_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $selected = pjQ.$('option:selected', pjQ.$(this));
				var $form = pjQ.$('#fdTypeForm_' + self.opts.index),
					add1 = $selected.attr('data-add1'),
					add2 = $selected.attr('data-add2'),
					city = $selected.attr('data-city'),
					state = $selected.attr('data-state'),
					zip = $selected.attr('data-zip'),
					country = $selected.attr('data-country');
				
				$form.find("select[name='d_country_id']").val(country);
				$form.find("input[name='d_address_1']").val(add1);
				$form.find("input[name='d_address_2']").val(add2);
				$form.find("input[name='d_city']").val(city);
				$form.find("input[name='d_state']").val(state);
				$form.find("input[name='d_zip']").val(zip);
				return false;
			}).on("click.fd", ".fdButtonSkipStep", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadCheckout");
				return false;
			}).on("click.fd", ".fdForogtPassword", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadForgot");
				return false;
			}).on("click.fd", ".fdButtonSend", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!self.validateForgot.call(self)) {
					return false;
				}
				var $frm = pjQ.$('#fdForgotForm_' + self.opts.index);
				pjQ.$('.fdLoader').css('display', 'block');
				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSendPassword"].join(""), $frm.serialize()).done(function (data) {
					pjQ.$('#fdForgotMessage_' + self.opts.index).html(self.opts.forgot_messages[parseInt(data.code, 10)]);
					pjQ.$('#fdForgotMessage_' + self.opts.index).css('display', 'block');
					pjQ.$('.fdLoader').css('display', 'none');
				});
				return false;
			}).on("click.fd", ".fdValidateLogin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.fdLoader').css('display', 'block');
				hashBang("#!/loadLogin");
				return false;
			});
			
			pjQ.$(window).on("loadMain", this.$container, function (e) {
				self.loadMain.call(self, 0);
			}).on("loadOptions", this.$container, function (e) {
				self.loadOptions.call(self);
			}).on("loadTypes", this.$container, function (e) {
				self.loadTypes.call(self);
			}).on("loadLogin", this.$container, function (e) {
				self.loadLogin.call(self);
			}).on("loadForgot", this.$container, function (e) {
				self.loadForgot.call(self);
			}).on("loadCheckout", this.$container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.$container, function (e) {
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadMain.call(this);
			} else {
				onHashChange.call(null);
			}
		},
		loadMain: function () {
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,							
							"type": ""
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionMain"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadCategories: function (category_id, lc_id) {
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": index,
							"category_id": category_id
						 };
			pjQ.$('.fdLoader').css('display', 'block');
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCategories"].join(""), params).done(function (data) {
				pjQ.$('#fdMain_' + index).html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadLocationCategories: function (location_id) {
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"lc": location_id,
							"type": ""
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionMain"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadCart: function (type, callback) {
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": index, 
							"type": self.cart
						 };
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionCart", type].join(""), params).done(function (data) {
				pjQ.$('#fdCart_' + index).html(data);
				if (typeof callback != "undefined") {
					callback.call(self);
				}
			});
		},
		loadTypes: function(callback){
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"type": "plain"
						 };
			
			if (self.type == "pickup" && self.location_id !== null) {
				self.setDays.apply(self, [self.location_id, "pickup"]);
			}
			if (self.type == "delivery" && self.location_id !== null) {
				self.setDays.apply(self, [self.location_id, "delivery"]);
			}
		
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionTypes"].join(""), params).done(function (data) {
				self.$container.html(data);
				if(pjQ.$('#fdTypeForm_' + self.opts.index).length > 0)
				{
					self.cart = 'plain';
					if (typeof window.initializeFD == "undefined") 
					{
						window.initializeFD = function () 
						{
							var id = pjQ.$("option:selected", pjQ.$('#fdMain_' + index).find("select[name='p_location_id']")).val();
							if (typeof id != "undefined" && parseInt(id, 10) > 0) {
								self.getPickupLocations.apply(self, [id, true]);
							}else{
								self.getPickupLocations.apply(self, [null, true]);
							}
							self.getLocations.call(self);
						};
						pjQ.$.getScript("https://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing&callback=initializeFD");
					} else {
						var id = pjQ.$("option:selected", pjQ.$('#fdMain_' + index).find("select[name='p_location_id']")).val();
						if (typeof id != "undefined" && parseInt(id, 10) > 0) {
							self.getPickupLocations.apply(self, [id, true]);
						}else{
							self.getPickupLocations.apply(self, [null, true]);
						}
						self.getLocations.call(self);
					}
					
					self.pickupDate = new Calendar({
						element: "fd_p_date_" + self.opts.index,
						dateFormat: self.opts.dateFormat,
						monthNamesFull: self.opts.monthNamesFull,
						dayNames: self.opts.dayNames,
						startDay: self.opts.startDay,
						disablePast: true,
						onBeforeShowDay: function (date) 
						{
							return self._daysOff.apply(self, [date]);
						},
						onSelect: function (element, selectedDate, date, cell){
							if(pjQ.$("select[name='p_location_id']").val() != '')
							{
								pjQ.$('.fdLoader').css('display', 'block');
								self.getWTime.apply(self, [selectedDate, pjQ.$("option:selected", pjQ.$("select[name='p_location_id']")).val(), 'pickup', function (data) {
									pjQ.$(".fdPickupTime").html(data);
								}]);
							}
						}
					});
					self.deliveryDate = new Calendar({
						element: "fd_d_date_" + self.opts.index,
						dateFormat: self.opts.dateFormat,
						monthNamesFull: self.opts.monthNamesFull,
						dayNames: self.opts.dayNames,
						startDay: self.opts.startDay,
						disablePast: true,
						onBeforeShowDay: function (date) {
							return self._daysOff.apply(self, [date]);
						},
						onSelect: function (element, selectedDate, date, cell) {
							pjQ.$('.fdLoader').css('display', 'block');
							self.getWTime.apply(self, [selectedDate, pjQ.$("select[name='d_location_id']").val(), 'delivery', function (data) {
								pjQ.$(".fdDeliveryTime").html(data);
							}]);
						}
					});
				}
			});
		},
		loadLogin: function(callback){
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"type": "plain"
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionLogin"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadForgot: function(callback){
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"type": "plain"
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionForgot"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadCheckout: function(callback){
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"type": "total"
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		loadPreview: function(callback){
			var self = this,
				index = this.opts.index,
				params = { "locale": this.opts.locale,
							"hide": this.opts.hide,
							"index": this.opts.index,
							"type": "total"
						 };
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPreview"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.fdLoader').css('display', 'none');
			}).fail(function () {
				
			});
		},
		removeItem: function (hash, extra_id) {
			var self = this;
			pjQ.$('.fdLoader').css('display', 'block');
			pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionRemove"].join(""), {
				"hash": hash,
				"extra_id": extra_id
			}).done(function (data) {
				self.loadCart.apply(self, ['', function(){
					pjQ.$('.fdLoader').css('display', 'none');
				}]);
			});
		},
		getPickupLocations: function (id, init, callback) {
			var self = this,
				LatLngList = [];
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetPickupLocations"].join(""), {
				"id": id
			}).done(function (data) {
				pjQ.$("#fdTypeMap_" + self.opts.index).show();
				
				if (myPickupMarkers) {
					for (i in myPickupMarkers) {
						if (myPickupMarkers.hasOwnProperty(i)) {
							myPickupMarkers[i].setMap(null);
						}
					}
					myPickupMarkers.length = 0;
				}
				
				if (data.length > 0) 
				{
					var selected_index = null;
					
					myPickupMap = new GoogleMaps({
						id: "fdTypeMap_" + self.opts.index,
						icon: self.opts.server + "app/web/img/frontend/pin.png"
					});
					myPickupOverlays = [];
					myPickupMarkers = [];
					myPickupBounds = new google.maps.LatLngBounds();
					for (var index = 0, ilen = data.length; index < ilen; index++) 
					{
						var fdPickupLatlng = new google.maps.LatLng(data[index].lat, data[index].lng),
							location_id = data[index].id;
						var marker = new google.maps.Marker({
							map: myPickupMap.map,
							position: fdPickupLatlng,
							icon: self.opts.server + "app/web/img/frontend/pin.png",
							title: data[index].name
						});
						marker.fdObj = {
							"id": data[index].id,
							"name": data[index].name,
							"address": data[index].address
						};
						myPickupMap.map.setCenter(marker.getPosition());
						LatLngList.push(fdPickupLatlng);
						
						if(id == data[index].id)
						{
							selected_index = index;
						}
						if (data[index].name != "") 
						{
							marker.infoWindow = new google.maps.InfoWindow({
								content: data[index].name + "<br/>" + data[index].address
							});
							google.maps.event.addListener(marker, "click", function() {
								//alert(myPickupMarkers.length);
								for (var i = myPickupMarkers.length - 1; i >= 0; i--) 
								{
									//myPickupMarkers[i].infoWindow.close();
								}
								this.infoWindow.open(myPickupMap.map, this);
								self.setPickupLocaton.apply(self, [this.fdObj]);
							});
						}
						myPickupMarkers.push(marker);
					}
					for (var i = myPickupMarkers.length - 1; i >= 0; i--) 
					{
						myPickupMarkers[i].setMap(myPickupMap.map);
					}
					if(selected_index != null)
					{
						google.maps.event.trigger(myPickupMarkers[selected_index], 'click');
					}
					
					for (var j = 0, len = LatLngList.length; j < len; j++) 
					{
						myPickupBounds.extend(LatLngList[j]);
					}
					myPickupMap.map.fitBounds(myPickupBounds);
				}
				if (typeof callback != "undefined") {
					callback.call(self);
				}else{
					pjQ.$('.fdLoader').css('display', 'none');
				}
			});
		},
		setPickupLocaton: function(fdObj)
		{
			var self = this;
			pjQ.$('.fdLoader').css('display', 'block');
			self.getWTime.apply(self, [pjQ.$("#fd_p_date_" + self.opts.index).val(), fdObj.id, 'pickup', function (data) {
				pjQ.$(".fdPickupTime").html(data);
				pjQ.$('#fdMain_' + self.opts.index).find("select[name='p_location_id']").val(fdObj.id);
				pjQ.$("#fdPickupAddressLabel_" + self.opts.index).html(fdObj.address);
				pjQ.$("#fdPickupAddress_" + self.opts.index).val(fdObj.address);
				pjQ.$("#fdPickupAddress_" + self.opts.index).parent().show();
				
			}]);
		},
		getLocation: function (id, init, callback) {
			var self = this;
			
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocation"].join(""), {
				"id": id
			}).done(function (data) {				
				pjQ.$("#fdPickupAddressLabel_" + self.opts.index).html(data.address);
				pjQ.$("#fdPickupAddress_" + self.opts.index).val(data.address);
				pjQ.$("#fdPickupUserid_" + self.opts.index).val(data.user_id);
				pjQ.$("#fdPickupAddress_" + self.opts.index).parent().show();
				
				if (myPickupMarkers)
				{
					for (var i = myPickupMarkers.length - 1; i >= 0; i--) 
					{
						if(myPickupMarkers[i].fdObj.id == id)
						{
							google.maps.event.trigger(myPickupMarkers[i], 'click');
							break;
						}
					}
				}
				if (typeof callback != "undefined") {
					callback.call(self);
				}else{
					pjQ.$('.fdLoader').css('display', 'none');
				}
			});
		},
		getLocations: function () {
			var self = this,
				LatLngList = [],
				latlng;
			
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocations"].join("")).done(function (data) {
				pjQ.$("#fdDeliveryMap_" + self.opts.index).show();
				if (data.length > 0) 
				{
					myDeliveryMap = new GoogleMaps({
						id: "fdDeliveryMap_" + self.opts.index,
						icon: self.opts.server + "app/web/img/frontend/pin.png"
					});
					myDeliveryOverlays = [];
					myDeliveryMarkers = [];
					myDeliveryBounds = new google.maps.LatLngBounds();
					myDeliveryMap.clearOverlays(myDeliveryMarkers);
					for (var index = 0, ilen = data.length; index < ilen; index++) 
					{
						if (typeof data[index].lat != "undefined" && typeof data[index].lng != "undefined") 
						{
							myDeliveryMap.addMarker(myDeliveryMarkers, new google.maps.LatLng(data[index].lat, data[index].lng));
							latlng = new google.maps.LatLng(data[index].lat, data[index].lng);
							LatLngList.push(latlng);
						}
						
						if (data[index].coords && data[index].coords.length > 0) 
						{
							for (var j = 0, jlen = data[index].coords.length; j < jlen; j++) 
							{
								myTmp.cnt += 1;
								switch (data[index].coords[j].type) {
									case 'circle':
										var str = data[index].coords[j].data.replace(/\(|\)|\s+/g, ""),
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
								            zIndex: 901
										});
										circle.myObj = {
											"id": data[index].coords[j].id,
											"location_id": data[index].coords[j].location_id,
											"name": data[index].name
										};
										circle.setMap(myDeliveryMap.map);
										google.maps.event.addListener(circle, "click", function () {
											myDeliveryMap.removeFocus(myDeliveryOverlays, this.myObj.id);
											myDeliveryMap.setFocus(this);
											self.coord_id = this.myObj.id;
											self.location_id = this.myObj.location_id;
											self.setDeliveryArea.call(self, this.myObj);
										});
										myDeliveryOverlays.push(circle);
										myTmp.type = "circle";
										break;
									case 'polygon':
										var path,
											str = data[index].coords[j].data.replace(/\(|\s+/g, ""),
											arr = str.split("),"),
											paths = [];
										arr[arr.length-1] = arr[arr.length-1].replace(")", "");
										for (var i = 0, len = arr.length; i < len; i++) {
											path = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
											paths.push(path);
										}
										var polygon = new google.maps.Polygon({
											paths: paths,
											strokeColor: '#008000',
											strokeOpacity: 1,
											strokeWeight: 1,
											fillColor: '#008000',
											fillOpacity: 0.5,
											zIndex: 901
									    });
										polygon.myObj = {
											"id": data[index].coords[j].id,
											"location_id": data[index].coords[j].location_id,
											"name": data[index].name
										};
										polygon.setMap(myDeliveryMap.map);
										google.maps.event.addListener(polygon, "click", function () {
											myDeliveryMap.removeFocus(myDeliveryOverlays, this.myObj.id);
											myDeliveryMap.setFocus(this);
											self.coord_id = this.myObj.id;
											self.location_id = this.myObj.location_id;
											self.setDeliveryArea.call(self, this.myObj);
										});
										myDeliveryOverlays.push(polygon);
										myTmp.type = "polygon";
										break;
									case 'rectangle':
										var bound,
											str = data[index].coords[j].data.replace(/\(|\s+/g, ""),
											arr = str.split("),"), 
											bounds = [];
										for (var i = 0, len = arr.length; i < len; i++) {
											arr[i] = arr[i].replace(/\)/g, "");
											bound = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
											bounds.push(bound);
										}
										var rectangle = new google.maps.Rectangle({
											strokeColor: '#008000',
								            strokeOpacity: 1,
								            strokeWeight: 1,
								            fillColor: '#008000',
								            fillOpacity: 0.5,
								            bounds: new google.maps.LatLngBounds(bounds[0], bounds[1]),
								            zIndex: 901
										});
										rectangle.myObj = {
											"id": data[index].coords[j].id,
											"location_id": data[index].coords[j].location_id,
											"name": data[index].name
										};
										rectangle.setMap(myDeliveryMap.map);
										google.maps.event.addListener(rectangle, "click", function () {
											myDeliveryMap.removeFocus(myDeliveryOverlays, this.myObj.id);
											myDeliveryMap.setFocus(this);
											self.coord_id = this.myObj.id;
											self.location_id = this.myObj.location_id;
											self.setDeliveryArea.call(self, this.myObj);
										});
										myDeliveryOverlays.push(rectangle);
										myTmp.type = "rectangle";
										break;
								}
							}
						}
					}
					
					if (self.coord_id !== null) 
					{
						for (var i = 0, len = myDeliveryOverlays.length; i < len; i++) {
							if (myDeliveryOverlays[i].myObj.id == self.coord_id) {
								myDeliveryMap.setFocus(myDeliveryOverlays[i]);
								self.setDeliveryArea.call(self, {
									name: myDeliveryOverlays[i].myObj.name,
									location_id: self.location_id
								});
								break;
							}
						}
					}
					
					for (var j = 0, len = LatLngList.length; j < len; j++) {
						myDeliveryBounds.extend(LatLngList[j]);
					}
					myDeliveryMap.map.fitBounds(myDeliveryBounds);
					google.maps.event.trigger(myDeliveryMap.map, "resize");
					
					self.myDeliveryOverlays = myDeliveryOverlays;
					self.myDeliveryMap = myDeliveryMap;
					
					pjQ.$('.fdLoader').css('display', 'none');
				}
			});
		},
		setDeliveryArea: function (data) {
			var self = this,
				d_location_id = pjQ.$("select[name='d_location_id']").val();
			
			pjQ.$(".fdDeliveryNote")
				.html(self.opts.messages[11].replace('{LOCATION}', data.name))
				.show();
			if(d_location_id != data.location_id)
			{
				pjQ.$("select[name='d_location_id']").val(data.location_id);
				
				self.setDays.apply(this, [data.location_id, 'delivery']);
				self.deliveryDate.opts.onBeforeShowDay = function (date) {
					return self._daysOff.apply(self, [date]);
				};
				self.deliveryDate.refresh();
				
				pjQ.$('.fdLoader').css('display', 'block');
				self.getWTime.apply(self, [pjQ.$("#fd_d_date_" + self.opts.index).val(), data.location_id, 'delivery', function (data) {
					pjQ.$(".fdDeliveryTime").html(data);
				}]);
			}
		},
		getWTime: function (date, location_id, type, callback) {
			var self = this;
			pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetWTime"].join(""), {
				"date": date,
				"location_id": location_id,
				"type": type,
				"index": this.opts.index
			}).done(function (data) {
				if (data.indexOf("fdSelect") == -1)
				{
					pjQ.$('.fdButtonPostPrice').addClass("fdButtonDisabled");
				}else{
					pjQ.$('.fdButtonPostPrice').removeClass("fdButtonDisabled");
				}
				callback(data);
				pjQ.$('.fdLoader').css('display', 'none');
			});
		},
		validateDetails: function() {
			var self = this,
				$form = pjQ.$('#fdCheckoutForm_' + self.opts.index);
			var remote_msg = self.opts.email_exiting_message;
			remote_msg = remote_msg.replace("{STAG}", '<a href="#" class="fdValidateLogin">');
			remote_msg = remote_msg.replace("{ETAG}", '</a>');
			$form.validate({
				rules: {
					"captcha": {
						remote: self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha"
					},
					"c_email": {
						remote: self.opts.folder + "index.php?controller=pjFront&action=pjActionCheckEmail"
					},
					"agreement": {
						required: true
					}
				},
				messages: {
					"c_title": {
						required: $form.find("select[name='c_title']").data("err")
					},
					"c_name": {
						required: $form.find("input[name='c_name']").data("err")
					},
					"c_phone": {
						required: $form.find("input[name='c_phone']").data("err")
					},
					"c_email": {
						required: $form.find("input[name='c_email']").data("err"),
						email: $form.find("input[name='c_email']").attr("data-email"),
						remote: remote_msg
					},
					"c_company": {
						required: $form.find("input[name='c_company']").data("err")
					},
					"c_notes": {
						required: $form.find("textarea[name='c_notes']").data("err")
					},
					"c_address_1": {
						required: $form.find("input[name='c_address_1']").data("err")
					},
					"c_address_2": {
						required: $form.find("input[name='c_address_2']").data("err")
					},
					"c_city": {
						required: $form.find("input[name='c_city']").data("err")
					},
					"c_state": {
						required: $form.find("input[name='c_state']").data("err")
					},
					"c_zip": {
						required: $form.find("input[name='c_zip']").data("err")
					},
					"c_country": {
						required: $form.find("select[name='c_country']").data("err")
					},
					"payment_method": {
						required: $form.find("select[name='payment_method']").data("err")
					},
					"cc_type": {
						required: $form.find("select[name='cc_type']").data("err")
					},
					"cc_num": {
						required: $form.find("input[name='cc_num']").data("err")
					},
					"cc_exp_month": {
						required: $form.find("select[name='cc_exp_month']").data("err")
					},
					"cc_exp_year": {
						required: $form.find("select[name='cc_exp_year']").data("err")
					},
					"cc_code": {
						required: $form.find("input[name='cc_code']").data("err")
					},
					"captcha": {
						required: $form.find("input[name='captcha']").data("err"),
						remote: $form.find("input[name='captcha']").attr("data-incorrect"),
					},
					"agreement": {
						required: $form.find("input[name='agreement']").data("err")
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'captcha' || element.attr('name') == 'cc_exp_month' || element.attr('name') == 'cc_exp_year')
					{
						error.insertAfter(element.parent());
					}else{
						error.insertAfter(element);
					}
				},
				onkeyup: false,
				errorClass: "fdError",
				submitHandler: function(form){
					pjQ.$('.fdLoader').css('display', 'block');
					pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveForm"].join(""), $form.serialize()).done(function (data) {
						hashBang("#!/loadPreview");
					});
					return false;
			    }
			});
			$form.submit();
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var qs = {
					"cid": this.opts.cid,
					"locale": this.opts.locale,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"order_id": obj.order_id, 
					"payment_method": obj.payment
				};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm"].join(""), qs).done(function (data) {
				var $msg_container = pjQ.$('#fdOrderMessage_' + index);
				$msg_container.html(data);
				$msg_container.parent().css('display', 'block');
				pjQ.$('.fdButtonContainer').css('display', 'none');
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='fdPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='fdAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		validateLogin: function () {
			var validator,
				self = this,
				$form = pjQ.$('#fdLoginForm_' + self.opts.index);
			validator = $form.validate({
				ignore: "",
				errorClass: "fdError",
				errorPlacement: function (error, element) {
					error.insertAfter(element);
				}
			});
			var $_email = $form.find("input[name='login_email']"),
				$_password = $form.find("input[name='login_password']");
			if ($_email.length > 0) {
				$_email.rules("add", {
					required: true,
					email: true,
					messages: {
						required: $_email.attr("data-required"),
						email: $_email.attr("data-email")
					}
				});
			}
			if ($_password.length > 0) {
				$_password.rules("add", {
					required: true,
					messages: {
						required: $_password.attr("data-required")
					}
				});
			}
			if (!validator.form()) {
				validator.showErrors();
				return false;
			}
			return true;
		},
		validateForgot: function () {
			var validator,
				self = this,
				$form = pjQ.$('#fdForgotForm_' + self.opts.index);
			validator = $form.validate({
				ignore: "",
				errorClass: "fdError",
				errorPlacement: function (error, element) {
					error.insertAfter(element);
				}
			});
			var $_email = $form.find("input[name='email']");
			if ($_email.length > 0) {
				$_email.rules("add", {
					required: true,
					email: true,
					messages: {
						required: $_email.attr("data-required"),
						email: $_email.attr("data-email")
					}
				});
			}
			if (!validator.form()) {
				validator.showErrors();
				return false;
			}
			return true;
		},
		validateTypes: function () {
			var validator,
				self = this,
				$form = pjQ.$('#fdMain_' + self.opts.index).find("form");

			validator = $form.validate({
				ignore: "",
				errorClass: "fdError",
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'p_date' || element.attr('name') == 'd_date')
					{
						error.insertAfter(element.parent());
					}else{
						error.insertAfter(element);
					}
				}
			});
			
			var $_ploc = $form.find("select[name='p_location_id']"),
				$_pdat = $form.find("input[name='p_date']"),
				
				$_dloc = $form.find("input[name='d_location_id']"),
				$_ddat = $form.find("input[name='d_date']"),
				
				$_dadd1 = $form.find("input[name='d_address_1']"),
				$_dadd2 = $form.find("input[name='d_address_2']"),
				$_dcity = $form.find("input[name='d_city']"),
				$_dstate = $form.find("input[name='d_state']"),
				$_dcountry = $form.find("select[name='d_country_id']"),
				$_dzip = $form.find("input[name='d_zip']"),
				$_dnotes = $form.find("textarea[name='d_notes']");
			switch (pjQ.$("input[name='type']:checked", $form).val()) {
				case "pickup":
					if ($_ploc.length > 0) {
						$_ploc.rules("add", {
							required: true,
							messages: {
								required: $_ploc.data("err")
							}
						});
					}
					if ($_pdat.length > 0) {
						$_pdat.rules("add", {
							required: true,
							messages: {
								required: $_pdat.data("err")
							}
						});
					}
					
					if ($_dloc.length > 0) {
						$_dloc.rules('remove', 'required minlength');
					}
					if ($_ddat.length > 0) {
						$_ddat.rules('remove', 'required');
					}
					if ($_dadd1.length > 0) {
						$_dadd1.rules('remove', 'required minlength');
					}
					if ($_dadd2.length > 0) {
						$_dadd2.rules('remove', 'required');
					}
					if ($_dcity.length > 0) {
						$_dcity.rules('remove', 'required');
					}
					if ($_dstate.length > 0) {
						$_dstate.rules('remove', 'required');
					}
					if ($_dcountry.length > 0) {
						$_dcountry.rules('remove', 'required');
					}
					if ($_dzip.length > 0) {
						$_dzip.rules('remove', 'required');
					}
					if ($_dnotes.length > 0) {
						$_dnotes.rules('remove', 'required');
					}
					break;
				case "delivery":
					if ($_ploc.length > 0) {
						$_ploc.rules("remove", "required");
					}
					if ($_pdat.length > 0) {
						$_pdat.rules("remove", "required");
					}
					if ($_dloc.length > 0) {
						$_dloc.rules('add', {
							required: true,
							minlength: 1,
							messages: {
								required: $_dloc.data("err"),
								minlength: $_dloc.data("err")
							}
						});
					}
					if ($_ddat.length > 0) {
						$_ddat.rules('add', {
							required: true,
							messages: {
								required: $_ddat.data("err")
							}
						});
					}
					if ($_dadd1.length > 0 && $_dadd1.hasClass('fdRequired')) {
						$_dadd1.rules('add', {
							required: true,
							messages: {
								required: $_dadd1.data("err")
							}
						});
					}
					if ($_dadd2.length > 0 && $_dadd2.hasClass('fdRequired')) {
						$_dadd2.rules('add', {
							required: true,
							messages: {
								required: $_dadd2.data("err")
							}
						});
					}
					if ($_dcity.length > 0 && $_dcity.hasClass('fdRequired')) {
						$_dcity.rules('add', {
							required: true,
							messages: {
								required: $_dcity.data("err")
							}
						});
					}
					if ($_dstate.length > 0 && $_dstate.hasClass('fdRequired')) {
						$_dstate.rules('add', {
							required: true,
							messages: {
								required: $_dstate.data("err")
							}
						});
					}
					if ($_dcountry.length > 0 && $_dcountry.hasClass('fdRequired')) {
						$_dcountry.rules('add', {
							required: true,
							messages: {
								required: $_dcountry.data("err")
							}
						});
					}
					if ($_dzip.length > 0 && $_dzip.hasClass('fdRequired')) {
						$_dzip.rules('add', {
							required: true,
							messages: {
								required: $_dzip.data("err")
							}
						});
					}
					if ($_dnotes.length > 0 && $_dnotes.hasClass('fdRequired')) {
						$_dnotes.rules('add', {
							required: true,
							messages: {
								required: $_dnotes.data("err")
							}
						});
					}
					break;
			}
			
			if (!validator.form()) {
				validator.showErrors();
				return false;
			}
			return true;
		}
	};
	
	function GoogleMaps(opts) {
		this.map = null;
		this.options = {
			id: "map_canvas",
			zoom: 8,
			icon: null
		};
		this.init(opts);
	}
	GoogleMaps.prototype = {
		init: function (opts) {
			var self = this;
			for (var x in opts) {
				if (opts.hasOwnProperty(x)) {
					self.options[x] = opts[x];
				}
			}
			self.map = new google.maps.Map(document.getElementById(self.options.id), {
				zoom: self.options.zoom,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
			return self;
		},
		addMarker: function (marker, position) {
			var self = this,
				obj = {
					map: self.map,
					position: position,
					clickable: false,
					zIndex: 900
				};
			if (self.options.icon != null) {
				obj.icon = self.options.icon;
			}
			marker.push(new google.maps.Marker(obj));
			self.map.setCenter(position);
			return self;
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
	
	window.FoodDelivery = FoodDelivery;	
})(window);