// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// JS glue for jCapture feature

function openJCaptureDialog() {


	var $appletDiv = $("#jCaptureAppletDiv");
	if (1 || $appletDiv.length === 0) {

		$appletDiv = $("<div id='jCaptureAppletDiv'> </div>");
		$(document.body).append($appletDiv);

		$.get($.service('jcapture', 'capture'), function(data){
			$appletDiv.html(data);
		});

	} else {
		$appletDiv[0].showCaptureFrame();
	}

}