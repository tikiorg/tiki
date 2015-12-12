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
			{remarksbox type="note" title="{tr}No translations in the database available to export{/tr}" close="n"}
				 <a href="tiki-edit_languages.php" class="btn btn-default">{tr}Edit languages{/tr}</a>
			{/remarksbox}
		{else}
			<div class="form-group">
				<label for="language" class="control-label">
					{tr}Language{/tr}
				</label>
				<select id="language" name="language" class="form-control">
					{section name=ix loop=$db_languages}
						<option value="{$db_languages[ix].value|escape}"
							{if $exp_language eq $db_languages[ix].value}selected="selected"{/if}>
							{$db_languages[ix].name}
						</option>
					{/section}
				</select>
			</div>
				{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
				{if $tiki_p_admin eq 'y' and $langIsWritable}
					{tr}The translations in the database will be merged with the other translations in language.php. After writing translations to language.php the translations are removed from the database.{/tr}
				{/if}
			{/remarksbox}
			{if !$langIsWritable}
				{remarksbox type="note" title="{tr}Note:{/tr}"}
					{tr}To be able to write your translations back to language.php make sure that the web server has write permission in the lang/ directory.{/tr}
				{/remarksbox}
			{/if}
			<div class="submit text-center">
				{if $tiki_p_admin eq 'y' and $langIsWritable}
					<input type="hidden" name="confirm" value="1">
					<input type="submit" class="btn btn-primary" name="exportToLanguage" value="{tr}Write to language.php{/tr}">
					<a href="tiki-edit_languages.php" class="btn btn-link">
						{tr}Cancel{/tr}
					</a>
				{/if}
			</div>
		{/if}
	</form>
{/block}