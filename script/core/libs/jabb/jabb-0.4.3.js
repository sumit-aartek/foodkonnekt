var JABB = JABB || {};
JABB.version = "0.4.3";
JABB.Ajax = {
	onStart: null,
	onStop: null,
	onError: null,
	XMLHttpFactories: [
		function () {return new XMLHttpRequest()},
		function () {return new ActiveXObject("Msxml2.XMLHTTP")},
		function () {return new ActiveXObject("Msxml3.XMLHTTP")},
		function () {return new ActiveXObject("Microsoft.XMLHTTP")}
	],
	sendRequest: function (url, callback, postData) {
		var req = this.createXMLHTTPObject();
		if (!req) {
			return;
		}
		var method = (postData) ? "POST" : "GET";
		var calledOnce = false;
		req.open(method, url, true);
		//Refused to set unsafe header "User-Agent"
		//req.setRequestHeader('User-Agent', 'XMLHTTP/1.0');
		req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		if (postData) {
			req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		}
		req.onreadystatechange = function () {
			switch (req.readyState) {
			case 1:
				if (!calledOnce) {
					JABB.Ajax.onAjaxStart();
					calledOnce = true;
				}
			break;
			case 2:
				return;
			break;
			case 3:
				return;
			break;
			case 4:
				JABB.Ajax.onAjaxStop();
				if (req.status == 200) {
					callback(req);
				} else {
					JABB.Ajax.onAjaxError();
				}
				delete req;
			break;
			}/*
			if (req.readyState != 4) {
				return;
			}
			if (req.status != 200 && req.status != 304) {
				return;
			}
			callback(req);*/
		};
		if (req.readyState == 4) {
			return;
		}
		req.send(postData);
	},
	onAjaxStart: function () {
		if (typeof this.onStart == 'function') {
			this.onStart();
		}
	},
	onAjaxStop: function () {
		if (typeof this.onStop == 'function') {
			this.onStop();
		}
	},
	onAjaxError: function () {
		if (typeof this.onError == 'function') {
			this.onError();
		}
	},
	createXMLHTTPObject: function () {
		var xmlhttp = false;
		for (var i = 0; i < this.XMLHttpFactories.length; i++) {
			try {
				xmlhttp = this.XMLHttpFactories[i]();
			}
			catch (e) {
				continue;
			}
			break;
		}
		return xmlhttp;
	},
	getJSON: function (url, callback) {
		this.sendRequest(url, function (req) {
			callback(eval("(" + req.responseText + ")"));
		});
	},
	postJSON: function (url, callback, postData) {
		this.sendRequest(url, function (req) {
			callback(eval("(" + req.responseText + ")"));
		}, postData);
	}, 
	get: function (url, container_id) {
		this.sendRequest(url, function (req) {
			var el = document.getElementById(container_id);
			if (el) {
				el.innerHTML = JABB.Utils.parseScript(req.responseText);
			}
		});
	},
	post: function (url, container_id, postData) {
		this.sendRequest(url, function (req) {
			var el = document.getElementById(container_id);
			if (el) {
				el.innerHTML = JABB.Utils.parseScript(req.responseText);
			}
		}, postData);
	}
};
JABB.Utils = {
	addClass: function (ele, cls) {
		if (!this.hasClass(ele, cls)) {
			ele.className += ele.className.match(/\S/) !== null ? " " + cls : cls;
		}
	},
	hasClass: function (ele, cls) {
		return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
	},
	removeClass: function (ele, cls) {
		if (this.hasClass(ele, cls)) {
			var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
			ele.className = ele.className.match(/\s/) !== null ? ele.className.replace(reg, '') : "";
		}
	},
	importCss: function (cssFile) {
		if (document.createStyleSheet) {
			document.createStyleSheet(cssFile);
		} else {
			var styles = "@import url(" + cssFile + ");";
			var newSS = document.createElement('link');
			newSS.rel = 'stylesheet';
			newSS.href = 'data:text/css,' + escape(styles);
			document.getElementsByTagName("head")[0].appendChild(newSS);
		}
	},
	importJs: function (jsFile) {
		var d = window.document;
		if (d.createElement) {
			var js = d.createElement("script");
			js.type = "text/javascript";
			js.src = jsFile;
			if (js) {
				d.getElementsByTagName("head")[0].appendChild(js);
			}
		}
	},
	getElementsByClass: function (searchClass, node, tag) {
		var classElements = new Array();
		if (node == null) {
			node = document;
		}
		if (tag == null) {
			tag = '*';
		}
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		for (var i = 0, j = 0; i < elsLen; i++) {
			if (pattern.test(els[i].className)) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	},
	addEvent: function (obj, type, fn) {
		if (obj.addEventListener) {
			obj.addEventListener(type, fn, false);
		} else if (obj.attachEvent) {
			obj["e" + type + fn] = fn;
			obj[type + fn] = function() { obj["e" + type + fn](window.event); };
			obj.attachEvent("on" + type, obj[type + fn]);
		} else {
			obj["on" + type] = obj["e" + type + fn];
		}
	},
	fireEvent: function (element, event) {
		if (!element) return false;
		if (document.createEventObject) {
			// dispatch for IE
			var evt = document.createEventObject();
			return element.fireEvent('on' + event, evt);
		} else {
			// dispatch for firefox + others
			var evt = document.createEvent("HTMLEvents");
			evt.initEvent(event, true, true); // event type,bubbling,cancelable
			return !element.dispatchEvent(evt);
		}
	},
	serialize: function (form) {
		if (!form || form.nodeName !== "FORM") {
			return undefined;
		}
		var i, j, q = [];
		for (i = form.elements.length - 1; i >= 0; i = i - 1) {
			if (form.elements[i].name === "") {
				continue;
			}
			switch (form.elements[i].nodeName) {
			case 'INPUT':
				switch (form.elements[i].type) {
				case 'text':
				case 'hidden':
				case 'password':
				case 'button':
				case 'reset':
				case 'submit':
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
					break;
				case 'checkbox':
				case 'radio':
					if (form.elements[i].checked) {
						q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
					}						
					break;
				case 'file':
					break;
				}
				break; 
			case 'TEXTAREA':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
			case 'SELECT':
				switch (form.elements[i].type) {
				case 'select-one':
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[form.elements[i].selectedIndex].value));
					break;
				case 'select-multiple':
					for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
						if (form.elements[i].options[j].selected) {
							q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
						}
					}
					break;
				}
				break;
			case 'BUTTON':
				switch (form.elements[i].type) {
				case 'reset':
				case 'submit':
				case 'button':
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
					break;
				}
				break;
			}
		}
		return q.join("&");
	},
	extend: function (obj, args) {
		var i;
		for (i in args) {
			obj[i] = args[i];
		}
		return obj;
	},
	createElement: function (element) {
		if (typeof document.createElementNS != 'undefined') {
			return document.createElementNS('http://www.w3.org/1999/xhtml', element);
		}
		if (typeof document.createElement != 'undefined') {
			return document.createElement(element);
		}
		return false;
	},
	getEventTarget: function (e) {
		var targ;
		if (!e) {
			e = window.event;
		}
		if (e.target) {
			targ = e.target;
		} else if (e.srcElement) {
			targ = e.srcElement;
		}
		if (targ.nodeType == 3) {
			targ = targ.parentNode;
		}	
		return targ;
	},
	inArray: function (needle, haystack) {
		var i, len = haystack.length;
		for (i = 0; i < len; i++) {
			if (haystack[i] == needle) {
				return true;
			}
		}
		return false;
	},
	getViewport: function () {
		var w, h;
		if (typeof window.innerWidth != 'undefined') {
			w = window.innerWidth;
			h = window.innerHeight;
		} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
			w = document.documentElement.clientWidth;
			h = document.documentElement.clientHeight;
		} else {
			w = document.getElementsByTagName('body')[0].clientWidth;
			h = document.getElementsByTagName('body')[0].clientHeight;
		}
		return {width: w, height: h};
	},
	getScrollXY: function() {
		var scrOfX = 0, 
			scrOfY = 0;
		if(typeof(window.pageYOffset) == 'number') {
			//Netscape compliant
			scrOfY = window.pageYOffset;
			scrOfX = window.pageXOffset;
		} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			//DOM compliant
			scrOfY = document.body.scrollTop;
			scrOfX = document.body.scrollLeft;
		} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			//IE6 standards compliant mode
			scrOfY = document.documentElement.scrollTop;
			scrOfX = document.documentElement.scrollLeft;
		}
		return [scrOfX, scrOfY];
	},
	getPageXY: function() {
		var PositionXY = {width: 0, height: 0},
			db = document.body,
			dde = document.documentElement;
		PositionXY.width = Math.max(db.scrollTop, dde.scrollTop, db.offsetWidth, dde.offsetWidth, db.clientWidth, dde.clientWidth); 
		PositionXY.height = Math.max(db.scrollHeight, dde.scrollHeight, db.offsetHeight, dde.offsetHeight, db.clientHeight, dde.clientHeight);
		return PositionXY;
	},
	getOffset: function(el) {
	    if (el.getBoundingClientRect) {
	        return el.getBoundingClientRect();
	    } else {
	        var x = 0, y = 0;
	        do {
	            x += el.offsetLeft - el.scrollLeft;
	            y += el.offsetTop - el.scrollTop;
	        } while (el = el.offsetParent);
	        return {
	        	"left": x,
	        	"top": y
	        };
	    }      
	},
	parseScript: function (_source) {
		var source = _source,
			scripts = [];
		while (source.indexOf("<script") > -1 || source.indexOf("</script") > -1) {
			var s = source.indexOf("<script");
			var s_e = source.indexOf(">", s);
			var e = source.indexOf("</script", s);
			var e_e = source.indexOf(">", e);
			scripts.push(source.substring(s_e+1, e));
			source = source.substring(0, s) + source.substring(e_e+1);
		}
		for (var i = 0; i < scripts.length; i++) {
			try {
				eval(scripts[i]);
			} catch(ex) {
				// do what you want here when a script fails
			}
		}
		return source;
	},
	loadRemote: function (url, id) {
		var script = document.createElement('script'),
			s = document.getElementsByTagName('script')[0];
		script.type = 'text/javascript';
		script.src = url;
		script.id = id;
		s.parentNode.insertBefore(script, s);
	}
};
JABB.Cookie = {
	create: function (name, value, days) {
		var expires;
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toGMTString();
		} else {
			expires = "";
		}
		document.cookie = name + "=" + value + expires + "; path=/";
	},
	read: function (name) {
		var nameEQ = name + "=",
			ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	},
	erase: function (name) {
		this.create(name, "", -1);
	}
};
JABB.Date = {
	format: function (str, format) {
		var jQuery = ['d', 'dd', 'm', 'mm', 'yy'],
			dateJs = ['d', 'dd', 'M', 'MM', 'yyyy'],
			php = ['j', 'd', 'n', 'm', 'Y'],
			limiters = ['.', '-', '/'],
			stack = [];
		switch (format) {
			case 'jquery':
				stack = jQuery;
				break;
			case 'datejs':
				stack = dateJs;
				break;
			default:
				return str;
		}
		for (var i = 0, len = limiters.length; i < len; i++) {
			if (str.indexOf(limiters[i]) !== -1) {
				var iFormat = str.split(limiters[i]);
				return [ 
					stack[php.indexOf(iFormat[0])], 
					stack[php.indexOf(iFormat[1])], 
					stack[php.indexOf(iFormat[2])]
				].join(limiters[i]);
			}
		}
		return str;
	}
};

if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (searchElement /*, fromIndex */ ) {
		"use strict";  
        if (this == null) {  
            throw new TypeError();  
        }  
        var t = Object(this);  
        var len = t.length >>> 0;  
        if (len === 0) {  
            return -1;  
        }  
        var n = 0;  
        if (arguments.length > 0) {  
            n = Number(arguments[1]);  
            if (n != n) { // shortcut for verifying if it's NaN  
                n = 0;  
            } else if (n != 0 && n != Infinity && n != -Infinity) {  
                n = (n > 0 || -1) * Math.floor(Math.abs(n));  
            }  
        }  
        if (n >= len) {  
            return -1;  
        }  
        var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);  
        for (; k < len; k++) {  
            if (k in t && t[k] === searchElement) {  
                return k;  
            }  
        }  
        return -1;  
	};
}