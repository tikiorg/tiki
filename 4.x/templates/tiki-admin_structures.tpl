{* $Id$ *}
{title help="Structures"}{tr}Structures{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	{button href='tiki-import_xml_zip.php' _text='{tr}XML Zip Import{/tr}'}
{/if}

{if $just_created neq 'n' && $tiki_p_edit_structures == 'y'}
<br />
{tr}The structure{/tr} <a class='tablename' href='tiki-edit_structure.php?page_ref_id={$just_created}'>{$just_created_name|escape}</a>&nbsp;&nbsp;<a class='link' href='tiki-index.php?page={$just_created_name|escape:"url"}' title="{tr}View{/tr}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>&nbsp;&nbsp;{tr}has just been created.{/tr}
<br /><br />
{/if}

{if $askremove eq 'y'}
<div class="simplebox highlight">
{tr}You will remove structure{/tr}: {$removename|escape}<br />
{button href="?rremove=$remove&amp;page=$removename" _text='{tr}Destroy the structure leaving the wiki pages{/tr}'}
{if $tiki_p_remove == 'y'}
{button href="?rremovex=$remove&amp;page=$removename" _text='{tr}Destroy the structure and remove the pages{/tr}'}
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

{tabset}
{tab name="{tr}Structures{/tr}"}
{if $channels or ($find ne '')}
  {include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y' }
{/if}
<br />
<table class="normal">
<tr>
  <th>{tr}Structure ID{/tr}</th>
  <th>{tr}Action{/tr}</th>
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
<a class="tablename" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}" title="{tr}View structure{/tr}">{icon _id='information' alt="{tr}View structure{/tr}"}</a>
<a class='link' href='{sefurl page=$channels[ix].pageName structure=$channels[ix].pageName page_ref_id=$channels[ix].page_ref_id}' title="{tr}View page{/tr}">{icon _id='magnifier' alt="{tr}View page{/tr}"}</a>
  {if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}<a title="{tr}Export Pages{/tr}" class="link" href="tiki-admin_structures.php?export={$channels[ix].page_ref_id|escape:"url"}">{icon _id='disk' alt="{tr}Export Pages{/tr}"}</a>{/if}
  {if $pdf_export eq 'y'}<a href="tiki-print_multi_pages.php?printstructures=a%3A1%3A%7Bi%3A0%3Bs%3A1%3A%22{$channels[ix].page_ref_id}%22%3B%7D&amp;display=pdf" title="{tr}PDF{/tr}">{icon _id='page_white_acrobat' alt="{tr}PDF{/tr}"}</a>{/if}
  {if $tiki_p_edit_structures == 'y'}<a title="{tr}Dump Tree{/tr}" class="link" href="tiki-admin_structures.php?export_tree={$channels[ix].page_ref_id|escape:"url"}">{icon _id='chart_organisation' alt="{tr}Dump Tree{/tr}"}</a>{/if}
  {if $tiki_p_edit_structures == 'y' and $channels[ix].editable == 'y'}<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_structures.php?remove={$channels[ix].page_ref_id|escape:"url"}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>{/if}
  {if $prefs.feature_create_webhelp == 'y' && $tiki_p_edit_structures == 'y'}<a title="{tr}Create WebHelp{/tr}" class="link" href="tiki-create_webhelp.php?struct={$channels[ix].page_ref_id|escape:"url"}">{icon _id='help' alt="{tr}Create WebHelp{/tr}"}</a>{/if}
  {if $prefs.feature_create_webhelp == 'y' && $channels[ix].webhelp eq 'y'} 
  <a title="{tr}View WebHelp{/tr}" class="link" href="whelp/{$channels[ix].pageName}/index.html">{icon _id='book_open' alt="{tr}View WebHelp{/tr}"}</a>
  {/if}
  {if $tiki_p_admin eq 'y'}
    <a title="{tr}XML Zip{/tr}" class="link" href="tiki-admin_structures.php?zip={$channels[ix].page_ref_id|escape:"url"}">{icon _id='pics/icons/mime/zip.png' alt="{tr}XML Zip{/tr}"}</a>
  {/if}
  </td>
</tr>
{sectionelse}
<tr>
  <td colspan="2" class="odd">{tr}No records found.{/tr}<td>
</tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $tiki_p_edit_structures == 'y'}
{tab name="{tr}Create new structure{/tr}"}
<form action="tiki-admin_structures.php" method="post">
<table class="normal">
<tr>
   <td class="formcolor"><label for="name">{tr}Structure ID{/tr}:</label></td>
   <td class="formcolor"><input type="text" name="name" id="name" /></td>
</tr>
<tr>
   <td class="formcolor"><label for="alias">{tr}Alias{/tr}:</label></td>
   <td class="formcolor"><input type="text" name="alias" id="alias" /></td>
</tr>    
<tr>
   <td class="formcolor"><label for="tree">{tr}Tree{/tr}:</label><br />(optional)</td>
   <td colspan="2" class="formcolor"><textarea rows="5" cols="60" id="tree" name="tree" style="width:95%"></textarea>
		{remarksbox type="tip" title="{tr}Note{/tr}"}{tr}Use single spaces to indent structure levels{/tr}{/remarksbox}
   </td>
</tr>    
{include file='categorize.tpl'}
<tr>
   <td class="formcolor">&nbsp;</td>
   <td colspan="2" class="formcolor"><input type="submit" value="{tr}Create New Structure{/tr}" name="create" /></td>
</tr>
</table>
</form>
{/tab}
{/if}
{/tabset}
