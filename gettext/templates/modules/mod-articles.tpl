{tikimodule error=$module_params.error title=$tpl_module_title name="articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $show_rating_selector eq 'y'}
		<form action="tiki-view_articles.php" style="padding: 5px;">
			<select name="min_rating">
				{section name=rt_val start=0 loop=11 step=1}
					<option {if $smarty.section.rt_val.index eq $min_rating}selected{/if}>{$smarty.section.rt_val.index}</option>
				{/section}
			</select>
			{tr}to{/tr}
			<select name="max_rating">
				{section name=rt_val start=10 loop=11 step=-1}
					<option {if $smarty.section.rt_val.index eq $max_rating}selected{/if}>{$smarty.section.rt_val.index}</option>
				{/section}
			</select>
			<input type="submit" value="{tr}Go{/tr}" />
		</form>
	{/if}
	{modules_list list=$modArticles nonums=$nonums}
		{section name=ix loop=$modArticles}
			<li>
				{if isset($module_params.img)}
					<div class="image">
						<img alt="" src="article_image.php?{if $modArticles[ix].hasImage eq 'y'}id={$modArticles[ix].articleId}{elseif $modArticles[ix].topicId}image_type=topic&amp;id={$modArticles[ix].topicId}{/if}" width="{$module_params.img}" />
					</div>
				{/if}
				<a class="linkmodule" href="{if $absurl == 'y'}{$base_url}{/if}{$modArticles[ix].articleId|sefurl:article}" title="{$modArticles[ix].created|tiki_short_date}, {tr}by{/tr} {$modArticles[ix].author|escape}">
					{$modArticles[ix].title|escape}{if $showcreated eq 'y'} <span class="date">({$modArticles[ix].created|tiki_short_date})</span>{/if}{if $showpubl eq 'y'} <span class="date">({$modArticles[ix].publishDate|tiki_short_date})</span>{/if}
				</a>
			</li>
		{/section}
	{/modules_list}
	{if $more eq 'y'}
		<div class="more">
			{assign var=queryArgs value=''}
			{foreach from=$urlParams item=urlParam key=urlParamKey}
				{if !empty($urlParam) and !empty($module_params[$urlParamKey])}
					{if empty($queryArgs)}
						{assign var=queryArgs value='?'}
					{else}
						{assign var=queryArgs value="$queryArgs&amp;"}
					{/if}
					{capture assign=queryArgs}{$queryArgs}{$urlParam}={$module_params[$urlParamKey]|escape:"url"}{/capture}
				{/if}
			{/foreach}
			{button href="tiki-view_articles.php$queryArgs" _text="{tr}More...{/tr}"}
		</div>
	{/if}
{/tikimodule}
