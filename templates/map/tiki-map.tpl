<div align="center">
   <form action="tiki-map.phtml" method="GET">
   <input type="hidden" name="mapfile" value="{$mapfile}">
	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
		<td align="center" valign="center">
		<table border="1">
		<tr><td>
		  <input type="image" id="map" src="{$image_url}" width="{$size}" height="{$size}" 
		  alt="{tr}click on the map to zoom or pan, do not drag{/tr}" >
		</td></tr>
		<tr><td>
		  <img id="scale" src="{$image_scale_url}" >
		</td></tr>
		<tr><td align="center">	  
		  <select name="zoom" size="1">
		  {html_options values=$zoom_values selected=$zoom output=$zoom_display}
		  </select>
		  <select name="size" size="1">
		  {html_options values=$possiblesizes selected=$size output=$displaysizes}
		  </select><br>
		  <small>{tr}select zoom/pan/query and image size{/tr}</small>
		</td></tr>
		<tr><td align="center">
		  <input name="{tr}Redraw{/tr}" value="Redraw" type="Submit"><br>
		  <small>{tr}Click on the map or click redraw{/tr}</small>
		  <input type="hidden" name="minx" value="{$minx}">
		  <input type="hidden" name="miny" value="{$miny}">
		  <input type="hidden" name="maxx" value="{$maxx}">
		  <input type="hidden" name="maxy" value="{$maxy}">
		  <a href="/tiki/tiki-view_faq.php?faqId=1"><small>{tr}Help{/tr}</small></a>&nbsp
		  <a href="/tiki/tiki-index.php?page=MapServer"><small>{tr}Comments{/tr}</small></a>
		</td></tr>
		</table>
		</td>
		<td valign="top">
		  <table border="1">
		  <tr><td align="center" valign="center">
		  <img id="ref" src="{$image_ref_url}" alt="{tr}overview{/tr}" ><br>
		  <small>{tr}overview{/tr}</small>
		  </td></tr>
		  <tr><td>
		  <img id="leg" src="{$image_leg_url}" >
		  </td></tr>
		  <tr><td>
		  <div class="separator"><a class='separator' href="javascript:setCookie('mylayer','c');hide('mylayer');">[-]</a>
		  {tr}Layer Manager{/tr}
		  <a class='separator' href="javascript:setCookie('mylayer','o');show('mylayer');">[+]</a></div>
		  <div id='mylayer' style="display:none;">
		  <table>
		  <tr><td>
		  <b>Layer</b></td><td><b>{tr}On{/tr}</b></td>
		  <td><img src="/tiki/img/icons/edit.gif" alt="{tr}Label{/tr}"></td>
		  <td><img src="/tiki/img/icons/ico_save.gif" alt="{tr}Download{/tr}"></td></tr>
		  {section name=j loop=$my_layers}
		  <tr><td>
     {$layer_wiki[j]}
		  </td>
		  <td>
		  <input type="checkbox" name="{$my_layers[j]->name}" value="1" {$my_layers_checked[j]}>
		  </td><td>
		  {if $layer_label[j] eq "On"}
		  <input type="checkbox" name="{$my_layers[j]->name}_label" value="1" {$my_layers_label_checked[j]}>
		  {else}
		  &nbsp
		  {/if}
		  </td><td>
		  {if $layer_download[j] eq "T"}
		  <small>
		  <a href="tiki-map_download.php?layer={$my_layers[j]->connection}">D</a>
		  </small>
		  {/if}
		  </td></tr>
		  {/section}
		  </table>
		  </div>
		  </td>
	   </tr>
	   </table>
   </td></tr>
	  </table>
	  </form>
	  {$map_querymsg}
</div>