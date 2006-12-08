{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_structures.tpl,v 1.32 2006-12-08 21:44:43 ohertel Exp $ *}
<h1><a href="tiki-admin_structures.php" class="pagetitle">{tr}Structures{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="{$helpurl}Structures" target="tikihelp" class="tikihelp" title="{tr}Structures{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_structures.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin structures template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}edit{/tr}' /></a>
{/if}</h1>

<h2>{tr}Structures{/tr}</h2>
<table class="normal">
<tr>
  <td class="heading">{tr}Structure ID{/tr}</td>
  <td  class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section loop=$channels name=ix}
<tr>
  <td class="{cycle advance=false}">
  <a class="tablename" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}">
      {$channels[ix].pageName}
	  {if $channels[ix].page_alias}
        ({$channels[ix].page_alias})
	  {/if}
  </a>
  </td>
  <td class="{cycle}">
  <a title="{tr}export pages{/tr}" class="link" href="tiki-admin_structures.php?export={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/disk.png' alt="{tr}export pages{/tr}" border='0' width='16' height='16' /></a>
  <a title="{tr}dump tree{/tr}" class="link" href="tiki-admin_structures.php?export_tree={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/chart_organisation.png' alt="{tr}dump tree{/tr}" border='0' width='16' height='16' /></a>
  <a title="{tr}delete{/tr}" class="link" href="tiki-admin_structures.php?remove={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/cross.png' alt="{tr}remove{/tr}" border='0' width='16' height='16' /></a>
  <a title="{tr}create webhelp{/tr}" class="link" href="tiki-create_webhelp.php?struct={$channels[ix].page_ref_id|escape:"url"}"><img src="pics/icons/help.png" alt="{tr}create webhelp{/tr}" border='0' width='16' height='16' /></a>
  {if $channels[ix].webhelp eq 'y'} 
  <a title="{tr}view webhelp{/tr}" class="link" href="whelp/{$channels[ix].pageName}/index.html"><img src="pics/icons/book_open.png" alt="{tr}view webhelp{/tr}" border='0' width='16' height='16' /></a>
  {/if}
  </td>
</tr>
{/section}
</table>

<div class="mini">
      {if $prev_offset >= 0}
        [<a class="galprevnext" href="tiki-admin_structures.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp; 
      {/if}
      {tr}Page{/tr}: {$actual_page}/{$cant_pages}
      {if $next_offset >= 0}
      &nbsp;[<a class="galprevnext" href="tiki-admin_structures.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
      {/if}
      {if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_structures.php?offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
  </div>

{if $askremove eq 'y'}
<br />
<a class="link" href="tiki-admin_structures.php?rremove={$remove|escape:"url"}">{tr}Destroy the structure leaving the wiki pages{/tr}</a>
{if $tiki_p_remove == 'y'}
<br /><a class="link" href="tiki-admin_structures.php?rremovex={$remove|escape:"url"}">{tr}Destroy the structure and remove the pages{/tr}</a>
{/if}
{/if}

<h2>{tr}Create new structure{/tr}</h2>
<small>{tr}Use single spaces to indent structure levels{/tr}</small>
<form action="tiki-admin_structures.php" method="post">
<table class="normal">
<tr>
   <td class="formcolor">{tr}Structure ID{/tr}:</td>
   <td class="formcolor"><input type="text" name="name" /></td>
   <td class="formcolor">{tr}Alias{/tr}:</td>
   <td class="formcolor"><input type="text" name="alias" /></td>
</tr>    
<tr>
   <td class="formcolor">{tr}tree{/tr}:<br />(optional)</td>
   <td colspan=3 class="formcolor"><textarea rows="5" cols="60" name="tree"></textarea></td>
</tr>    
<tr>
   <td class="formcolor">&nbsp;</td>
   <td colspan=3 class="formcolor"><input type="submit" value="{tr}create new structure{/tr}" name="create" /></td>
</tr>
</table>
</form>
