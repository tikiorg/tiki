{* $Id$ *}
{if $prefs.feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page|escape:"url"}" class="pagetitle">
  {if $structure eq 'y' and $page_info.page_alias ne ''}
    {$page_info.page_alias}
  {else}
    {$page}
  {/if}</a>
{if $lock and $print_page ne 'y'}
<img src="pics/icons/lock.png" height="19" width="19" alt="{tr}Locked{/tr}" title="{tr}Locked by{/tr} {$page_user}" />
{/if}
</h1>{/if}
{if $prefs.feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

<table class="wikibar">

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
<tr>
<td align="right" colspan="{if $prefs.feature_multilingual eq "y"}5{else}4{/if}">
<span style="text-align:right;">{$display_catpath}</span>
</td>
</tr>
{/if}
<tr>
<td>
{if $prefs.feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>({tr}Cached{/tr})</small>
{/if}
</td>
{if $print_page ne 'y'}

{if $prefs.feature_multilingual == 'y'}{include file="translated-lang.tpl" td='y'}{/if}
<td style="text-align:right;">

{if !$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
<a title="{tr}Edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16"  alt='{tr}Edit{/tr}' /></a>
{/if}


{if $prefs.feature_backlinks eq 'y'}
<a title="{tr}Backlinks{/tr}" href="tiki-backlinks.php?page={$page|escape:"url"}">{html_image file='img/icons/ico_link.gif' border='0' alt='{tr}Backlinks{/tr}'}</a>
{/if}

{if $prefs.feature_morcego eq "y" && $prefs.wiki_feature_3d eq "y"}
<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$prefs.wiki_3d_width}, {$prefs.wiki_3d_height})"><img src="pics/icons/wiki3d.png" border="0" width="13" height="16" alt='{tr}3d browser{/tr}' /></a>
{/if}

{if $cached_page eq 'y'}
<a title="{tr}Refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1">{html_image file='pics/icons/arrow_refresh.png' border='0' alt='{tr}Refresh{/tr}'}</a>
{/if}

{if $prefs.feature_wiki_print eq 'y'}
<a title="{tr}Print{/tr}" href="tiki-print.php?page={$page|escape:"url"}">{html_image file='pics/icons/printer.png' border='0' alt='{tr}Print{/tr}'}</a>
{/if}

{*
{if $prefs.feature_wiki_pdf eq 'y'}
<a title="{tr}Create pdf{/tr}" href="tiki-config_pdf.php?page={$page|escape:"url"}">{html_image file='pics/icons/page_white_acrobat.png' border='0' alt='{tr}pdf{/tr}'}</a>
{/if}
*}

{if $user and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1">{html_image file='pics/icons/disk.png' border='0' alt='{tr}Save{/tr}'}</a>
{/if}

{if $user and $prefs.feature_user_watches eq 'y'}
  {if $user_watching_page eq 'n'}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add"><img border='0' alt='{tr}Monitor this Page{/tr}' title='{tr}Monitor this Page{/tr}' src='pics/icons/eye.png' /></a>
  {else}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove"><img border='0' alt='{tr}Stop Monitoring this Page{/tr}' title='{tr}Stop Monitoring this Page{/tr}' src='pics/icons/no_eye.png' /></a>
  {/if}
{/if}
</td>
<td style="text-align:right; width: 1px;">
{if $prefs.feature_backlinks eq 'y' and $backlinks}
<form action="tiki-index.php" method="get">
  <select name="page" onchange="page.form.submit()">
    <option>{tr}Backlinks{/tr}...</option>
	{section name=back loop=$backlinks}
	  <option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
	{/section}
  </select>
</form>
{/if}
</td>
<td>
{if !$page_ref_id and count($showstructs) ne 0}
<form action="tiki-index.php" method="post">
  <select name="page_ref_id" onchange="page_ref_id.form.submit()">
    <option>{tr}Structures{/tr}...</option>
    {section name=struct loop=$showstructs}
    <option value="{$showstructs[struct].req_page_ref_id}">
{if $showstructs[struct].page_alias}
	{$showstructs[struct].page_alias}
{else}
	{$showstructs[struct].pageName}
{/if}</option>
	{/section}
  </select>
</form>
{/if}
</td>
</tr>
{/if}
</table>

{strip}{* remove all additional and unwanted white space produced by smarty code indentation to get the tabs displayed as intended *}

{if $print_page ne 'y'}
{if !$lock}
	{if $tiki_p_edit eq 'y' or $page|lower eq 'sandbox'}
		{if $beingEdited eq 'y'}
			{popup_init src="lib/overlib.js"}
			<span class="tabbut"><a style="background: #FFAAAA;" href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink" {popup text="$semUser" width="-1"}>{tr}Edit{/tr}</a></span>
		{else}
			<span class="tabbut"><a href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink">{tr}Edit{/tr}</a></span>
		{/if}
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $tiki_p_remove eq 'y'}
		<span class="tabbut"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="tablink">{tr}Remove{/tr}</a></span>
	{/if}
	{if $tiki_p_rename eq 'y'}
		<span class="tabbut"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="tablink">{tr}Rename{/tr}</a></span>
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($prefs.feature_wiki_usrlock eq 'y')))}
		<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="tablink">{tr}Unlock{/tr}</a></span>
	{/if}
	{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($prefs.feature_wiki_usrlock eq 'y')))}
		<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="tablink">{tr}Lock{/tr}</a></span>
	{/if}
	{if $tiki_p_admin_wiki eq 'y'}
		<span class="tabbut"><a href="tiki-objectpermissions.php?objectId={$page|escape:"url"}&amp;objectName={$page|escape:"url"}&amp;objectType=wiki+page&amp;permType=wiki" class="tablink">{tr}Perms{/tr}</a></span>
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
		<span class="tabbut"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="tablink">{tr}History{/tr}</a></span>
	{/if}
{/if}

