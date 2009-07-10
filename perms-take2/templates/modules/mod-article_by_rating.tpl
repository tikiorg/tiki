{* $Id: mod-article_archives.tpl 18779 2009-05-14 16:28:19Z pkdille $ *}

{if $prefs.feature_articles eq 'y'}
	{if !isset($tpl_module_title)}
		{if $module_sort_mode}
			{eval var="<a href=\"tiki-view_articles.php?topic=$topicId&amp;type=$type\">{tr}Articles by `$module_sort_mode`{/tr}</a>" assign="tpl_module_title"}
		{else}
			{eval var="{tr}Articles by Rating{/tr}" assign="tpl_module_title"}
		{/if}
	{/if}

	{tikimodule error=$module_params.error title=$tpl_module_title name="article_archives" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

		{if $show_rating_selector eq 'y'}
			<form action="tiki-view_articles.php" style="padding: 5px;">
				{if $module_params.sort_mode}<input type='hidden' name="sort_mode" value="{$module_params.sort_mode}" />{/if}
					<select name="min_rating">
						{section name=rt_val start=0 loop=11 step=1}
							<option {if $smarty.section.rt_val.index eq $min_rating}selected{/if}>{$smarty.section.rt_val.index}</option>
						{/section}
					</select>
					to
					<select name="max_rating">
						{section name=rt_val start=10 loop=11 step=-1}
							<option {if $smarty.section.rt_val.index eq $max_rating}selected{/if}>{$smarty.section.rt_val.index}</option>
						{/section}
					</select>
				<input type="submit" value="Go" />
			</form>
		{/if}

		{if $nonums != 'y'}<ol>{else}<ul>{/if}
		{section name=ix loop=$modArticleArchives}
			<li>
				{if !empty($showImg) or $showDate eq 'y'}
					<div class="module">
						{if $showDate eq 'y'}
							<div class="date">{$modArticleArchives[ix].publishDate|tiki_short_date}</div>
						{/if}
						{if isset($showImg)}
							{if $modArticleArchives[ix].hasImage eq 'y'}
								<div class="image">
									<img alt="" src="article_image.php?id={$modArticleArchives[ix].articleId}" width="{$showImg}" />
								</div>
							{elseif $modArticleArchives[ix].topicId}
								<div class="image">
									<img alt="" src="article_image.php?image_type=topic&amp;id={$modArticleArchives[ix].topicId}" width="{$showImg}" />
								</div>
							{/if}
						{/if}
					</div>
				{/if}
		
				{if $absurl == 'y'}
					<a class="linkmodule" href="{$base_url}{$modArticleArchives[ix].articleId|sefurl:article}" title="{$modArticleArchives[ix].publishDate|tiki_short_datetime}, {tr}by{/tr} {$modArticleArchives[ix].author}">{$modArticleArchives[ix].title}</a>
				{else}
					<a class="linkmodule" href="{$base_url}{$modArticleArchives[ix].articleId|sefurl:article}" title="{$modArticleArchives[ix].publishDate|tiki_short_datetime}, {tr}by{/tr} {$modArticleArchives[ix].author}">{$modArticleArchives[ix].title}</a>
				{/if}
				{if isset($showHeading)}
					<div class="heading">
						{if $showHeading > 0 and $showHeading ne 'y'}
							{$modArticleArchives[ix].parsedHeading|truncate:$showHeading}
						{else}
							{$modArticleArchives[ix].parsedHeading}
						{/if}
					</div>
				{/if}
			</li>
		{/section}
		{if $nonums != 'y'}</ol>{else}</ul>{/if}

		{if $module_params.more eq 'y'}
			<div class="more">
				{assign var=queryArgs value=''}
				{foreach from=$urlParams item=urlParam key=urlParamKey}
					{if !empty($urlParam) and !empty($module_params[$urlParamKey])}
						{if empty($queryArgs)}
							{assign var=queryArgs value='?'}
						{else}
							{assign var=queryArgs value="$queryArgs&amp;"}
						{/if}
						{assign var=queryArgs value="$queryArgs$urlParam=`$module_params[$urlParamKey]`"}
					{/if}
				{/foreach}
				{button href="tiki-view_articles.php$queryArgs" _text="{tr}More...{/tr}"}
			</div>
		{/if}
	{/tikimodule}
{/if}
