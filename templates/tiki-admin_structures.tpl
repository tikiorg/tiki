{* $Id$ *}
{title help="Structures"}{tr}Structures{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	<div class="t_navbar">
		{button href='tiki-import_xml_zip.php' _text="{tr}XML Zip Import{/tr}"}
	</div>
{/if}

{if $just_created neq 'n' && $tiki_p_edit_structures == 'y'}
	{remarksbox type='feedback' title="{tr}Feedback{/tr}"}
		{tr}The structure{/tr} <a class='tablename' href='tiki-edit_structure.php?page_ref_id={$just_created}'>{$just_created_name|escape}</a>&nbsp;&nbsp;<a class='link' href='tiki-index.php?page={$just_created_name|escape:"url"}' title="{tr}View{/tr}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>&nbsp;&nbsp;{tr}has just been created.{/tr}
	{/remarksbox}
{/if}

{if $askremove eq 'y'}
	{remarksbox type='confirm' title="{tr}Please Confirm{/tr}"}
		{tr}You will remove structure:{/tr} {$removename|escape}<br>
		{button href="?rremove=$remove&amp;page=$removename" _text="{tr}Destroy the structure leaving the wiki pages{/tr}"}
		{if $tiki_p_remove == 'y'}
			{button href="?rremovex=$remove&amp;page=$removename" _text="{tr}Destroy the structure and remove the pages{/tr}"}
		{/if}
	{/remarksbox}
{/if}

{if count($alert_in_st) > 0}
	{remarksbox type='warning' title="{tr}Warning{/tr}"}
		{tr}Note that the following pages are also part of another structure. Make sure that access permissions (if any) do not conflict:{/tr}
		{foreach from=$alert_in_st item=thest}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thest|escape:"url"}' target="_blank">{$thest}</a>
		{/foreach}
	{/remarksbox}
{/if}

{if count($alert_categorized) > 0}
	{remarksbox type='feedback' title="{tr}Feedback{/tr}"}
		{tr}The following pages have automatically been categorized with the same categories as the structure:{/tr}
		{foreach from=$alert_categorized item=thecat}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
		{/foreach}
	{/remarksbox}
{/if}

{if count($alert_to_remove_cats) > 0}
	{remarksbox type='warning' title="{tr}Warning{/tr}"}
		{tr}The following pages have categories but the structure has none. You may wish to uncategorize them to be consistent:{/tr}
		{foreach from=$alert_to_remove_cats item=thecat}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$thecat|escape:"url"}' target="_blank">{$thecat}</a>
		{/foreach}
	{/remarksbox}
{/if}

{if count($alert_to_remove_extra_cats) > 0}
	{remarksbox type='warning' title="{tr}Warning{/tr}"}
		{tr}The following pages are in categories that the structure is not in. You may wish to recategorize them in order to be consistent:{/tr}
		{foreach from=$alert_to_remove_extra_cats item=theextracat}
			&nbsp;&nbsp;<a class='tablename' href='tiki-index.php?page={$theextracat|escape:"url"}' target="_blank">{$theextracat}</a>
		{/foreach}
	{/remarksbox}
{/if}

{if !empty($error)}
	{remarksbox type='warning' title="{tr}Error{/tr}"}
		{$error|escape}
	{/remarksbox}
{/if}

