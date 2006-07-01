<h1><a class="pagetitle" href="tiki-edit_map.php">{tr}Edit or create maps{/tr}</a></h1>
<a class="linkbut" href="tiki-list_maps.php">{tr}List maps{/tr}</a><br /><br />
<form action="tiki-edit_map.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="mapId" value="{$mapId|escape}" />
<div class="simplebox">
  <table ><tr><td>
  <table >
  <tr><td class="form">{tr}Name{/tr}:</td>
      <td class="form">
      <input type="hidden" name="mapId" value="{$mapId}" />
      <input type="hidden" name="author" value="{$author}" />
      <input type="text" name="name" value="{$name}" />
      </td>
   </tr>
   <tr><td  class="form">{tr}Type{/tr}:</td>
       <td class="form"><select name="type">
           <option value="tikimap" {if $type eq "tikimap"}selected="selected"{/if}>tikimap</option>
           <option value="cartoweb" {if $type eq "cartoweb"}selected="selected"{/if}>cartoweb</option>
           </select>
        </td>
   </tr>
   </table>
   </td>
   <td align="right"  class="form">
   {tr}Path{/tr}:
   <input type="text" name="path" size="40" value="{$path}" /><br />
   {tr}Project Name{/tr}:
   <input type="text" name="projectName" size="10" value="{$projectName}" /><br />
   {tr}Database{/tr}:
   <input type="text" name="db" size="15" value="{$db}" /><br />
   </td>
   </tr>
  </tr>
  <tr>
  <td class="form">
	{include file=categorize.tpl}
  </td>
  </tr>
  <tr><td  class="form">{tr}Gateway{/tr}:</td>
  <td class="form">
    <select name="gateway">
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
<tr><td  class="form">{tr}Description{/tr}:</td>
  <td class="form"><textarea  name="description" rows="5" cols="50">{$description|escape}</textarea>
</td>
</tr>

   </table>
</div>


<div align="center" class="simplebox">
<input type="submit" name="save" value="{tr}save the map{/tr}" />
</div>
</form>

