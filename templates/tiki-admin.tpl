{* $Id$ *}

{include file="admin/admin_navbar.tpl"}

{if $prefs.sender_email eq ''}
	{remarksbox type=warning title="{tr}Warning{/tr}" close="y"}
		{tr _0="tiki-admin.php?page=general&highlight=sender_email"}Your sender email is not set. You can set it <a href="%0" class="alert-link">in the general admin panel.</a>{/tr}
	{/remarksbox}
{/if}
<div class="page-header">
	{title help="$helpUrl"}{$admintitle}{/title}
	<span class="help-block">{$description}</span>
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

	{if $installer_not_locked}
		{remarksbox type="error" title="{tr}Installer not locked{/tr}"}
			{tr} The installer allows a user to change or destroy the site's database through the browser so it is very important to keep it locked. {/tr}
			{tr}<br />You can re-run the installer (tiki-install.php), skip to the last step and select <strong>LOCK THE INSTALLER</strong>. Alternatively, you can simply <strong>add a lock file</strong> (file without any extension) in your db/ folder.{/tr}
		{/remarksbox}
	{/if}

	{if $search_index_outdated}
		{remarksbox type="error" title="{tr}Search Index outdated{/tr}"}
		{tr}The search index might be outdated. It is recommended to rebuild the search index.{/tr}
		{/remarksbox}
	{/if}

	{*{tr}{$description}{/tr}*}
</div>

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

{if $template_not_found eq 'y'}
	{remarksbox type="error" title="{tr}Error{/tr}"}
	{tr _0="page" _1={$include|escape}}The <strong>%0</strong> parameter has an invalid value: <strong>%1</strong>.{/tr}
	{/remarksbox}
{else}
	{include file="admin/include_$include.tpl"}
{/if}
