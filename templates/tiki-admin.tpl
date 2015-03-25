{* $Id$ *}

{include file="admin/admin_navbar.tpl"}

{if $prefs.sender_email eq ''}
	{remarksbox type=warning title="{tr}Warning{/tr}" close="y"}
		{tr _0="tiki-admin.php?page=general&highlight=sender_email"}Your sender email is not set. You can set it <a href="%0" class="alert-link">in the general admin panel.</a>{/tr}
	{/remarksbox}
{/if}
<div class="page_header">
	{title help="$helpUrl"}{$admintitle}{/title}
</div>

<div id="pageheader">
	{* bother to display this only when breadcrumbs are on *}
	{*
	{if $prefs.feature_breadcrumbs eq 'y'}
		{breadcrumbs type="trail" loc="page" crumbs=$crumbs}
		{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
	{/if}
	*}
	{if $db_requires_update}
		{remarksbox type="error" title="{tr}Database Version Problem{/tr}"}
			{tr}Your database requires an update to match the current Tiki version. Please proceed to <a class="alert-link" href="tiki-install.php">the installer</a>. Using Tiki with an incorrect database version usually provokes errors.{/tr}
			{tr}If you have shell (SSH) access, you can also use the following, on the command line, from the root of your Tiki installation:{/tr}
			<kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} database:update</kbd>
		{/remarksbox}
	{/if}
	{*{tr}{$description}{/tr}*}
</div>
{* Determines which page to include using "page" GET parameter. Default : list-sections
Add a value in first check when you create a new admin page. *}
{if in_array($adminpage, array("features", "general", "login", "wiki",
"gal", "fgal", "articles", "polls", "search", "blogs", "forums", "faqs",
"trackers", "webmail", "comments", "rss", "directory", "userfiles", "maps",
"metatags", "performance", "security", "wikiatt", "score", "community", "messages",
"calendar", "intertiki", "video", "freetags", 
"i18n", "wysiwyg", "copyright", "category", "module", "look", "textarea",
 "ads", "profiles", "semantic", "plugins", "webservices",
'sefurl', 'connect', 'metrics', 'payment', 'rating', 'socialnetworks', 'share', "workspace"))}
	{assign var="include" value=$smarty.get.page}
{else}
	{assign var="include" value="list_sections"}
{/if}

{if $upgrade_messages|count}
	{if $upgrade_messages|count eq 1}
		{$title="{tr}Upgrade Available{/tr}"}
	{else}
		{$title="{tr}Upgrades Available{/tr}"}
	{/if}
	{remarksbox type="note" title=$title icon="announce"}
		{foreach from=$upgrade_messages item=um}
			<p>{$um|escape}</p>
		{/foreach}
	{/remarksbox}
{/if}

{* seems to be unused? jonnyb: tiki5 
if $pagetop_msg}
	{remarksbox type="note" title="{tr}Note{/tr}"}
		{$pagetop_msg}
	{/remarksbox}
{/if*}

{include file="admin/include_$include.tpl"}
