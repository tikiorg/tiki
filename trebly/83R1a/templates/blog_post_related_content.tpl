{* $Id: blog_post_related_content.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{if isset($post_info.related_posts) && !empty($post_info.related_posts)}
	<div class="related_posts">
		<h4>{tr}Related content:{/tr}</h4>
		<ul>	
			{foreach from=$post_info.related_posts item=related}
				<li>{self_link postId=$related.postId}{$related.name}{/self_link}</li>
			{/foreach}
		</ul>
	</div>
{/if}
