<div style='width: {$mypage_width}; height: {$mypage_height};'>
<div id='mypage' style='position: absolute; width: {$mypage_width}; height: {$mypage_height}; background: {$mypage_bgcolor}; overflow: hidden;'>

{if $editit}
 <!-- sidebar -->
 <div id='sideBar' style='position: absolute; width: auto; height: auto; top: 0px; right: 0px; z-index: 255;'>

  <a href="#" id="sideBarTab" style='float:left; height:137px; width:28px; border: 0;'></a>
	
  <div id="sideBarContents" style="width:0px; overflow: hidden !important;">
   <div id="sideBarContentsInner" style="width: 250px;">
	<div id="container" >
		<div id="tab-block-1" >
			<h5>{tr}Tools{/tr}</h5>
    			<div id="components_list">
				<h6>New Component:</h6>
    				<ul>
     				{foreach from=$components item=component}
     					<li>
      					<a href='#' onclick='mypage_editComponent("{$component}", true);'>{$component|escape}</a>
     					</li>
     				{/foreach}
    				</ul>

				<h6>Configure selected component:</h6>
				<ul><li>
					<a href='#' onclick='mypage_editComponent(lastFocusedWindoo, false);'><span id='mypage_configcomp_span'></span></a>
				</li></ul>
    			</div>

    			<h5>{tr}Style{/tr}</h5>
    			<div id="components_colors">
				<ul style='padding-left: 16px'>
					<li>
						Windows Text:
						<span id='myRainbow_wintext' style='cursor: pointer; background: {$mypage_wintextcolor};'>
							[&nbsp;&nbsp;&nbsp;&nbsp;]
						</span>
					</li>
					<li>
						Windows Title:
						<span id='myRainbow_wintitle' style='cursor: pointer; background: {$mypage_wintitlecolor};'>
							[&nbsp;&nbsp;&nbsp;&nbsp;]
						</span>
					</li>
					<li>
					 Windows Background:
					 <div><input type='radio' id='radio_winbg_color' name='radio_winbg' />Color: <span id='myRainbow_winbg' style='cursor: pointer; background: {$mypage->getParam('winbgcolor')|escape:'javascript'};'>[&nbsp;&nbsp;&nbsp;&nbsp;]</span></div>
					 <div><input type='radio' id='radio_winbg_image' name='radio_winbg' />Image (url): <input id='input_winbgimage' type='text' style='width: 144px'><input type='button' value='ok' onclick="mypageSetwinbgimage($('input_winbgimage').value, true, true)" /></div>
					</li>
					<li>
					 Background:
					 <div><input type='radio' id='radio_bg_color' name='radio_bg' />Color: <span id='myRainbow_bg' style='cursor: pointer; background: {$mypage->getParam('bgcolor')|escape:'javascript'};'>[&nbsp;&nbsp;&nbsp;&nbsp;]</span></div>
					 <div><input type='radio' id='radio_bg_image' name='radio_bg' />Image (url): <input id='input_bgimage' type='text' style='width: 145px'><input type='button' value='ok' onclick="mypageSetbgimage($('input_bgimage').value, true, true)" /></div>
					</li>
				</ul>
    			</div>
   		</div>
  	</div>
   </div>
   </div>
 </div> <!-- sidebar -->
{/if}

</div>
</div>

<span style="float: right">
{if $editit}
<a href="tiki-mypage.php?mypage={$pagename|escape:url}">View</a>
{else}
<a href="tiki-mypage.php?mypage={$pagename}&edit=1">Edit</a>
{/if}
{if $tiki_p_admin} | <a href="tiki-mypage_types.php">Admin Types</a>{/if}
</span>

{literal}
<script type="text/javascript">

function initSimpleTabs() {
	var tabs1 = new SimpleTabs($('tab-block-1'), {
		entrySelector: 'h5',
		onSelect: function(toggle, container) {
			toggle.addClass('tab-selected');
			container.effect('opacity').start(0, 1); // 1) first start the effect
			container.setStyle('display', ''); // 2) then show the element, to prevent flickering
			},
		onShow: function(toggle, container, index) {
				toggle.addClass('tab-selected');
				container.effect('opacity').start(0, 7); // 1) first start the effect
				container.setStyle('display', '');
			}
	});
}

////////////////////

var lastFocusedWindoo=0;
var tikimypagewin=[];
function initSavedWindows() {
	// open saved windows
	{/literal}{$mypagejswindows}{literal}
}

//////////////////////
//
// initialize buttons
//
var mypage_winconf=null;

