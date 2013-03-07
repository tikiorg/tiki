<article class="article">
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
			{if $show_author eq 'y' && $authorName}{tr}Author:{/tr} {$authorName|escape} - {/if}
			{if $show_pubdate eq 'y' && $publishDate}{$publishDate|tiki_short_datetime:'Published At:'} - {/if}
			{if $show_expdate eq 'y' && $expireDate}{tr}Expires At:{/tr} {$expireDate|tiki_short_datetime} - {/if}
			{if $show_reads eq 'y'}({$reads} {tr}Reads{/tr}){/if}
		</span>
	</header>

	{if $use_ratings eq 'y'}
		<div class="articleheading">
			{tr}Rating:{/tr} 
			{repeat count=$rating}
				{icon _id='star' alt="{tr}star{/tr}"}
			{/repeat}
			{if $rating > $entrating}
				{icon _id='star_half' alt="{tr}half star{/tr}"}
			{/if}
			({$rating}/10)
		</div>
	{/if}
	{if !isset($preview) and $prefs.article_user_rating eq 'y' && $tiki_p_rate_article eq 'y'}
		<form method="post" action="">
			{rating type=article id=$articleId}
		</form>
	{/if}

	{if $prefs.art_trailer_pos ne 'between'}{include file='article_trailer.tpl'}{/if}

	<div class="articleheading">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
				{capture name=imgTitle}{if $show_image_caption eq 'y' and $image_caption}{$image_caption|escape}{elseif isset($topicName)}{tr}{$topicName}{/tr}{/if}{/capture}
				{assign var="big_image" value=$prefs.art_header_text_pos eq 'below' && $list_image_x > 0}
				{if $big_image}
					 <div class="imgbox" style="margin:auto; width:{$width}px">
				{/if}
				
				{* Show either a topic name, image OR a custom image (if there is a custom image or a topic). If a topic is set, link to it even if we show a custom image. *}
				{if $topicId}
					<a href="tiki-view_articles.php?topic={$topicId}" title="{if $show_image_caption and $image_caption}{$image_caption|escape}{else}{tr}List all articles of this same topic:{/tr} {tr}{$topicName|escape}{/tr}{/if}">
				{/if}
				{if $useImage eq 'y' and $hasImage eq 'y'}
					{* display article image *}{$style=''}
					<img 
						 {if $big_image}class="cboxElement"{elseif $isfloat eq 'y'}{$style="margin-right:4px;float:left;"}{else}class="articleimage"{/if}
						 alt="{$smarty.capture.imgTitle}"
						 src="article_image.php?image_type={if isset($preview) and $imageIsChanged eq 'y'}preview&amp;id={$previewId}{elseif isset($preview) and $subId}submission&amp;id={$subId}{else}article&amp;id={$articleId}{/if}"
						 {if $image_x > 0}{$style=$style|cat:"max-width:"|cat:$image_x|cat:"px;"}{/if}
						 {if $image_y > 0}{$style=$style|cat:"max-height:"|cat:$image_y|cat:"px;"}{/if} style="{$style}" />
				{elseif $topicId}
						{if $topics[$topicId].image_size > 0}
							<img 
								 {if $big_image}class="cboxElement"{elseif $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if}
								 alt="{tr}{$topicName}{/tr}"
								 src="article_image.php?image_type=topic&amp;id={$topicId}" />
					{else}
						{tr}{$topics[$topicId].name|escape}{/tr}
					{/if}
				{/if}
				{if $topicId}</a>{/if}
				
				{if $big_image}
					{if $show_image_caption eq 'y' and $image_caption || $image_x > 0}
						<div class="mini thumbcaption">
						{if $image_x > 0}<div class="magnify"><a class="internal cboxElement" rel="box" href="article_image.php?image_type=article&amp;id={$articleId}">{icon _id='magnifier' title=$smarty.capture.imgTitle}</a></div>{/if}
						{if $show_image_caption eq 'y' and $image_caption}{$image_caption|escape}{else}&nbsp;{/if}
						</div>
					{/if}
					</div>
				{/if}
				{if  $prefs.art_header_text_pos eq 'below' && $list_image_x > 0}
					 </td></tr><tr><td valign="top">
				{elseif $isfloat eq 'n'}
					</td>
					<td valign="top" width="100%">
				{/if}
				<div class="articleheadingtext">
					{if $article_attributes}
						<div class="articleattributes">
							{foreach from=$article_attributes key=attname item=attvalue}
							{$attname|escape}: {$attvalue|escape}<br />
							{/foreach}
						</div>
					{/if}
					{$parsed_heading}
				</div>
				</td>
			</tr>
		</table>
	</div>

	{if $prefs.art_trailer_pos eq 'between'}{include file='article_trailer.tpl'}{/if}

	<div class="articlebody clearfix">
		{if $tiki_p_read_article eq 'y'}
			{$parsed_body}
		{else}
			<div class="error simplebox">
				{tr}You do not have permission to read complete articles.{/tr}
			</div>
		{/if}
	
		{if $prefs.article_paginate eq 'y' and $pages > 1}
			<div align="center">
				<a href="{$articleId|sefurl:article:with_next}page={$first_page}"><img src='img/icons/resultset_first.png' alt="{tr}First page{/tr}" title="{tr}First page{/tr}" width='16' height='16' /></a>

				<a href="{$articleId|sefurl:article:with_next}page={$prev_page}">{icon _id='resultset_previous' alt="{tr}Previous page{/tr}"}</a>

				<small>{tr}page:{/tr}{$pagenum}/{$pages}</small>

				<a href="{$articleId|sefurl:article:with_next}page={$next_page}">{icon _id='resultset_next' alt="{tr}Next page{/tr}"}</a>

				<a href="{$articleId|sefurl:article:with_next}page={$last_page}"><img src='img/icons/resultset_last.png' alt="{tr}Last page{/tr}" title="{tr}Last page{/tr}" width='16' height='16' ></a>
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
		<footer class="editdate">
			{$smarty.capture.copyright_section}
		</footer>
	{/if}
</article>
