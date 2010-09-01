{title url="tiki-edit_structure.php?page_ref_id=$page_ref_id"}{if $editable == 'y'}{tr}Modify Structure{/tr}{else}{tr}Structure{/tr}{/if}: {$structure_name}{/title}

<div class="navbar">
	{button href="tiki-admin_structures.php" _text="{tr}Structures{/tr}"}
</div>

{if $remove eq 'y'}
	{tr}You will remove{/tr} '{$removePageName}' {if $page_removable == 'y'}{tr}and its subpages from the structure, now you have two options:{/tr}{else}{tr}and its subpages from the structure{/tr}{/if}
	<ul>
		<li>
			<a class="link" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;rremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}Remove only from structure{/tr}</a>
		</li>
		{if $page_removable == 'y'}
			<li>
				<a class="link" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;sremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}Remove from structure and remove page too{/tr}</a>
			</li>
		{/if}
	</ul>
	<br />
{/if}

{if $alert_exists eq 'y'}2
	<strong>{tr}The page already exists. The page that has been added to the structure is the existing one.{/tr}</strong>
	<br />
{/if}

{if count($alert_in_st) > 0}
	{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
	{foreach from=$alert_in_st item=thest}
		&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
	{/foreach}
	<br />
	<br />
{/if}

{if count($alert_categorized) > 0}
	{tr}The following pages added have automatically been categorized with the same categories as the structure:{/tr}
	{foreach from=$alert_categorized item=thecat}
		&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
	{/foreach}
	<br />
	<br />
{/if}

{if count($alert_to_remove_cats) > 0}
	{tr}The following pages have categories but the structure has none. You may wish to uncategorize them to be consistent:{/tr}
	{foreach from=$alert_to_remove_cats item=thecat}
		&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
	{/foreach}
	<br />
	<br />
{/if}

{if count($alert_to_remove_extra_cats) > 0}
	{tr}The following pages are in categories that the structure is not in. You may wish to recategorize them in order to be consistent:{/tr}
	{foreach from=$alert_to_remove_extra_cats item=theextracat}
		&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$theextracat|escape:"url"}' target="_blank">{$theextracat}</a>
	{/foreach}
	<br />
	<br />
{/if}

<h2>{tr}Structure Layout{/tr}</h2>

