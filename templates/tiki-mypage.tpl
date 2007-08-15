<div style='width: {$mypage_width}; height: {$mypage_height};'>
<div id='mypage' style='position: absolute; width: {$mypage_width}; height: {$mypage_height}; overflow: hidden;'>

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
    				<ul>
     				{foreach from=$components item=component}
     					<li>
      					<a href='#' onclick='mypage_newComponent("{$component}");'>{$component|escape}</a>
     					</li>
     				{/foreach}
    				</ul>
    			</div>
    			<h5>Colors</h5>
    			<div id="components_colors">
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
// open saved windows
tikimypagewin=[];
{/literal}{$mypagejswindows}{literal}


//////////////////////
//
// initialize buttons
//
var mypage_winconf=null;

function mypage_newComponent(compname) {
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
		"title": "New "+compname+" :"
	}).setHTML("<p>Titre: <input type='text' id='mypage_configure_title' value=''></p><form id='mypage_formconfigure'><div id='mypage_divconfigure'></div></form><input type='button' value='Create' onclick='mypage_configuresubmit();'><input type='hidden' id='mypage_config_contenttype' valye='' />")
	.show();

	$('mypage_config_contenttype').value=compname;
	xajax_mypage_win_prepareConfigure('{/literal}{$id_mypage}{literal}', compname);

}

function mypage_configuresubmit() {
	xajax_mypage_win_create('{/literal}{$id_mypage}{literal}', $('mypage_config_contenttype').value, $('mypage_configure_title').value, xajax.getFormValues("mypage_formconfigure"));
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

function init(){
	{/literal}{if $editit}{literal}
	$('sideBarTab').addEvent('click', function(){extendContract()});
	{/literal}{/if}{literal}
}

window.addEvent('load', function(){init()});

///////////////////////////


</script>
{/literal}
