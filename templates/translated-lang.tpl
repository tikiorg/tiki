{* Display the list of available translations for an object and manage its translations *}
{** Currently works for the following object types: 'article' and 'wiki page' **}
{if $prefs.javascript_enabled != 'y'}
	{$js = 'n'}
{else}
	{$js = 'y'}
{/if}

{if empty($submenu) || $submenu neq 'y'}
	<div class="btn-group">
		{* For all object types: First show the translate icon and on hover the language of the current object *}
		{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
		<a class="btn btn-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
			{icon name="translate"}
		</a>
{else}
	{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
	<a tabindex="-1" href="#">
		{icon name="translate"} {tr}Translation...{/tr}
	</a>
{/if}
	{* ..than on hover first show the list of translations including the current language highlighted *}
	{if empty($trads[0].lang)}
		<ul class="dropdown-menu dropdown-menu-right" role="menu">
			<li class="dropdown-header">
				{tr}No language assigned{/tr}
			</li>
			<li role="separator" class="divider"></li>
			{if $object_type eq 'wiki page' and ($tiki_p_edit eq 'y' or (!$user and $prefs.wiki_encourage_contribution eq 'y')) and !$lock}
				<li>
					<a href="tiki-edit_translation.php?page={$page|escape}">
						{tr}Set page language{/tr}
					</a>
				</li>
			{elseif $object_type eq 'article' and $tiki_p_edit_article eq 'y'}
				<li>
					<a href="tiki-edit_article.php?articleId={$articleId|escape}">
						{tr}Set article language{/tr}
					</a>
				</li>
				<li role="separator" class="divider"></li>
			{/if}
		</ul>
	{else}
		<ul class="dropdown-menu dropdown-menu-right" role="menu">
			{* First the language of the object *}
			{if $object_type eq 'wiki page'}
				<li>
					<a href="tiki-index.php?page={$trads[0].objName|escape}&no_bl=y" class="tips selected" title="{tr}Current language{/tr}: {$trads[0].objName}">
						<em>{$trads[0].langName|escape} ({$trads[0].lang|escape})</em>
					</a>
				</li>
			{elseif $object_type eq 'article'}
				<li>
					<a href="tiki-read_article.php?articleId={$trads[0].objId}" title="{tr}Current language{/tr}: {$trads[0].objName}" class="tips selected">
						<em>{$trads[0].langName|escape} ({$trads[0].lang|escape})</em>
					</a>
				</li>
			{/if}
			{* Show the list of available translations *}
			{section name=i loop=$trads}
				{* For wiki pages *}
				{if $object_type eq 'wiki page' and $trads[i] neq $trads[0]}
					<li>
						<a href="tiki-index.php?page={$trads[i].objName|escape}&no_bl=y" title="{tr}View{/tr}: {$trads[i].objName}" class="tips {$trads[i].class}">
							{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					</li>
				{/if}
				{* For articles *}
				{if $object_type eq 'article' and $trads[i] neq $trads[0]}
					<li>
						<a href="tiki-read_article.php?articleId={$trads[i].objId}" title="{tr}View{/tr}: {$trads[i].objName}" class="tips {$trads[i].class}">
							{$trads[i].langName|escape} ({$trads[i].lang|escape})
						</a>
					</li>
				{/if}
			{/section}
			{* For wiki pages only: Show a link to view all translations on a single page *}
			{if $object_type eq 'wiki page' and $prefs.feature_multilingual_one_page eq 'y' and $translationsCount gt 1}
				<li role="separator" class="divider"></li>
				<li>
					<a href="tiki-all_languages.php?page={$trads[0].objName|escape:url}&no_bl=y" title=":{tr}Show all translations of this page on a single page{/tr}" class="tips">
						{tr}All languages{/tr}
					</a>
				</li>
			{/if}
			{* For wiki pages only: List of machine translation candidates if feature is switched on *}
			{if $object_type eq 'wiki page' and $prefs.feature_machine_translation eq 'y'}
				<li role="separator" class="divider"></li>
				<li class="dropdown-header">
					{tr}Machine translations{/tr}
				</li>
			{* List machine translation candidates for available language of the site *}
				{foreach from=$langsCandidatesForMachineTranslation item=mtl}
					<li>
						<a href="tiki-index.php?machine_translate_to_lang={$mtl.lang|escape}&page={$page|escape:"quotes"}&no_bl=y" title="{$mtl.langName|escape} ({$mtl.lang|escape})" class="tips">
							{$mtl.langName|escape} *
						</a>
					</li>
				{/foreach}
			{/if}
			{* Translation maintenance *}
			{capture}
				{if $object_type eq 'wiki page' and $tiki_p_edit eq 'y'}
					<li role="separator" class="divider"></li>
					<li>
						<a class="tips" href="tiki-edit_translation.php?page={$trads[0].objName|escape:url}&no_bl=y" title=":{tr}Translate page{/tr}">
							{tr}Translate{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=translation action=manage type='wiki page' source=$page}" class="attach_detach_translation tips" data-object_type="wiki page" data-object_id="{$page|escape:'quotes'}" title=":{tr}Manage page translations{/tr}">
							{tr}Manage translations{/tr}
						</a>
					</li>
				{elseif $object_type eq 'article' and $tiki_p_edit_article eq 'y'}
					<li role="separator" class="divider"></li>
					<li>
						<a href="tiki-edit_article.php?translationOf={$articleId}" title="{tr}Translate article{/tr}">
						{tr}Translate{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=translation action=manage type=article source=$articleId}" class="attach_detach_translation tips" data-object_id="{$articleId|escape:'quotes'}" data-object_type="article" title="{tr}Manage article translations{/tr}">
							{tr}Manage translations{/tr}
						</a>
					</li>
				{/if}
			{/capture}
			{if !empty($smarty.capture.default)}{* Only display the header if there's content *}
				{$smarty.capture.default}
			{/if}
		</ul>
	{/if}
	{if $js == 'n'}</li></ul>{/if}
{if empty($submenu) || $submenu neq 'y'}
	</div>
{/if}
