{block name="title"}
	{title help="Articles" admpage="articles"}
	{if $prefs.art_home_title eq 'topic' and !empty($topic)}
		{tr}{$topic|escape}{/tr}
	{elseif $prefs.art_home_title eq 'type' and !empty($type)}
		{tr}{$type|escape}{/tr}
	{else}{tr}Articles{/tr}{/if}
	{/title}
{/block}
{wikiplugin _name="customsearch" tpl="lists/article-search.tpl" id="article-list" searchfadediv="search-results" autosearchdelay="500" recalllastsearch="0"}
{literal}
	{pagination max="{/literal}{$maxArticles}{literal}"}
	{filter type="article"}
	{filter content="{/literal}{$type}{literal}" field="article_type"}
	{sort mode="modification_date_desc"}
	{output template="{/literal}{custom_template basetpl='article-teaser.tpl' modifiers=$type}{literal}"}
	{FORMAT(name="art_content")}{display name="article_content" format="snippet" length="400" suffix="..." end="word" }{FORMAT}
{/literal}
{/wikiplugin}