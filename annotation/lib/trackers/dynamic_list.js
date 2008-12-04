
function selectValues(args,me,filter) {
	var req = null;
	var envdoc = this;
	var tabsel = me.split(",");
	var filtervalue = "";
	var sel = new Array();

	for (var i=0; i < tabsel.length; i++)
	{
		sel[i] = document.getElementsByName("ins_" + tabsel[i])[0];
		while (sel[i].childNodes.length != 0) {
			sel[i].removeChild(sel[i].lastChild);
		}
	}

	if (typeof(filter) != "undefined") {
		filtervalue = "&filtervalue=";
		var selct = document.getElementsByName(filter)[0].childNodes;
		for (var i=0; i < selct.length; i++) {
			if (selct[i].nodeType == 1) {
				filtervalue += selct[i].childNodes[0].nodeValue;
				break;
			}
		}
	}

	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try {
			req = new ActiveXObject( "Microsoft.XMLHTTP" );
		} catch( ex ) {
			req = new ActiveXObject("MSXML2.XMLHTTP");
		}
	} else {
		alert("http request not supported");
		return;
	}
	if( ! req ) {
		return false;
	}

	req.onreadystatechange = function(){
		if (req.readyState == 4 && req.status == 200) {
			envdoc.eval(req.responseText);
		}
	};

	req.open( "GET", "tiki-tracker_http_request.php?" + args + filtervalue, true);
	req.send(null);
}
