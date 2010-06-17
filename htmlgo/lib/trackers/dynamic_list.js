// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

var tracker_dynamic_options;

function selectValues(args, me, filter) {
	var req = null;
	var envdoc = this;
	var tabsel = me.split(",");
	var filtervalue = "";
	
	tracker_dynamic_options = [];

	for (var i = 0; i < tabsel.length; i++)
	{
		tracker_dynamic_options[i] = document.getElementsByName("ins_" + tabsel[i])[0];
		if (typeof tracker_dynamic_options[i] === "undefined") {
			tracker_dynamic_options[i] = document.getElementsByName("track[" + tabsel[i] + "]")[0];
		}
		while (tracker_dynamic_options[i].childNodes.length !== 0) {
			tracker_dynamic_options[i].removeChild(tracker_dynamic_options[i].lastChild);
		}
	}

	if (typeof(filter) !== "undefined") {
		filtervalue = "&filtervalue=";
		var selct = document.getElementsByName(filter)[0].childNodes;
		for (i = 0; i < selct.length; i++) {
			if (selct[i].nodeType === 1 && selct[i].childNodes.length) {
				filtervalue += selct[i].childNodes[0].nodeValue;
				break;
			}
		}
	}

	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try {
			req = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (ex) {
			req = new ActiveXObject("MSXML2.XMLHTTP");
		}
	} else {
		alert("http request not supported");
		return;
	}
	if (!req) {
		return false;
	}

	req.onreadystatechange = function () {
		if (req.readyState === 4 && req.status === 200) {
			envdoc.eval(req.responseText);
		}
	};

	req.open("GET", "tiki-tracker_http_request.php?" + args + filtervalue, true);
	req.send(null);
}
