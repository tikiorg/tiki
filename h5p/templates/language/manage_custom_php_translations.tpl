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
	<form action="{service controller=language action=manage_custom_php_translations}" method="post" role="form" class="form">
		{if !empty($custom_error)}
			{remarksbox title="{tr}Error{/tr}" type="error"}
				{if $custom_error eq 'param'}
					{tr}Incorrect param{/tr}
				{elseif $custom_error eq 'parse'}
					{tr}Syntax error{/tr}
				{else}
					{tr}Cannot open/write this file:{/tr} {$custom_file}. {tr}Custom translation will not be saved. Ask your administration to change the permission.{/tr}
				{/if}
			{/remarksbox}
		{/if}
		{if !empty($custom_ok)}
			{remarksbox title="{tr}ok{/tr}"}
				{tr}The file has been saved{/tr}
			{/remarksbox}
		{/if}
		<div class="form-group clearfix">
			<div class="col-md-6">
				<label class="control-label" for="custom_lang_select">
					{tr}Language{/tr}
				</label>
				<div class="input-group">
					<input type="text" name="language_name" class="form-control" value="{$language_name}">
					<div class="input-group-btn">
						<a class="btn btn-default tips" href="{service controller=language action=select_language language={$language}}">
							{tr}Select{/tr}
						</a>
					</div>
				</div>
			</div>
			<div class="col-md-6 pull-right">
				{remarksbox type="info" title="{tr}File{/tr}" close="n"}
					{if $custom_file}
						{$custom_file}
						<a class="btn btn-link tips" href="{service controller=language action=download language={$language} file_type="custom_php"}" title="{tr}Download{/tr}:{tr}Download custom.php file for the selected language.{/tr}">
						{icon name="download"} {tr}Download{/tr}
						</a>
					{else}
						{tr}Custom translation file does not exist. Save a translation to create the file.{/tr}
					{/if}
				{/remarksbox}
			</div>
		</div>
		<h2>
			{tr}Translations{/tr} <span class="badge">{$custom_translation_item_count}</span>
		</h2>
		<div class="form-group">
			<div class="table-responsive">
				<table class="table" id="custom_translations_table">
					<thead>
						<tr>
							<th>
								{tr}English{/tr}
							</th>
							<th>
								{tr}Translation{/tr}
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
								<input type="text" name="from[]" class="form-control" placeholder="{tr}English{/tr}..."/>
							</td>
							<td>
								<input type="text" name="to[]" class="form-control" placeholder="{tr}Translation{/tr}..."/>
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
			<input type="hidden" name="language" value="{$language}">
			<input type="submit" class="btn btn-primary btn-sm" name="custom_save" value="{tr}Save{/tr}" />
		</div>
	</form>
	{jq}
		$('#add_row').click(function() {
		   $('#custom_translations_table tbody').append('<tr><td><input type="text" name="from[]" class="form-control" placeholder="{tr}English{/tr}..."/></td><td><input type="text" name="to[]" class="form-control" placeholder="{tr}Translation{/tr}..."/></td></tr>');
		});
	{/jq}
{/block}