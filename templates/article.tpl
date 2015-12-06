<article class="article">
    {if $prefs.art_trailer_pos ne 'between'}{include file='article_trailer.tpl'}{/if}
	{if $show_topline eq 'y' and $topline}
		<div class="articletopline">{$topline|escape}</div>
	{/if}
	<header class="articletitle">
		<h2>
			{object_link type=article id=$articleId title=$arttitle}
		</h2>
		{if $show_subtitle eq 'y' and $subtitle}
			<div class="articlesubtitle">{$subtitle|escape}</div>
		{/if}

		<span class="titleb">
			{if $show_author eq 'y' && ($authorName or $author)}{tr}Author:{/tr} {if $authorName}{$authorName|escape}{else}{$author|username}{/if}
				{if $show_pubdate eq 'y' || $show_expdate eq 'y' || $show_reads eq 'y'} - {/if}
			{/if}
			{if $show_pubdate eq 'y' && $publishDate}{$publishDate|tiki_short_datetime:'Published At:'}
				{if $show_expdate eq 'y' || $show_reads eq 'y'} - {/if}
			{/if}
			{if $show_expdate eq 'y' && $expireDate}{tr}Expires At:{/tr} {$expireDate|tiki_short_datetime}
				{if $show_reads eq 'y'} - {/if}
			{/if}
			{if $show_reads eq 'y'}({$reads} {tr}Reads{/tr}){/if}
		</span>
		{if $comment_can_rate_article eq 'y' and $prefs.article_user_rating eq 'y' && ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
			- <span class="ratingResultAvg">{tr}Users rating: {/tr}</span>{rating_result_avg id=$articleId type=article}
		{/if}
	</header>

	{if $use_ratings eq 'y'}
		<div class="articlerating">
			{tr}Rating:{/tr}
			{repeat count=$rating}
				{icon name='star'}
			{/repeat}
			{if $rating > $entrating}
				{icon name='star-half'}
			{/if}
			({$rating}/10)
		</div>
	{/if}
	{if $author ne $user and $comment_can_rate_article eq 'y' and !isset($preview) and $prefs.article_user_rating eq 'y' and ($tiki_p_rate_article eq 'y' or $tiki_p_admin_cms eq 'y')}
		<form method="post" action="">
			{rating type=article id=$articleId}
		</form>
	{/if}
	{if $comment_can_rate_article eq 'y' and $prefs.article_user_rating eq 'y' && ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
		{rating_result id=$articleId type=article}
	{/if}

{*	{if $prefs.art_trailer_pos ne 'between'}{include file='article_trailer.tpl'}{/if} *}

	<div class="articleheading row">

		<div class="col-xs-6 col-sm-5 col-md-3{if $isfloat eq 'y'} pull-left{/if}{if $useImage eq 'y' and $hasImage eq 'n'} well well-sm{/if}">
			{capture name=imgTitle}{if $show_image_caption eq 'y' and $image_caption}{$image_caption|escape}{elseif isset($topicName)}{tr}{$topicName}{/tr}{/if}{/capture}
			{assign var="big_image" value=$prefs.art_header_text_pos eq 'below' && $list_image_x > 0}
			{if $big_image}
			<div class="imgbox" style="margin:auto; width:{$width}px">
			{/if}

			{* Show either a topic name, image OR a custom image (if there is a custom image or a topic). If a topic is set, link to it even if we show a custom image. *}
			{if $topicId}
				<a href="tiki-view_articles.php?topic={$topicId}" class="{if $useImage eq 'y' and $hasImage neq 'y'}{else}thumbnail{/if}{if $big_image} cboxElement{/if}" title="{if $show_image_caption and $image_caption}{$image_caption|escape}{else}{tr}List all articles of the same topic{/tr}{if $topicName ne ''} ({tr}{$topicName|escape}{/tr}){/if}{/if}"
			>{/if}
			{if $useImage eq 'y'}
				{if $hasImage eq 'y'}
				{* display own article image *}
				<img
					alt="{$smarty.capture.imgTitle}"
					src="article_image.php?image_type={if isset($preview) and $imageIsChanged eq 'y'}preview&amp;id={$previewId}{elseif isset($preview) and $subId}submission&amp;id={$subId}{else}article&amp;id={$articleId}{if $image_x > 0}&width={$image_x}{/if}{/if}"
					{$style=''}
					{if $image_y > 0}{$style=$style|cat:"max-height:"|cat:$image_y|cat:"px;"}{/if} style="{$style}"
				>
				{else}
				{* display just the topic name *}
				{$topicName|escape}
				{/if}
			{elseif $topicId}
				{if $topics[$topicId].image_size > 0}
				<img
					{if $big_image}class="cboxElement"{/if}
					alt="{tr}{$topicName}{/tr}"
					src="article_image.php?image_type=topic&amp;id={$topicId}"
				>
				{else}
					{tr}{$topics[$topicId].name|escape}{/tr}
				{/if}
			{/if}
			{if $topicId}</a>{/if}

			{if $big_image}
				{if $show_image_caption eq 'y' and $image_caption || $image_x > 0}
					<div class="center-block thumbcaption">
						{if $image_x > 0}<div class="magnify"><a class="internal cboxElement" data-box="box" href="article_image.php?image_type=article&amp;id={$articleId}">{icon name='view' title=$smarty.capture.imgTitle}</a></div>{/if}
						{if $show_image_caption eq 'y' and $image_caption}{$image_caption|escape}{else}&nbsp;{/if}
					</div>
				{/if}
			</div> {* class="imgbox" *}
			{/if}
		</div>
			{if $prefs.art_header_text_pos eq 'below' && $list_image_x > 0}
		<div class="col-xs-12">
			{elseif $isfloat eq 'n' and $topics[$topicId].image_size > 0}
		<div class="col-xs-6 col-sm-7 col-md-9">
			{else}
		<div class="col-sm-7 col-md-9">
			{/if}
			<div class="articleheadingtext">
				{if $article_attributes}
					<div class="articleattributes">
						{foreach from=$article_attributes key=attname item=attvalue}
							{$attname|escape}: {$attvalue|escape}<br>
						{/foreach}
					</div>
				{/if}
				{$parsed_heading}
			</div>
		</div>

	</div>

	{if $prefs.art_trailer_pos eq 'between'}{include file='article_trailer.tpl'}{/if}
	<div class="articlebody clearfix">
		{if $tiki_p_read_article eq 'y'}
			{$parsed_body}
		{else}
			<div class="alert alert-danger">
				{tr}You do not have permission to read complete articles.{/tr}
			</div>
		{/if}

		{if $prefs.article_paginate eq 'y' and $pages > 1}
			<div align="center">
				<a href="{$articleId|sefurl:article:with_next}page={$first_page}" class="tips" title=":{tr}First page{/tr}">
					{icon name="backward_step"}
				</a>

				<a href="{$articleId|sefurl:article:with_next}page={$prev_page}" class="tips" title=":{tr}Previous page{/tr}">
					{icon name='backward'}
				</a>

				<small>{tr}page:{/tr}{$pagenum}/{$pages}</small>

				<a href="{$articleId|sefurl:article:with_next}page={$next_page}" class="tips" title=":{tr}Next page{/tr}">
					{icon name='forward'}
				</a>

				<a href="{$articleId|sefurl:article:with_next}page={$last_page}" class="tips" title=":{tr}Last page{/tr}">
					{icon name='forward_step'}
				</a>
			</div>
		{/if}
	</div>

	{if $show_linkto eq 'y' and $linkto}
		<div class="articlesource">
			{tr}Source:{/tr} <a href="{$linkto|escape}"{if $prefs.popupLinks eq 'y'} target="_blank"{/if}>{$linkto|escape}</a>
		</div>
	{/if}

	{if isset($related_articles)}
		<div class="related_articles">
			<h4>{tr}Related content:{/tr}</h4>
			<ul>
				{foreach from=$related_articles item=related}
					<li>{self_link articleId=$related.articleId}{$related.name}{/self_link}</li>
				{/foreach}
			</ul>
		</div>
	{/if}

	{capture name='copyright_section'}
		{include file='show_copyright.tpl'}
	{/capture}

	{* When copyright section is not empty show it *}
	{if $smarty.capture.copyright_section neq ''}
		<footer class="help-block">
			{$smarty.capture.copyright_section}
		</footer>
	{/if}
</article>
