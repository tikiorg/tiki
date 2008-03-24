////////// mypage

function htmlspecialchars(ch) {
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/\"/g,"&quot;");
	ch = ch.replace(/\'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch;
}

function mypage_editComponent(compname, asnew) {
	var compid=0;

	if (!asnew) {
		compid=compname;
		compname=tikimypagewin[compid].options.title;
	}

	mypage_winconf=new Windoo({
		"modal": true,
		"width": 400,
		"height": 260,
		"top": 100,
		"left": 300,
		"resizeLimit": {
			"x": {
				"0": 400
			},
			"y": {
				"0": 130,
				"1": 600
			}
		},
		"buttons": {
			"minimize": false
		},
		"destroyOnClose": true,
		"container": false,
		"resizable": false,
		"draggable": false,
		"theme": "aero",
		"shadow": false,
		"title": (asnew ? "New " : "Edit ")+compname+" :"
	}).setHTML((asnew ? "<p>Titre: <input type='text' id='mypage_configure_title' value=''></p>" : "")+"<form id='mypage_formconfigure'><div id='mypage_divconfigure'></div></form><input type='button' value='"+(asnew ? "Create" : "Update")+"' onclick='mypage_configuresubmit();'><input type='hidden' id='mypage_config_contenttype' value='' /><input type='hidden' id='mypage_config_compid' value='' />")
	.show();

	if (asnew) {
		$('mypage_config_contenttype').value=compname;
		$('mypage_config_compid').value=0;
		xajax_mypage_win_prepareConfigure(id_mypage, 0, compname);
	} else {
		$('mypage_config_contenttype').value='';
		$('mypage_config_compid').value=compid;
		xajax_mypage_win_prepareConfigure(id_mypage, compid);
	}
}

function mypage_configuresubmit() {
	var compid=$('mypage_config_compid').value;

	if (compid > 0) {
		xajax_mypage_win_configure(id_mypage, compid, xajax.getFormValues("mypage_formconfigure"));
	} else {
		xajax_mypage_win_create(id_mypage,
			$('mypage_config_contenttype').value,
			$('mypage_configure_title').value,
			xajax.getFormValues("mypage_formconfigure"));
	}

	if (mypage_winconf) {
		mypage_winconf.close();
		mypage_winconf=null;
	}
}

function mypage_addComponent(compname) {
	xajax_mypage_win_create(id_mypage, compname);
}

function windooFocusChanged(id) {
	lastFocusedWindoo=id;
}

function windooStartDrag(id) {
	if (isExtended) extendContract();
}

function hideAllWins() {
	for (var i in tikimypagewin) {
		if ((typeof(i) == 'number') || ((typeof(i) == 'string') && (parseInt(i) == i))) {
	      		tikimypagewin[i].hide();
		}
	}
}

function showAllWins() {
	for (var i in tikimypagewin) {
		if ((typeof(i) == 'number') || ((typeof(i) == 'string') && (parseInt(i) == i))) {
	      		tikimypagewin[i].show();
		}
	}
}

function closeAllWins() {
	for (var i in tikimypagewin) {
		if ((typeof(i) == 'number') || ((typeof(i) == 'string') && (parseInt(i) == i))) {
	      		tikimypagewin[i].close();
		}
	}
}

function destroyAllWins() {
	for (var i in tikimypagewin) {
		if ((typeof(i) == 'number') || ((typeof(i) == 'string') && (parseInt(i) == i))) {
	      		tikimypagewin[i].destroy();
		}
	}
	tikimypagewin=[];
}

function mypagewin_create(id_mypage, id_mypagewin, comptype, options, content) {
	var win=new Windoo(options);

	if (mypage_editit) {
		win.addEvent('onResizeComplete', function() {
			xajax_mypage_win_setrect(id_mypage, id_mypagewin, this.getState().outer);
		});
		win.addEvent('onDragComplete', function() {
			var state=this.getState();
			if (state.outer.left < 0) state.outer.left=0;
			if (state.outer.top < 0) state.outer.top=0;
			this.setPosition(state.outer.left, state.outer.top);
			xajax_mypage_win_setrect(id_mypage, id_mypagewin, state.outer);
		});
		win.addEvent('onClose', function() {
			if ($('elem_addComponent_'+comptype))
				$('elem_addComponent_'+comptype).setStyle('display', '');
			xajax_mypage_win_destroy(id_mypage, id_mypagewin);
		});
		win.addEvent('onFocus', function() {
			windooFocusChanged(id_mypagewin);
		});
		win.addEvent('onStartDrag', function() {
			windooStartDrag(id_mypagewin);
		});
		win.addEvent('onMenu', function() {
			mypage_editComponent(id_mypagewin);
		});

		if ($('elem_addComponent_'+comptype))
			$('elem_addComponent_'+comptype).setStyle('display', 'none');
	}

	if (comptype != 'iframe') win.setHTML(content);

	tikimypagewin[id_mypagewin]=win;
	win.show();
}
