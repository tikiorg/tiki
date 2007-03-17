<h1><a class="pagetitle" href="tiki-mapeditor_edit_layer.php">{tr}Edit or create layers{/tr}</a></h1>
<a class="linkbut" href="tiki-mapeditor_layers.php?mapId={$mapId}">{tr}List layers{/tr}</a><br /><br />
<form action="tiki-mapeditor_edit_layer.php" method="post" enctype="multipart/form-data">
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
      </td>
   </tr>
   <tr><td  class="form">{tr}Type{/tr}:</td>
       <td class="form"><select name="type">
           <option value="POINT" {if $type eq "POINT"}selected="selected"{/if}>POINT</option>
           <option value="LINE" {if $type eq "LINE"}selected="selected"{/if}>LINE</option>
           <option value="POLYGON" {if $type eq "POLYGON"}selected="selected"{/if}>POLYGON</option>
           </select>
        </td>
   </tr>
   <tr><td  class="form">{tr}Parent LayerGroup{/tr}:</td>
   <td class="form"><select name="layerGroupId">
   	<option value="0" {if $layerGroupId eq 0}selected="selected"{/if}>none</option>
	{section name=page loop=$parentgroup}
        	<option value="{$parentgroup[page].layerId}" {if $layerGroupId eq $parentgroup[page].layerId++}selected="selected"{/if}>{$parentgroup[page].name}</option>
	{/section}
   </select>
   </td>
   </tr>
   </table>
   </td>
   <td align="right"  class="form">
   {tr}Database{/tr}:
   <input type="text" name="db" size="15" value="{$db}" /><br />
   {tr}Project Name{/tr}:
   <input type="text" name="projectName" size="10" value="{$projectName}" /><br />
   {tr}Table{/tr}:
   <input type="text" name="table" size="15" value="{$table}" /><br />
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
<tr><td  class="form">{tr}CLASS{/tr}:</td>
  <td class="form"><textarea  name="config" rows="15" cols="50">{$config|escape}</textarea>
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

