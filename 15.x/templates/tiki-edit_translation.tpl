{title help="i18n" admpage="i18n"}{tr}Translate:{/tr} {$name}{if isset($languageName)} ({$languageName}, {$langpage}){/if}{/title}

<div class="t_navbar margin-bottom-md clearfix">
	{if $type eq 'wiki page'}
		{assign var=thisname value=$name|escape:'url'}
		{button href="tiki-index.php?page=$thisname&no_bl=y" _text="{tr}View Page{/tr}" _icon_name="view" _class="btn btn-link"}
	{else}
		{button href="tiki-read_article.php?articleId=$id" _text="{tr}View Article{/tr}" _icon_name="view" _class="btn btn-link"}
	{/if}
	<a href="{service controller=translation action=manage type='wiki page' source=$page}" class="attach_detach_translation btn btn-link tips" data-object_type="wiki page" data-object_id="{$page|escape:'quotes'}" title=":{tr}Attach or detach existing translations of this page{/tr}">{tr}Manage Translations{/tr}</a>
</div>

{if $error}
	{remarksbox type="error" title="{tr}Error{/tr}" close="n"}
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
	{/remarksbox}
{/if}

{if $langpage}
	{if $type == 'wiki page'}
		<div class="clearfix">
			<div class="col-md-8 col-md-offset-2">
				<a id="translate_updates"></a>
				{$content_of_update_translation_section}
			</div>
			<div class="col-md-8 col-md-offset-2">
				<a id="new_translation"></a>
				<form method="post" action="tiki-editpage.php" onsubmit="return validate_translation_request(this)" class="form-horizontal" role="form">
					<div class="panel panel-default">
						<div class="panel-heading">
							{tr}Translate this page to a new language{/tr}
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="lang" class="control-label col-md-4">
									{tr}Select language to translate to:{/tr}
								</label>
								<div class="col-md-8">
									<select name="lang" id="language_list" size="1" class="form-control">
										<option value="unspecified">{tr}Unspecified{/tr}</option>
										{section name=ix loop=$languages}
											<option value="{$languages[ix].value|escape}"{if $default_target_lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name|escape}</option>
										{/section}
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="page" class="control-label col-md-4">
									{tr}Enter the page title:{/tr}
								</label>
								<div class="col-md-8">
									<input type="text" name="page" id="translation_name" value="{$translation_name|escape}" class="form-control">
									<input type="hidden" name="source_page" value="{$name|escape}">
									<input type="hidden" name="oldver" value="-1">
									<input type="hidden" name="is_new_translation" value="y">
								</div>
							</div>
							{if $prefs.feature_categories eq 'y'}
								<label for="page" class="control-label col-md-4">
									{tr}Categories{/tr}
								</label>
								<div class="col-md-8">
									{include file="categorize.tpl" notable=y}
									<span class="help-block">
										{tr}Assign categories to this new translation (Note: they should probably be the same as the categories of the page being translate){/tr}
									</span>
								</div>
							{/if}
						</div>
						<div class="panel-footer text-center">
							<input type="submit" class="btn btn-primary btn-sm" value="{tr}Create translation{/tr}">
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-8 col-md-offset-2">
				<a id="change_language"></a>
				<form method="post" action="tiki-edit_translation.php" class="form-horizontal" role="form">
					<div class="panel panel-default">
						<div class="panel-heading">
							{tr}Change language for this page{/tr}
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="langpage" class="control-label col-md-4">
									{tr}Language{/tr}
								</label>
								<div class="col-md-8">
									<select name="langpage" class="form-control">
										<option value="">{tr}Unspecified{/tr}</option>
										{foreach item=lang from=$languages}
											<option value="{$lang.value|escape}" {if $lang.value eq $langpage} selected="selected"{/if}>{$lang.name}</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>
						<div class="panel-footer text-center">
							<input type="hidden" name="id" value="{$id}">
							<input type="hidden" name="type" value="{$type}">
							<input type="submit" class="btn btn-primary btn-sm" name="switch" value="{tr}Change Language{/tr}">
						</div>
					</div>
				</form>
			</div>
		</div>
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
{else}
	{remarksbox type="error" title="{tr}Error{/tr}" close="n"}
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