function mypage_editComponent(compname, asnew) {
	var compid=0;

	if (!asnew) {
		compid=compname;
		compname=tikimypagewin[compid].options.title;
	}

	mypage_winconf=new Windoo({
		"modal": true,
		"width": 700,
		"height": 400,
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
		xajax_mypage_win_prepareConfigure('{/literal}{$id_mypage}{literal}', 0, compname);
	} else {
		$('mypage_config_contenttype').value='';
		$('mypage_config_compid').value=compid;
		xajax_mypage_win_prepareConfigure('{/literal}{$id_mypage}{literal}', compid);
	}
}

function mypage_configuresubmit() {
	var compid=$('mypage_config_compid').value;

	if (compid > 0) {
		xajax_mypage_win_configure('{/literal}{$id_mypage}{literal}', compid, xajax.getFormValues("mypage_formconfigure"));
	} else {
		xajax_mypage_win_create('{/literal}{$id_mypage}{literal}',
			$('mypage_config_contenttype').value,
			$('mypage_configure_title').value,
			xajax.getFormValues("mypage_formconfigure"));
	}

	if (mypage_winconf) {
		mypage_winconf.close();
		mypage_winconf=null;
	}
}

////////////
//
// slidebar
//
// this code come from http://www.andrewsellick.com/examples/sliding-side-bar/
//

var isExtended = 0;
var height = 450;
var width = 200;
var slideDuration = 1000;
var opacityDuration = 1500;

function extendContract(){
	
	if(isExtended == 0){
		
		sideBarSlide(0, height, 0, width);
		
		sideBarOpacity(0, 0.8);
	
		isExtended = 1;
		
		// make expand tab arrow image face left (inwards)
		//$('sideBarTab').childNodes[0].src = $('sideBarTab').childNodes[0].src.replace(/(\.[^.]+)$/, '-active$1');
		
	}
	else{
		
		sideBarSlide(height, 0, width, 0);
		
		sideBarOpacity(1, 0);
		
		isExtended = 0;
		
		// make expand tab arrow image face right (outwards)
		
		//$('sideBarTab').childNodes[0].src = $('sideBarTab').childNodes[0].src.replace(/-active(\.[^.]+)$/, '$1');
	}

}

function sideBarSlide(fromHeight, toHeight, fromWidth, toWidth){
		var myEffects = new Fx.Styles('sideBarContents', {duration: slideDuration, transition: Fx.Transitions.linear});
		myEffects.custom({
			 'height': [fromHeight, toHeight],
			 'width': [fromWidth, toWidth]
		});
}

function sideBarOpacity(from, to){
		var myEffects = new Fx.Styles('sideBarContents', {duration: opacityDuration, transition: Fx.Transitions.linear});
		myEffects.custom({
			 'opacity': [from, to]
		});
}

function initSideBar(){
	{/literal}{if $editit}{literal}
	$('sideBarTab').addEvent('click', function(){extendContract()});
	initColorPicker();
	{/literal}{/if}{literal}
}

///////////////////////////

var stylepos_wintext;
var stylepos_wintitle;
var stylepos_winbg;

function mypageSetbgcolor(color, selectit, saveit) {
	$('myRainbow_bg').style.background=color;
	if (selectit) {
		$('mypage').style.background=color;
		$('radio_bg_color').checked=true;
	}
	if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'bgtype' : 'color', 'bgcolor': color });
}

function mypageSetbgimage(imageurl, selectit, saveit) {
	$('input_bgimage').value=imageurl;
	if (selectit) {
		$('mypage').style.background="url('"+htmlspecialchars(imageurl)+"')";
		$('radio_bg_image').checked=true;
	}
	if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'bgtype' : 'imageurl', 'bgimage': imageurl });
}

function mypageSetwinbgcolor(color, selectit, saveit) {
	$('myRainbow_winbg').style.background=color;
	if (selectit) {
		stylepos_winbg=updateStyleRule('.windoo-body', "background: "+htmlspecialchars(color), stylepos_winbg);
		$('radio_winbg_color').checked=true;
	}
	if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'winbgtype' : 'color', 'winbgcolor': color });
}

function mypageSetwinbgimage(imageurl, selectit, saveit) {
	$('input_winbgimage').value=imageurl;
	if (selectit) {
		stylepos_winbg=updateStyleRule('.windoo-body', "background: url('"+htmlspecialchars(imageurl)+"')", stylepos_winbg);
		$('radio_winbg_image').checked=true;
	}
	if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'winbgtype' : 'imageurl', 'winbgimage': imageurl });
}

function mypageSetwintitlecolor(color, selectit, saveit) {
	$('myRainbow_wintitle').style.background=color;
	if (selectit)
		stylepos_wintitle=updateStyleRule('.title-text', 'color: '+color, stylepos_wintitle);
	if (saveit)
		xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'wintitle': color });
}

function mypageSetwintextcolor(color, selectit, saveit) {
	$('myRainbow_wintext').style.background=color;
	if (selectit)
		stylepos_wintext=updateStyleRule('.windoo-body *', 'color: '+color, stylepos_wintext);
	if (saveit)
		xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'wintextcolor': color });
}

