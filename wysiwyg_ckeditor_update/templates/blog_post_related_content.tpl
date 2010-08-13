{* $Id$ *}

{if !empty($post_info.related_posts)}
	<div class="related_posts">
		<hr>
		<h3>{tr}Related content:{/tr}</h3>
		<ul>	
			{foreach from=$post_info.related_posts item=related}
				<li>{self_link postId=$related.postId}{$related.name}{/self_link}</li>
			{/foreach}
		</ul>
	</div>
{/if}
