{* $Id$ *}
<div class="clearfix postbody-title">
	{if $blog_data.use_title eq 'y'}
		<div class="title">
			<h2>{$post_info.title|escape}</h2>
		</div>
	{/if}
	{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y'}
		{if $tags.data|@count >0}
			<div class="freetaglist">
				 {tr}Tags:{/tr}&nbsp;
    			{foreach from=$tags.data item=tag}
					{capture name=tagurl}{if (strstr($tag.tag, ' '))}"{$tag.tag}"{else}{$tag.tag}{/if}{/capture}
					<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$tag.tag}</a> 
				{/foreach}
			</div>
		{/if}
	{/if}
</div>