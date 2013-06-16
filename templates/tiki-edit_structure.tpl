{title url="tiki-edit_structure.php?page_ref_id=$page_ref_id"}{if $editable == 'y'}{tr}Modify Structure{/tr}{else}{tr}Structure{/tr}{/if}: {$structure_name}{/title}

<div class="navbar">
{button href="tiki-admin_structures.php" _text="{tr}Structures{/tr}"}
</div>

{if $remove eq 'y'}
	{tr}You will remove{/tr} '{$removePageName}' {if $page_removable == 'y'}{tr}and its subpages from the structure, now you have two options:{/tr}{else}{tr}and its subpages from the structure{/tr}{/if}
<ul>
	<li>
		<a class="link"
		   href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;rremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}
			Remove only from structure{/tr}</a>
	</li>
	{if $page_removable == 'y'}
		<li>
			<a class="link"
			   href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;sremove={$removepage}&amp;page={$removePageName|escape:"url"}">{tr}
				Remove from structure and remove page too{/tr}</a>
		</li>
	{/if}
</ul>
<br/>
{/if}

{if $alert_exists eq 'y'}
<strong>{tr}The page already exists. The page that has been added to the structure is the existing one.{/tr}</strong>
<br/>
{/if}

{if count($alert_in_st) > 0}
	{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
	{foreach from=$alert_in_st item=thest}
	&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
	{/foreach}
<br/>
<br/>
{/if}

{if count($alert_categorized) > 0}
	{tr}The following pages added have automatically been categorized with the same categories as the structure:{/tr}
	{foreach from=$alert_categorized item=thecat}
	&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
	{/foreach}
<br/>
<br/>
{/if}

{if count($alert_to_remove_cats) > 0}
	{tr}The following pages have categories but the structure has none. You may wish to uncategorize them to be consistent:{/tr}
	{foreach from=$alert_to_remove_cats item=thecat}
	&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
	{/foreach}
<br/>
<br/>
{/if}

{if count($alert_to_remove_extra_cats) > 0}
	{tr}The following pages are in categories that the structure is not in. You may wish to recategorize them in order to be consistent:{/tr}
	{foreach from=$alert_to_remove_extra_cats item=theextracat}
	&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$theextracat|escape:"url"}'
				   target="_blank">{$theextracat}</a>
	{/foreach}
<br/>
<br/>
{/if}

<h2>{tr}Structure Layout{/tr}</h2>
{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_structure" _ajax="n" _auto_args="save_structure,page_ref_id"}
{self_link page_ref_id=$structure_id}
	{if $structure_id eq $page_ref_id}<strong>{/if}
	<big>{tr}Top{/tr}</big>
	{if $structure_id eq $page_ref_id}</strong>
	{/if}
{/self_link}
<form action="tiki-edit_structure.php" method="post" style="display: inline-block; margin-left: 1em;">
	<input type="hidden" name="page_ref_id" value="{$structure_id}">
	<label for="pageAlias">{tr}Alias:{/tr}</label>
	<input type="text" name="pageAlias" id="pageAlias" value="{$topPageAlias}">
	<small><input type="submit" name="create" value="{tr}Update{/tr}"></small>
</form>
<div class="structure-container">
	{$nodelist}
</div>
{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_structure" _ajax="n" _auto_args="save_structure,page_ref_id"}

{if $editable == 'y'}
	<form action="tiki-edit_structure.php" method="post">
		<h3>{tr}Add pages{/tr}</h3>
		<input type="hidden" name="page_ref_id" value="{$page_ref_id}">

		<table class="formcolor">
			<tr>
				<td>
					<label for="page_list_container">{tr}Use pre-existing page by dragging into the structure above{/tr}</label>
					<ul id="page_list_container">
						{section name=list loop=$listpages}
							<li class="ui-state-default">
								{$listpages[list].pageName}
							</li>
						{/section}
					</ul>
					<label for="find_objects" style="display: inline-block;">{tr}Find:{/tr}</label>
					<input type="text" name="find_objects" id="find_objects" value="{$find_objects|escape}">
					<input type="submit" value="{tr}Filter{/tr}" name="search_objects">
					{autocomplete element='#find_objects' type='pagename'}

					{if $prefs.feature_categories eq 'y'}
						<select name="categId">
							<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
							{foreach $categories as $catix}
								<option value="{$catix.categId|escape}"
										{if !empty($find_categId) and $find_categId eq $catix.categId}selected="selected"{/if}>{tr}{$catix.categpath}{/tr}</option>
							{/foreach}
						</select>
					{/if}
				</td>
			</tr>
		</table>
	</form>
	{if $prefs.feature_wiki_categorize_structure == 'y' && $all_editable == 'y'}
		<form action="tiki-edit_structure.php" method="post">
			<input type="hidden" name="page_ref_id" value="{$page_ref_id}">

			<h3>{tr}Categorize all pages in structure together:{/tr}</h3>
			<table class="normal">
			{include file='categorize.tpl'}
			</table>
			<input type="submit" name="recategorize" value="{tr}Update{/tr}">
			&nbsp;&nbsp;{tr}Remove existing categories from ALL pages before recategorizing:{/tr} <input type="checkbox"
																										 name="cat_override">
		</form>
	{/if}
	<div id="move_dialog" style="display: none;">
		<form action="tiki-edit_structure.php" method="post">
			<input type="hidden" name="page_ref_id" value="{$page_ref_id}">
			<div class="clearfix" style="margin-bottom: 1em;">
				<label for="structure_id">{tr}Move to another structure:{/tr}</label>
				<select name="structure_id" id="structure_id"{if $structures|@count eq '1'} disabled="disabled"{/if}>
					{section name=ix loop=$structures}
						{if $structures[ix].page_ref_id ne $structure_id}
							<option value="{$structures[ix].page_ref_id}">{$structures[ix].pageName}</option>
						{/if}
						{if $structures|@count eq '1'}
							<option value="">{tr}None{/tr}</option>
						{/if}
					{/section}
				</select>
			</div>
			<label class="floatleft" for="begin1">{tr}at the beginning{/tr}</label>
			<div class="floatleft"><input type="radio" id="begin1" name="begin" value="1" checked="checked" {if $structures|@count eq '1'} disabled="disabled"{/if}></div>
			<label class="floatleft" for="begin2">{tr}at the end{/tr}</label>
			<div class="floatleft"><input type="radio" id="begin2" name="begin" value="0" {if $structures|@count eq '1'}disabled="disabled"{/if}></div>
			<hr>
			<div class="floatleft input_submit_container">
				<input type="submit" name="move_to" value="{tr}Move{/tr}" {if $structures|@count eq '1'} disabled="disabled"{/if}>
			</div>
		</form>
	</div>
	<div id="newpage_dialog" style="display: none;">
		<form action="tiki-edit_structure.php" method="post">
			<input type="hidden" name="page_ref_id" value="{$page_ref_id}">
			<table class="formcolor">
				<tr>
					<td>
						<label for="name">{tr}Create Page:{/tr}</label>
						<input type="text" name="name" id="name">
						{autocomplete element='#name' type='pagename'}
						<input type="submit" name="create" value="{tr}Update{/tr}">
					</td>
				</tr>
			</table>
		</form>
	</div>
{/if}{* end of if structure editable *}
