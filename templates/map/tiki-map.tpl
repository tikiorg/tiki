{* $Id$ *}
<script src="lib/x/x_core.js"></script>
<script src="lib/x/x_event.js"></script>
<script src="lib/x/x_dom.js"></script>
<script src='lib/x/x_slide.js'></script>
<script src='lib/x/x_misc.js'></script>
<script src='lib/x/x_drag.js'></script>
<script src="lib/map/map.js"></script>
{$xajax_javascript}

<h1>{$pagelink}</h1>
<div align="center">
  <form name="frmmap" id="frmmap" action="tiki-map.php" method="get">
   <input type="hidden" name="mapfile" value="{$mapfile}" />
	<table border="0" cellpadding="0" cellspacing="0" >
	  <tr>
	     <td align="center" valign="middle">
		<table class="normal">
		  <tr><td align="center"> 
		  <div id="mapWindow" style="z-index:50;overflow:hidden;
		  	{if $xsize != ""}width:{$xsize}px;{/if} 
				{if $ysize != ""}height:{$ysize}px;{/if}
			">	
					  <div id="zoomselect" style="position:absolute; left:0px; top:0px; width: 0px; height: 0px; 
							border: blue solid 2px; z-index:30; visibility:hidden;overflow:hidden;">
			      </div>
		      {*	<input type="image" id="map" src="{$image_url}"  *}
		        <img id="map" src="{$image_url}"
						{if $xsize != ""}width="{$xsize}"{/if} 
						{if $ysize != ""}height="{$ysize}"{/if} 
					  border="0"
		  			alt="{tr}click on the map to zoom or pan, do not drag{/tr}" 
					  title="{tr}click on the map to zoom or pan, do not drag{/tr}"
					  style="z-index:20;position:relative"/> 
			</div>		  
		  <script type="text/javascript">	
		    var minx={$minx};
		    var miny={$miny};
		    var maxx={$maxx};
		    var maxy={$maxy};
		    var xsize={$xsize};
		    var ysize={$ysize};
		    var mapfile='{$mapfile}';
		    var layers= new Array();
		    {section name=j loop=$my_layers}
		    {if $my_layers_checked[j] eq "checked"}
		    	layers[{$smarty.section.j.index}]=true;
		    {else}
		    	layers[{$smarty.section.j.index}]=false;
		    {/if}
		    {/section}
		    var labels= new Array();
		    {section name=j loop=$my_layers}
		    {if $my_layers_label_checked[j] eq "checked"}
		    	labels[{$smarty.section.j.index}]=true;
		    {else}
		    	labels[{$smarty.section.j.index}]=false;
		    {/if}
		    {/section}
				xAddEventListener(xGetElementById('map'),'mousemove',map_mousemove,false);
				xAddEventListener(xGetElementById('map'),'mousedown',map_mousedown,false);			
				xAddEventListener(xGetElementById('map'),'mouseup',map_mouseup,false);
				xAddEventListener(xGetElementById('map'),'mouseout',map_mouseup,false);
				xAddEventListener(xGetElementById('map'),'click',map_mouseclick,false);
				{literal}
				if (xIE4Up) {
					xAddEventListener(xGetElementById('map'),'drag',function() {return false;},false);
				}
				{/literal}
				xEnableDrag(xGetElementById('map'));
			</script>
			
			<div id="queryWindow" style="position:absolute; top:0px; left:0px; visibility:hidden; z-Index:100">
				<div id="outerBox" style="position:absolute; top:0px; left:0px;
					height:200px; width:275px; overflow:hidden; border-top:4px solid #ffffff;
					border-left:4px solid #ffffff; border-right:4px solid #666666;
					border-bottom:4px solid #666666; background-color:#ffffff; z-Index:100">
					<div id="queryBar" style="position:absolute; top:0px; left:0px; padding:1px;
						font:8px Arial, Helvetical, sans-serif; font-weight:bold; z-Index:200;
						background-color: #D0D0FF; width:255px; cursor: move">{tr}Query Results{/tr}</div>
					<div id="innerBox" style="position:absolute; top:15px; left:0px; padding:8px;
						height:160px; width:245px; font:12px Arial, Helvetical, sans-serif; z-Index:150;
						overflow:hidden">
					<div id="innerBoxContent" style="position:relative; top:0px">
						<p>{tr}Querying{/tr}...</p>
					</div>
					</div>
					<img id="queryClose" src="img/icons/close.gif" height="13" width="13" alt="{tr}Scroll Up{/tr}"
						style="position:absolute; top:0px; left:257px" />
					<img id="queryUp" src="img/icons2/up.gif" height="8" width="10" alt="{tr}Scroll Up{/tr}"
						style="position:absolute; top:15px; left:257px" />
					<img id="queryDown" src="img/icons2/down.gif" height="8" width="10" alt="{tr}Scroll Down{/tr}"
						style="position:absolute; top:188px; left:257px" />
				  <script type="text/javascript">
				  	var scrollActive = false, scrollStop = true, scrollIncrement = 10, scrollInterval = 60;
				  	xAddEventListener(xGetElementById('queryClose'),'click',query_close,false);
				  	xAddEventListener(xGetElementById('queryUp'),'mousemove',query_up,false);
				  	xAddEventListener(xGetElementById('queryUp'),'mouseout',query_scroll_stop,false);
				  	xAddEventListener(xGetElementById('queryDown'),'mousemove',query_down,false);
				  	xAddEventListener(xGetElementById('queryDown'),'mouseout',query_scroll_stop,false);
				  	xEnableDrag(xGetElementById('queryBar'), queryOnDragStart, queryOnDrag, null);
				  </script>
				</div>
			</div>
				
		  </td></tr>
		  <tr><td align="center">
		 	<img id="scale" src="{$image_scale_url}" border="0" alt="{tr}Scale{/tr}" title="{tr}Scale{/tr}" />
		 	<div align="center">
		 	<input type="text" id="xx"/><input type="text" id="yy"/>
			</div>
		  </td></tr>
		  <tr><td align="center">	
			{if $zoom eq -2}
			<img id="imgzoom2" src="img/icons/zoom-2.gif" onclick="zoomin(0)" alt="-x2" title="{tr}Zoom out{/tr}" border="1" />
			{else}
			<img id="imgzoom2" src="img/icons/zoom-2.gif" onclick="zoomin(0)" alt="-x2" title="{tr}Zoom out{/tr}" />
			{/if}
			{if $zoom eq 0}
			<img id="imgzoom3" src="img/icons/info.gif" onclick="zoomin(1)" alt="Q" title="{tr}Query{/tr}" border="1" />
			<script type="text/javascript">
  			var map=xGetElementById('map');
  			map.style.cursor='help';
			</script>
			{else}
			<img id="imgzoom3" src="img/icons/info.gif" onclick="zoomin(1)" alt="Q" title="{tr}Query{/tr}" />
			{/if}
			{if $zoom eq 1}
			<img id="imgzoom4" src="img/icons/move.gif" onclick="zoomin(2)" alt="P" title="{tr}Pan{/tr}" border="1" />
			<script type="text/javascript">
  			var map=xGetElementById('map');
  			map.style.cursor='move';
			</script>
			{else}
			<img id="imgzoom4" src="img/icons/move.gif" onclick="zoomin(2)" alt="P" title="{tr}Pan{/tr}" />
			{/if}
			{if $zoom eq 2}
			<img id="imgzoom5" src="img/icons/zoom+2.gif" onclick="zoomin(3)" alt="x2" title="{tr}Zoom in{/tr}" border="1" />
			{else}
			<img id="imgzoom5" src="img/icons/zoom+2.gif" onclick="zoomin(3)" alt="x2" title="{tr}Zoom in{/tr}" />
			{/if}
			&nbsp;
			<select id="zoom" name="zoom" size="1" onchange="cbzoomchange()">
				{html_options values=$zoom_values selected=$zoom output=$zoom_display name=$oldzoom}
			</select>
			<select name="size" size="1">
				{html_options values=$possiblesizes selected=$size output=$displaysizes}
			</select><br />
			<input name="Redraw" value="{tr}Redraw{/tr}" type="Submit" /><br />
			<small>{tr}select zoom/pan/query and image size{/tr}</small>
		</td></tr>
			<tr><td align="center"> 
			{if $map_view eq "" }
                  		{*if view is empty do not display empty list*} 
			{else}
			<select name="view" size="1"> 
			<option selected value="#">Select Location and Go!
				{html_options values=$view_name name=$view output=$view_name}
			</select>
				<input type="submit" name="Go" value="{tr}Go{/tr}" />&nbsp;
			{/if}
			 <input type="image" name="maponly" value="yes" src="img/icn/png.gif" border="0" alt="{tr}View the Map Only{/tr}" title="{tr}View the Map Only{/tr}" />
			{if $tiki_p_map_edit eq 'y'}
				&nbsp; 
				<a class="link" href="tiki-map_edit.php?mapfile={$mapfile}&amp;mode=editing">
				<img src="pics/icons/wrench.png" border="0" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" width="16" height="16" /></a>
			{/if}
			&nbsp;
			<a href="tiki-map.php?mapfile={$mapfile}" ><small>{tr}Reset Map{/tr}</small></a><br /> 
			<small>{tr}Click on the map or click redraw{/tr}</small>
			<input id="minx" type="hidden" name="minx" value="{$minx}" />
			<input id="miny" type="hidden" name="miny" value="{$miny}" />
			<input id="maxx" type="hidden" name="maxx" value="{$maxx}" />
			<input id="maxy" type="hidden" name="maxy" value="{$maxy}" />
			<input id="size" type="hidden" name="oldsize" value="{$size}" />
			<a href="tiki-index.php?page={$prefs.map_help}"><small>{tr}Help{/tr}</small></a>&nbsp;
			<a href="tiki-index.php?page={$prefs.map_comments}"><small>{tr}Comments{/tr}</small></a><br />
		</td></tr>
	<tr><td><div id="resultBox">{$map_querymsg}</div></td></tr>	
		</table>
		<p class="editdate">{tr}Last modification date{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$lastUser}">{$lastUser}</a> ({$ip})-{tr}Hits{/tr}:{$mapstats}({$mapstats7days})</p>
	     
	     </td>
		<td valign="top">
		<table class="normal">
		   <tr><td class="heading" align="center"><b>{tr}Overview{/tr}</b></td></tr>
		   <tr><td align="center" valign="middle" bgcolor="FFFFFF">
		   <img id="ref" src="{$image_ref_url}" border="1" alt="{tr}Overview{/tr}" title="{tr}Overview{/tr}" /></td ></tr>
		   <tr><td class="heading" align="center"><b>{tr}Legend{/tr}</b></td></tr>
		   <tr><td align="center" bgcolor="FFFFFF"><img id="leg" src="{$image_leg_url}" border="0" alt="{tr}Legend{/tr}" title="{tr}Legend{/tr}" /></td></tr>
		   <tr><td>
    			<div class="separator">
			{if $prefs.feature_menusfolderstyle eq 'y'}
				<a class="separator" href="javascript:toggle('layermenu');"><img src="img/icons/fo.gif" border="0" name="layermenuicn" alt=""/>&nbsp;</a>
			{else}
			<a class="separator" href="javascript:toggle('layermenu');"><b>[+/-]</b></a>
			{/if}
			{tr}Layer Manager{/tr}
			</div>
			<div id='layermenu' style="{$mnu_layermenu}">
    			<table class="normal">
			<tr>
				<td class="heading"><b>{tr}Layer{/tr}</b></td>
		  		<td class="heading"><b>{tr}On{/tr}</b></td>
		  		<td class="heading">
				<img src="img/icons/edit.gif" border="0" alt="{tr}Label{/tr}" title="{tr}Label{/tr}" /></td>
		  		<td class="heading">
				<img src="img/icons/question.gif" border="0" alt="{tr}Query{/tr}" title="{tr}Query{/tr}" /></td>
		  		<td class="heading"><img src="pics/icons/disk.png" width="16" height="16" border="0" alt="{tr}Download{/tr}" title="{tr}Download{/tr}" /></td>
			</tr>
			{section name=j loop=$my_layers}
			{if $my_layers[j]->group neq "" }
			{if $my_layers[j]->group eq $unique_layer_group[j]}
			<tr>
				{if $smarty.section.j.index % 2}
				<td class="odd" colspan="5">
				{else}
				<td class="even" colspan="5">
				{/if}
				<div class="separator">
					{if $prefs.feature_menusfolderstyle eq 'y'}
					<a class="separator" href="javascript:icntoggle('submenu{$unique_layer_group[j]}');"><img src="img/icons/fo.gif" border="0" name="layermenuicn" alt=""/>&nbsp;</a>
					{else}
					
					&nbsp;&nbsp;<a class="separator" href="javascript:toggle('submenu{$unique_layer_group[j]}');">[+/-]</a>
					{/if}
					{tr}{$my_layers[j]->group}{/tr}		
				</div>
				<div id='submenu{$unique_layer_group[j]}' style="{$mnu_submenu}"> 
					<table class="normal">
					{section name=i loop=$my_layers}
					{if $my_layers[i]->group neq "" }
					{if $my_layers[i]->group == $my_layers[j]->group}
					<tr>
					
						{if $smarty.section.i.index % 2}
						<td class="odd">
						{else}
						<td class="even">
						{/if}
						{tr}{$layer_wiki[i]}{/tr}
						</td>
						{if $smarty.section.i.index % 2}
						<td class="odd" width=20px>
						{else}
						<td class="even" width=20px>
						{/if}
						<input type="checkbox" onclick="changelayer({$smarty.section.i.index})" name="{$my_layers[i]->name}" value="1" {$my_layers_checked[i]} />
						</td>
						{if $smarty.section.i.index % 2}
						<td class="odd" width=20px>
						{else}
						<td class="even" width=20px>
						{/if}
						{if $layer_label[i] eq "On"}
						<input type="checkbox" onclick="changelabel({$smarty.section.i.index})" name="{$my_layers[i]->name}_label" value="1" {$my_layers_label_checked[i]} />
						{else}
						&nbsp;
						{/if}
						</td>
						{if $smarty.section.i.index % 2}
						<td class="odd" width=20px>
						{else}
						<td class="even" width=20px>
						{/if}
						{if $layer_query[i] eq "On"}
						<img src="img/icons/question.gif" border="0" alt="{tr}Query{/tr}" title="{tr}Query{/tr}" />
						{else}
						&nbsp;
						{/if}
						</td>
						{if $smarty.section.i.index % 2}
						<td class="odd" width=20px>
						{else}
						<td class="even" width=20px>
						{/if}
						{if $layer_download[i] eq "T"}
						<small>
						<a href="tiki-map_download.phtml?mapfile={$mapfile}&amp;layer={$my_layers[i]->name}">
						<img src="pics/icons/disk.png" border="0" alt="{tr}Download{/tr}" title="{tr}Download{/tr}" width="16" height="16" /></a>
						</small>
						{/if}
						</td>
					</tr>
					{/if}
					{/if}
					{/section}
					</table>
				</div>
				</td>
			    </tr>
			    {/if}
			{else}{*end of if group not empty loop*}
			    <tr>
				{if $smarty.section.j.index % 2}
				<td class="odd">
				{else}
				<td class="even">
				{/if}
				{tr}{$layer_wiki[j]}{/tr}
				</td>
				{if $smarty.section.j.index % 2}
				<td class="odd">
				{else}
				<td class="even">
				{/if}
				<input type="checkbox" onclick="changelayer({$smarty.section.j.index})" name="{$my_layers[j]->name}" value="1" {$my_layers_checked[j]} />
				</td>
				{if $smarty.section.j.index % 2}
				<td class="odd">
				{else}
				<td class="even">
				{/if}
				{if $layer_label[j] eq "On"}
				<input type="checkbox" onclick="changelabel({$smarty.section.j.index})" name="{$my_layers[j]->name}_label" value="1" {$my_layers_label_checked[j]} />
				{else}
				&nbsp;
				{/if}
				</td>
				{if $smarty.section.j.index % 2}
				<td class="odd">
				{else}
				<td class="even">
				{/if}
				{if $layer_query[j] eq "On"}
				<img src="img/icons/question.gif" border="0" alt="{tr}Query{/tr}" title="{tr}Query{/tr}" />
				{else}
				&nbsp;
				{/if}
				</td>
				{if $smarty.section.j.index % 2}
				<td class="odd">
				{else}
				<td class="even">
				{/if}
				{if $layer_download[j] eq "T"}
				<small>
				<a href="tiki-map_download.phtml?mapfile={$mapfile}&amp;layer={$my_layers[j]->name}">
				<img src="pics/icons/disk.png" border="0" alt="{tr}Download{/tr}" title="{tr}Download{/tr}" width="16" height="16" /></a>
				</small>
				{/if}
				</td>
			</tr>
			{/if}	
			{/section}
			</table>
			</div>
		</td>
		</tr>
	</table>
	</td></tr>
	</table>		  
  </form>
</div>
