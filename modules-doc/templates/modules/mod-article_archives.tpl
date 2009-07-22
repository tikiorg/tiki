{* $Id: mod-article_archives.tpl 18779 2009-05-14 16:28:19Z pkdille $ *}

{if $prefs.feature_articles eq 'y'}
	{if !isset($tpl_module_title)}
		{if $module_sort_mode}
			{eval var="<a href=\"tiki-view_articles.php?topic=$topicId&amp;type=$type\">{tr}Article archives by `$module_sort_mode`{/tr}</a>" assign="tpl_module_title"}
		{else}
			{eval var="{tr}Articles archives{/tr}" assign="tpl_module_title"}
		{/if}
	{/if}

	{tikimodule error=$module_params.error title=$tpl_module_title name="article_archives" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

		{if $nonums == 'y'}<ol>{else}<ul>{/if}
		{foreach key=maa_key item=maa_value from=$modArticleArchives}
			<li>
				<a class="linkmodule" href="tiki-view_articles.php?date_min={$maa_value.start_month}&date_max={$maa_value.end_month}{if $module_params.sort_mode}&sort_mode={$module_params.sort_mode}{/if}" title="">{$maa_key}{if $arch_count eq 'y'} [{$maa_value.item_count}]{/if}</a>
			</li>
		{/foreach}

		{if $nonums == 'y'}</ol>{else}</ul>{/if}

		{if $module_params.more eq 'y'}
			<div class="more">
				{assign var=queryArgs value=''}
				{foreach from=$urlParams item=urlParam key=urlParamKey}
					{if !empty($urlParam) and !empty($module_params[$urlParamKey])}
						{if empty($queryArgs)}{assign var=queryArgs value='?'}{else}{assign var=queryArgs value="$queryArgs&amp;"}{/if}
						{assign var=queryArgs value="$queryArgs$urlParam=`$module_params[$urlParamKey]`"}
					{/if}
				{/foreach}
				{button href="tiki-view_articles.php$queryArgs" _text="{tr}More...{/tr}"}
			</div>
		{/if}
	{/tikimodule}
{/if}
