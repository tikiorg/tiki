{title help="i18n" admpage="i18n"}{tr}Translate:{/tr}&nbsp;{$name}{if isset($languageName)}&nbsp;({$languageName}, {$langpage}){/if}{/title}

<div class="navbar">
	{if $type eq 'wiki page'}
		{assign var=thisname value=$name|escape:'url'}
		{button href="tiki-index.php?page=$thisname&no_bl=y" _text="{tr}View Page{/tr}"}
	{else}
		{button href="tiki-read_article.php?articleId=$id" _text="{tr}View Article{/tr}"}
	{/if}
</div>

{if $error}
	<div class="simplebox highlight">
	{if $error == "traLang"}
		{tr}You must specify the object language{/tr}
	{elseif $error == "srcExists"}
		{tr}The object doesn't exist{/tr}
	{elseif $error == "srcLang"}
		{tr}The object doesn't have a language{/tr}
	{elseif $error == "alreadyTrad"}
		{tr}The object has already a translation for this language{/tr}
	{elseif $error == "alreadySet"}
		{tr}The object is already in the set of translations{/tr}
	{/if}
	</div>
	<br />
{/if}

{if $langpage}


{if $type == 'wiki page'}
<ul>
	<li><a href="#translate_updates">{tr}Translate updates made on this page or one of its translations{/tr}</a></li>
	<li><a href="#new_translation">{tr}Translate this page to a new language{/tr}</a></li>
	<li><a href="#attach_detach_translations">{tr}Attach or detach existing translations of this page{/tr}</a></li>
	<li><a href="#change_language">{tr}Change language for this page{/tr}</a></li>
</ul>

<hr />

<a name="translate_updates"></a>
<h3>{tr}Translate updates made on this page or one of its translations{/tr}</h3>

<div style="width:50%">
	{$content_of_update_translation_section}
</div>

<br />
<hr />
<br />

<a name="new_translation"></a>
<h3>{tr}Translate this page to a new language{/tr}</h3>
<form method="post" action="tiki-editpage.php" onsubmit="return validate_translation_request(this)">
	<fieldset>
		<p>{tr}Select language to translate to:{/tr}
			<select name="lang" id="language_list" size="1">
			   <option value="unspecified">{tr}Unspecified{/tr}</option>
				{section name=ix loop=$languages}
				{if in_array($languages[ix].value, $prefs.available_languages) or $prefs.available_languages|@count eq 0 or !is_array($prefs.available_languages)}
				<option value="{$languages[ix].value|escape}"{if $only_one_language_left eq "y"} selected="selected"{/if}>{$languages[ix].name|escape}</option>
				{/if}
				{/section}
			</select>
		</p>
		<p>{tr}Enter the page title:{/tr}
			<input type="text" size="40" name="page" id="translation_name"/>
			<input type="hidden" name="source_page" value="{$name|escape}"/>
			<input type="hidden" name="oldver" value="-1"/>
			<input type="hidden" name="is_new_translation" value="y"/>
		</p>
		{if $prefs.feature_categories eq 'y'}
			{tr}Below, assign categories to this new translation (Note: they should probably be the same as the categories of the page being translate){/tr}
			<br />
			{include file="categorize.tpl" notable=y}
		{/if}
		<p align="center"><input type="submit" value="{tr}Create translation{/tr}"/></p>
		<textarea name="edit" style="display:none">{$translate_message}{$pagedata|escape:'htmlall':'UTF-8'}</textarea>
	</fieldset>
