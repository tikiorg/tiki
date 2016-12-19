{* $Id$ *}
{title admpage="i18n"}{tr}Edit languages{/tr}{/title}
<div class="t_navbar margin-bottom-md">
	{if $smarty.session.interactive_translation_mode eq 'on'}
		{button href="tiki-interactive_trans.php?interactive_translation_mode=off" _text="{tr}Toggle interactive translation off{/tr}" _ajax="n"}
	{else}
		{button href="tiki-interactive_trans.php?interactive_translation_mode=on" _text="{tr}Toggle interactive translation on{/tr}" _ajax="n"}
	{/if}
	<a class="btn btn-link tips" href="{service controller=language action=manage_custom_php_translations}" title="{tr}Customized String Translation{/tr}:{tr}Manage local translations in a custom.php file{/tr}">
		{icon name="file-code-o"} {tr}Custom Translations{/tr}
	</a>
	<a class="btn btn-link tips" href="{service controller=language action=upload language={$edit_language}}" title="{tr}Upload Translations{/tr}:{tr}Upload a file with translations for the selected language.{/tr}">
		{icon name="upload"} {tr}Upload Translations{/tr}
	</a>
</div>
<form action="tiki-edit_languages.php" id="select_action" method="post" class="form-horizontal">
	{if isset($find)}
		<input type="hidden" name="find" value="{$find}">
	{/if}
	{if isset($maxRecords)}
		<input type="hidden" name="maxRecords" value="{$maxRecords}">
	{/if}
	<div class="form-group">
		<div class="adminoptionbox">
			<label for="edit_language" class="col-md-4 control-label">{tr}Language{/tr}</label>
			<div class="col-md-6">
				<select id="edit_language" class="translation_action form-control" name="edit_language">
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}" {if $edit_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
				</select>
			</div>
			<div class="col-md-2">
				<a class="btn btn-link tips" href="{service controller=language action=download language={$edit_language} file_type=language_php}" title="{tr}Download{/tr}:{tr}Download language.php file for the selected language.{/tr}">
					{icon name="download"}
				</a>
				<a class="btn btn-link tips" href="{service controller=language action=download_db_translations language={$edit_language}}" title="{tr}Download Database Translations{/tr}:{tr}Download a file with all the translations in the database for the selected language.{/tr}">
					{icon name="file-text-o"}
				</a>
				<a class="btn btn-link tips" href="{bootstrap_modal controller=language action=write_to_language_php language={$edit_language}}" title="{tr}Write to language.php{/tr}:{tr}Translations in the database will be merged with the other translations in language.php for the selected language.{/tr}">
					{icon name="flash"}
				</a>				
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="adminoptionbox">
			<label for="add_tran_sw" class="col-md-4 control-label">{tr}Add a translation{/tr}</label>
			<div class="col-md-8">
				<label class="radio-inline"><input id="add_tran_sw" class="translation_action" type="radio" name="action" value="add_tran_sw" {if $action eq 'add_tran_sw'}checked="checked"{/if}>{tr}Add{/tr}</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="adminoptionbox">
			<label for="add_tran_sw" class="col-md-4 control-label">{tr}Edit translations{/tr}</label>
			<div class="col-md-8">
				<label class="radio-inline"><input id="edit_tran_sw" class="translation_action" align="right" type="radio" name="action" value="edit_tran_sw" {if $action eq 'edit_tran_sw'}checked="checked"{/if}>{tr}Edit{/tr}</label>
				<div class="adminoptionboxchild">
					<label class="checkbox-inline"><input id="only_db_translations" class="translation_action" type="checkbox" name="only_db_translations" {if $only_db_translations eq 'y'}checked="checked"{/if}>{tr}Show only database stored translations{/tr}</label>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="adminoptionbox">
			<label for="add_tran_sw" class="col-md-4 control-label">{tr}Unstranslated strings{/tr}</label>
			<div class="col-md-8">
				<label class="radio-inline"><input id="edit_rec_sw" class="translation_action" align="right" type="radio" name="action" value="edit_rec_sw" {if $action eq 'edit_rec_sw'}checked="checked"{/if}>{tr}Untranslated{/tr}</label>
				{if $prefs.record_untranslated eq 'y'}
				<div class="adminoptionboxchild">
					<label class="checkbox-inline"><input id="only_db_untranslated" class="translation_action" type="checkbox" name="only_db_untranslated" {if $only_db_untranslated eq 'y'}checked="checked"{/if}>{tr}Show only database stored untranslated strings{/tr}</label>
				</div>
				{/if}
			</div>
		</div>
	</div>
