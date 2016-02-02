{title help="i18n" admpage="i18n"}{tr}Translate:{/tr} {$name}{if isset($languageName)} ({$languageName}, {$langpage}){/if}{/title}

<div class="navbar">
	{if $type eq 'wiki page'}
		{assign var=thisname value=$name|escape:'url'}
		{button href="tiki-index.php?page=$thisname&no_bl=y" _text="{tr}View Page{/tr}"}
	{else}
		{button href="tiki-read_article.php?articleId=$id" _text="{tr}View Article{/tr}"}
	{/if}
</div>

{if $error}
	<div class="alert alert-warning">
	{if $error == "traLang"}
		{tr}You must specify the object language{/tr}
	{elseif $error == "srcExists"}
		{tr}The object doesn't exist{/tr}
	{elseif $error == "srcLang"}
		{tr}The object doesn't have a language{/tr}
	{elseif $error == "alreadyTrad"}
		{tr}The object already has a translation for this language{/tr}
	{elseif $error == "alreadySet"}
		{tr}The object is already in the set of translations{/tr}
	{/if}
	</div>
	<br>
{/if}

{if $langpage}


{if $type == 'wiki page'}
<ul>
	<li><a href="#translate_updates">{tr}Translate updates made on this page or one of its translations{/tr}</a></li>
	<li><a href="#new_translation">{tr}Translate this page to a new language{/tr}</a></li>
	<li><a href="{service controller=translation action=manage type='wiki page' source=$page}" class="attach_detach_translation" data-object_type="wiki page" data-object_id="{$page|escape:'quotes'}">{tr}Attach or detach existing translations of this page{/tr}</a></li>
	<li><a href="#change_language">{tr}Change language for this page{/tr}</a></li>
</ul>

<hr>

<a id="translate_updates"></a>
<h3>{tr}Translate updates made on this page or one of its translations{/tr}</h3>

<div style="width:50%">
	{$content_of_update_translation_section}
</div>

<br>
<hr>
<br>

<a id="new_translation"></a>
<h3>{tr}Translate this page to a new language{/tr}</h3>
<form method="post" action="tiki-editpage.php" onsubmit="return validate_translation_request(this)">
	<fieldset>
		<p>{tr}Select language to translate to:{/tr}
			<select name="lang" id="language_list" size="1">
				<option value="unspecified">{tr}Unspecified{/tr}</option>
				{section name=ix loop=$languages}
				<option value="{$languages[ix].value|escape}"{if $default_target_lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name|escape}</option>
				{/section}
			</select>
		</p>
		<p>{tr}Enter the page title:{/tr}
			<input type="text" size="40" name="page" id="translation_name" value="{$translation_name|escape}">
			<input type="hidden" name="source_page" value="{$name|escape}">
			<input type="hidden" name="oldver" value="-1">
			<input type="hidden" name="is_new_translation" value="y">
		</p>
		{if $prefs.feature_categories eq 'y'}
			{tr}Below, assign categories to this new translation (Note: they should probably be the same as the categories of the page being translate){/tr}
			<br>
			{include file="categorize.tpl" notable=y}
		{/if}
		<p align="center"><input type="submit" class="btn btn-default btn-sm" value="{tr}Create translation{/tr}"></p>
	</fieldset>
</form>
{/if}
{if !isset($articles)}
	{jq notonready=true}
		{literal}
		// Make the translation name have the focus.
		window.onload = function()
		{
			document.getElementById("translation_name").focus();
		}

		function validate_translation_request() {
			var success = true;
			var language_of_translation = $("#language_list").val();

			if (language_of_translation == "unspecified") {
		{/literal}
				var message = {tr}You forgot to specify the language of the translation. Please choose a language in the picklist.{/tr};
		{literal}
				alert(message);
				success = false;
			} else {
				var page_list = $("#existing-page-src");
				var page_name = $('#translation_name').val();
				var matching_options = $('#existing-page-src option[value="' + page_name + '"]').attr( 'selected', true );

				if( matching_options.length > 0 ) {
					var message = {tr}The page already exists. It was selected in the list below.{/tr};
					alert( message );

					success = false;
				}
			}
			return success;
		}
		{/literal}
	{/jq}
{/if}

<hr>

<a id="change_language"></a>
<h3>{tr}Change language for this page{/tr}</h3>
<form method="post" action="tiki-edit_translation.php" class="form">
	<div class="input-group">
		<select name="langpage" class="form-control">
			<option value="">{tr}Unspecified{/tr}</option>
			{foreach item=lang from=$languages}
				<option value="{$lang.value|escape}">{$lang.name}</option>
			{/foreach}
		</select>
		<div class="input-group-btn">
			<input type="hidden" name="id" value="{$id}">
			<input type="hidden" name="type" value="{$type}">
			<input type="submit" class="btn btn-primary" name="switch" value="{tr}Change Language{/tr}">
		</div>
	</div>
</form>

{else}
	{remarksbox type="error" title="{tr}Error{/tr}"}
		{tr}No language is assigned to this page.{/tr}
	{/remarksbox}
	<strong>{tr}Please select a language before translating.{/tr}</strong>
	<form method="post" action="tiki-edit_translation.php" class="form">
		<div class="input-group">
			<select name="langpage" class="form-control">
				{foreach item=lang from=$languages}
					<option value="{$lang.value|escape}">{$lang.name}</option>
				{/foreach}
			</select>
			<div class="input-group-btn">
				<input type="hidden" name="id" value="{$id}">
				<input type="hidden" name="type" value="{$type|escape}">
				<input type="submit" class="btn btn-primary" value="{tr}Set Page Language{/tr}">
			</div>
		</div>
	</form>
{/if}

{jq}
	$('a.attach_detach_translation').click(function() {
		var object_type = $(this).data('object_type');
		var object_to_translate = $(this).data('object_id');
		$(this).serviceDialog({
			title: '{tr}Manage translations{/tr}',
			data: {
			controller: 'translation',
			action: 'manage',
			type: object_type,
			source: object_to_translate
			}
		});
		return false;
	});
{/jq}
