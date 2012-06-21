// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// JS glue for jCapture feature

function openJCaptureDialog() {


	var $appletDiv = $("#jCaptureAppletDiv");
	if ($appletDiv.length) {
		$appletDiv.remove();
	}

	$appletDiv = $("<div id='jCaptureAppletDiv' style='width:1px;height:1px;visibility:hidden;'> </div>");
	$(document.body).append($appletDiv);

	$.get($.service('jcapture', 'capture'), {
			area: "none",
			page: "test"
	}, function(data){
		$appletDiv.html(data);
	});

//	} else {	TODO later
//		$appletDiv[0].showCaptureFrame();
//	}

}

function insertAtCarret( areaId, dokuTag ) {
	var tag = dokuTag.substring(3, dokuTag.length - 3);
	alert("file : " + tag);
	if (areaId) {
		insertAt( areaId, tag);
	}
}
