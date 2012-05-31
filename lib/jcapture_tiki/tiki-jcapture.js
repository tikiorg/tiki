// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// JS glue for jCapture feature

function openJCaptureDialog() {


	$(this).serviceDialog({
		title: "Capture",
		data: {
			controller: 'jcapture',
			action: 'capture'
		},
		success: function () {
			alert("ok");
		}
	});



}