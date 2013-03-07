{* Display the list of available translations for an object and manage its translations *}
{** Currently works for the following object types: 'article' and 'wiki page' **}
<ul class="clearfix cssmenu_horiz">
	<li class="tabmark">
	{* For all object types: First show the world icon and on hover the language of the current object *}
		{icon _id=world class="icon" title="{tr}Current language:{/tr} {$trads[0].langName|escape} ({$trads[0].lang|escape})"}
	{* ..than on hover first show the list of translations including the current language highlighted *}
		<ul>
			<li class="tabcontent">
			{* First the language of the object *}
			{if $object_type eq 'wiki page'}
				<a href="tiki-index.php?page={$trads[0].objName|escape}&no_bl=y" title="{$trads[0].langName|escape} ({$trads[0].lang|escape}): {$trads[0].objName}" class="selected">
					{$trads[0].langName|escape} ({$trads[0].lang|escape})
				</a>
			{elseif $object_type eq 'article'}
				<a href="tiki-read_article.php?articleId={$trads[0].objId}" title="{$trads[0].langName|escape} ({$trads[0].lang|escape}): {$trads[0].objName}" class="selected">
					{$trads[0].langName|escape} ({$trads[0].lang|escape})
				</a>
			{/if}
			{* Than the header for human translations - shown only if there is a translation availble *}
				{if isset($trads) and count($trads) > 1}
					<h1>
						{icon _id=group title="{tr}Translations{/tr}"} {tr}Translations{/tr}
					</h1>
				{/if}
			{* Show the list of available translations *}
				{section name=i loop=$trads}
				{* For wiki pages *}		
					{if $object_type eq 'wiki page' and $trads[i] neq $trads[0]}
						<a href="tiki-index.php?page={$trads[i].objName|escape}&no_bl=y" title="{$trads[i].langName|escape} ({$trads[i].lang|escape}): {$trads[i].objName}" class="linkmodule {$trads[i].class}">
							{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					{/if}
				{* For articles *}
					{if $object_type eq 'article' and $trads[i] neq $trads[0]}
						<a href="tiki-read_article.php?articleId={$trads[i].objId}" title="{$trads[i].langName|escape} ({$trads[i].lang|escape}): {$trads[i].objName}" class="linkmodule {$trads[i].class}">
							{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					{/if}
				{/section}
			{* For wiki pages only: Show a link to view all translations on a single page *}
				{if $object_type eq 'wiki page' and $prefs.feature_multilingual_one_page eq 'y' and $translationsCount gt 1}
					<h1>
						<a href="tiki-all_languages.php?page={$trads[0].objName|escape:url}&no_bl=y" title="{tr}Show all translations of this page on a single page{/tr}">{icon _id=application_view_columns title=""}
							{tr}All languages{/tr}
						</a>
					</h1>
				{/if}
			{* For wiki pages only: List of machine translation candidates if feature is switched on *}
				{if $object_type eq 'wiki page' and $prefs.feature_machine_translation eq 'y'}
					<h1>
						{icon _id=google title="{tr}Google translate{/tr}"} {tr}Machine translations{/tr}
					</h1>
				{* List machine translation candidates for available language of the site *}
					{foreach from=$langsCandidatesForMachineTranslation item=mtl}
						<a href="tiki-index.php?machine_translate_to_lang={$mtl.lang|escape}&page={$page|escape:"quotes"}&no_bl=y" title="{$mtl.langName|escape} ({$mtl.lang|escape})">
							{$mtl.langName|escape} *
						</a>
					{/foreach}
				{/if}
			{* For all object types: Translation maintenance *}
				{capture}{if $object_type eq 'wiki page' and $tiki_p_edit eq 'y'}
					<a href="tiki-edit_translation.php?page={$trads[0].objName|escape:url}&no_bl=y" title="{tr}Translate page{/tr}">
						{tr}Translate{/tr}
					</a>
					<a href="#" onclick="attach_detach_translation('wiki page', '{$page|escape:"quotes"}')" title="{tr}Manage page translations{/tr}">
						{tr}Manage translations{/tr}
					</a>
				{elseif $object_type eq 'article' and $tiki_p_edit_article eq 'y'}
					<a href="tiki-edit_article.php?translationOf={$articleId}" title="{tr}Translate article{/tr}">
						{tr}Translate{/tr}
					</a>
					<a href="#" onclick="attach_detach_translation('article', '{$articleId|escape:"quotes"}')" title="{tr}Manage article translations{/tr}">
						{tr}Manage translations{/tr}
					</a>
				{/if}{/capture}
				{if !empty($smarty.capture.default)}{* Only display the header if there's content *}
					<h1>
						{icon _id=world_edit title="{tr}Maintenance{/tr}"} {tr}Maintenance{/tr}
					</h1>
					{$smarty.capture.default}
				{/if}				
			</li>
		</ul>
	</li>
</ul>
{* this section is for the related javascripts *}
{jq notonready=true}
function attach_detach_translation( object_type, object_to_translate ) {
	$(document).serviceDialog({
		title: '{tr}Manage translations{/tr}', 
		data: {
			controller: 'translation',
			action: 'manage',
			type: object_type,
			source: object_to_translate
		}
	});
	return;
}
{/jq}
