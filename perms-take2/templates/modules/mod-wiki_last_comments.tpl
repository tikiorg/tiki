{* $Id$ *}

{if ($module_params.type eq 'wiki page' and $prefs.feature_wiki eq 'y')
	or ($module_params.type eq 'article' and $prefs.feature_articles eq 'y')}
	{if !isset($tpl_module_title)}
		{if $nonums eq 'y'}
			{if $module_params.type eq 'wiki page'}
				{eval var='{tr}Last `$module_rows` wiki comments{/tr}' assign='tpl_module_title'}
			{elseif $module_params.type eq 'article'}
				{eval var='{tr}Last `$module_rows` article comments{/tr}' assign='tpl_module_title'}
			{else}
				{eval var='{tr}Last `$module_rows` comments{/tr}' assign='tpl_module_title'}
			{/if}			
		{else}
			{if $module_params.type eq 'wiki page'}
				{eval var='{tr}Last wiki comments{/tr}' assign='tpl_module_title'}
			{elseif $module_params.type eq 'article'}
				{eval var='{tr}Last article comments{/tr}' assign='tpl_module_title'}
			{else}
				{eval var='{tr}Last comments{/tr}' assign='tpl_module_title'}
			{/if}
		{/if}
	{/if}
	{tikimodule error=$module_params.error title=$tpl_module_title name="wiki_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if $nonums != 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$comments}
			<li><a class="linkmodule" href="{$comments[ix].object|sefurl:$module_params.type}&amp;comzone=show#threadId{$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$comments[ix].userName}{if $module_params.moretooltips eq 'y'} {tr}on page{/tr} {$comments[ix].name}{/if}">
				   {if $module_params.moretooltips ne 'y'}<strong>{$comments[ix].name|escape}</strong>: {/if}
				   {$comments[ix].title|escape}
			</a></li>
		{/section}
		{if $nonums != 'y'}</ol>{else}</ul>{/if}
	{/tikimodule}
{/if}
