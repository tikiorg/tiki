{* $Id: *}
{extends 'layout_view.tpl'}
{block name="title"}
	{title admpage="i18n"}{$title|escape}{/title}
{/block}
{block name="navigation"}
	{if $tiki_p_edit_languages}
		<div class="t_navbar margin-bottom-md clearfix">
			<a class="btn btn-link tips" href="{service controller=language action=upload language={$language}}" title="{tr}Upload Translations{/tr}:{tr}Upload a file with translations for the selected language.{/tr}">
				{icon name="upload"} {tr}Upload Translations{/tr}
			</a>
			{if $prefs.lang_use_db eq "y"}
				{button _type="link" _class="tips" href="tiki-edit_languages.php" _icon_name="edit" _text="{tr}Edit languages{/tr}" _title="{tr}Edit languages{/tr}:{tr}Edit, export and import languages{/tr}"}
			{/if}
			{if $prefs.freetags_multilingual eq 'y'}
				{button _type="link" _class="tips" href="tiki-freetag_translate.php" _icon_name="tags" _text="{tr}Translate Tags{/tr}" _title=":{tr}Translate tags{/tr}"}
			{/if}
		</div>
	{/if}
{/block}
{block name="content"}
	<form action="{service controller=language action=manage_custom_translations}" method="post" role="form" class="form">
		<div class="form-group clearfix">
			<div class="col-md-6">
				<label class="control-label" for="custom_lang_select">
					{tr}Language{/tr}
				</label>
				<select name="language" id="custom_lang_select" class="form-control" onchange="this.form.submit()">
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"
							{if (empty($language) && $languages[ix].value eq $prefs.site_language) || (!empty($language) && $languages[ix].value eq $language)} selected="selected"{/if}>
							{$languages[ix].name|escape}
						</option>
					{/section}
				</select>
			</div>
			<div class="col-md-6 pull-right">
				{if $custom_file}
					{remarksbox type="info" title="{tr}Download{/tr}" close="n"}
						<a class="btn btn-link tips" href="{service controller=language action=download language={$language} file_type="custom_php"}" title="{tr}custom.php{/tr}:{tr}Download custom.php file for the selected language.{/tr}">
							{icon name="download"} {tr}custom.php{/tr}
						</a>
						<a class="btn btn-link tips" href="{service controller=language action=download language={$language} file_type="custom_json"}" title="{tr}custom.json{/tr}:{tr}Download custom.json file for the selected language.{/tr}">
							{icon name="download"} {tr}custom.json{/tr}
						</a>
					{/remarksbox}
				{else}
					{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
					{tr}Custom translation file does not exist. Save a custom translation to create the file.{/tr}
					{/remarksbox}
				{/if}
			</div>
		</div>
	</form>
	<form action="{service controller=language action=manage_custom_translations}" method="post" role="form" class="form">
		<h2>
			{tr}Translations{/tr} <span class="badge">{$custom_translation_item_count}</span>
		</h2>
		{if $custom_file}
			<span class="help-block">
				{icon name="file"} {$custom_file}
			</span>
		{/if}
		<div class="form-group">
			<div class="table-responsive">
				<table class="table" id="custom_translations_table">
					<thead>
					<tr>
						<th>
							{tr}Text{/tr}
						</th>
						<th>
							{tr}Translation text{/tr}
						</th>
					</tr>
					</thead>
					<tbody>
					{if !empty($custom_translations)}
						{foreach from=$custom_translations key=cfrom item=cto}
							<tr>
								<td>
									<input type="text" name="from[]" value="{$cfrom|escape}" class="form-control"/>
								</td>
								<td>
									<input type="text" name="to[]" value="{$cto|escape}" class="form-control"/>
								</td>
							</tr>
						{/foreach}
					{/if}
					<tr>
						<td>
							<input type="text" name="from[]" class="form-control" placeholder="{tr}Text{/tr}..."/>
						</td>
						<td>
							<input type="text" name="to[]" class="form-control" placeholder="{tr}Translation text{/tr}..."/>
						</td>
					</tr>
					</tbody>
				</table>
				<a id="add_row" href="javascript:void(0);" class="btn btn-default btn-block tips" title=":{tr}Add a new row{/tr}">
					{icon name="add"} {tr}Add row{/tr}
				</a>
			</div>
		</div>
		<div class="submit text-center">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="language" value={$language}>
			<input type="submit" class="btn btn-primary btn-sm" name="custom_save" value="{tr}Save{/tr}" />
		</div>
		{jq}
			$('#add_row').click(function() {
			$('#custom_translations_table tbody').append('<tr><td><input type="text" name="from[]" class="form-control" placeholder="{tr}Text{/tr}..."/></td><td><input type="text" name="to[]" class="form-control" placeholder="{tr}Translation text{/tr}..."/></td></tr>');
			});
		{/jq}
	</form>
{/block}