function updateStyleRule(selector, rule, stylepos) {
	if (document.all) {
		if (stylepos)
			document.styleSheets[stylepos[0]].removeRule(stylepos[1]);
		else {
			stylepos=[ document.styleSheets.length - 1, 0 ];
			stylepos[1]=document.styleSheets[stylepos[0]].rules.length;
		}
		document.styleSheets[stylepos[0]].addRule(selector,rule);
	} else if (document.getElementById) {
		if (stylepos)
			document.styleSheets[stylepos[0]].deleteRule(stylepos[1]);
		else {
			stylepos=[ document.styleSheets.length - 1, 0 ];
			stylepos[1]=document.styleSheets[stylepos[0]].cssRules.length;
		}
		document.styleSheets[stylepos[0]].insertRule(selector + '{' + rule + '}', stylepos[1]);
 	}
	return stylepos;
}

function initCSSStyle() {
	{/literal}
	var bgtype='{$mypage->getParam('bgtype')|escape:'javascript'}';
	var bgimage='{$mypage->getParam('bgimage')|escape:'javascript'}';
	var bgcolor='{$mypage->getParam('bgcolor')|escape:'javascript'}';

	var winbgtype='{$mypage->getParam('winbgtype')|escape:'javascript'}';
	var winbgimage='{$mypage->getParam('winbgimage')|escape:'javascript'}';
	var winbgcolor='{$mypage->getParam('winbgcolor')|escape:'javascript'}';
	
	mypageSetwintitlecolor('{$mypage->getParam('wintitlecolor')|escape:'javascript'}', true, false);
	mypageSetwintextcolor('{$mypage->getParam('wintextcolor')|escape:'javascript'}', true, false);

	mypageSetbgcolor(bgcolor, bgtype=='color', false);
	mypageSetbgimage(bgimage, bgtype=='imageurl', false);

	mypageSetwinbgcolor(winbgcolor, winbgtype=='color', false);
	mypageSetwinbgimage(winbgimage, winbgtype=='imageurl', false);

	{literal}
}

function initColorPicker() {
	var r_wintext = new MooRainbow('myRainbow_wintext', {
		'id': 'moorainbox_wintext',
		'startColor': new Color('{/literal}{$mypage->getParam('wintextcolor', '#000000')|escape:'javascript'}{literal}', 'hex'),
		'imgPath': 'lib/mootools/extensions/mooRainbow/images/',
		'onComplete': function(color) {
			mypageSetwintextcolor(color.hex, true, true);
		}
	});
	var r_wintitle = new MooRainbow('myRainbow_wintitle', {
		'id': 'moorainbox_wintitle',
		'startColor': new Color('{/literal}{$mypage->getParam('wintitlecolor', '#000000')|escape:'javascript'}{literal}', 'hex'),
		'imgPath': 'lib/mootools/extensions/mooRainbow/images/',
		'onComplete': function(color) {
			mypageSetwintitlecolor(color.hex, true, true);
		}
	});
	var r_winbg = new MooRainbow('myRainbow_winbg', {
		'id': 'moorainbox_winbg',
		'startColor': new Color('{/literal}{$mypage->getParam('winbgcolor', '#ffffff')|escape:'javascript'}{literal}', 'hex'),
		'imgPath': 'lib/mootools/extensions/mooRainbow/images/',
		'onComplete': function(color) {
			mypageSetwinbgcolor(color.hex, true, true);
		}
	});
	var r_bg = new MooRainbow('myRainbow_bg', {
		'id': 'moorainbox_bg',
		'startColor': new Color('{/literal}{$mypage->getParam('bgcolor', '#ffffff')|escape:'javascript'}{literal}', 'hex'),
		'imgPath': 'lib/mootools/extensions/mooRainbow/images/',
		'onComplete': function(color) {
			mypageSetbgcolor(color.hex, true, true);
		}
	});
}

////////////////////////////

function htmlspecialchars(ch) {
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/\"/g,"&quot;");
	ch = ch.replace(/\'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch;
}

function windooFocusChanged(id) {
	lastFocusedWindoo=id;
	$('mypage_configcomp_span').innerHTML=htmlspecialchars(tikimypagewin[id].options.title);
}

function windooStartDrag(id) {
	if (isExtended) extendContract();
}

function initMyPage() {
	initCSSStyle();
	initSavedWindows();
	initSimpleTabs();
	initSideBar();
}

{/literal}
{if $feature_phplayers eq 'y'}{* this is an ugly hack ... *}
window.addEvent('load', initMyPage); // <-work better with phplayers
{else}
window.addEvent('domready', initMyPage);
{/if}
{literal}

</script>
{/literal}
