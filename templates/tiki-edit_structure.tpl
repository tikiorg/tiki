<h1><a class="pagetitle" href="tiki-edit_structure.php?page_ref_id={$page_ref_id}">
  {if $editable == 'y'}{tr}Modify Structure{/tr}{else}{tr}Structure{/tr}{/if}: {$structure_name}
</a></h1>
<div class="navbar"><a class="linkbut" href="tiki-admin_structures.php" title="{tr}Structures{/tr}">{tr}Structures{/tr}</a></div>
{if $remove eq 'y'}
{tr}You will remove{/tr} '{$removePageName}' {if $page_removable == 'y'}{tr}and its subpages from the structure, now you have two options:{/tr}{else}{tr}and its subpages from the structure{/tr}{/if}
<ul>
<li><a class="link" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;rremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}Remove only from structure{/tr}</a></li>
{if $page_removable == 'y'}<li><a class="link" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;sremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}Remove from structure and remove page too{/tr}</a></li>{/if}
</ul>
<br />
{/if}

{if $alert_exists eq 'y'}
<strong>{tr}The page already exists. The page that has been added to the structure is the existing one.{/tr}</strong>
<br />
{/if}

{if count($alert_in_st) > 0}
{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
{foreach from=$alert_in_st item=thest}
&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
{/foreach}
<br /><br />
{/if}

{if count($alert_categorized) > 0}
{tr}The following pages added have automatically been categorized with the same categories as the structure:{/tr}
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

<h2>{tr}Structure Layout{/tr}</h2>

<table>
{section name=ix loop=$subtree}
{if $subtree[ix].first or not $subtree[ix].last}
<tr {if $page_ref_id eq $subtree[ix].page_ref_id}class="even"{else}class="odd"{/if}>
{if $subtree[ix].pos eq ''}
	<td class="heading"><a class='link' href='tiki-index.php?page={$subtree[ix].pageName|escape:"url"}' title="{tr}View{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16" alt="{tr}View{/tr}" /></a>
		{if $editable == 'y'}
		{if $subtree[ix].flag == 'L'}<img src="pics/icons/lock.png" border="0" height="16" width="16" alt="locked" title="locked by {$subtree[ix].user}" />
		{else}<a class="link" href='tiki-editpage.php?page={$subtree[ix].pageName|escape:"url"}'><img border='0' title='{tr}Edit{/tr}' alt='{tr}Edit{/tr}' src='pics/icons/page_edit.png' height='16' width='16' /></a>{/if}
		{/if}
		{if $tiki_p_watch_structure eq 'y'}
			{if !$subtree[ix].event}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=add&amp;page={$subtree[ix].pageName|escape:"url"}">{html_image file='pics/icons/eye_arrow_down.png' border='0' alt='{tr}Monitor the Sub-Structure{/tr}' title='{tr}Monitor the Sub-Structure{/tr}'}</a>
			{else}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=remove">{html_image file='pics/icons/no_eye_arrow_down.png' border='0' alt='{tr}Stop Monitoring the Sub-Structure{/tr}' title='{tr}Stop Monitoring the Sub-Structure{/tr}'}</a>
			{/if}
		{/if}
	</td>
	<td class="heading">
		<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}'><b>{$subtree[ix].pageName}{if $subtree[ix].page_alias} ({$subtree[ix].page_alias}){/if}</b></a>
	</td>
{else}
	<td {if $page_ref_id eq $subtree[ix].page_ref_id}style="border-style:dotted; border-width:1px; border-color:gray;"{/if}>
	<!--
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1'><img src="img/icons2/nav_dot_right.gif" hspace="3" height="11" width="8" border="0" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4'><img src="img/icons2/nav_dot_left.gif" hspace="3" height="11" width="8" border="0" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2'><img src="img/icons2/nav_home.gif" hspace="3" height="11" width="13" border="0" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3'><img src="img/icons2/nav_down.gif" hspace="3" height="11" width="13" border="0" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" /></a>
	-->
		{if $editable == 'y'}<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1'><img src="pics/icons/resultset_previous.png" height="16" width="16" border="0" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4'><img src="pics/icons/resultset_next.png" height="16" width="16" border="0" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2'><img src="pics/icons/resultset_up.png" height="16" width="16" border="0" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3'><img src="pics/icons/resultset_down.png" height="16" width="16" border="0" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" style="margin-right:10px;"/>{/if}</a>
		{if $subtree[ix].viewable == 'y'}<a class='link' href='tiki-index.php?page={$subtree[ix].pageName|escape:"url"}&amp;structure={$structure_name|escape:"url"}' title="{tr}View{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16" alt="{tr}View{/tr}" /></a>{else}&nbsp;{/if}
		{if $subtree[ix].editable == 'y'}
		{if $subtree[ix].flag == 'L'}<img src="pics/icons/lock.png" border="0" height="16" width="16" alt="locked" title="locked by {$subtree[ix].user}" />
		{else}<a class="link" href='tiki-editpage.php?page={$subtree[ix].pageName|escape:"url"}'><img border='0' title='{tr}Edit{/tr}' alt='{tr}Edit{/tr}' src='pics/icons/page_edit.png' height='16' width='16' /></a>{/if}
		{/if}
		{if $tiki_p_watch_structure eq 'y'}
			{if !$subtree[ix].event}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=add&amp;page={$subtree[ix].pageName|escape:"url"}">{html_image file='pics/icons/eye_arrow_down.png' border='0' alt='{tr}Monitor the Sub-Structure{/tr}' title='{tr}Monitor the Sub-Structure{/tr}'}</a>
			{else}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=remove">{html_image file='pics/icons/no_eye_arrow_down.png' border='0' alt='{tr}Stop Monitoring the Sub-Structure{/tr}' title='{tr}Stop Monitoring the Sub-Structure{/tr}'}</a>
			{/if}
		{/if}
		{if $editable == 'y'}<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;remove={$subtree[ix].page_ref_id}'><img src='pics/icons/cross.png' border='0' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' width='16' height='16' style="margin-right:20px;"/></a>{/if}
	</td>
	<td {if $page_ref_id eq $subtree[ix].page_ref_id}style="border-style:dotted; border-width:1px; border-color:gray;"{/if}>
		{if $page_ref_id eq $subtree[ix].page_ref_id}<b>{/if}
		{$subtree[ix].pos} &nbsp; {if $editable == 'y'}<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}'>{/if}{$subtree[ix].pageName}{if $subtree[ix].page_alias} ({$subtree[ix].page_alias}){/if}{if $editable == 'y'}</a>{/if}
		{if $page_ref_id eq $subtree[ix].page_ref_id}</b>{/if}
	</td>
{/if}
</tr>
{/if}
{/section}
</table>

