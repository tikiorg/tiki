/**
 * @author jonny
 */

 var s = getSelection($jq("textarea[name=\'" + areaname + "\']")[0]);
var m = /\|\|([\s\S]*?)\|\|/mg.exec(s);
var vals = [], rows=3, cols=3, c, r, i;
if (m) {
	m = m[1];
	m = m.split("\n");
	rows = 0;
	cols = 1;
	for(i = 0; i < m.length; i++) {
		var a2 = m[i].split("|");
		var a = [];
		for (i = 0; i < a2.length; i++) {	// links can have | chars in
			if ((a2[i].indexOf("[") > -1 && i < a.length-1 && a2[i+1].indexOf("]") > -1) ||	// external link
					(a2[i].indexOf("((") > -1 && i < a.length-1 && a2[i+1].indexOf("))") > -1)) {
				a[a.length] = a2[i] + a2[i+1];
				i++;
			} else {
				a[a.length] = a2[i];
			}
		}
		vals[vals.length] = a;
		if (a.length > cols) { cols = a.length; }
		if (a.length) { rows++; }
	}
}