<table>
{section name=ix loop=$subtree}
{if $subtree[ix].first or not $subtree[ix].last}
<tr {if $page_ref_id eq $subtree[ix].page_ref_id}class="even"{else}class="odd"{/if}>
{if $subtree[ix].pos eq ''}
	<th><a class='link' href='{sefurl page=$subtree[ix].pageName structure=$structure_name page_ref_id=$subtree[ix].page_ref_id}' title="{tr}View{/tr}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
		{if $editable == 'y'}
		{if $subtree[ix].flag == 'L'}{icon _id='lock' alt="locked" title="locked by `$subtree[ix].user`"}
		{else}<a class="link" href='tiki-editpage.php?page={$subtree[ix].pageName|escape:"url"}'>{icon _id='page_edit'}</a>{/if}
		{/if}
		{if $tiki_p_watch_structure eq 'y'}
			{if !$subtree[ix].event}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=add&amp;page={$subtree[ix].pageName|escape:"url"}">{icon _id='eye_arrow_down' alt="{tr}Monitor the Sub-Structure{/tr}"}</a>
			{else}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=remove">{icon _id='no_eye_arrow_down' alt="{tr}Stop Monitoring the Sub-Structure{/tr}"}</a>
			{/if}
		{/if}
	</th>
	<th>
		<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}'><b>{$subtree[ix].pageName}{if $subtree[ix].page_alias} ({$subtree[ix].page_alias}){/if}</b></a>
	</th>
{else}
	<td {if $page_ref_id eq $subtree[ix].page_ref_id}style="border-style:dotted; border-width:1px; border-color:gray;"{/if}>
	<!--
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1'><img src="img/icons2/nav_dot_right.gif" hspace="3" height="11" width="8" title="{tr}Promote{/tr}" alt="{tr}Promote{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4'><img src="img/icons2/nav_dot_left.gif" hspace="3" height="11" width="8" title="{tr}Demote{/tr}" alt="{tr}Demote{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2'><img src="img/icons2/nav_home.gif" hspace="3" height="11" width="13" title="{tr}Previous{/tr}" alt="{tr}Previous{/tr}" /></a>
		<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3'><img src="img/icons2/nav_down.gif" hspace="3" height="11" width="13" title="{tr}Next{/tr}" alt="{tr}Next{/tr}" /></a>
	-->
		{if $editable == 'y'}<a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=1'>{icon _id='resultset_previous' alt="{tr}Promote{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=4'>{icon _id='resultset_next' alt="{tr}Demote{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=2'>{icon _id='resultset_up' alt="{tr}Previous{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;move_node=3'>{icon _id='resultset_down' style="margin-right:10px;" alt="{tr}Next{/tr}"}{/if}</a>
		{if $subtree[ix].viewable == 'y'}<a class='link' href='{sefurl page=$subtree[ix].pageName structure=$structure_name page_ref_id=$subtree[ix].page_ref_id}' title="{tr}View{/tr}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>{else}&nbsp;{/if}
		{if $subtree[ix].editable == 'y'}
		{if $subtree[ix].flag == 'L'}{icon _id='lock' alt="locked" title="locked by `$subtree[ix].user`"}
		{else}<a class="link" href='tiki-editpage.php?page={$subtree[ix].pageName|escape:"url"}'>{icon _id='page_edit'}</a>{/if}
		{/if}
		{if $tiki_p_watch_structure eq 'y'}
			{if !$subtree[ix].event}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=add&amp;page={$subtree[ix].pageName|escape:"url"}">{icon _id='eye_arrow_down' alt="{tr}Monitor the Sub-Structure{/tr}"}</a>
			{else}
				<a href="tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;watch_object={$subtree[ix].page_ref_id}&amp;watch_action=remove">{icon _id='no_eye_arrow_down' alt="{tr}Stop Monitoring the Sub-Structure{/tr}"}</a>
			{/if}
		{/if}
		{if $editable == 'y'}<a class='link' href='tiki-edit_structure.php?page_ref_id={$subtree[ix].page_ref_id}&amp;remove={$subtree[ix].page_ref_id}'>{icon _id='cross' style="margin-right:20px;" alt="{tr}Delete{/tr}"}</a>{/if}
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
<table class="formcolor">
  <tr>
  <td><label for="pageAlias">{tr}Page alias{/tr}:</label></td>
  <td>
  <input type="text" name="pageAlias" id="pageAlias" value="{$pageAlias}" />  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
  </tr>
  <tr>
  <td>{tr}Move in this structure{/tr}</td>
  <td>
  <a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=1'>{icon _id='resultset_previous' alt="{tr}Promote{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=4'>{icon _id='resultset_next' alt="{tr}Demote{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=2'>{icon _id='resultset_up' alt="{tr}Previous{/tr}"}</a><a href='tiki-edit_structure.php?page_ref_id={$page_ref_id}&amp;move_node=3'>{icon _id='resultset_down' style="margin-right:10px;" alt="{tr}Next{/tr}"}</a>
</td></tr>
<tr><td><label for="structure_id">{tr}Move to another structure{/tr}:</label></td>
<td>
<select name="structure_id" id="structure_id"{if $structures|@count eq '1'} disabled="disabled"{/if}>
{section name=ix loop=$structures}
{if $structures[ix].page_ref_id ne $page_ref_id}
<option value="{$structures[ix].page_ref_id}">{$structures[ix].pageName}</option>
{/if}
{if $structures|@count eq '1'}
<option value="">{tr}None{/tr}</option>
{/if}
{/section}
</select>
<label for="begin1">{tr}at the beginning{/tr}</label><input type="radio" id="begin1" name="begin" value="1" checked="checked"{if $structures|@count eq '1'} disabled="disabled"{/if} /> <label for="begin2">{tr}at the end{/tr}</label><input type="radio" id="begin2" name="begin" value="0"{if $structures|@count eq '1'} disabled="disabled"{/if} />
 <input type="submit" name="move_to" value="{tr}Move{/tr}"{if $structures|@count eq '1'} disabled="disabled"{/if} />
  </td>
  </tr>
</table>
<h3>{tr}Add pages to current node{/tr}:</h3>
<table class="formcolor">
  <tr>
  <td>
  <label for="after_ref_id">{tr}After page{/tr}:</label>
  </td>
  <td>
  <select name="after_ref_id" id="after_ref_id">
  {section name=ix loop=$subpages}
  <option value="{$subpages[ix].page_ref_id}" {if $insert_after eq $subpages[ix].page_ref_id}selected="selected"{/if}>{$subpages[ix].pageName}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td>
  <label for="name">{tr}Create Page{/tr}:</label>
  </td>
  <td>
  <input type="text" name="name" id="name" />
  </td>
  </tr>
  <tr>
  <td>
  <label for="name2">{tr}Use pre-existing page{/tr}:</label><br /><br />
        <input type="text" name="find_objects" id="find_objects" value="{$find_objects|escape}" />
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
  <td>
  <select id="name2" name="name2[]" multiple="multiple" size="8">
  {section name=list loop=$listpages}
  <option value="{$listpages[list].pageName|escape}">{$listpages[list].pageName|truncate:40:"(...)":true}</option>
  {/section}
  </select>
  </td>
  </tr>
  <tr>
  <td>&nbsp;</td>
  <td>
  <input type="submit" name="create" value="{tr}Update{/tr}" />
  </td>
</tr>
</table>
</form>
{if $prefs.feature_wiki_categorize_structure == 'y' && $all_editable == 'y'}
<form action="tiki-edit_structure.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />
<h3>{tr}Categorize all pages in structure together{/tr}:</h3>
<table class="normal">
{include file='categorize.tpl'}
</table>
<input type="submit" name="recategorize" value="{tr}Update{/tr}" />
&nbsp;&nbsp;{tr}Remove existing categories from ALL pages before recategorizing{/tr}: <input type="checkbox" name="cat_override" />
</form>
{/if}
<br />
{/if}{* end of if structure editable *}
