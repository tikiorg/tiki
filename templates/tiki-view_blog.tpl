{* $Id$ *}
{if !isset($show_heading) or $show_heading neq "n"}
	{if strlen($heading) > 0 and $prefs.feature_blog_heading eq 'y'}
		{eval var=$heading}
	{else}
		{include file='blog_heading.tpl'}
	{/if}
	{if $use_find eq 'y'}
		<div class="row row-sidemargins-zero">
			<div class="col-md-6">
				{include file='find.tpl' find_show_num_rows='y'}
			</div>
		</div>
	{/if}
{/if}

{if $excerpt eq 'y'}
	{assign "request_context" "excerpt"}
{else}
	{assign "request_context" "view_blog"}
{/if}

{foreach from=$listpages item=post_info}
	<article class="blogpost post panel panel-default {if !empty($container_class)} {$container_class}{/if}">
		{include file='blog_wrapper.tpl' blog_post_context=$request_context}
	</article>
{/foreach}

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}