</form>
<form action="tiki-edit_languages.php" method="post" class="form-horizontal">
	<input type="hidden" name="edit_language" value="{$edit_language}">
	<input type="hidden" name="action" value="{$action}">
	{if $only_db_translations eq 'y'}
		<input type="hidden" name="only_db_translations" value="{$only_db_translations}">
	{/if}
	{if $only_db_untranslated eq 'y'}
		<input type="hidden" name="only_db_untranslated" value="{$only_db_untranslated}">
	{/if}
	{if $action eq 'add_tran_sw'}
		<div class="panel panel-default">
			<div class="panel-heading">
				{tr}Add a translation{/tr}
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-md-4 control-label">{tr}Original:{/tr}</label>
					<div class="col-md-8">
						<input name="add_tran_source" maxlength="255" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">{tr}Translation:{/tr}</label>
					<div class="col-md-8">
						<input name="add_tran_tran" maxlength="255" class="form-control">
					</div>
				</div>
			</div>
			<div class="panel-footer text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="add_tran" value="{tr}Add{/tr}">
			</div>
		</div>
	{/if}
	{if $action eq 'edit_tran_sw' || $action eq 'edit_rec_sw'}
		<div class="panel panel-default">
			<div class="panel-heading">
				{if $action eq 'edit_tran_sw'}
					{tr}Edit translations{/tr}
				{else}
					{tr}Untranslated strings{/tr}
				{/if}
			</div>
			<div class="panel-body">
				<table class="table table-condensed table-hover" id="edit_translations">
					<thead>
						<tr>
							<div class="col-md-8">
								{include file='find.tpl' find_show_num_rows='y'}
							</div>
						</tr>					
						<tr>
							<th>
								{tr}Original string{/tr}
							</th>
							<th>
								{tr}Original translation{/tr}
							</th>
							<th>
								{tr}Translation{/tr}
							</th>
							<th></th>								
						</tr>
					</thead>
					<tbody>					
						{foreach from=$translations name=translations item=item}
							<tr>
								<td class="col-md-3">
									<textarea id="source_{$smarty.foreach.translations.index}" name="source_{$smarty.foreach.translations.index}" class="form-control" rows="2" readonly="readonly">{$item.source|escape}</textarea>
								</td>
								<td class="col-md-3">
									{if isset($item.originalTranslation)}
										<textarea id="original_tran_{$smarty.foreach.translations.index}" name="original_tran_{$smarty.foreach.translations.index}" class="form-control" rows="2" readonly="readonly">{$item.originalTranslation|escape}</textarea>
									{/if}
								</td>
								<td class="col-md-3">
									<textarea id="tran_{$smarty.foreach.translations.index}" name="tran_{$smarty.foreach.translations.index}" class="form-control" rows="2">{$item.tran|escape}</textarea>
								</td>
								<td class="col-md-3 text-center">
									<button type="submit" class="btn btn-primary btn-sm tips" name="edit_tran_{$smarty.foreach.translations.index}" title=":{tr}Save translation in the database{/tr}">
										{tr}Translate{/tr}
									</button>
									{if $action eq 'edit_tran_sw' && isset($item.changed)}
										<button type="submit" class="btn btn-warning btn-sm tips" name="del_tran_{$smarty.foreach.translations.index}" title=":{tr}Delete translation from the database{/tr}">
											{tr}Delete{/tr}
										</button>
									{/if}
									{assign var=itemIndex value=$smarty.foreach.translations.index}
									{if isset($item.originalTranslation)}
										{button _flip_id="diff_$itemIndex" _flip_hide_text="n" _text="{tr}Compare{/tr}" _title=":{tr}Compare the origional translation with the database translation{/tr}" _class="btn btn-default btn-sm tips"}
									{/if}
									{if isset($item.user) && isset($item.lastModif)}
										<span class="help-block">
											<small>{tr _0=$item.user|userlink _1=$item.lastModif|tiki_short_date}Last changed by %0 on %1{/tr}</small>
										</span>
									{/if}
								</td>
							</tr>
							{if isset($item.originalTranslation)}
								<tr>
									<td colspan="4">
										<div class="col-md-6 col-md-push-3">
											<table class="table" id="diff_{$smarty.foreach.translations.index}" style="display: none;">
												{$item.diff}
											</table>
										</div>
									</td>
								</tr>
							{/if}
						{foreachelse}
							{norecords _colspan=3}
						{/foreach}
					</tbody>
				</table>
				<div class="text-center">
					{pagination_links cant=$total step=$maxRecords offset=$offset _ajax='n'}{strip}
					tiki-edit_languages.php?edit_language={$edit_language}&action={$action}&maxRecords={$maxRecords}&only_db_translations={$only_db_translations}&only_db_untranslated={$only_db_untranslated}{if isset($find)}&find={$find}{/if}
					{/strip}{/pagination_links}
				</div>
			</div>
			<div class="panel-footer text-center">
				<input type="hidden" name="offset" value="{$offset|escape}">
				{if !empty($translations)}
					<input type="submit" class="btn btn-primary btn-sm" name="translate_all" value="{tr}Translate all{/tr}">
					{if $action eq 'edit_rec_sw' && $hasDbTranslations == true && $only_db_untranslated eq 'y'}
						<input type="submit" class="btn btn-warning btn-sm" name="tran_reset" value="{tr}Delete all{/tr}" onclick="return confirm('{tr}Are you sure you want to delete all untranslated strings from database?{/tr}')">
					{/if}
					{if $action eq 'edit_tran_sw' && $only_db_translations eq 'y' && $tiki_p_admin eq 'y'}
						<input type="submit" class="btn btn-warning btn-sm" name="delete_all" value="{tr}Delete all{/tr}" onclick="return confirm('{tr}Are you sure you want to delete all translations from database?{/tr}')">
					{/if}
				{/if}
			</div>
		</div>
	{/if}
</form>
