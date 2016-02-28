{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<form method="post" id="selectLanguageForm" role="form" class="form" action="{service controller=language action=manage_custom_php_translations}">
		<div class="form-group">
			<label class="control-label" for="custom_lang_select">
				{tr}Language{/tr}
			</label>
			<select name="language" id="custom_lang_select" class="form-control">
				{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}"
						{if (empty($language) && $languages[ix].value eq $prefs.site_language) || (!empty($language) && $languages[ix].value eq $language)} selected="selected"{/if}>
						{$languages[ix].name|escape}
					</option>
				{/section}
			</select>
		</div>
		<div class="submit">
			<input type="submit" class="btn btn-primary" value="{tr}Confirm{/tr}">
		</div>
	</form>
{/block}