{if $prefs.feature_likePages eq 'y'}
	<span class="tabbut"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="tablink">{tr}Similar{/tr}</a></span>
{/if}

{if $prefs.feature_wiki_undo eq 'y' and $canundo eq 'y'}
	<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="tablink">{tr}Undo{/tr}</a></span>
{/if}

{if $prefs.wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
	<span class="tabbut"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="tablink">{tr}Slides{/tr}</a></span>
{elseif $structure eq 'y'}
	<span class="tabbut"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="tablink">{tr}Slides{/tr}</a></span>
{/if}
{/if}

{if $tiki_p_admin_wiki eq 'y'}
        <span class="tabbut"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="tablink">{tr}Export{/tr}</a></span>
{/if}

{if $prefs.feature_wiki_discuss eq 'y'}
	<span class="tabbut"><a href="tiki-view_forum.php?forumId={$prefs.wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page}{"|"}{$page}{"] page."|escape:"url"}&amp;comment_topictype=n" class="tablink">{tr}Discuss{/tr}</a></span>
{/if}

{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y' and $show_page eq 'y'}
<span class="tabbut">
<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="tablink">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</span>
{/if}

{* Attaching a file and viewing attachments are separate permissions! *}
{if $prefs.feature_wiki_attachments eq 'y' and $show_page eq 'y'}
{if $tiki_p_wiki_attach_files eq 'y'}
<span class="tabbut">
<a href="#" onclick="javascript:flip('attzone');flip('attzone_close','inline');return false;" class="tablink">
{if $atts|@count == 0 || $tiki_p_wiki_attach_files == 'y' && $tiki_p_wiki_view_attachments == 'n' && $tiki_p_wiki_admin_attachments == 'n'}
{tr}Attach File{/tr}
{elseif $atts|@count == 1}
<span class="highlight">{tr}1 File Attached{/tr}</span>
{else}
<span class="highlight">{tr}{|} Files Attached{/tr}$atts|@count{tr}{|} Files Attached{/tr}</span>
{/if}
<span id="attzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</span>
{/if}
{/if}
{/if}

{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
    <span class="tabbut">
      <a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="tablink">{tr}Translation{/tr}</a>
    </span>
{/if}

{/strip}

<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table width='100%'>
<tr>
	<td>
	  {section loop=$structure_path name=ix}
	    {if $structure_path[ix].parent_id}->{/if}
	    <a class="tocnavlink" href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}">
	    {if $structure_path[ix].page_alias}
		{$structure_path[ix].page_alias}
	    {else}
		{$structure_path[ix].pageName}
	    {/if}
	    </a>
	  {/section}
	</td>
</tr>
<tr>
		<td>
			<table width='100%'>
				<tr>
	<td width='33%'>
		{if $prev_info and $prev_info.page_ref_id}
			<a class="tocnavlink" href="tiki-index.php?page_ref_id={$prev_info.page_ref_id}">&lt;&lt;
				{if $prev_info.page_alias}
					{$prev_info.page_alias}
				{else}
					{$prev_info.pageName}
				{/if}
			</a>

		{else}
			&nbsp;
		{/if}
	</td>
	<td align='center' width='33%'>
	{if $parent_info}
		<a class="tocnavlink" href="tiki-index.php?page_ref_id={$parent_info.page_ref_id}">
	        {if $parent_info.page_alias}
			{$parent_info.page_alias}
		{else}
			{$parent_info.pageName}
		{/if}
		</a>
	{/if}
	</td>
	<td align='right' width='33%'>
		{if $next_info and $next_info.page_ref_id}
				<a class="tocnavlink" href="tiki-index.php?page_ref_id={$next_info.page_ref_id}">
					{if $next_info.page_alias}
						{$next_info.page_alias}
					{else}
						{$next_info.pageName}
					{/if}
					&gt;&gt;
				</a>
			{else}
				&nbsp;
			{/if}</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
{/if}
{if $prefs.feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}{$pageNumUrlExtra}"><img src='pics/icons/resultset_first.png' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}{$pageNumUrlExtra}"><img src='pics/icons/resultset_previous.png' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}{$pageNumUrlExtra}"><img src='pics/icons/resultset_next.png' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}{$pageNumUrlExtra}">{html_image file='pics/icons/resultset_last.png' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}'}</a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

{if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
<p class="editdate">
  {tr}Last edited by{/tr} {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if $smarty.section.author.first}, {tr}based on work by{/tr}
   {else}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />                                         
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'collaborative'}
<p class="editdate">
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'none'}
{else}
<p class="editdate">
  {tr}Created by{/tr}: {$creator|userlink}
  {tr}Last Modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
</p>
{/if}

{if $wiki_extras eq 'y'}
<br />
{include file=attachments.tpl}

{if $prefs.feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y'}
{include file=comments.tpl}
{/if}
{/if}
{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}
