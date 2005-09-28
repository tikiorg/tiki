{popup_init src="lib/overlib.js"}
<div id="pageheader">
{breadcrumbs type="trail" loc="admin" crumbs=$crumbs}{breadcrumbs type="pagetitle" loc="admin" crumbs=$crumbs}
{breadcrumbs type="desc" loc="page" crumbs=$trail}
</div>
{* The rest determines which page to include using "page" GET parameter. Default : list-sections
Add a value in first check when you create a new admin page. *}
{if in_array($smarty.get.page, array("features", "general", "login", "wiki", "gal", "fgal", "cms", "polls", "search", "blogs", "forums", "faqs", "trackers", "webmail", "rss", "directory", "userfiles", "maps", "metatags", "wikiatt","score", "community", "siteid", "calendar"))}
  {assign var="include" value=$smarty.get.page}
{else}
  {if $smarty.get.page eq "admin"}
    {assign var="include" value="list-admin-sections"}
  {else}
    {assign var="include" value="list-sections"}
  {/if}
{/if}
{if $include != "list-sections"}
  {include file="tiki-admin-include-anchors.tpl"}
{/if}
{if $tikifeedback}
<div class="simplebox highlight">{section name=n loop=$tikifeedback}{$tikifeedback[n].mes}<br />{/section}</div>
{/if}
{include file="tiki-admin-include-$include.tpl"}
