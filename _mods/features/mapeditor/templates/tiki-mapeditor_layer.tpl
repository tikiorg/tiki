{if $smarty.get.action eq 'edit' || $submit eq 'add'}<div id="box" style="font-size: 9px; float:right;background-color:yellow; width:250px; text-align:left">
{$layer.start_tag}<br />
&nbsp;&nbsp;{$layer.tag_name}<br/>
&nbsp;&nbsp;{$layer.tag_status}<br/>
&nbsp;&nbsp;{$layer.tag_connexiontype}<br/>
&nbsp;&nbsp;{$layer.tag_connexion}<br/>
&nbsp;&nbsp;{$layer.tag_data}<br/>
&nbsp;&nbsp;{$layer.tag_type}<br/>

{$layer.end_tag}</div>{/if}
{if isset($error_msg) }{$error_msg}{/if}
<h1><a class="pagetitle" href="tiki-map_layer.php">{tr}Map Layers{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="http://dev.sigfreed.net/tiki-index.php?page=MapLayerDoc#id222862" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Layers{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" />
</a>
{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-map_layer.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}list quizzes tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

&nbsp;&nbsp;<a class="linkbut" href="tiki-map_layer.php?action=edit">{tr}edit layers{/tr}</a>
&nbsp;&nbsp;<a class="linkbut" href="tiki-map_layer.php?action=add">{tr}add layers{/tr}</a>&nbsp;&nbsp;<a class="linkbut" href="tiki-map_layer.php?action=list">{tr}list layers{/tr}</a>
{if $smarty.get.action eq 'list'}
<! -- begin find field ---!>
<br />
<br />

<div  align="center">
<table class="findtable">
<tr>
<td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-map_layer.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="mapId" value="{$mapId|escape}" />
   </form>
   </td>
</tr>
</table>
</div>
<!-- begin the table  -->
<br /><br />
<table class="normal">
<tr>
<td class="heading">
ICON
</td>
<td class="heading">
<a class="tableheading" href="tiki-map_layer.php?action={$action}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}layer name{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-map_layer.php?action={$action}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}Author{/tr}</a>
</td>

<td class="heading">
<a class="tableheading" href="tiki-map_layer.php?action={$action}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a>
</td>
<td class="heading">
<a class="tableheading" href="tiki-map_layer.php?action={$action}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'table_desc'}table_asc{else}table_desc{/if}">{tr}Table{/tr}</a>
</td>
<!-- the edit heading won't sort -->
<td class="heading">{tr}editer{/tr}</td>
</tr>
{section name=layer loop=$layers}
{if ($tiki_p_admin eq 'y') }
{if $smarty.section.layer.index % 2}
<tr>
<td class="odd">
<img src="generated/icons/Sigfreed/World/{$layers[layer].table}_class_0.png" />
</td>
<td class="odd">
{$layers[layer].name}
</td>
<td class="odd">{$layers[layer].author}</td>
<td class="odd">{$layers[layer].type} </td>
<td class="odd">{$layers[layer].table}</td>
<td class="odd"><a href="tiki-map_layer.php?action=edit&amp;layerId={$layers[layer].layerId}"><img src="img/icons/edit.gif" /></a>&nbsp;<a href="tiki-map_layer.php?action=delete&amp;layerId={$layers[layer].layerId}"><img src="img/icons2/delete.gif" /></a>&nbsp;</td>
</tr>
{else}
<tr>
<td class="even">
<img src="generated/icons/Sigfreed/World/{$layers[layer].table}_class_0.png" />
</td>
<td class="even">{$layers[layer].name}
</td>
<td class="even">{$layers[layer].author}</td>
<td class="even">{$layers[layer].type}</td>
<td class="even">{$layers[layer].table}</td>
<td class="even"><a href="tiki-map_layer.php?action=edit&amp;layerId={$layers[layer].layerId}"><img src="img/icons/edit.gif" /></a>&nbsp;<a href="tiki-map_layer.php?action=delete&amp;layerId={$layers[layer].layerId}"><img src="img/icons2/delete.gif" /></a>&nbsp;</td>
</tr>
{/if}
{/if}
{/section}
</table>
<!-- the next/ prev  -->
<br />
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-map_layer.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-map_layer.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-map_layer.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
{**end if list **}
{else if $smarty.get.action eq 'edit' || if $smarty.get.action eq 'add'} 
<div  align="center">
{if $msg }{$msg}{/if}
<form name="form" method="get" action="tiki-map_layer.php"><table class="findtable">
<tr>
<td class="findtable">{tr}Nom du layer{/tr}</td>
   <td class="findtable">
     <input type="text" name="namelayer" value="{$namelayer|escape}" />&nbsp;<input type="hidden" name="action" value="{$smarty.get.action}" /><input type="hidden" name="cat_restric_child" value="{$map.catId}" />


{tr}Map: {/tr}&nbsp;{$map.mapName|escape} 
      </td>
</tr>
<tr>
<td class="findtable">{tr}Type de layer{/tr}</td>
<td class="findtable">
	<input type="hidden" name="author" value="{$author|escape}" />
	<select name="type">
	<option value="POINT" {if $type eq 'POINT'} selected=selected{/if} >Point</option>
	<option value="LINE" {if $type eq 'LINE'} selected=selected{/if}>Ligne</option>
	<option value="POLYGON" {if $type eq 'POLYGON'} selected=selected{/if}>Polygone</option>
	</select>&nbsp; &nbsp;{tr}Connexion{/tr}:{$map.default_connexion_type}- {tr}host{/tr}: {$project.host} {tr}table{/tr}:{$tablename}
	</td>
</tr>
<tr><td class="findtable">     
	{include file=categorize.tpl}
</td>
</tr>
{if $submit eq "add"}
<tr>
<td class="findtable">{tr}Config layer{/tr}</td>
   <td class="findtable">
     <textarea  name="config" rows="20" cols="40">{$option_config|escape}</textarea>
    </td>
</tr>
{else }
<tr>
	<td colspan=2><input type="hidden" value="" /></td>
</tr>
{/if}
<tr>
<td class="findtable">{tr}Description{/tr}</td>
   <td class="findtable">
     <textarea  name="description" rows="5" cols="70">{$description|escape}</textarea>
    </td>
</tr>

<tr>
<td class="findtable">{tr}Copyrights{/tr}</td>
   <td class="findtable">
     <select name="copyright">
	<option value="DGL" {if $copyright eq 'DGL'} selected=selected{/if} >Public Geodata License</option>
	<option value="IGN" {if $copyright eq 'IGN'} selected=selected{/if}>IGN</option>
	</select>
{tr}Copyright url{/tr}&nbsp;<input type="text" name="copyrightUrl"  value="{$copyrightUrl|escape}tiki-index.php?page=PGL" />
      </td>
</tr>

<tr>
<td class="findtable">&nbsp;</td>
<td class="findtable">
      <input type="submit" value="{$smarty.get.action}" name="submit" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="layerId" value="{$layerId|escape}" />

</td>
</tr>
</table>
   </form>
</div>

{/if}
</div>
</div>
