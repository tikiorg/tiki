{popup_init src="lib/overlib.js"}
<a class="pagetitle" href="tiki-admin.php">{tr}Administration{/tr}</a>
{if $feature_help}
	<a title="{tr}Help{/tr}" href="http://tikiwiki.org/tiki-index.php?page=TikiAdminSettings" target="help"><img alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}
{* The rest determines which page to include using "page" GET parameter. Default: list-sections
Add a value in first check when you create a new admin page. *}
{if in_array($smarty.get.page, array("features", "general", "login", "wiki", "gal", "fgal", "cms", 
"polls", "blogs", "forums", "faqs", "trackers", "webmail", "rss", "directory", "userfiles", "maps"))}
	{assign var="include" value=$smarty.get.page}
		{else}
			{assign var="include" value="list-sections"}
{/if}
{if $include != "list-sections"}
	{include file="tiki-admin-include-anchors.tpl"}
{/if}
{include file="tiki-admin-include-$include.tpl"}