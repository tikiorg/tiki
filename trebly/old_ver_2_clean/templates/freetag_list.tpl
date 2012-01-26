{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
	<div class="freetaglist">{tr}Tags:{/tr} 
		{foreach from=$freetags.data item=taginfo}
			{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
			{if isset($links_inactive) and $links_inactive eq 'y'}
				<a class="freetag" href="#">{$taginfo.tag|escape}</a>
			{else}
				<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag|escape}</a>{if isset($deleteTag) and $tiki_p_admin eq 'y'}<a title="{tr}Untag{/tr} {$taginfo.tag|escape}" href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}delTag={$taginfo.tag|escape:'url'}">{icon _id=cross alt="{tr}Untag{/tr}"}</a>&nbsp;{/if}
			{/if}
		{/foreach}
		{if $freetags_mixed_lang}
			(<a href="{$freetags_mixed_lang}">{tr}Translate tags{/tr}</a>)
		{/if}
	</div>
{/if}