</form>
{/if}
{if !isset($articles)}
{jq}
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
      var message = {tr}"You forgot to specify the language of the translation. Please choose a language in the picklist."{/tr};
{literal}   
      alert(message);
      success = false;
   } else {
      var page_list = $("#existing-page-src");
	  var page_name = $('#translation_name').val();
      var matching_options = $('#existing-page-src option[value="' + page_name + '"]').attr( 'selected', true );

	  if( matching_options.length > 0 ) {
          var message = {tr}"The page already exists. It was selected in the list below."{/tr};
          alert( message );
	  	
          success = false;
	  }
   }
   return success;
}
{/literal}
{/jq}
{/if}
{if !empty($langpage)}
	<br />
	<hr />
	<br />

	<a name="attach_detach_translations"></a>
	<h3>{if isset($articles)}{tr}Attach or detach existing translations of this article{/tr}{else}{tr}Attach or detach existing translations of this page{/tr}{/if}</h3>
		<table class="normal">
		<tr><th>{tr}Language{/tr}</th><th>{if isset($articles)}{tr}Article{/tr}{else}{tr}Page{/tr}{/if}</th><th>{tr}Actions{/tr}</th></tr>
		{cycle values="odd,even" print=false}
		{section name=i loop=$trads}
		<tr class="{cycle}">
			<td>{$trads[i].langName}</td>
			<td>{if $type == 'wiki page'}<a href="tiki-index.php?page={$trads[i].objName|escape:url}&no_bl=y">{else}<a href="tiki-read_article.php?articleId={$trads[i].objId|escape:url}">{/if}{$trads[i].objName|escape}</a></td>
			<td>
				{if $tiki_p_detach_translation eq 'y'}
					<a rel="nofollow" class="link" href="tiki-edit_translation.php?detach&amp;id={$id|escape:url}&amp;srcId={$trads[i].objId|escape:url}&amp;type={$type|escape:url}">{icon _id='cross' alt="{tr}detach{/tr}"}</a>
				{/if}
		</td></tr>
		{/section}
		</table>
{/if}

{if ($articles and ($articles|@count ge '1')) or ($pages|@count ge '1')}
	{* only show if there are articles or pages to select *}
	<form action="tiki-edit_translation.php" method="post">
		<fieldset>
			<input type="hidden" name="id" value="{$id}" />
			<input type="hidden" name="type" value="{$type|escape}" />
			<p>{if isset($articles)}{tr}Mark existing article as a translation of this one:{/tr}{else}{tr}Add existing page as a translation of this page:{/tr}{/if}<br />

			{if $articles}
				<select name="srcId">{section name=ix loop=$articles}{if !empty($articles[ix].lang) and $langpage ne $articles[ix].lang}<option value="{$articles[ix].articleId|escape}" {if $articles[ix].articleId == $srcId}checked="checked"{/if}>{$articles[ix].title|truncate:80:"(...)":true|escape}</option>{/if}{/section}</select>
			{else}
				<select name="srcName" id="existing-page-src">{section name=ix loop=$pages}<option value="{$pages[ix].pageName|escape}" {if $pages[ix].pageName == $srcId}checked="checked"{/if}>{$pages[ix].pageName|truncate:80:"(...)":true|escape} ({$pages[ix].lang|escape})</option>{/section}</select>
			{/if}
			&nbsp;
			<input type="submit" class="wikiaction" name="set" value="{tr}Go{/tr}"/>
			</p>
		</fieldset>
	</form>
{/if}

<br />
<hr />

<a name="change_language"></a>
<h3>{tr}Change language for this page{/tr}</h3>
<form method="post" action="tiki-edit_translation.php">
<div>
	<select name="langpage">
		<option value="">{tr}Unspecified{/tr}</option>
		{foreach item=lang from=$languages}
		<option value="{$lang.value|escape}">{$lang.name}</option>
		{/foreach}
	</select>
	<input type="hidden" name="id" value="{$id}"/>
	<input type="hidden" name="type" value="{$type}" />
	<input type="submit" name="switch" value="{tr}Change Language{/tr}"/>
</div>
</form>

{else}
	<div class="simplebox">
		{icon _id=delete alt="{tr}Alert{/tr}" style="vertical-align:middle"} 
		{tr}No language is assigned to this page.{/tr}
	</div>
	<p>{tr}Please select a language before performing translation.{/tr}</p>
	<form method="post" action="tiki-edit_translation.php">
		<p>
			<select name="langpage">
				{foreach item=lang from=$languages}
				<option value="{$lang.value|escape}">{$lang.name}</option>
				{/foreach}
			</select>
			<input type="hidden" name="id" value="{$id}" />
			<input type="hidden" name="type" value="{$type|escape}" />
			<input type="submit" value="{tr}Set Current Page's Language{/tr}"/>
		</p>
	</form>
{/if}
