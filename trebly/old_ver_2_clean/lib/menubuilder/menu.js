function sfHoverEvents(sfEls) {
	var len = sfEls.length;
	for (var i = 0; i < len; i += 1) {
		sfEls[i].onmouseover = function () {
			this.className += " sfhover";
		};
		sfEls[i].onmouseout = function () {
			this.className = this.className.replace(" sfhover", "");
		};
	}
}

function sfHover() {
	var ULs = document.getElementsByTagName("UL");
	var len = ULs.length;
	for (var i = 0; i < len; i++) {
		if (ULs[i].className.indexOf("cssmenu_horiz") !== -1) {
			sfHoverEvents(ULs[i].getElementsByTagName("LI"));
		}
		if (ULs[i].className.indexOf("cssmenu") !== -1) {
			sfHoverEvents(ULs[i].getElementsByTagName("LI"));
		}
		if (ULs[i].className.indexOf("cssmenu_vert") !== -1) {
			sfHoverEvents(ULs[i].getElementsByTagName("LI"));
		}
	}
}

if (window.attachEvent) {
	window.attachEvent("onload", sfHover);
}
