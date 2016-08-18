{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form action="{service controller=language action=write_to_language_php}" method="post" role="form" class="form">
		{if isset($expmsg)}
			{remarksbox type="note" title="{tr}Note:{/tr}"}
				{$expmsg}
			{/remarksbox}
		{/if}
		{if (empty($db_languages))}
			{remarksbox type="note" title="{tr}Information{/tr}" close="n"}
				 {tr}No translations in the database available to export{/tr}
			{/remarksbox}
		{else}
			{if $tiki_p_admin eq 'y' and $langIsWritable}
				<div class="form-group">
					<label>
						{tr}Translations in the database{/tr}: <span class="badge">{$db_translation_count}</span>
					</label>
				</div>
				<div class="form-group">
					<label>
						{tr}File{/tr}: {$langFile}
					</label>
				</div>
				{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
					{tr}The translations in the database will be merged with the other translations in language.php. After writing translations to language.php the translations are removed from the database.{/tr}
				{/remarksbox}
			{/if}
			{if !$langIsWritable}
				{remarksbox type="note" title="{tr}Note:{/tr}"}
					{tr}To be able to write your translations back to language.php make sure that the web server has write permission in the lang/ directory.{/tr}
				{/remarksbox}
			{/if}
			<div class="submit text-center">
				{if $langIsWritable}
					<input type="hidden" name="confirm" value="1">
					<input type="hidden" name="language" value={$language}>
					<input type="submit" class="btn btn-primary" name="exportToLanguage" value="{tr}Write to language.php{/tr}">
				{/if}
			</div>
		{/if}
	</form>
{/block}