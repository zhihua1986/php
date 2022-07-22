function isDevType() {
	var a = navigator.userAgent,
		c = a.indexOf("Android") > -1 || a.indexOf("Adr") > -1,
		b = !! a.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
	if (c) {
		return 1
	} else {
		if (b) {
			return 2
		} else {
			return 0
		}
	}
}
function browserType() {
	var e = navigator.userAgent;
	var b = e.indexOf("Opera") > -1;
	var h = e.indexOf("Maxthon") > -1;
	var d = e.indexOf("compatible") > -1 && e.indexOf("MSIE") > -1 && !b;
	var g = e.indexOf("Firefox") > -1;
	var c = e.indexOf("Safari") > -1 && e.indexOf("Chrome") < 1;
	var f = e.indexOf("Chrome") > -1;
	var a = isIE55 = isIE6 = isIE7 = isIE8 = false;
	if (g) {
		return 1
	}
	if (h) {
		return 2
	}
	if (b) {
		return 3
	}
	if (c) {
		return 4
	}
	if (f) {
		return 5
	}
	if (d) {
		return 6
	}
	return 0
}
function browserOperator() {
	var a = navigator.userAgent.toLowerCase();
	if (a.match(/MicroMessenger/i) == "micromessenger") {
		return 1
	} else {
		if (a.match(/WeiBo/i) == "weibo") {
			return 2
		} else {
			if (a.match(/QQ/i) == "qq") {
				return 3
			} else {
				return 0
			}
		}
	}
}
function is_weixnb() {
	var a = navigator.userAgent.toLowerCase();
	if (a.match(/MicroMessenger/i) == "micromessenger") {
		return true
	} else {
		return false
	}
}