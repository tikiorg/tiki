sfHover = function() {
	var id = 0; 
	var cssmenu = document.getElementById("cssmenu"+id++);
	while (cssmenu) {
	var sfEls = cssmenu.getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
	cssmenu = document.getElementById("cssmenu"+id++);
  }
}
if (window.attachEvent) window.attachEvent("onload", sfHover);
