{* $Id$ *}

{if ($type eq 'wiki page' and $prefs.feature_wiki eq 'y')
	or ($type eq 'article' and $prefs.feature_articles eq 'y')}
	{tikimodule error=$module_params.error title=$tpl_module_title name="wiki_last_comments" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $module_params.avatars eq 'y'}{$nonums = 'y'}{/if}
	{modules_list list=$comments nonums=$nonums}
		{section name=ix loop=$comments}
			<li{if $module_params.avatars eq 'y'} style="list-style-type: none;"{/if}>
				{if $module_params.avatars eq 'y'}
					{$comments[ix].userName|avatarize:'right'}
				{/if}
				<a class="linkmodule tips" href="{$comments[ix].object|sefurl:$type:with_next}comzone=show#threadId={$comments[ix].threadId}" title="{$comments[ix].commentDate|tiki_short_datetime}| {tr}by{/tr} {$comments[ix].userName|username}{if $moretooltips eq 'y'} {tr}on{/tr} {$comments[ix].name|escape}{/if}">
					{if $moretooltips ne 'y'}
						<strong>{$comments[ix].name|escape}</strong>:
					{/if}
					{$comments[ix].title|escape}
				</a>
			</li>
		{/section}
	{/modules_list}
	{/tikimodule}
{/if}
