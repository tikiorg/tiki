{include file='tiki-mypage_content.tpl'}
{literal}
<script type="text/javascript">

var id_mypage={/literal}{$id_mypage}{literal};
var mypage_editit={/literal}{if $editit}true{else}false{/if}{literal};

{/literal}{if $editit}{literal}
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

//////////////////////
//
// initialize buttons
//
var mypage_winconf=null;

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
{/literal}{/if}{literal}
///////////////////////////

var stylepos_wintext;
var stylepos_wintitle;
var stylepos_winbg;

function mypageSetbgcolor(color, selectit, saveit) {
	{/literal}{if $editit}{literal}
		if (color != null) $('myRainbow_bg').style.background=color;
		if (selectit) {
			if (color == null) $('radio_bg_transparent').checked=true;
			else $('radio_bg_color').checked=true;
		}
		if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'bgtype' : 'color', 'bgcolor': color });
	{/literal}{/if}{literal}
	if (selectit) {
		if (color == null) $('mypage').style.background='none 100%';
		else $('mypage').style.background=color;
	}
}

function mypageSetbgimage(imageurl, selectit, saveit) {
	{/literal}{if $editit}{literal}
		$('input_bgimage').value=imageurl;
		if (selectit) $('radio_bg_image').checked=true;
		if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'bgtype' : 'imageurl', 'bgimage': imageurl });
	{/literal}{/if}{literal}
	if (selectit) $('mypage').style.background="url('"+htmlspecialchars(imageurl)+"')";
}

function mypageSetwinbgcolor(color, selectit, saveit) {
	{/literal}{if $editit}{literal}
		if (color != null) $('myRainbow_winbg').style.background=color;
		if (selectit) {
			if (color == null) $('radio_winbg_transparent').checked=true;
			else $('radio_winbg_color').checked=true;
		}
		if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'winbgtype' : 'color', 'winbgcolor': color });
	{/literal}{/if}{literal}
	if (selectit) {
	{/literal}{if $editit}{literal}
		if (color == null) stylepos_winbg=updateStyleRule('div.windoo-mypage div.windoo-body', "background: none", stylepos_winbg);
		else stylepos_winbg=updateStyleRule('div.windoo-mypage div.windoo-body', "background: "+htmlspecialchars(color), stylepos_winbg);
	{/literal}{else}{literal}
		if (color == null) stylepos_winbg=updateStyleRule('div.windoo-mypage_view div.windoo-body', "background: none", stylepos_winbg);
		else stylepos_winbg=updateStyleRule('div.windoo-mypage_view div.windoo-body', "background: "+htmlspecialchars(color), stylepos_winbg);
	{/literal}{/if}{literal}
	}
}

function mypageSetwinbgimage(imageurl, selectit, saveit) {
	{/literal}{if $editit}{literal}
		$('input_winbgimage').value=imageurl;
		if (selectit) $('radio_winbg_image').checked=true;
		if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'winbgtype' : 'imageurl', 'winbgimage': imageurl });
	{/literal}{/if}{literal}
	if (selectit) stylepos_winbg=updateStyleRule('div.windoo-mypage div.windoo-body,div.windoo-mypage_view div.windoo-body', "background: url('"+htmlspecialchars(imageurl)+"')", stylepos_winbg);
}

function mypageSetwintitlecolor(color, selectit, saveit) {
	{/literal}{if $editit}{literal}
		$('myRainbow_wintitle').style.background=color;
		if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'wintitlecolor': color });
	{/literal}{/if}{literal}
	if (selectit) stylepos_wintitle=updateStyleRule('.title-text', 'color: '+color, stylepos_wintitle);
}

function mypageSetwintextcolor(color, selectit, saveit) {
	{/literal}{if $editit}{literal}
		$('myRainbow_wintext').style.background=color;
		if (selectit) stylepos_wintext=updateStyleRule('.windoo-body *', 'color: '+color, stylepos_wintext);
	{/literal}{/if}{literal}
	if (saveit) xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'wintextcolor': color });
}

function updateStyleRule(selector, rule, stylepos) {
	if (document.all) {
		if (stylepos)
			document.styleSheets[stylepos[0]].removeRule(stylepos[1]);
		else {
			stylepos=[ document.styleSheets.length - 1, 0 ];
			stylepos[1]=document.styleSheets[stylepos[0]].rules.length;
		}
		document.styleSheets[stylepos[0]].addRule(selector,rule, stylepos[1]);
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

	/* workarround for null values not converted by xajax */
	if (bgcolor == '') bgcolor=null;
	if (winbgcolor == '') winbgcolor=null;

	mypageSetwintitlecolor('{$mypage->getParam('wintitlecolor')|escape:'javascript'}', true, false);
	mypageSetwintextcolor('{$mypage->getParam('wintextcolor')|escape:'javascript'}', true, false);

	mypageSetbgcolor(bgcolor, bgtype=='color', false);
	mypageSetbgimage(bgimage, bgtype=='imageurl', false);

	mypageSetwinbgcolor(winbgcolor, winbgtype=='color', false);
	mypageSetwinbgimage(winbgimage, winbgtype=='imageurl', false);

	{literal}
}

////////////////////////////


var lastFocusedWindoo=0;
var tikimypagewin=[];
function initSavedWindows() {
	// open saved windows
	{/literal}{$mypagejswindows}{literal}
}

function initMyPage() {
	initCSSStyle();
	{/literal}{if $editit}
	initSimpleTabs();
	initSideBar();
	{/if}{literal}
	initSavedWindows();
}

window.addEvent('domready', initMyPage);

</script>
{/literal}
