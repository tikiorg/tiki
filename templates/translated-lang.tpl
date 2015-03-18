{* Display the list of available translations for an object and manage its translations *}
{** Currently works for the following object types: 'article' and 'wiki page' **}
{if empty($submenu) || $submenu neq 'y'}
	<div class="btn-group">
		{* For all object types: First show the world icon and on hover the language of the current object *}
		<a class="btn btn-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
			{icon name="translate"}
		</a>
{else}
	<a tabindex="-1" href="#">
		{icon name="translate"} {tr}Translation...{/tr}
	</a>
{/if}
	{* ..than on hover first show the list of translations including the current language highlighted *}
	{if empty($trads[0].lang)}
		<ul class="dropdown-menu dropdown-menu-right" role="menu">
			<li role="presentation" class="dropdown-title">
				{tr}No language assigned{/tr}
			</li>
			<li class="divider"></li>
			{if $object_type eq 'wiki page' and ($tiki_p_edit eq 'y' or (!$user and $prefs.wiki_encourage_contribution eq 'y')) and !$lock}
				<li role="presentation">
					<a role="menuitem" tabindex="-1" href="tiki-edit_translation.php?page={$page|escape}">
						{tr}Set page language{/tr}
					</a>
				</li>
			{elseif $object_type eq 'article' and $tiki_p_edit_article eq 'y'}
				<li role="presentation">
					<a role="menuitem" tabindex="-1" href="tiki-edit_article.php?articleId={$articleId|escape}">
						{tr}Set article language{/tr}
					</a>
				</li>
				<li class="divider"></li>
			{/if}
		</ul>
	{else}
		<ul class="dropdown-menu dropdown-menu-right" role="menu">
			{* First the language of the object *}
			<li role="presentation" class="dropdown-title">
				{tr}Current language{/tr}
			</li>
			<li class="divider"></li>
			{if $object_type eq 'wiki page'}
				<li role="presentation">
					<a role="menuitem" tabindex="-1" href="tiki-index.php?page={$trads[0].objName|escape}&no_bl=y" class="tips" title="{$trads[0].langName|escape} ({$trads[0].lang|escape}): {$trads[0].objName}" class="selected">
						{$trads[0].langName|escape} ({$trads[0].lang|escape})
					</a>
				</li>
			{elseif $object_type eq 'article'}
				<li role="presentation">
					<a tabindex="-1" role="menuitem" href="tiki-read_article.php?articleId={$trads[0].objId}" title="{$trads[0].langName|escape} ({$trads[0].lang|escape}): {$trads[0].objName}" class="selected">
						{$trads[0].langName|escape} ({$trads[0].lang|escape})
					</a>
				</li>
			{/if}
			{* Than the header for human translations - shown only if there is a translation available *}
			{if isset($trads) and count($trads) > 1}
				<li role="presentation" class="divider"></li>
				<li role="presentation" class="dropdown-title">
					{tr}Translations{/tr}
				</li>
			{/if}
			{* Show the list of available translations *}
			{section name=i loop=$trads}
				{* For wiki pages *}
				{if $object_type eq 'wiki page' and $trads[i] neq $trads[0]}
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="tiki-index.php?page={$trads[i].objName|escape}&no_bl=y" title="{$trads[i].langName|escape} ({$trads[i].lang|escape}): {$trads[i].objName}" class="linkmodule {$trads[i].class}">
						{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					</li>
				{/if}
				{* For articles *}
				{if $object_type eq 'article' and $trads[i] neq $trads[0]}
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="tiki-read_article.php?articleId={$trads[i].objId}" title="{$trads[i].langName|escape} ({$trads[i].lang|escape}): {$trads[i].objName}" class="linkmodule {$trads[i].class}">
							{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					</li>
				{/if}
			{/section}
			{* For wiki pages only: Show a link to view all translations on a single page *}
			{if $object_type eq 'wiki page' and $prefs.feature_multilingual_one_page eq 'y' and $translationsCount gt 1}
				<li role="presentation">
					<a role="menuitem" tabindex="-1" href="tiki-all_languages.php?page={$trads[0].objName|escape:url}&no_bl=y" title="{tr}Show all translations of this page on a single page{/tr}">
						{tr}All languages{/tr}
					</a>
				</li>
			{/if}
			{* For wiki pages only: List of machine translation candidates if feature is switched on *}
			{if $object_type eq 'wiki page' and $prefs.feature_machine_translation eq 'y'}
				<li role="presentation" class="divider"></li>
				<li role="presentation" class="dropdown-title">
					{tr}Machine translations{/tr}
				</li>
			{* List machine translation candidates for available language of the site *}
				{foreach from=$langsCandidatesForMachineTranslation item=mtl}
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="tiki-index.php?machine_translate_to_lang={$mtl.lang|escape}&page={$page|escape:"quotes"}&no_bl=y" title="{$mtl.langName|escape} ({$mtl.lang|escape})">
							{$mtl.langName|escape} *
						</a>
					</li>
				{/foreach}
			{/if}
			{* Translation maintenance *}
			{capture}
				{if $object_type eq 'wiki page' and $tiki_p_edit eq 'y'}
					<li role="presentation">
						<a role="menuitem" tabindex="-1" class="tips" href="tiki-edit_translation.php?page={$trads[0].objName|escape:url}&no_bl=y" title=":{tr}Translate page{/tr}">
							{tr}Translate{/tr}
						</a>
					</li>
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="{bootstrap_modal controller=translation action=manage type='wiki page' source=$page}" class="attach_detach_translation tips" data-object_type="wiki page" data-object_id="{$page|escape:'quotes'}" title=":{tr}Manage page translations{/tr}">
							{tr}Manage translations{/tr}
						</a>
					</li>
				{elseif $object_type eq 'article' and $tiki_p_edit_article eq 'y'}
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="tiki-edit_article.php?translationOf={$articleId}" title="{tr}Translate article{/tr}">
						{tr}Translate{/tr}
						</a>
					</li>
					<li role="presentation">
						<a role="menuitem" tabindex="-1" href="{bootstrap_modal controller=translation action=manage type=article source=$articleId}" class="attach_detach_translation" data-object_id="{$articleId|escape:'quotes'}" data-object_type="article" title="{tr}Manage article translations{/tr}">
							{tr}Manage translations{/tr}
						</a>
					</li>
				{/if}
			{/capture}
			{if !empty($smarty.capture.default)}{* Only display the header if there's content *}
				<li role="presentation" class="divider"></li>
				{$smarty.capture.default}
			{/if}
		</ul>
	{/if}
{if empty($submenu) || $submenu neq 'y'}
	</div>
{/if}
