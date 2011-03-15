{tikimodule error=$module_params.error title=$tpl_module_title name="article_archives" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modArticleArchives nonums=$nonums}
	{foreach key=maa_key item=maa_value from=$modArticleArchives}
		<li>
			<a class="linkmodule" href="tiki-view_articles.php?date_min={$maa_value.start_month}&amp;date_max={$maa_value.end_month}" title="">{$maa_key}{if $arch_count eq 'y'} [{$maa_value.item_count}]{/if}</a>
		</li>
	{/foreach}
{/modules_list}
{if $more eq 'y'}
	<div class="more">
		{assign var=queryArgs value=''}
		{foreach from=$urlParams item=urlParam key=urlParamKey}
			{if !empty($urlParam) and !empty($module_params[$urlParamKey])}
				{if empty($queryArgs)}{assign var=queryArgs value='?'}{else}{assign var=queryArgs value="$queryArgs&amp;"}{/if}
				{capture assign=queryArgs}{$queryArgs}{$urlParam}={$module_params[$urlParamKey]|escape:"url"}{/capture}
			{/if}
		{/foreach}
		{button class='more' href="tiki-view_articles.php$queryArgs" _text="{tr}More...{/tr}"}
	</div>
{/if}
{/tikimodule}
