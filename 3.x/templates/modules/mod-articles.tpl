{* $Id$ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}$module_title{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
 {if $module_params.nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modArticles}
       <li>
        <a class="linkmodule" href="{$modArticles[ix].articleId|sefurl:article}" title="{$modArticles[ix].created|tiki_short_date}, {tr}by{/tr} {$modArticles[ix].author|escape}">
          {$modArticles[ix].title}{if $module_params.showcreated eq 'y'} <span class="date">({$modArticles[ix].created|tiki_short_date})</span>{/if}{if $module_params.showpubl eq 'y'} <span class="date">({$modArticles[ix].publishDate|tiki_short_date})</span>{/if}
        </a>
        </li>
    {/section}
 {if $module_params.nonums != 'y'}</ol>{else}</ul>{/if}
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