{if $editable == 'y'}
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />

<h2>{tr}Current Node{/tr}: {$pageName}</h2>
<table class="normal">
  <tr>
  <td class="formcolor">{tr}Page alias{/tr}</td>
  <td class="formcolor">
  <input type="text" name="pageAlias" value="{$pageAlias}" />  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">{tr}Move in this structure{/tr}</td>
  <td class="formcolor">
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=1'><img src="pics/icons/resultset_previous.png" height="16" width="16" border="0" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=4'><img src="pics/icons/resultset_next.png" height="16" width="16" border="0" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=2'><img src="pics/icons/resultset_up.png" height="16" width="16" border="0" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=3'><img src="pics/icons/resultset_down.png" height="16" width="16" border="0" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" style="margin-right:10px;"/></a>
</td></tr>
<tr><td class="formcolor">{tr}Move to another structure{/tr}</td>
<td class="formcolor">
<select name="structure_id">
{section name=ix loop=$structures}
{if $structures[ix].page_ref_id ne $page_ref_id}
<option value="{$structures[ix].page_ref_id}">{$structures[ix].pageName}</option>
{/if}
{/section}
</select>
{tr}at the beginning{/tr}<input type="radio" name="begin" value="1" checked="checked" /> {tr}at the end{/tr}<input type="radio" name="begin" value="0" />
 <input type="submit" name="move_to" value="{tr}Move{/tr}" />
  </td>
  </tr>
</table>
<h3>{tr}Add pages to current node{/tr}:</h3>
<table class="normal">
  <tr>
  <td class="formcolor">
  {tr}After page{/tr}
  </td>
  <td class="formcolor">
  <select name="after_ref_id">
  {section name=ix loop=$subpages}
  <option value="{$subpages[ix].page_ref_id}" {if $insert_after eq $subpages[ix].page_ref_id}selected="selected"{/if}>{$subpages[ix].pageName}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}Create Page{/tr}
  </td>
  <td class="formcolor">
  <input type="text" name="name" />
  </td>
  </tr>
  <tr>
  <td class="formcolor">
  {tr}Use pre-existing page{/tr}<br />
        <input type="text" name="find_objects" value="{$find_objects|escape}" />
        <input type="submit" value="{tr}Filter{/tr}" name="search_objects" />
        {if $prefs.feature_categories eq 'y'}	
		<select name="categId">
		<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
		{section name=ix loop=$categories}
			<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>{tr}{$categories[ix].categpath}{/tr}</option>
		{/section}
		</select>
		{/if}
  </td>
  <td class="formcolor">
  <select name="name2[]" multiple="multiple" size="8">
  {section name=list loop=$listpages}
  <option value="{$listpages[list].pageName|escape}">{$listpages[list].pageName|truncate:40:"(...)":true}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor">
  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
</tr>
</table>
</form>
{if $tiki_p_view_categories == 'y' && $prefs.feature_wiki_categorize_structure == 'y' && $all_editable == 'y'}
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />
<h3>{tr}Categorize all pages in structure together{/tr}:</h3>
<table class="normal">
{include file=categorize.tpl}
</table>
<input type="submit" name="recategorize" value="{tr}Update{/tr}" />
&nbsp;&nbsp;{tr}Remove existing categories from ALL pages before recategorizing{/tr}: <input type="checkbox" name="cat_override" />
</form>
{/if}
<br />
{/if}{* end of if structure editable *}
