{* $Id$ *}
<div class="clearfix postbody-title">
	<div class="title">
		<h2>{$post_info.title|escape}</h2>
	</div>
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
		{if $tags.data|@count >0}
			<div class="freetaglist">
				{tr}Tags:{/tr}&nbsp;
    		{foreach from=$tags.data item=tag}
					{if isset($preview) and $preview eq 'y'}
						<a class="freetag" href="#">{$tag.tag}</a>
					{else}
						{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
						<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag}</a>
					{/if}
				{/foreach}
			</div>
		{/if}
	{/if}
</div>
