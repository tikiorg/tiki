{popup_init src="lib/overlib.js"}
<h2><a title="{tr}refresh{/tr}" class="pagetitle" href="tiki-admin.php">{tr}Administration{/tr}</a>
{if $feature_help}
<a title="{tr}Help{/tr}" href="{$helpurl}TikiAdminSettings" target="help"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}
</h2>
{* The rest determines which page to include using "page" GET parameter. Default : list-sections
Add a value in first check when you create a new admin page. *}
{if in_array($smarty.get.page, array("features", "general", "login", "wiki", "gal", "fgal", "cms", 
"polls", "search", "blogs", "forums", "faqs", "trackers", "webmail", "rss", "directory", "userfiles", "maps", "metatags", "jukebox","wikiatt"))}
  {assign var="include" value=$smarty.get.page}
{else}
  {assign var="include" value="list-sections"}
{/if}
{if $include != "list-sections"}
  {include file="tiki-admin-include-anchors.tpl"}
{/if}
{if $tikifeedback}
<div class="simplebox highlight">{section name=n loop=$tikifeedback}{$tikifeedback[n].mes}<br />{/section}</div>
{/if}
{include file="tiki-admin-include-$include.tpl"}
