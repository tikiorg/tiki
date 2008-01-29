{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_structures.tpl,v 1.51.2.7 2008-01-29 13:20:27 luciash Exp $ *}
<h1><a href="tiki-admin_structures.php" class="pagetitle">{tr}Structures{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Structures" target="tikihelp" class="tikihelp" title="{tr}Structures{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_structures.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin structures template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>
{/if}</h1>

{if $just_created neq 'n' && $tiki_p_edit_structures == 'y'}
<br />
{tr}The structure{/tr} <a class='tablename' href='tiki-edit_structure.php?page_ref_id={$just_created}'>{$just_created_name|escape}</a>&nbsp;&nbsp;<a class='link' href='tiki-index.php?page={$just_created_name|escape:"url"}' title="{tr}View{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16" alt="{tr}View{/tr}" /></a>&nbsp;&nbsp;{tr}has just been created.{/tr}
<br /><br />
{/if}

{if $askremove eq 'y'}
<div class="simplebox highlight">
{tr}You will remove structure{/tr}: {$removename|escape}<br />
<a class="link" href="tiki-admin_structures.php?rremove={$remove|escape:"url"}&amp;page={$removename|escape:"url"}">{tr}Destroy the structure leaving the wiki pages{/tr}</a>
{if $tiki_p_remove == 'y'}
<br /><a class="link" href="tiki-admin_structures.php?rremovex={$remove|escape:"url"}&amp;page={$removename|escape:"url"}">{tr}Destroy the structure and remove the pages{/tr}</a>
</div>
{/if}
{/if}

{if count($alert_in_st) > 0}
{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
{foreach from=$alert_in_st item=thest}
&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
{/foreach}
<br /><br />
{/if}

{if count($alert_categorized) > 0}
{tr}The following pages have automatically been categorized with the same categories as the structure:{/tr}
{foreach from=$alert_categorized item=thecat}
&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
{/foreach}
<br /><br />
{/if}

{if count($alert_to_remove_cats) > 0}
{tr}The following pages have categories but the structure has none. You may wish to uncategorize them to be consistent:{/tr}
{foreach from=$alert_to_remove_cats item=thecat}
&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
{/foreach}
<br /><br />
{/if}

{if count($alert_to_remove_extra_cats) > 0}
{tr}The following pages are in categories that the structure is not in. You may wish to recategorize them in order to be consistent:{/tr}
{foreach from=$alert_to_remove_extra_cats item=theextracat}
&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$theextracat|escape:"url"}' target="_blank">{$theextracat}</a>
{/foreach}
<br /><br />
{/if}

{include file="find.tpl"}

<h2>{tr}Structures{/tr}</h2>
<table class="normal">
<tr>
  <td class="heading">{tr}Structure ID{/tr}</td>
  <td  class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section loop=$channels name=ix}
<tr>
  <td class="{cycle advance=false}">
  <a class="tablename" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}" title="{tr}Edit structure{/tr}">
      {$channels[ix].pageName}
	  {if $channels[ix].page_alias}
        ({$channels[ix].page_alias})
	  {/if}	
  </a>
  </td>
  <td class="{cycle}">
<a class="tablename" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}" title="{tr}Edit structure{/tr}"><img src='pics/icons/page_edit.png' alt="{tr}Edit{/tr}" border='0' width='16' height='16' /></a>
<a class='link' href='tiki-index.php?page={$channels[ix].pageName|escape:"url"}&amp;structure={$channels[ix].pageName|escape:"url"}' title="{tr}View page{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16" alt="{tr}View{/tr}" /></a>
  {if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}<a title="{tr}Export Pages{/tr}" class="link" href="tiki-admin_structures.php?export={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/disk.png' alt="{tr}Export Pages{/tr}" border='0' width='16' height='16' /></a>{/if}
  {if $pdf_export eq 'y'}<a href="tiki-print_multi_pages.php?printstructures=a%3A1%3A%7Bi%3A0%3Bs%3A1%3A%22{$channels[ix].page_ref_id}%22%3B%7D&amp;display=pdf" title="{tr}PDF{/tr}"><img width="16" height="16" border="0" alt="{tr}PDF{/tr}" src="pics/icons/page_white_acrobat.png"/></a>{/if}
  {if $tiki_p_edit_structures == 'y'}<a title="{tr}Dump Tree{/tr}" class="link" href="tiki-admin_structures.php?export_tree={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/chart_organisation.png' alt="{tr}Dump Tree{/tr}" border='0' width='16' height='16' /></a>{/if}
  {if $tiki_p_edit_structures == 'y' and $channels[ix].editable == 'y'}<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_structures.php?remove={$channels[ix].page_ref_id|escape:"url"}"><img src='pics/icons/cross.png' alt="{tr}Remove{/tr}" border='0' width='16' height='16' /></a>{/if}
  {if $prefs.feature_create_webhelp == 'y' && $tiki_p_edit_structures == 'y'}<a title="{tr}Create WebHelp{/tr}" class="link" href="tiki-create_webhelp.php?struct={$channels[ix].page_ref_id|escape:"url"}"><img src="pics/icons/help.png" alt="{tr}Create WebHelp{/tr}" border='0' width='16' height='16' /></a>{/if}
  {if $prefs.feature_create_webhelp == 'y' && $channels[ix].webhelp eq 'y'} 
  <a title="{tr}View WebHelp{/tr}" class="link" href="whelp/{$channels[ix].pageName}/index.html"><img src="pics/icons/book_open.png" alt="{tr}View WebHelp{/tr}" border='0' width='16' height='16' /></a>
  {/if}&nbsp;
  </td>
</tr>
{/section}
</table>

<div style="text-align: center">
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>

{if $tiki_p_edit_structures == 'y'}
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
   <td class="formcolor">{tr}Tree{/tr}:<br />(optional)</td>
   <td colspan="3" class="formcolor"><textarea rows="5" cols="60" name="tree"></textarea></td>
</tr>    
{if $tiki_p_view_categories eq 'y'}
{include file=categorize.tpl}
{/if}
<tr>
   <td class="formcolor">&nbsp;</td>
   <td colspan="3" class="formcolor"><input type="submit" value="{tr}Create New Structure{/tr}" name="create" /></td>
</tr>
</table>
</form>
{/if}
