{if !isset($actions) or $actions eq "y"}
	{if $prefs.art_home_title ne ''}
		{title help="Articles" admpage="articles"}
			{if $prefs.art_home_title eq 'topic' and !empty($topic)}{tr}{$topic|escape}{/tr}
			{elseif $prefs.art_home_title eq 'type' and !empty($type)}{tr}{$type|escape}{/tr}
			{else}{tr}Articles{/tr}{/if}
		{/title}
	{/if}
	{if $headerLinks eq "y"}
	<div class="t_navbar">
		{if $tiki_p_edit_article eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
			{button href="tiki-edit_article.php" class="btn btn-default" _text="{tr}New Article{/tr}"}
		{/if}
		{if $prefs.feature_submissions == 'y' && $tiki_p_edit_submission == "y" && $tiki_p_edit_article neq 'y' && $tiki_p_admin neq 'y' && $tiki_p_admin_cms neq 'y'}
			{button href="tiki-edit_submission.php" class="btn btn-default" _text="{tr}New Submission{/tr}"}
		{/if}		
		{if $tiki_p_read_article eq 'y' or $tiki_p_articles_read_heading eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		{button href="tiki-list_articles.php" class="btn btn-default" _text="{tr}List Articles{/tr}"}
		{/if}
	
		{if $prefs.feature_submissions == 'y' && ($tiki_p_approve_submission == "y" || $tiki_p_remove_submission == "y" || $tiki_p_edit_submission == "y")}
			{button href="tiki-list_submissions.php" class="btn btn-default" _text="{tr}View Submissions{/tr}"}
		{/if}
	</div>
	<div class="clearfix" style="clear: both;">
		<div style="float: right; padding-left:10px; white-space: nowrap">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $user_watching_articles eq 'n'}
				{self_link watch_event='article_*' watch_object='*' watch_action='add' _icon='eye' _alt="{tr}Monitor Articles{/tr}" _title="{tr}Monitor Articles{/tr}" _class="btn btn-default"}{/self_link}
			{else}
				{self_link watch_event='article_*' watch_object='*' watch_action='remove' _icon='no_eye' _alt="{tr}Stop Monitoring Articles{/tr}" _title="{tr}Stop Monitoring Articles{/tr}" _class="btn btn-default"}{/self_link}
			{/if}
		{/if}
		{if $prefs.feature_group_watches eq 'y' and $tiki_p_admin_users eq 'y'}
			<a href="tiki-object_watches.php?watch_event=article_*&amp;objectId=*" class="btn btn-default">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}"}</a>
		{/if}
		</div>
	</div>
	{/if}
{/if}
{section name=ix loop=$listpages}
	{capture name=href}{strip}
		{if empty($urlparam)}
			{if $useLinktoURL eq 'n' or empty($listpages[ix].linkto)}
				{$listpages[ix].articleId|sefurl:article}
			{else}
				{$listpages[ix].linkto}
			{/if}
		{else}
			{$listpages[ix].articleId|sefurl:article:with_next}{$urlparam}
		{/if}
	{/strip}{/capture}
	{if $listpages[ix].disp_article eq 'y'}
		{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and $listpages[ix].freetags.data|@count >0}
			<div class="freetaglist">
				{foreach from=$listpages[ix].freetags.data item=taginfo}
				{capture name=tagurl}{if (strstr($taginfo.tag, ' '))}"{$taginfo.tag}"{else}{$taginfo.tag}{/if}{/capture}
				<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo.tag}</a>
				{/foreach}
			</div>
		{/if} 
		<article class="article media{if !empty($container_class)} {$container_class}{/if} article{$smarty.section.ix.index}">
			{if ($listpages[ix].show_avatar eq 'y')}
				<div class="avatar">
					{$listpages[ix].author|avatarize}
				</div>
			{/if}
			{if $listpages[ix].show_topline eq 'y' and $listpages[ix].topline}<div class="articletopline">{$listpages[ix].topline|escape}</div>{/if}
			<header class="articletitle clearfix">
				<h2>{object_link type=article id=$listpages[ix].articleId url=$smarty.capture.href title=$listpages[ix].title}</h2>
				{if $listpages[ix].show_subtitle eq 'y' and $listpages[ix].subtitle}<div class="articlesubtitle">{$listpages[ix].subtitle|escape}</div>{/if}
				{if ($listpages[ix].show_author eq 'y')
				 or ($listpages[ix].show_pubdate eq 'y')
				 or ($listpages[ix].show_expdate eq 'y')
				 or ($listpages[ix].show_reads eq 'y')}	
					<span class="titleb">
						{if $listpages[ix].show_author eq 'y'}
							{if $listpages[ix].authorName}
								<span class="author">{tr}Author:{/tr} {$listpages[ix].authorName|escape}&nbsp;- </span>
							{else}
								<span class="author">{tr}Author:{/tr} {$listpages[ix].author|username}&nbsp;- </span>
							{/if}
						{/if}
						{if $listpages[ix].show_pubdate eq 'y'}
							<span class="pubdate">{tr}Published At:{/tr} {$listpages[ix].publishDate|tiki_short_datetime}&nbsp;- </span>
						{/if}
						{if $listpages[ix].show_expdate eq 'y'}
							<span class="expdate">{tr}Expires At:{/tr} {$listpages[ix].expireDate|tiki_short_datetime}&nbsp;- </span>
						{/if}
						{if $listpages[ix].show_reads eq 'y'}
							<span class="reads">({$listpages[ix].nbreads} {tr}Reads{/tr})</span>
						{/if}
						{if $listpages[ix].comment_can_rate_article eq 'y' && $prefs.article_user_rating eq 'y' && ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
							- <span class="ratingResultAvg">{tr}Users Rating: {/tr}</span>{rating_result_avg id=$listpages[ix].articleId type=article}
						{/if}
					</span><br>
				{/if}
				{if $author ne $user and $listpages[ix].comment_can_rate_article eq 'y' and empty({$listpages[ix].body}) and !isset($preview) and $prefs.article_user_rating eq 'y' and ($tiki_p_rate_article eq 'y' or $tiki_p_admin_cms eq 'y')}
					<div class="articleheading">
					<form method="post" action="">
						{rating type=article id=$listpages[ix].articleId}
					</form>
					</div>
				{/if}
				{if $listpages[ix].comment_can_rate_article eq 'y' && $prefs.article_user_rating eq 'y' && ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
					<div class="articleheading">
					{rating_result id=$listpages[ix].articleId type=article}
					</div>
				{/if}				
			</header>
			{if $listpages[ix].use_ratings eq 'y'}
				<div class="articleheading">
					{tr}Rating:{/tr} 
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
						<div {if $listpages[ix].isfloat eq 'n'}style="display:table-cell"{/if}>
							{if $listpages[ix].show_image eq 'y'}
								{if $listpages[ix].useImage eq 'y'}
									{if $listpages[ix].hasImage eq 'y'}
										<a href="{$smarty.capture.href}" class="thumbnail" {if $listpages[ix].isfloat eq 'y'}{$style="margin-right:4px;float:left;"}{/if}
												title="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption|escape}{elseif $listpages[ix].topicName}{tr}{$listpages[ix].topicName}{/tr}{else}{tr}Read More{/tr}{/if}">
											{$style=''}
											<img {*{if $listpages[ix].isfloat eq 'y'}{$style="margin-right:4px;float:left;"}{else}*}class="articleimage"{*{/if}*}
													alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption|escape}{elseif $listpages[ix].topicName}{tr}{$listpages[ix].topicName}{/tr}{/if}"
													{strip}src="article_image.php?image_type=article&amp;id={$listpages[ix].articleId}
													{if $listpages[ix].list_image_x > 0 and ($largefirstimage neq 'y' or not $smarty.section.ix.first)}
														&amp;width={$listpages[ix].list_image_x}
													{elseif $listpages[ix].image_x > 0}
														&amp;width={$listpages[ix].image_x}
													{/if}
													&amp;cache=y"
													{if $listpages[ix].list_image_y > 0 and ($largefirstimage neq 'y' or not $smarty.section.ix.first)}
														{$style=$style|cat:"max-height:"|cat:$listpages[ix].list_image_y|cat:"px;"}
													{elseif $listpages[ix].image_y > 0}
														{$style=$style|cat:"max-height:"|cat:$listpages[ix].image_y|cat:"px;"}
													{/if}
													style="{$style}"
											>{/strip}
										</a>
									{else}
										{* Intentionally left blank to allow user add an image from somewhere else through the img tag and no other extra image *}
									{/if}
								{else}
									{if isset($topics[$listpages[ix].topicId].image_size) and $topics[$listpages[ix].topicId].image_size > 0}
										<a href="{$smarty.capture.href}" class="thumbnail" {if $listpages[ix].isfloat eq 'y'} style="margin-right:4px;float:left;"{/if}
												title="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption|escape}{else}{tr}{$listpages[ix].topicName}{/tr}{/if}">
											<img {if $listpages[ix].isfloat eq 'y'}{*style="margin-right:4px;float:left;*}"{else}class="articleimage"{/if}
													alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption|escape}{else}{tr}{$listpages[ix].topicName}{/tr}{/if}"
													src="article_image.php?image_type=topic&amp;id={$listpages[ix].topicId}">
										</a>
									{/if}
								{/if}
							{/if}
							{if $listpages[ix].isfloat eq 'n'}
						</div>
                        <div style="display:table-cell; vertical-align: top">
							{/if}
							<div class="articleheadingtext">{$listpages[ix].parsed_heading}</div>
							{if isset($fullbody) and $fullbody eq "y"}
								<div class="articlebody">{$listpages[ix].parsed_body}</div>
							{/if}
						</div>
			</div>
			<div class="articletrailer">
				{if ($listpages[ix].size > 0) or (($prefs.feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y'))}
					{if ($tiki_p_read_article eq 'y' and $listpages[ix].heading_only ne 'y' and (!isset($fullbody) or $fullbody ne "y"))}
						{if ($listpages[ix].size > 0 and !empty($listpages[ix].body))}
							<div class="pull-left status"> {* named to be similar to forum/blog item *}
								<a href="{$smarty.capture.href}" class="more">{tr}Read More{/tr}</a>
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
							<a href="{$listpages[ix].articleId|sefurl:article:with_next}{if $prefs.feature_sefurl neq 'y'}&amp;{/if}show_comzone=y{if !empty($urlparam)}&amp;{$urlparam}{/if}#comments"{if $listpages[ix].comments_cant > 0} class="highlight"{/if}>
								{if $listpages[ix].comments_cant == 0 and $tiki_p_post_comments == 'y'}
									{if !isset($actions) or $actions eq "y"}
										{tr}Add Comment{/tr}
									{/if}
								{elseif $tiki_p_read_comments eq 'y'}
									{if $listpages[ix].comments_cant == 1}
										{tr}1 comment{/tr}
									{else}
										{$listpages[ix].comments_cant}&nbsp;{tr}comments{/tr}
									{/if}
								{/if}
							</a>
						</span>
					{/if}
				{/if}
				{if !isset($actions) or $actions eq "y"}
					<div class="pull-right actions">
						{if $tiki_p_edit_article eq 'y' or (!empty($user) and $listpages[ix].author eq $user and $listpages[ix].creator_edit eq 'y')}
							<a class="btn btn-default" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}">{icon _id='page_edit'}</a>
						{/if}
						{if $prefs.feature_cms_print eq 'y'}
							<a class="btn btn-default" href="tiki-print_article.php?articleId={$listpages[ix].articleId}">{icon _id='printer' alt="{tr}Print{/tr}"}</a>
						{/if}
						{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
							<span class="btn-i18n">
								{include file='translated-lang.tpl' object_type='article' trads=$listpages[ix].translations articleId=$listpages[ix].articleId}
							</span>
						{/if}
						{if $tiki_p_remove_article eq 'y'}
							<a class="btn btn-default" href="tiki-list_articles.php?remove={$listpages[ix].articleId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
						{/if}
					</div>
				{/if}
			</div>
		</article>
	{/if}
{sectionelse}
	{if $quiet ne 'y'}
		{remarksbox type=info title="{tr}No articles yet.{/tr}" close="n"}
		{/remarksbox}
	{/if}
{/section}
{if !isset($actions) or $actions eq "y"}
	{if $tiki_p_edit_article eq 'y' or $tiki_p_admin eq 'y' or $tiki_p_admin_cms eq 'y'}
		<br/><img src="img/icons/add.png" alt="{tr}Add an article{/tr}"> <a href="tiki-edit_article.php{if (isset($topicId) && !empty($topicId)) or (isset($type) && !empty($type))}?{/if}{if isset($topicId) && !empty($topicId) and is_numeric($topicId)}topicId={$topicId|escape}{/if}{if isset($type) && !empty($type)}&type={$type|escape}{/if}" class="alert-link">{tr}New article{/tr}</a>
	{/if}
	{if $prefs.feature_submissions == 'y' && $tiki_p_edit_submission == "y" && $tiki_p_edit_article neq 'y' && $tiki_p_admin neq 'y' && $tiki_p_admin_cms neq 'y'}
		<br/><img src="img/icons/add.png" alt="{tr}New Submission{/tr}"> <a href="tiki-edit_submission.php{if (isset($topicId) && !empty($topicId)) or (isset($type) && !empty($type))}?{/if}{if isset($topicId) && !empty($topicId) and is_numeric($topicId)}topicId={$topicId|escape}{/if}{if isset($type) && !empty($type)}&type={$type|escape}{/if}" class="alert-link">{tr}New Submission{/tr}</a>
	{/if}
{/if}
{if !empty($listpages) && (!isset($usePagination) or $usePagination ne 'n')}
	{pagination_links cant=$cant step=$maxArticles offset=$offset}{if isset($urlnext)}{$urlnext}{/if}{/pagination_links}
{/if}
