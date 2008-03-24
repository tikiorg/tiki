{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_articles.tpl,v 1.18 2007/10/14 17:51:00 mose *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` articles{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last articles{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol class="module">{else}<ul class="module">{/if}
    {section name=ix loop=$modLastArticles}
      <li>
		{if $showTopicImg eq 'y' or $showDate eq 'y'}
		<div class="module">
			{if $showDate eq 'y'}
				<div class="date">{$modLastArticles[ix].publishDate|tiki_short_date}</div>
			{/if}
			{if isset($showImg)}
				{if $modLastArticles[ix].hasImage eq 'y'}<div class="image"><img alt="" src="article_image.php?id={$modLastArticles[ix].articleId}" width="{$showImg}" /></div>
				{elseif $modLastArticles[ix].topicId}<div class="image"><img alt="" src="topic_image.php?id={$modLastArticles[ix].topicId}" width="{$showImg}" /></div>
				{/if}
			{/if}
		</div>		
		{/if}
	  	{if $absurl == 'y'}
          <a class="linkmodule" href="{$feature_server_name}tiki-read_article.php?articleId={$modLastArticles[ix].articleId}" title="{$modLastArticles[ix].publishDate|tiki_short_datetime}, by {$modLastArticles[ix].author}">
            {$modLastArticles[ix].title}
          </a>
		  {else}
		  <a class="linkmodule" href="tiki-read_article.php?articleId={$modLastArticles[ix].articleId}" title="{$modLastArticles[ix].publishDate|tiki_short_datetime}, by {$modLastArticles[ix].author}">
            {$modLastArticles[ix].title}
          </a>
		  {/if}
		  {if isset($showHeading)}
		  	<div class="heading">
		  	   {if $showHeading > 0 and $showHeading ne 'y'}{$modLastArticles[ix].parsedHeading|truncate:$showHeading}{else}{$modLastArticles[ix].parsedHeading}{/if}
			</div>
		  {/if}
        </li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{eval var="<a style=\"margin-left: 20px\" href=\"tiki-view_articles.php?topic=$topicId&type=$type\">...{tr}more{/tr}</a>"}	
{/tikimodule}
{/if}
