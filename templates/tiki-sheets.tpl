{* $Id$ *}

{title help="Spreadsheet"}{tr}Spreadsheets{/tr}{/title}

{tabset}
	{tab name="{tr}List{/tr}"}
		<h2>{tr}Spreadsheets{/tr}</h2>
		{if $sheets or $find ne ''}
			{include file='find.tpl'}
		{/if}

		<div class="{if $prefs.javascript_enabled === 'y'}table-responsive{/if} sheet-table">
			<table class="table table-striped table-hover">
				<tr>
					<th>{self_link _sort_arg='sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last modified{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
					<th></th>
				</tr>

				{foreach item=sheet from=$sheets}
					{include name='base' file='tiki-sheets_listing.tpl' sheet=$sheet}
					{foreach item=childSheet from=$sheet.children}
						{include name='child' file='tiki-sheets_listing.tpl' sheet=$childSheet}
					{/foreach}
				{foreachelse}
					{norecords _colspan=6}
				{/foreach}
			</table>
		</div>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{if $tiki_p_edit_sheet eq 'y'}
		{capture name=title}{if $sheetId eq 0}{tr}Create{/tr}{else}{tr}Configure{/tr}{/if}{/capture}
		{tab name=$smarty.capture.title}
			{if $sheetId eq 0}
				<h2>{tr}Create sheet{/tr}</h2>
			{else}
				<h2>{tr}Configure sheet{/tr}: {$title|escape}</h2>
			{/if}
			{if $individual eq 'y'}
				{permission_link mode=link type=sheet id=$sheetId title=$name label="{tr}There are individual permissions set for this sheet{/tr}"}
			{/if}
			<form action="tiki-sheets.php" method="post" class="form-horizontal" role="form">
				<input type="hidden" name="sheetId" value="{$sheetId|escape}">
				<div class="form-group">
					<label for="title" class="control-label col-sm-3">
						{tr}Title{/tr}
					</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="title" value="{$title|escape}">
					</div>
				</div>
				<div class="form-group">				
					<label for="description" class="control-label col-sm-3">
						{tr}Description{/tr}
					</label>
					<div class="col-sm-9">
						<textarea rows="5" class="form-control" name="description">{$description|escape}</textarea>
					</div>
				</div>
				<!--<tr><td>{tr}Class Name:{/tr}</td><td><input type="text" name="className" value="{$className|escape}"></td></tr>
				<tr><td>{tr}Header Rows:{/tr}</td><td><input type="text" name="headerRow" value="{$headerRow|escape}"></td></tr>
				<tr><td>{tr}Footer Rows:{/tr}</td><td><input type="text" name="footerRow" value="{$footerRow|escape}"></td></tr>-->
				<div class="checkbox col-sm-push-3">
					<label for="parseValues">
						<input type="checkbox" name="parseValues"{if $parseValues eq 'y'} checked="checked"{/if}>
						{tr}Wiki Parse Values{/tr}
					</label>
				</div>
				{if $tiki_p_admin_sheet eq "y"}
					<div class="form-group">
						<label for="creator" class="control-label col-sm-3">
							{tr}Creator{/tr}
						</label>
						<div class="col-sm-9">
							{user_selector name="creator" editable=$tiki_p_admin_sheet user=$creator}
						</div>
					</div>
				{/if}
				<div class="form-group">
					<label for="parentSheetId" class="control-label col-sm-3">
						{tr}Parent Spreadsheet{/tr}
					</label>
					<div class="col-sm-9">
						<select name="parentSheetId" class="form-control">
							<option value="0">{tr}None{/tr}</option>
							{foreach item=sheet from=$sheets}
								<option value="{$sheet.sheetId}"{if $parentSheetId eq $sheet.sheetId} selected="selected"{/if}>
									{$sheet.title|escape} - ({$sheet.sheetId})
								</option>
							{/foreach}
						</select>
						<span class="help-block">
							{tr}Makes this sheet a "child" sheet of a multi-sheet set{/tr}
						</span>
					</div>
				</div>
				<div class="form-group">
					{include file='categorize.tpl'}
				</div>
				<div class="form-group text-center">
					<input type="submit" class="btn btn-primary btn-sm" value="{tr}Save{/tr}" name="edit">
				</div>
			</form>
			{if $sheetId > 0}
				<div class="wikitext col-sm-push-3 col-sm-9">
					{remarksbox type="tip" title="{tr}Tip{/tr}" close="n"}
						{tr}You can access the sheet using the following URL:{/tr} <a class="alert-link" href="{$url}?sheetId={$sheetId}">{$url}?sheetId={$sheetId}</a>
					{/remarksbox}
				</div>
			{/if}
		{/tab}
	{/if}
{/tabset}
