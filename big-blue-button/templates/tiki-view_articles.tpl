{if $prefs.art_home_title ne ''}
	{title help="Articles" admpage="cms"}
		{if $prefs.art_home_title eq 'topic' and !empty($topic)}{tr}{$topic|escape}{/tr}
		{elseif $prefs.art_home_title eq 'type' and !empty($type)}{tr}{$type|escape}{/tr}
		{else}{tr}Articles{/tr}{/if}
	{/title}
{/if}
{section name=ix loop=$listpages}
	{if $listpages[ix].disp_article eq 'y'}
		{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and $listpages[ix].freetags.data|@count >0}
			<div class="freetaglist">
				{foreach from=$listpages[ix].freetags.data item=taginfo}
				{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
				<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag}</a>
				{/foreach}
			</div>
		{/if} 
		<div class="article{if !empty($container_class)} {$container_class}{/if}">
			{if $listpages[ix].show_topline eq 'y' and $listpages[ix].topline}<div class="articletopline">{$listpages[ix].topline|escape}</div>{/if}
			<div class="articletitle">
				<h2><a href="{$listpages[ix].articleId|sefurl:article}">{$listpages[ix].title|escape}</a></h2>
				{if $listpages[ix].show_subtitle eq 'y' and $listpages[ix].subtitle}<div class="articlesubtitle">{$listpages[ix].subtitle|escape}</div>{/if}
				{if ($listpages[ix].show_author eq 'y')
				 or ($listpages[ix].show_pubdate eq 'y')
				 or ($listpages[ix].show_expdate eq 'y')
				 or ($listpages[ix].show_reads eq 'y')}	
					<span class="titleb">
						{if $listpages[ix].show_author eq 'y'}	
							{tr}By:{/tr} {$listpages[ix].authorName|escape}&nbsp;
						{/if}
						{if $listpages[ix].show_pubdate eq 'y'}
							{tr}on:{/tr} {$listpages[ix].publishDate|tiki_short_datetime}&nbsp;
						{/if}
						{if $listpages[ix].show_expdate eq 'y'}
							{tr}expires:{/tr} {$listpages[ix].expireDate|tiki_short_datetime}&nbsp;
						{/if}
						{if $listpages[ix].show_reads eq 'y'}
							({$listpages[ix].nbreads} {tr}Reads{/tr})
						{/if}
					</span><br />
				{/if}
			</div>
			{if $listpages[ix].use_ratings eq 'y'}
				<div class="articleheading">
					{tr}Rating{/tr}: 
					{repeat count=$listpages[ix].rating}
						{icon _id='star' alt="{tr}star{/tr}"}
					{/repeat}
					{if $listpages[ix].rating > $listpages[ix].entrating}
						{icon _id='star_half' alt="{tr}half star{/tr}"}
					{/if}
					({$listpages[ix].rating}/10)
				</div>
			{/if}
			<div class="articleheading">
				<table  cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">
							{if $listpages[ix].show_image eq 'y'}
								{if $listpages[ix].useImage eq 'y'}
									{if $listpages[ix].hasImage eq 'y'}
										<a href="{$listpages[ix].articleId|sefurl:article}"
												title="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}">
											<img  {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
													alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}"
													src="article_image.php?image_type=article&amp;id={$listpages[ix].articleId}"
													{if $listpages[ix].image_x > 0} width="{$listpages[ix].image_x}"{/if}{if $listpages[ix].image_y > 0 } height="{$listpages[ix].image_y}"{/if} />
										</a>
									{else}
										{* Intentionally left blank to allow user add an image from somewhere else through the img tag and no other extra image *}
									{/if}
								{else}
									{section name=it loop=$topics}
										{if ($topics[it].topicId eq $listpages[ix].topicId) and ($topics[it].image_size > 0)}
											<a href="{$listpages[ix].articleId|sefurl:article}"
													title="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}">
												<img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
														alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}"
														src="article_image.php?image_type=topic&amp;id={$listpages[ix].topicId}" />
											</a>
										{/if}
									{/section}
								{/if}
							{/if}
							{if ($listpages[ix].show_avatar eq 'y')}
								{$listpages[ix].author|avatarize}
							{/if}
							{if $listpages[ix].isfloat eq 'n'}
								</td><td  valign="top">
							{/if}
							<div class="articleheadingtext">{$listpages[ix].parsed_heading}</div>
							{if isset($fullbody) and $fullbody eq "y"}
								<div class="articlebody">{$listpages[ix].parsed_body}</div>
							{/if}
						</td>
					</tr>
				</table>
			</div>
			<div class="articletrailer">
				{if ($listpages[ix].size > 0) or (($prefs.feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y'))}
					{if ($tiki_p_read_article eq 'y' and $listpages[ix].heading_only ne 'y' and (!isset($fullbody) or $fullbody ne "y"))}
						{if ($listpages[ix].size > 0)}
							<div class="status"> {* named to be similar to forum/blog item *}
								<a href="{$listpages[ix].articleId|sefurl:article}" class="more">{tr}Read More{/tr}</a>
							</div>
							{if ($listpages[ix].show_size eq 'y')}
								<span>
									({$listpages[ix].size} {tr}bytes{/tr})
								</span>
							{/if}
						{/if}
					{/if}
					{if ($prefs.feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y') and ($listpages[ix].allow_comments eq 'y')}
						<span>
							<a href="{$listpages[ix].articleId|sefurl:article:with_next}show_comzone=y#comments"{if $listpages[ix].comments_cant > 0} class="highlight"{/if}>
								{if $listpages[ix].comments_cant == 0 or ($tiki_p_read_comments == 'n' and $tiki_p_post_comments == 'y')}
									{tr}Add Comment{/tr}
								{elseif $listpages[ix].comments_cant == 1}
									{tr}1 comment{/tr}
								{else}
									{$listpages[ix].comments_cant}&nbsp;{tr}comments{/tr}
								{/if}
							</a>
						</span>
					{/if}
				{/if}
				<div class="actions">
					{if $tiki_p_edit_article eq 'y' or ($listpages[ix].author eq $user and $listpages[ix].creator_edit eq 'y')}
						<a class="icon" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}">{icon _id='page_edit'}</a>
					{/if}
					{if $prefs.feature_cms_print eq 'y'}
						<a class="icon" href="tiki-print_article.php?articleId={$listpages[ix].articleId}">{icon _id='printer' alt='{tr}Print{/tr}'}</a>
					{/if}
					{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
						<a class="icon" href="tiki-edit_translation.php?id={$listpages[ix].articleId}&amp;type=article">{icon _id='world' alt='{tr}Translation{/tr}'}</a>
					{/if}
					{if $tiki_p_remove_article eq 'y'}
						<a class="icon" href="tiki-list_articles.php?remove={$listpages[ix].articleId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
					{/if}
				</div>
			</div>
		</div>
	{/if}
{sectionelse}
	{if $quiet ne 'y'}{tr}No articles yet.{/tr}
		{if $tiki_p_edit_article eq 'y'}<a href="tiki-edit_article.php">{tr}Add an article{/tr}</a>{/if}
	{/if}
{/section}

{pagination_links cant=$cant step=$maxArticles offset=$offset}{/pagination_links}
