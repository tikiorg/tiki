<h1><a class="pagetitle" href="tiki-list_maps.php">{tr}Maps{/tr}</a>
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}Maps" target="tikihelp" class="tikihelp" title="{tr}admin Maps{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_maps.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Maps tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>

<a class="linkbut" href="tiki-edit_map.php">{tr}Create map{/tr}</a>
<br /><br />
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form path="get" action="tiki-list_maps.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'mapId_desc'}mapId_asc{else}mapId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'author_desc'}author_asc{else}author_desc{/if}">{tr}Author{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'path_desc'}path_asc{else}path_desc{/if}">{tr}path{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'copyright_desc'}copyright_asc{else}copyright_desc{/if}">{tr}Copyright{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'gateway_desc'}gateway_asc{else}gateway_desc{/if}">{tr}Gateway{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'db_desc'}db_asc{else}db_desc{/if}">{tr}Database{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</a></td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].mapId}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].name}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].author}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].type}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].path}&nbsp;</td>
<td class="odd">&nbsp;<a class="link" href="{$listpages[changes].copyrightUrl}" >{$listpages[changes].copyright}</a>&nbsp;</td>
<td class="odd">&nbsp;<a class="link" href="tiki-index.php?page={$listpages[changes].gateway}">{$listpages[changes].gateway}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].db}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="odd">
{if $tiki_p_map_edit eq 'y'}
<a class="link" href="tiki-edit_map.php?mapId={$listpages[changes].mapId}"><img src="img/icons/edit.gif" halt="{tr}Edit{/tr}" /></a><a class="link" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].mapId}"><img src="img/icons2/delete.gif" halt="{tr}Remove{/tr}" /></a><a class="link" href="tiki-list_layers.php?mapId={$listpages[changes].mapId}"><img src="img/icons/config.gif" halt="{tr}See Layer{/tr}" /></a><a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].name}&objectType=maps&permType=maps&objectId={$listpages[changes].mapId}"><img src="img/icons/key.gif" halt="{tr}Set Permissions{/tr}" /></a>
{/if}
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].mapId}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].name}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].author}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].type}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].path}&nbsp;</td>
<td class="even">&nbsp;<a class="link" href="{$listpages[changes].copyrightUrl}" >{$listpages[changes].copyright}</a>&nbsp;</td>
<td class="even">&nbsp;<a class="link" href="tiki-index.php?page={$listpages[changes].gateway}">{$listpages[changes].gateway}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].db}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="even">
{if $tiki_p_map_edit eq 'y'}
<a class="link" href="tiki-edit_map.php?mapId={$listpages[changes].mapId}"><img src="img/icons/edit.gif" halt="{tr}Edit{/tr}" /></a><a class="link" href="tiki-list_maps.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].mapId}"><img src="img/icons2/delete.gif" halt="{tr}Remove{/tr}" /></a><a class="link" href="tiki-list_layers.php?mapId={$listpages[changes].mapId}"><img src="img/icons/config.gif" halt="{tr}See Layer{/tr}" /></a><a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].name}&objectType=maps&permType=maps&objectId={$listpages[changes].mapId}"><img src="img/icons/key.gif" halt="{tr}Set Permissions{/tr}" /></a>
{/if}
</td>
{/if}
</tr>
{sectionelse}
<tr><td class="odd" colspan="10">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_maps.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_maps.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-list_maps.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>
