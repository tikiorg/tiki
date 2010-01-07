{* $Id: tiki-editpage.tpl 23991 2009-12-22 15:01:53Z lphuberdeau $ *}

{if $prefs.feature_ajax == 'y'}
  <script type="text/javascript" src="lib/wiki/wiki-ajax.js"></script>
{/if}

{if $page|lower neq 'sandbox'}
	{remarksbox type='tip' title='{tr}Tip{/tr}'}
	{tr}This edit session will expire in{/tr} <span id="edittimeout">{math equation='x / y' x=$edittimeout y=60}</span> {tr}minutes{/tr}. {tr}<strong>Preview</strong> or <strong>Save</strong> your work to restart the edit session timer.{/tr}
	{if $prefs.feature_contribution eq 'y' and $prefs.feature_contribution_mandatory eq 'y'}
		<strong class='mandatory_note'>{tr}Fields marked with a * are mandatory.{/tr}</strong>
	{/if}
	{/remarksbox}
{/if}
	
{if $translation_mode eq 'n'}
	{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{assign var=pp value=$approvedPageName}{else}{assign var=pp value=$page}{/if}
	{title}{if isset($hdr) && $prefs.wiki_edit_section eq 'y'}{tr}Edit Section{/tr}{else}{tr}Edit{/tr}{/if}: {$pp|escape}{if $pageAlias ne ''}&nbsp;({$pageAlias|escape}){/if}{/title}
{else}
   {title}{tr}Update '{$page|escape}'{/tr}{/title}
{/if}
   
{if $beingStaged eq 'y'}
	<div class="tocnav">{icon _id=information style="vertical-align:middle" align="left"} 
		{if $approvedPageExists}
			{tr}You are editing the staging copy of the approved version of this page. Changes will be merged in after approval.{/tr}
		{else}
			{tr}This is a new staging page that has not been approved before.{/tr}
		{/if}
			{if $outOfSync eq 'y'}
				{tr}The current staging copy may contain changes that have yet to be approved.{/tr}
			{/if}
		{if $lastSyncVersion}
			<a class="link" href="tiki-pagehistory.php?page={$page|escape:'url'}&amp;diff2={$lastSyncVersion}" target="_blank">{tr}View changes since last approval.{/tr}</a>
		{/if}
	</div>
{/if}
{if $needsStaging eq 'y'}
	<div class="tocnav">
		{icon _id=information style="vertical-align:middle" align="left"} 
		{tr}You are editing the approved copy of this page.{/tr}
		{if $outOfSync eq 'y'}
			{tr}There are currently changes in the staging copy that have yet to be approved.{/tr}
		{/if}
		{tr}Are you sure you do not want to edit{/tr} <a class="link" href="tiki-editpage.php?page={$stagingPageName|escape:'url'}">{tr}the staging copy{/tr}</a> {tr}instead?{/tr}
	</div>
{/if}
{if isset($data.draft)}
	{tr}Draft written on{/tr} {$data.draft.lastModif|tiki_long_time}<br/>
	{if $data.draft.lastModif < $data.lastModif}
		<b>{tr}Warning: new versions of this page have been made after this draft{/tr}</b>
	{/if}
{/if}
{if $page|lower eq 'sandbox'}
	{remarksbox type='tip' title='{tr}Tip{/tr}'}
		{tr}The SandBox is a page where you can practice your editing skills, use the preview feature to preview the appearance of the page, no versions are stored for this page.{/tr}
	{/remarksbox}
{/if}
{if $category_needed eq 'y'}
	{remarksbox type='Warning' title='{tr}Warning{/tr}'}
	<div class="highlight"><em class='mandatory_note'>{tr}A category is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}
{if $contribution_needed eq 'y'}
	{remarksbox type='Warning' title='{tr}Warning{/tr}'}
	<div class="highlight"><em class='mandatory_note'>{tr}A contribution is mandatory{/tr}</em></div>
	{/remarksbox}
{/if}
{if $likepages}
	<div>
		{tr}Perhaps you are looking for:{/tr}
		{if $likepages|@count < 0}
			<ul>
				{section name=back loop=$likepages}
					<li>
						<a href="{$likepages[back]|sefurl}" class="wiki">{$likepages[back]|escape}</a>
					</li>
				{/section}
			</ul>
		{else}
			<table class="normal"><tr>
				{cycle name=table values=',,,,</tr><tr>' print=false advance=false}
				{section name=back loop=$likepages}
					<td><a href="{$likepages[back]|sefurl}" class="wiki">{$likepages[back]|escape}</a></td>{cycle name=table}
				{/section}
			</tr></table>
		{/if}
	</div>
{/if}

{if $preview && $translation_mode eq 'n'}
	{include file='tiki-preview.tpl'}
{/if}

{include file='tiki-editpage-include-show_diff.tpl'}
{include file='tiki-editpage-include-wiki_editor.tpl'}
{include file='tiki-page_bar.tpl'}
