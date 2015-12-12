{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form action="{service controller=language action=download_db_translations}" method="post" role="form" class="form">
		{if (empty($db_languages))}
			{remarksbox type="note" title="{tr}No translations in the database available to export{/tr}" close="n"}
				 <a href="tiki-edit_languages.php" class="btn btn-default">{tr}Edit languages{/tr}</a>
			{/remarksbox}
		{else}
			{remarksbox type="note" title="{tr}Information{/tr}" close="n"}
				{tr}Download a custom.php file with all the translations in the database for the selected language.{/tr}
			{/remarksbox}
			<div class="form-group">
				<label for="language" class="control-label">
					{tr}Language{/tr}
				</label>
				<select id="language" class="form-control" name="language">
					{section name=ix loop=$db_languages}
						<option value="{$db_languages[ix].value|escape}"
							{if $exp_language eq $db_languages[ix].value}selected="selected"{/if}>
							{$db_languages[ix].name}
						</option>
					{/section}
				</select>
			</div>
			<div class="submit text-center">
				<input type="hidden" name="confirm" value="1">
				<input type="submit" class="btn btn-primary" name="downloadFile" value="{tr}Download database translations{/tr}">
				<a href="tiki-edit_languages.php" class="btn btn-link">
					{tr}Cancel{/tr}
				</a>
			</div>
		{/if}
	</form>
{/block}