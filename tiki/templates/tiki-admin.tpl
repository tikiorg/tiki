{popup_init src="lib/overlib.js"}
<h2><a class="pagetitle" href="tiki-admin.php">{tr}Administration{/tr}</a></h2>

{if $smarty.get.page == "features"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-features.tpl"}
{elseif $smarty.get.page == "general"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-general.tpl"}
{elseif $smarty.get.page == "login"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-login.tpl"}
{elseif $smarty.get.page == "wiki"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-wiki.tpl"}
{elseif $smarty.get.page == "gal"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-gal.tpl"}
{elseif $smarty.get.page == "fgal"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-fgal.tpl"}
{elseif $smarty.get.page == "cms"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-cms.tpl"}
{elseif $smarty.get.page == "polls"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-polls.tpl"}
{elseif $smarty.get.page == "blogs"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-blogs.tpl"}
{elseif $smarty.get.page == "forums"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-forums.tpl"}
{elseif $smarty.get.page == "faqs"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-faqs.tpl"}
{elseif $smarty.get.page == "trackers"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-trackers.tpl"}
{elseif $smarty.get.page == "webmail"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-webmail.tpl"}
{elseif $smarty.get.page == "rss"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-rss.tpl"}
{elseif $smarty.get.page == "directory"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-directory.tpl"}
{elseif $smarty.get.page == "userfiles"}
  {include file="tiki-admin-include-anchors.tpl"}
  {include file="tiki-admin-include-userfiles.tpl"}
{else}
  {include file="tiki-admin-include-list-sections.tpl"}
{/if}

<br/><br/>