{tabset}
	{tab name="{tr}Structures{/tr}"}
		<h2>{tr}Structures{/tr}</h2>
		{if $channels or ($find ne '')}
			{include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}
		{/if}
		<br>
		<form>
			<div class="table-responsive">
				<table class="table normal">
					<tr>
						{if $tiki_p_admin eq 'y'}<th width="15">{select_all checkbox_names='action[]'}</th>{/if}
						<th>{tr}Structure ID{/tr}</th>
						<th>{tr}Action{/tr}</th>
					</tr>

					{section loop=$channels name=ix}
						<tr>
							{if $tiki_p_admin eq 'y'}
								<td class="checkbox-cell">
									<input type="checkbox" name="action[]" value='{$channels[ix].page_ref_id}' style="border:1px;font-size:80%;">
								</td>
							{/if}
							<td class="text">
								<a class="tablename" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}" title="{tr}Edit structure{/tr}">
									{$channels[ix].pageName}
									{if $channels[ix].page_alias}
										({$channels[ix].page_alias})
									{/if}
								</a>
							</td>
							<td class="action">
								<a class="tips" href="tiki-edit_structure.php?page_ref_id={$channels[ix].page_ref_id}" title=":{tr}View structure{/tr}">{icon name="information"}</a>
								<a class='tips' href='{sefurl page=$channels[ix].pageName structure=$channels[ix].pageName page_ref_id=$channels[ix].page_ref_id}' title=":{tr}View page{/tr}">{icon name="view"}</a>

								{if $prefs.feature_wiki_export eq 'y' and $tiki_p_admin_wiki eq 'y'}
									<a title=":{tr}Export Pages{/tr}" class="tips" href="tiki-admin_structures.php?export={$channels[ix].page_ref_id|escape:"url"}">{icon name="export"}</a>
								{/if}

								{if $pdf_export eq 'y'}<a href="tiki-print_multi_pages.php?printstructures=a%3A1%3A%7Bi%3A0%3Bs%3A1%3A%22{$channels[ix].page_ref_id}%22%3B%7D&amp;display=pdf" title="{tr}PDF{/tr}">{icon _id='page_white_acrobat' alt="{tr}PDF{/tr}"}</a>
								{/if}

								{if $tiki_p_edit_structures == 'y'}<a title=":{tr}Dump Tree{/tr}" class="tips" href="tiki-admin_structures.php?export_tree={$channels[ix].page_ref_id|escape:"url"}">{icon name="structure"}</a>{/if}

								{if $tiki_p_edit_structures == 'y' and $channels[ix].editable == 'y'}<a title=":{tr}Delete{/tr}" class="tips" href="tiki-admin_structures.php?remove={$channels[ix].page_ref_id|escape:"url"}">{icon name="delete"}</a>{/if}

								{if $prefs.feature_create_webhelp == 'y' && $tiki_p_edit_structures == 'y'}<a title=":{tr}Create WebHelp{/tr}" class="tips" href="tiki-create_webhelp.php?struct={$channels[ix].page_ref_id|escape:"url"}">{icon name="help"}</a>{/if}

								{if $prefs.feature_create_webhelp == 'y' && $channels[ix].webhelp eq 'y'}
									<a title=":{tr}View WebHelp{/tr}" class="tips" href="whelp/{$channels[ix].pageName}/index.html">{icon name="documentation"}</a>
								{/if}

								{if $tiki_p_admin eq 'y'}
									<a title=":{tr}XML Zip{/tr}" class="tips" href="tiki-admin_structures.php?zip={$channels[ix].page_ref_id|escape:"url"}">{icon name="zip"}</a>
								{/if}
							</td>
						</tr>
					{sectionelse}
						{if $tiki_p_admin eq 'y'}{norecords _colspan=3}{else}{norecords _colspan=2}{/if}
					{/section}
				</table>
			</div>

			{if $tiki_p_admin eq 'y'}
				<div style="text-align:left">
					{tr}Perform action with checked:{/tr}
					<select name="batchaction">
						<option value="">{tr}...{/tr}</option>
						<option value="delete">{tr}Delete{/tr}</option>
						<option value="delete_with_page">{tr}Delete with the pages{/tr}</option>
					</select>
					<input type="submit" class="btn btn-default btn-sm" name="act" value="{tr}OK{/tr}">
				</form>
			</div>
		{/if}

		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{if $tiki_p_edit_structures == 'y'}
		{tab name="{tr}Create New Structure{/tr}"}
			<h2>{tr}Create New Structure{/tr}</h2>
			<form class="form-horizontal" action="tiki-admin_structures.php" method="post">
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Structure ID{/tr}</label>
					<div class="col-md-9">
						<input type="text" name="name" id="name" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Alias{/tr}</label>
					<div class="col-md-9">
						<input type="text" name="alias" id="alias" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">{tr}Tree{/tr}</label>
					<div class="col-md-9">
						<textarea rows="5" cols="60" id="tree" name="tree" class="form-control"></textarea>
						<div class="help-block">{tr}Use single spaces to indent structure levels{/tr}</div>
					</div>
				</div>
				{include file='categorize.tpl'}
				<div class="form-group">
					<div class="submit col-md-9 col-md-push-3">
						<input type="submit" class="btn btn-primary" value="{tr}Create New Structure{/tr}" name="create">
					</div>
				</div>
			</form>
		{/tab}
	{/if}
{/tabset}
