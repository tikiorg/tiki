{title}{tr}Edit or export languages{/tr}{/title}

<div class="navbar">
	{if $smarty.session.interactive_translation_mode eq 'on'}
		{button href="tiki-interactive_trans.php?interactive_translation_mode=off" _text="{tr}Toggle interactive translation off{/tr}" _ajax="n"}
	{else}
		{button href="tiki-interactive_trans.php?interactive_translation_mode=on" _text="{tr}Toggle interactive translation on{/tr}" _ajax="n"}
	{/if}
</div>

{tabset}
	{tab name="{tr}Edit languages{/tr}"}
		<form action="tiki-edit_languages.php" id="select_action" method="post">
			{if isset($find)}
				<input type="hidden" name="find" value="{$find}" />
			{/if}
			{if isset($maxRecords)}
				<input type="hidden" name="maxRecords" value="{$maxRecords}" />
			{/if}
			<div class="adminoptionbox">
				<label for="edit_language">{tr}Select the language to edit:{/tr}</label>
				<select id="edit_language" class="translation_action"name="edit_language">
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}" {if $edit_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
				</select>
			</div>
			<div class="adminoptionbox">
				<input id="add_tran_sw" class="translation_action" align="right" type="radio" name="action" value="add_tran_sw" {if $action eq 'add_tran_sw'}checked="checked"{/if}/>
				<label for="add_tran_sw">{tr}Add a translation{/tr}</label>
			</div>
			<div class="adminoptionbox">
				<input id="edit_tran_sw" class="translation_action" align="right" type="radio" name="action" value="edit_tran_sw" {if $action eq 'edit_tran_sw'}checked="checked"{/if}/>
				<label for="edit_tran_sw">{tr}Edit translations{/tr}</label>
				<div class="adminoptionboxchild">
					<input id="only_db_translations" class="translation_action" type="checkbox" name="only_db_translations" {if $only_db_translations eq 'y'}checked="checked"{/if}>
					<label for="only_db_translations">{tr}Show only database stored translations{/tr}</label>
				</div>
			</div>

			{if $prefs.record_untranslated eq 'y'}
				<div class="adminoptionbox">
					<input id="edit_rec_sw" class="translation_action" align="right" type="radio" name="action" value="edit_rec_sw" {if $action eq 'edit_rec_sw'}checked="checked"{/if}/>
					<label for="edit_rec_sw">{tr}Translate recorded{/tr}</label>
				</div>
			{/if}
		</form>

		<form action="tiki-edit_languages.php" method="post">
			<input type="hidden" name="edit_language" value="{$edit_language}" />
			<input type="hidden" name="action" value="{$action}" />
			{if isset($only_db_translations)}
				<input type="hidden" name="only_db_translations" value="{$only_db_translations}" />
			{/if}
			{if $action eq 'add_tran_sw'}
				<div class="simplebox">
					<h4>{tr}Add a translation:{/tr}</h4>
					<table class="formcolor">
						<tr>
							<td>{tr}Original:{/tr}</td>
							<td><input name="add_tran_source" size=20 maxlength=255></td>
							<td>{tr}Translation:{/tr}</td>
							<td><input name="add_tran_tran" size=20 maxlength=255></td>
							<td align="center"><input type="submit" name="add_tran" value="{tr}Add{/tr}" /></td>
						</tr>
					</table>
				</div>
			{/if}
			{if $action eq 'edit_tran_sw' || $action eq 'edit_rec_sw'}
				<div class="simplebox">
					<h4>{if $action eq 'edit_tran_sw'}{tr}Edit translations:{/tr}{else}{tr}Translate recorded:{/tr}{/if}</h4>
					<table class="formcolor normal" id="edit_translations">
						<tr>
							<td align="center" colspan=3>
								{include file='find.tpl' find_show_num_rows='y'}
								<hr />
							</td>
						</tr>
						{foreach from=$translations name=translations item=item}
							<tr>
								<td><label for="source_{$smarty.foreach.translations.index}">{tr}Original:{/tr}</label></td>
								<td><input id="source_{$smarty.foreach.translations.index}" name="source_{$smarty.foreach.translations.index}" value="{$item.source|escape}" size=65 readonly="readonly"/>
								<td align="center" align="center" rowspan="{if isset($item.originalTranslation)}5{else}3{/if}">
									<input type="submit" name="edit_tran_{$smarty.foreach.translations.index}" value="{tr}Translate{/tr}" />
									{if $action eq 'edit_tran_sw' && isset($item.changed)}
										<input type="submit" name="del_tran_{$smarty.foreach.translations.index}" value="{tr}Delete{/tr}" />
									{/if}
									{assign var=itemIndex value=$smarty.foreach.translations.index}
									{if isset($item.originalTranslation)}
										{button _flip_id="diff_$itemIndex" _flip_hide_text=n _text="{tr}Diff{/tr}"}
									{/if}
								</td>
							</tr>
							{if isset($item.originalTranslation)}
								<tr>
									<td><label for="original_tran_{$smarty.foreach.translations.index}">{tr}Original translation:{/tr}</label></td>
									<td><input id="original_tran_{$smarty.foreach.translations.index}" name="original_tran_{$smarty.foreach.translations.index}" value="{$item.originalTranslation|escape}" size=65 readonly="readonly" /></td>
								</tr>
							{/if}
							<tr>
								<td><label for="tran_{$smarty.foreach.translations.index}">{tr}Translation:{/tr}</label></td>
								<td><input id="tran_{$smarty.foreach.translations.index}" name="tran_{$smarty.foreach.translations.index}" value="{$item.tran|escape}" size=65 /></td>
							</tr>
							<tr>
								<td colspan="2">
									{if isset($item.originalTranslation)}
										<table class="normal" id="diff_{$smarty.foreach.translations.index}" style="display: none;">{$item.diff}</table>
									{/if}
								</td>
							</tr>
							<tr class="last">
								<td colspan="2">
									{if isset($item.user) && isset($item.lastModif)}
										{tr 0=$item.user|userlink 1=$item.lastModif|tiki_short_date}Last changed by %0 on %1{/tr}
									{/if}
								</td>
							</tr>
						{foreachelse}
							{norecords _colspan=3}
						{/foreach}
						<tr>
							<td colspan="3">
								{if !empty($translations)}
									<input type="submit" name="translate_all" value="{tr}Translate all{/tr}" />
									{if $action eq 'edit_rec_sw'}
										<input type="submit" name="tran_reset" value="{tr}Delete all{/tr}" onclick="confirm('{tr}Are you sure you want to delete all untranslated strings from database?{/tr}')" />
									{/if}
									{if $action eq 'edit_tran_sw' && $hasDbTranslations == true && $tiki_p_admin eq 'y'}
										<input type="submit" name="delete_all" value="{tr}Delete all{/tr}" onclick="confirm('{tr}Are you sure you want to delete all translations from database?{/tr}')" />
									{/if}
								{/if}								
							</td>
						</tr>
					</table>
					<input type="hidden" name="offset" value="{$offset|escape}" />
										
					{pagination_links cant=$total step=$maxRecords offset=$offset _ajax='n'}{/pagination_links}
				</div>
			{/if}
		</form>
	{/tab}

	{tab name="{tr}Export languages{/tr}"}
		<form action="tiki-edit_languages.php" method="post">
			{if isset($expmsg)}
			    {remarksbox type="note" title="{tr}Note:{/tr}"}
					{$expmsg}
				{/remarksbox}
			{/if}
			{if (empty($db_languages))}
			    {remarksbox type="note" title="{tr}Note:{/tr}"}
					{tr}No translations in the database available to export. First translate strings using interactive translation or "Edit languages" tab.{/tr}
				{/remarksbox}
			{else}
				<div class="adminoptionbox">
					<label for="exp_language">{tr}Select the language to Export:{/tr}</label>
					<select id="exp_language" name="exp_language">
						{section name=ix loop=$db_languages}
							<option value="{$db_languages[ix].value|escape}"
								{if $exp_language eq $db_languages[ix].value}selected="selected"{/if}>
								{$db_languages[ix].name}
							</option>
						{/section}
					</select>
				</div>
			    {remarksbox type="note" title="{tr}Note:{/tr}"}
					{tr}If you click "Download database translations", you will download a file with all the translations in the database.{/tr}
					{if $tiki_p_admin eq 'y' and $langIsWritable}
						{tr}If you click "Write to language.php", the translations in the database will be merged with the other translations in language.php. Note that after writing translations to language.php they are removed from the database.{/tr}
					{/if}
				{/remarksbox}
				{if !$langIsWritable}
					{remarksbox type="note" title="{tr}Note:{/tr}"}
						{tr}To be able to write your translations back to language.php make sure that the web server has write permission in the lang/ directory.{/tr}
					{/remarksbox}
				{/if}
				<div class="adminoptionbox">
					<input type="submit" name="downloadFile" value="{tr}Download database translations{/tr}" />
					{if $tiki_p_admin eq 'y' and $langIsWritable}
						<input type="submit" name="exportToLanguage" value="{tr}Write to language.php{/tr}" />
					{/if}
				</div>
			{/if}
		</form>
	{/tab}
{/tabset}
