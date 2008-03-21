{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-translation.tpl,v 1.1.2.11 2008-03-21 12:23:53 ricks99 Exp $ *}

{if $show_translation_module}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Page translation{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="translation" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $trads|@count eq '1'}<p>{tr}There are no translations of this page.{/tr}<p>{/if}
	{if $prefs.quantify_changes eq 'y'}
		<div>
			{tr}Up-to-date-ness{/tr}: {$mod_translation_quantification}%
		</div>
	{/if}
	{if $mod_translation_better_known or $mod_translation_better_other}
		<div>			
			{tr}Better translations{/tr}:
			{if $mod_translation_better_known}
			<ul>
				{foreach item=better from=$mod_translation_better_known}
				<li>
					<a href="tiki-index.php?page={$better.page|escape:'url'}" title="{$better.page}">{$better.page}</a> ({$better.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update from it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{else}
{if $prefs.change_language eq 'y'}
{* only show if users can set a preferred language *}
				<div id="mod-translation-better-intro" style="display:block">{tr}None match your{/tr} <a href="tiki-user_preferences.php" title="{tr}Set your preferred languages.{/tr}">{tr}preferred languages{/tr}</a>.</div>
{/if}
			{/if}
			{if $mod_translation_better_other}
{if $prefs.change_language eq 'y'}
			<a href="javascript:void(0)" onclick="document.getElementById('mod-translation-better-intro').style.display='none';document.getElementById('mod-translation-better-ul').style.display='block';this.style.display='none'" class="linkmenu">{icon _id=plus_small.png alt="{tr}More...{/tr}" width="11" height="8" style="vertical-align:middle;border:0"} {tr}More...{/tr}</a>
{/if}
			<ul id="mod-translation-better-ul"{if $prefs.change_language eq 'y'} style="display:none"{/if}>
				{foreach item=better from=$mod_translation_better_other}
				<li>
					<a href="tiki-index.php?page={$better.page|escape:'url'}" title="{$better.page}">{$better.page}</a> ({$better.lang})
					{if $tiki_p_edit}
					<div>
						- <a href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style=inlinediff-full">{tr}update from it{/tr}</a>
					</div>
					{/if}
				</li>
				{/foreach}
			</ul>
			{/if}
		</div><br />
	{/if}
	{if $mod_translation_equivalent_known or $mod_translation_equivalent_other}
		<div>			
			{tr}Equivalent translations{/tr}:
			{if $mod_translation_equivalent_known}
			<ul>
				{foreach item=equiv from=$mod_translation_equivalent_known}
				<li>
					<a href="tiki-index.php?page={$equiv.page|escape:'url'}" title="{$equiv.page}">{$equiv.page}</a> ({$equiv.lang})
				</li>
				{/foreach}
			</ul>
			{else}
{if $prefs.change_language eq 'y'}
				<div id="mod-translation-equiv-intro" style="display:block">{tr}None match your{/tr} <a href="tiki-user_preferences.php" title="{tr}Set your preferred languages.{/tr}">{tr}preferred languages{/tr}</a>.</div>
{/if}
			{/if}
			{if $mod_translation_equivalent_other}
{if $prefs.change_language eq 'y'}
			<a href="javascript:void(0)" onclick="document.getElementById('mod-translation-equiv-intro').style.display='none';document.getElementById('mod-translation-equiv-ul').style.display='block';this.style.display='none'" class="linkmenu">{icon _id=plus_small.png alt="{tr}More...{/tr}" width="11" height="8" style="vertical-align:middle;border:0"} {tr}More...{/tr}</a>
{/if}
			<ul id="mod-translation-equiv-ul"{if $prefs.change_language eq 'y'} style="display:none"{/if}>
				{foreach item=equiv from=$mod_translation_equivalent_other}
				<li>
					<a href="tiki-index.php?page={$equiv.page|escape:'url'}" title="{$equiv.page}">{$equiv.page}</a> ({$equiv.lang})
				</li>
				{/foreach}
			</ul>
			{/if}
		</div><br />
	{/if}
	{if $mod_translation_worst_known or $mod_translation_worst_other}
		<div>			
			{tr}Translations that need improvement{/tr}:
			{if $mod_translation_worst_known}
			<ul>
				{foreach item=worst from=$mod_translation_worst_known}
				<li>
					{if $tiki_p_edit}
						<a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style=inlinediff-full">{icon _id=page_edit.png alt="{tr}update it{/tr}" style="vertical-align:middle"}</a>
					{/if}
					<a href="tiki-index.php?page={$worst.page|escape:'url'}" title="{$worst.page}" title="{$worst.page}">{$worst.page}</a> ({$worst.lang})
				</li>
				{/foreach}
			</ul>
			{else}
{if $prefs.change_language eq 'y'}
				<div id="mod-translation-worst-intro" style="display:block">{tr}None match your{/tr} <a href="tiki-user_preferences.php">{tr}preferred languages{/tr}</a>.</div>
{/if}
			{/if}
			{if $mod_translation_worst_other}
{if $prefs.change_language eq 'y'}
			<a href="javascript:void(0)" onclick="{if $prefs.change_language eq 'y'}document.getElementById('mod-translation-worst-intro').style.display='none';{/if}document.getElementById('mod-translation-worst-ul').style.display='block';this.style.display='none'" class="linkmenu">{icon _id=plus_small.png alt="{tr}More...{/tr}" width="11" height="8" style="vertical-align:middle;border:0"}{tr}More...{/tr}</a>
{/if}
			<ul id="mod-translation-worst-ul"{if $prefs.change_language eq 'y'} style="display:none"{/if}>
				{foreach item=worst from=$mod_translation_worst_other}
				<li>
					{if $tiki_p_edit}
						<a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style=inlinediff-full">{icon _id=page_edit.png alt="{tr}update it{/tr}" style="vertical-align:middle"}</a>
					{/if}
					<a href="tiki-index.php?page={$worst.page|escape:'url'}" title="{$worst.page}">{$worst.page}</a> ({$worst.lang})
				</li>
				{/foreach}
			</ul>
			{/if}
		</div>
	{/if}
{/tikimodule}
{/if}
