{title url="tiki-edit_structure.php?page_ref_id=$page_ref_id"}{tr}Structure{/tr}: {$structure_name}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-admin_structures.php" _text="{tr}Structures{/tr}"}
</div>

{if $remove eq 'y'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}You will remove{/tr} '{$removePageName}' {if $page_removable == 'y'}{tr}and its subpages from the structure, now you have two options:{/tr}{else}{tr}and its subpages from the structure{/tr}{/if}
		<div class="text-center">
			<a class="btn btn-warning btn-sm" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;rremove={$removepage}&amp;page={$removePageName|escape:"url"}">{icon name="remove"} {tr}Remove from structure{/tr}</a>
			{if $page_removable == 'y'}
				<a class="btn btn-warning btn-sm" href="tiki-edit_structure.php?page_ref_id={$structure_id}&amp;sremove={$removepage}&amp;page={$removePageName|escape:"url"}">{icon name="delete"} {tr}Remove from structure and remove page too{/tr}</a>
			{/if}
		</div>
	{/remarksbox}
{/if}

{if $alert_exists eq 'y'}
	<strong>{tr}The page already exists. The page that has been added to the structure is the existing one.{/tr}</strong>
	<br/>
{/if}

{if count($alert_in_st) > 0}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
	{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
		{foreach from=$alert_in_st item=thest}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
		{/foreach}
	{/remarksbox}
{/if}

{if count($alert_categorized) > 0}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}The following pages added have automatically been categorized with the same categories as the structure:{/tr}
		{foreach from=$alert_categorized item=thecat}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
		{/foreach}
	{/remarksbox}
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
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}The following pages are in categories that the structure is not in. You may wish to recategorize them in order to be consistent:{/tr}
		{foreach from=$alert_to_remove_extra_cats item=theextracat}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$theextracat|escape:"url"}' target="_blank">{$theextracat}</a>
		{/foreach}
	{/remarksbox}
{/if}

<div>
	<h2>{tr}Structure Layout{/tr}</h2>
	{if $editable eq 'y'}
		<form action="tiki-edit_structure.php?page_ref_id={$page_ref_id}" method="post" class="form-inline" role="form" style="display: inline-block">
			<div class="form-group">
				<label for="pageAlias" class="control-label">{tr}Alias{/tr}:</label>
				<input type="hidden" name="page_ref_id" value="{$structure_id}">
				<div class="input-group">
					<input type="text" class="form-control input-sm" name="pageAlias" id="pageAlias" value="{$topPageAlias}">
					<div class="input-group-btn">
						<input type="submit" class="btn btn-primary btn-sm" name="create" value="{tr}Update{/tr}">
					</div>
				</div>
			</div>
		</form>
		{if $prefs.lock_wiki_structures eq 'y'}
			{lock type='wiki structure' object=$page_ref_id}
		{/if}
	{/if}
</div>
<div>
	{self_link page_ref_id=$structure_id}
		{if $structure_id eq $page_ref_id}<strong>{/if}
		<span class="lead">{tr}Top{/tr}</span>
		{if $structure_id eq $page_ref_id}</strong>{/if}
	{/self_link}
</div>
{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_structure" _type="primary" _ajax="n" _auto_args="save_structure,page_ref_id"}
<div class="structure-container">
	{$nodelist}
</div>
{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_structure" _type="primary" _ajax="n" _auto_args="save_structure,page_ref_id"}

{if $editable == 'y'}
	<form action="tiki-edit_structure.php" method="post" class="form-inline" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>{tr}Add pages{/tr}</strong> <small>{tr}Use an existing page by dragging it into the structure above{/tr}</small>
			</div>
			<div class="panel-body">
				<div>
					<input type="hidden" name="page_ref_id" value="{$page_ref_id}">
					<div class="form-group">
						<label class="sr-only" for="find_objects">{tr}Find{/tr}</label>
						<div class="input-group">
							<input type="text" name="find_objects" id="find_objects" value="{$find_objects|escape}" class="form-control input-sm" placeholder="{tr}Find{/tr}...">
							<div class="input-group-btn">
								<input type="submit" class="btn btn-default btn-sm" value="{tr}Filter{/tr}" name="search_objects">
							</div>
							{autocomplete element='#find_objects' type='pagename'}
						</div>
					</div>
					{if $prefs.feature_categories eq 'y'}
						<div class="form-group">
							<select name="categId" class="form-control input-sm">
								<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
								{foreach $categories as $catix}
									<option value="{$catix.categId|escape}" {if !empty($find_categId) and $find_categId eq $catix.categId}selected="selected"{/if}>{tr}{$catix.categpath}{/tr}</option>
								{/foreach}
							</select>
						</div>
					{/if}
				</div>
				<ul id="page_list_container">
					{section name=list loop=$listpages}
						<li class="ui-state-default">
							{$listpages[list].pageName}
						</li>
					{/section}
				</ul>
			</div>
		</div>
	</form>
	{if $prefs.feature_categories eq 'y' && $prefs.feature_wiki_categorize_structure == 'y' && $all_editable == 'y'}
		<form action="tiki-edit_structure.php" method="post">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong>{tr}Categorize all pages in structure together{/tr}</strong>
				</div>
				<div class="panel-body">
					<input type="hidden" name="page_ref_id" value="{$page_ref_id}">
					{include file='categorize.tpl'}
				</div>
				<div class="panel-footer text-center">
					<input type="submit" class="btn btn-primary btn-sm" name="recategorize" value="{tr}Update{/tr}">
					<input type="checkbox" name="cat_override" >{tr}Remove existing categories from ALL pages before recategorizing{/tr}
				</div>
			</div>
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
			<label class="pull-left" for="begin1">{tr}at the beginning{/tr}</label>
			<div class="pull-left"><input type="radio" id="begin1" name="begin" value="1" checked="checked" {if $structures|@count eq '1'} disabled="disabled"{/if}></div>
			<label class="pull-left" for="begin2">{tr}at the end{/tr}</label>
			<div class="pull-left"><input type="radio" id="begin2" name="begin" value="0" {if $structures|@count eq '1'}disabled="disabled"{/if}></div>
			<hr>
			<div class="pull-left input_submit_container">
				<input type="submit" class="btn btn-primary btn-sm" name="move_to" value="{tr}Move{/tr}" {if $structures|@count eq '1'} disabled="disabled"{/if}>
			</div>
		</form>
	</div>
	<div id="newpage_dialog" style="display: none;">
		<form action="tiki-edit_structure.php" method="post" class="form-horizontal">
			<input type="hidden" name="page_ref_id" value="{$page_ref_id}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Create Page{/tr}</label>
				<div class="col-sm-7">
		      		<input type="text" name="name" id="name" class="form-control">
					{autocomplete element='#name' type='pagename'}
	      		</div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7">
		      		<input type="submit" class="btn btn-primary btn-sm" name="create" value="{tr}Update{/tr}">
	      		</div>
		    </div>
		</form>
	</div>
{/if}{* end of if structure editable *}
