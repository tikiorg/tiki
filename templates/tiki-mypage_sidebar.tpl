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
     					<li id='elem_addComponent_{$component|escape}'>
      					<a href='#' onclick='mypage_addComponent("{$component}");'>{$component|escape}</a>
     					</li>
     				{/foreach}
    				</ul>
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
					 <div><input type='radio' id='radio_winbg_transparent' name='radio_winbg' onclick='mypageSetwinbgcolor(null, true, true);' />{tr}Transparent{/tr}</div>
					 <div><input type='radio' id='radio_winbg_color' name='radio_winbg' />{tr}Color{/tr}: <span id='myRainbow_winbg' style='cursor: pointer; background: {$mypage->getParam('winbgcolor')|escape:'javascript'};'>[&nbsp;&nbsp;&nbsp;&nbsp;]</span></div>
					 <div><input type='radio' id='radio_winbg_image' name='radio_winbg' onclick="mypageSetwinbgimage($('input_winbgimage').value, true, true)" />{tr}Image (url){/tr}: <input id='input_winbgimage' type='text' style='width: 144px'><input type='button' value='ok' onclick="mypageSetwinbgimage($('input_winbgimage').value, true, true)" /></div>
					</li>
					<li>
					 Background:
					 <div><input type='radio' id='radio_bg_transparent' name='radio_bg' onclick='mypageSetbgcolor(null, true, true);' />{tr}Transparent{/tr}</div>
					 <div><input type='radio' id='radio_bg_color' name='radio_bg' />{tr}Color{/tr}: <span id='myRainbow_bg' style='cursor: pointer; background: {$mypage->getParam('bgcolor')|escape:'javascript'};'>[&nbsp;&nbsp;&nbsp;&nbsp;]</span></div>
					 <div><input type='radio' id='radio_bg_image' name='radio_bg' onclick="mypageSetbgimage($('input_bgimage').value, true, true)"/>{tr}Image (url){/tr}: <input id='input_bgimage' type='text' style='width: 145px'><input type='button' value='ok' onclick="mypageSetbgimage($('input_bgimage').value, true, true)" /></div>
					</li>
				</ul>
    			</div>
   		</div>
  	</div>
   </div>
   </div>
 </div>
