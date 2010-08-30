{* $Id$ *}
<div class="clearfix postbody-title">
	<div class="title"> {* because used in forums, but I don't know purpose *}
		<h2><a class="link" href="{$listpages[ix].postId|sefurl:blogpost}">{$listpages[ix].title|escape}</a></h2>
	</div>

	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
		{if $listpages[ix].freetags.data|@count >0}
			<div class="freetaglist">{tr}Tags{/tr}:
				{foreach from=$listpages[ix].freetags.data item=taginfo}
					{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
					<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag|escape}</a>
				{/foreach}
			</div>
		{/if}
	{/if}
</div>
