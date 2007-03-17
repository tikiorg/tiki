<h1><a class="pagetitle" href="tiki-mapeditor_edit_layer.php">{tr}Edit or create layer's group{/tr}</a></h1>
 <a class="linkbut" href="tiki-mapeditor_layers.php?mapId={$mapId}">{tr}List layers{/tr}</a>&nbsp;&nbsp;<a class="linkbut" href="tiki-mapeditor_maps.php?mapId={$mapId}">{tr}List Maps{/tr}</a>
<br /><br />
<form action="tiki-mapeditor_edit_layergroup.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="layerId" value="{$layerId|escape}" />
<div class="simplebox">
  <table ><tr><td>
  <table >
  <tr><td class="form">{tr}Name{/tr}:</td>
      <td class="form">
<input type="hidden" name="layerId" value="{$layerId}" />
      <input type="hidden" name="author" value="{$author}" />
      <input type="text" name="name" value="{$name}" />
      <input type="hidden" name="mapId" value="{$mapId}" />
      <input type="hidden" name="islayerGroup" value="{$islayerGroup}" />
      </td>
   </tr>
   <tr><td  class="form">{tr}Layer rendering{/tr}:</td>
       <td class="form"><select name="layerRendering">
           <option value="" {if $layerRendering eq ""}selected="selected"{/if}>None</option>
           <option value="block" {if $layerRendering eq "block"}selected="selected"{/if}>Block</option>
           <option value="radio" {if $layerRendering eq "radio"}selected="selected"{/if}>Radio</option>
           <option value="dropdown" {if $layerRendering eq "dropdown"}selected="selected"{/if}>Dropdown</option>
           </select>
        </td>
   </tr>
   </table>
   </td>
   <td align="right"  class="form">
  {tr}Project Name{/tr}:
   <input type="text" name="projectName" size="10" value="{$projectName}" /><br />
  {tr}Layer aggregate{/tr}:
  <select name="layerAggregate">
  	<option value="0" {if $layerAggregate eq 0}selected="selected"{/if}>Distinct</option>
	<option value="1" {if $layerAggregate eq 1}selected="selected"{/if}>Merged</option>
  </select>
   {tr}Parent{/tr}:
   <select name="layerGroupId">
   	<option value="0" {if $layerGroupId eq 0}selected="selected"{/if}>none</option>
	{section name=page loop=$parentgroup}
           <option value="{$parentgroup[page].layerId}" {if $layerGroupId eq $parentgroup[page].layerId++}selected="selected"{/if}>{$parentgroup[page].name}</option>
	{/section}
   </select>
   </td>
   </tr>
  </tr>
  <tr>
  <td class="form">
	{include file=categorize.tpl}
  </td>
  </tr>
  <tr><td  class="form">{tr}Gateway{/tr}:</td>
  <td class="form"><select name="gateway">
     {section name=page loop=$gateways}
       <option value="{$gateways[page].name}" {if $gateway eq $gateways[page].name}selected="selected"{/if}>{$gateways[page].name}</option>
   {/section}
	</select>
    &nbsp;&nbsp;{tr}Copyright{/tr}:
	<select name="copyright">
	<option value="GPL" {if $copyright eq 'GPL'}selected="selected"{/if}>GPL</option>
	<option value="IGN" {if $copyright eq 'IGN'}selected="selected"{/if}>IGN</option>
	</select>
</td>
</tr>
<tr><td  class="form">{tr}Descrition{/tr}:</td>
  <td class="form"><textarea  name="description" rows="5" cols="50">{$description|escape}</textarea>
</td>
</tr>

   </table>
</div>


<div align="center" class="simplebox">
<input type="submit" name="save" value="{tr}save the layer{/tr}" />
</div>
</form>

