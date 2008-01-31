{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-translation.tpl,v 1.1.2.5 2008-01-31 17:51:10 nkoth Exp $ *}

{if $show_translation_module}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Page translation{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="translation" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $mod_translation_better_known or $mod_translation_better_other}
		<div>			
			{if $mod_translation_better_known}
			{tr}Translations with more up-to-date content{/tr}:
			<ul>
				{foreach item=better from=$mod_translation_better_known}
				<li>
					<a href="tiki-index.php?page={$better.page|escape:'url'}">{$better.page}</a> ({$better.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={$page|escape:'url'}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update from it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{else}
				{tr}No translations with more up-to-date content match your{/tr} <a href="tiki-user_preferences.php">{tr}preferred languages{/tr}</a>.
			{/if}
			{if $mod_translation_better_other}
			<a href="javascript:void(0)" onclick="document.getElementById('mod-translation-better-ul').style.display='block';this.style.display='none'">{tr}More...{/tr}</a>
			<ul id="mod-translation-better-ul" style="display:none">
				{foreach item=better from=$mod_translation_better_other}
				<li>
					<a href="tiki-index.php?page={$better.page|escape:'url'}">{$better.page}</a> ({$better.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={if isset($stagingPageName)}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update from it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{/if}
		</div>
	{/if}
	{if $mod_translation_worst_known or $mod_translation_worst_other}
		<div>			
			{if $mod_translation_worst_known}
			{tr}Translations with less up-to-date content{/tr}:
			<ul>
				{foreach item=worst from=$mod_translation_worst_known}
				<li>
					<a href="tiki-index.php?page={$worst.page|escape:'url'}">{$worst.page}</a> ({$worst.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{else}
				{tr}No translations with less up-to-date content match your{/tr} <a href="tiki-user_preferences.php">{tr}preferred languages{/tr}</a>.
			{/if}
			{if $mod_translation_worst_other}
			<a href="javascript:void(0)" onclick="document.getElementById('mod-translation-worst-ul').style.display='block';this.style.display='none'">{tr}More...{/tr}</a>
			<ul id="mod-translation-worst-ul" style="display:none">
				{foreach item=worst from=$mod_translation_worst_other}
				<li>
					<a href="tiki-index.php?page={$worst.page|escape:'url'}">{$worst.page}</a> ({$worst.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{/if}
		</div>
	{/if}
	{if !$mod_translation_better_known and !$mod_translation_better_other and !$mod_translation_worst_known and !$mod_translation_worst_other}
		{tr}This page and all its translations are up to date.{/tr}
	{/if}
{/tikimodule}
{/if}