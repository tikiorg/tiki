<div style='width: {$mypage_width}; height: {$mypage_height};'>
<div id='mypage' style='position: absolute; width: {$mypage_width}; height: {$mypage_height}; background: {$mypage_bgcolor}; overflow: hidden;'>

{if $editit}
 <!-- sidebar -->
 <div id='sideBar' style='position: absolute; width: auto; height: auto; top: 200px; right: 7px; z-index: 255;'>

  <a href="#" id="sideBarTab" style='float:left; height:137px; width:28px; border: 0;'>
   <img src="img/cord.png" alt="Tools" title="Tools" style='width: 28px; height: 137px; border: 0;'/>
  </a>
	
  <div id="sideBarContents" style="width:0px; overflow: hidden !important; background: white;">
   <div id="sideBarContentsInner" style="width: 250px;">
	<div id="container" >
		<div id="tab-block-1" >
			<h5>Tools</h5>
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

    			<h5>Colors</h5>
    			<div id="components_colors">
				<ul>
					<li>
						Background:
						<span id='myRainbow' style='cursor: pointer; background: {$mypage_bgcolor};'>
							[&nbsp;&nbsp;&nbsp;&nbsp;]
						</span>
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
		
		sideBarOpacity(0, 1);
	
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
	var r = new MooRainbow('myRainbow', {
		'startColor': new Color('{/literal}{$mypage_bgcolor}{literal}', 'hex'),
		'imgPath': 'lib/mootools/extensions/mooRainbow/images/',
		'onComplete': function(color) {
			$('myRainbow').style.background=color.hex;
			$('mypage').style.background=color.hex;
			xajax_mypage_update({/literal}{$id_mypage}{literal}, { 'bgcolor': color.hex });
		},
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

function initMyPage() {
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
