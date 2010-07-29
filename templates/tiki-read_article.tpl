{* $Id$ *}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
	<div align="right">{$display_catpath}</div>
{/if}

{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0]) and $prefs.freetags_show_middle eq 'y'}
	{include file='freetag_list.tpl'}
{/if}

<div class="article">
	{if $show_topline eq 'y' and $topline}
		<div class="articletopline">{$topline|escape}</div>
	{/if}
	<div class="articletitle">
		<h2>
			{$arttitle|escape}
		</h2>
		{if $show_subtitle eq 'y' and $subtitle}
			<div class="articlesubtitle">{$subtitle|escape}</div>
		{/if}
		
		<span class="titleb">
			{if $show_author eq 'y' && $authorName}{tr}By:{/tr} {$authorName|escape} {/if}
			{if $show_pubdate eq 'y' && $publishDate}{tr}on:{/tr} {$publishDate|tiki_short_datetime} {/if}
			{if $show_expdate eq 'y' && $expireDate}{tr}expires:{/tr} {$expireDate|tiki_short_datetime} {/if}
			{if $show_reads eq 'y'}({$reads} {tr}Reads{/tr}){/if}
		</span>
		<br />
	</div>

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
	{if $prefs.article_user_rating eq 'y' && $tiki_p_rate_article eq 'y'}
		<form method="post" action="">
			{rating type=article id=$articleId}
		</form>
	{/if}

	{if $prefs.art_trailer_pos ne 'between'}{include file='article_trailer.tpl}{/if}
	<div class="articleheading">
		<table cellpadding="0" cellspacing="0">
			<tr>
				{if $isfloat eq 'n'}
					<td valign="top">
				{else}
					<td valign="top">
				{/if}
				
					{if $useImage eq 'y'}
						{if $hasImage eq 'y'}
							<a href="tiki-view_articles.php?topic={$topicId}" title="{if $show_image_caption and $image_caption}{$image_caption}{else}{tr}List all articles of this same topic{/tr}: {$topics[$topicId].name|escape}{/if}"><img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} alt="{if $show_image_caption and $image_caption}{$image_caption}{else}{tr}List all articles of this same topic{/tr}{/if}" src="article_image.php?image_type=article&amp;id={$articleId}"{if $image_x > 0} width="{$image_x}"{/if}{if $image_y > 0 } height="{$image_y}"{/if} /></a>
						{else}
								<a class="link" href="tiki-view_articles.php?topic={$topicId}" title="{tr}List all articles of this same topic{/tr}">{$topics[$topicId].name|escape}</a>
						{/if}
					{else}
						{section name=it loop=$topics}
							{if ($topics[it].topicId eq $topicId) and ($topics[it].image_size > 0)}
								<a class="link" href="tiki-view_articles.php?topic={$topics[it].topicId}" title="{tr}List all articles of this same topic{/tr}"><img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} alt="{$topicName}" src="article_image.php?image_type=topic&amp;id={$topicId}" /></a>
							{/if}
						{/section}
					{/if}
					{if $isfloat eq 'n'}
						</td>
						<td valign="top">
					{/if}
					<div class="articleheadingtext">
						{if $article_attributes}
						<div class="articleattributes">
							{foreach from=$article_attributes key=attname item=attvalue}
							{tr}{$attname|escape}{/tr}: {$attvalue|escape}<br />
							{/foreach}
						</div>
						{/if}
						{$parsed_heading}
					</div>
				</td>
			</tr>
		</table>
	</div>

	{if $prefs.art_trailer_pos eq 'between'}{include file='article_trailer.tpl}{/if}

	<div class="articlebody">
		{if $tiki_p_read_article eq 'y'}
			{$parsed_body}
		{else}
			<div class="error simplebox">
				{tr}Permission denied. You do not have permission to read complete articles.{/tr}
			</div>
		{/if}
	
		{if $pages > 1}
			<div align="center">
				<a href="{$articleId|sefurl:article:with_next}page={$first_page}"><img src='pics/icons/resultset_first.png' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' width='16' height='16' /></a>

				<a href="{$articleId|sefurl:article:with_next}page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>

				<small>{tr}page:{/tr}{$pagenum}/{$pages}</small>

				<a href="{$articleId|sefurl:article:with_next}page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>

				<a href="{$articleId|sefurl:article:with_next}page={$last_page}"><img src='pics/icons/resultset_last.png' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' width='16' height='16' ></a>
			</div>
		{/if}
	</div>

	{if $show_linkto eq 'y' and $linkto}
		<div class="articlesource">
			{tr}Source:{/tr} <a href="{$linkto}"{if $prefs.popupLinks eq 'y'} target="_blank"{/if}>{$linkto}</a>
		</div>
	{/if}

	{capture name='copyright_section'}
		{include file='show_copyright.tpl'}
	{/capture}

	{* When copyright section is not empty show it *}
	{if $smarty.capture.copyright_section neq ''}
		<p class="editdate">
			{$smarty.capture.copyright_section}
		</p>
	{/if}
</div>

{if $prefs.feature_article_comments == 'y' && 
		(($tiki_p_read_comments == 'y' && $comments_cant != 0) || $tiki_p_post_comments == 'y' || $tiki_p_edit_comments == 'y')}

	<div id="page-bar" class="clearfix">
		{include file='comments_button.tpl'}
	</div>

	{include file='comments.tpl'}
{/if}

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
	{$display_catobjects}
{/if}

