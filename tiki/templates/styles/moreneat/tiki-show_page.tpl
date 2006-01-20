{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/moreneat/tiki-show_page.tpl,v 1.55 2006-01-20 09:54:57 sylvieg Exp $ *}
{if $feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page|escape:"url"}" class="pagetitle">
  {if $structure eq 'y' and $page_info.page_alias ne ''}
    {$page_info.page_alias}
  {else}
    {$page}
  {/if}</a>
{if $lock and $print_page ne 'y'}
<img src="img/icons/lock_topic.gif" height="19" width="19" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
{/if}
</h1>{/if}
{if $feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

{if $pear_wiki_parser}
<a href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1{if !$smarty.request.tikiParser}&amp;tikiParser=1{/if}{if $pagenum > 1}&amp;pagenum={$pagenum}{/if}">View parsed by {if ($smarty.request.tikiParser and ($pear_wiki_parser eq 'y')) or (!$smarty.request.tikiParser and ($pear_wiki_parser eq 'n'))}Text_Wiki{else}Tikilib{/if}</a>
{if $smarty.request.tikiParser}
{assign var="pageNumUrlExtra" value="&amp;tikiParser=1"}
{/if}
{/if}

<table class="wikibar">

{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
<tr>
<td align="right" colspan="{if $feature_multilingual eq "y"}5{else}4{/if}">
<span style="text-align:right;">{$display_catpath}</span>
</td>
</tr>
{/if}
<tr>
<td>
{if $feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>({tr}cached{/tr})</small>
{/if}
</td>
{if $print_page ne 'y'}

{if $feature_multilingual == 'y'}{include file="translated-lang.tpl" td='y'}{/if}
<td style="text-align:right;">

{if !$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img src="img/icons/edit.gif" border="0" width="20" height="16"  alt='{tr}edit{/tr}'></a>
{/if}


{if $feature_backlinks eq 'y'}
<a title="{tr}backlinks{/tr}" href="tiki-backlinks.php?page={$page|escape:"url"}">{html_image file='img/icons/ico_link.gif' border='0' alt='{tr}backlinks{/tr}'}</a>
{/if}

{if $feature_morcego eq "y" && $wiki_feature_3d eq "y"}
<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$wiki_3d_width}, {$wiki_3d_height})"><img src="img/icons/ico_wiki3d.gif" border="0" width="13" height="16" alt='{tr}3d browser{/tr}' /></a>
{/if}

{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1">{html_image file='img/icons/ico_redo.gif' border='0' alt='{tr}refresh{/tr}'}</a>
{/if}

{if $feature_wiki_print eq 'y'}
<a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}">{html_image file='img/icons/ico_print.gif' border='0' alt='{tr}print{/tr}'}</a>
{/if}

{if $feature_wiki_pdf eq 'y'}
<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?page={$page|escape:"url"}">{html_image file='img/icons/ico_pdf.gif' border='0' alt='{tr}pdf{/tr}'}</a>
{/if}

{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1">{html_image file='img/icons/ico_save.gif' border='0' alt='{tr}save{/tr}'}</a>
{/if}

{if $user and $feature_user_watches eq 'y'}
  {if $user_watching_page eq 'n'}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add"><img border='0' alt='{tr}monitor this page{/tr}' title='{tr}monitor this page{/tr}' src='img/icons/icon_watch.png' /></a>
  {else}
    <a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this page{/tr}' title='{tr}stop monitoring this page{/tr}' src='img/icons/icon_unwatch.png' /></a>
  {/if}
{/if}
</td>
<td style="text-align:right; width: 1px;">
{if $feature_backlinks eq 'y' and $backlinks}
<form action="tiki-index.php" method="get">
  <select name="page" onchange="page.form.submit()">
    <option>{tr}backlinks{/tr}...</option>
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
			<span class="tabbut"><a style="background: #FFAAAA;" href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink" {popup text="$semUser" width="-1"}>{tr}edit{/tr}</a></span>
		{else}
			<span class="tabbut"><a href="tiki-editpage.php?page={$page|escape:"url"}" class="tablink">{tr}edit{/tr}</a></span>
		{/if}
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $tiki_p_remove eq 'y'}
		<span class="tabbut"><a href="tiki-removepage.php?page={$page|escape:"url"}&amp;version=last" class="tablink">{tr}remove{/tr}</a></span>
	{/if}
	{if $tiki_p_rename eq 'y'}
		<span class="tabbut"><a href="tiki-rename_page.php?page={$page|escape:"url"}" class="tablink">{tr}rename{/tr}</a></span>
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $lock and ($tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user or $user eq "admin") and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
		<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=unlock" class="tablink">{tr}unlock{/tr}</a></span>
	{/if}
	{if !$lock and ($tiki_p_admin_wiki eq 'y' or (($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y')))}
		<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;action=lock" class="tablink">{tr}lock{/tr}</a></span>
	{/if}
	{if $tiki_p_admin_wiki eq 'y'}
		<span class="tabbut"><a href="tiki-pagepermissions.php?page={$page|escape:"url"}" class="tablink">{tr}perms{/tr}</a></span>
	{/if}
{/if}

{if $page|lower ne 'sandbox'}
	{if $feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
		<span class="tabbut"><a href="tiki-pagehistory.php?page={$page|escape:"url"}" class="tablink">{tr}history{/tr}</a></span>
	{/if}
{/if}

{if $feature_likePages eq 'y'}
	<span class="tabbut"><a href="tiki-likepages.php?page={$page|escape:"url"}" class="tablink">{tr}similar{/tr}</a></span>
{/if}

{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
	<span class="tabbut"><a href="tiki-index.php?page={$page|escape:"url"}&amp;undo=1" class="tablink">{tr}undo{/tr}</a></span>
{/if}

{if $wiki_uses_slides eq 'y'}
{if $show_slideshow eq 'y'}
	<span class="tabbut"><a href="tiki-slideshow.php?page={$page|escape:"url"}" class="tablink">{tr}slides{/tr}</a></span>
{elseif $structure eq 'y'}
	<span class="tabbut"><a href="tiki-slideshow2.php?page_ref_id={$page_info.page_ref_id}" class="tablink">{tr}slides{/tr}</a></span>
{/if}
{/if}

{if $tiki_p_admin_wiki eq 'y'}
        <span class="tabbut"><a href="tiki-export_wiki_pages.php?page={$page|escape:"url"}" class="tablink">{tr}export{/tr}</a></span>
{/if}

{if $feature_wiki_discuss eq 'y'}
	<span class="tabbut"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&amp;comments_postComment=post&amp;comments_title={$page|escape:"url"}&amp;comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page}{"|"}{$page}{"] page."|escape:"url"}&amp;comment_topictype=n" class="tablink">{tr}discuss{/tr}</a></span>
{/if}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y' and $show_page eq 'y'}
<span class="tabbut">
{if $comments_cant > 0}
	<a href="{if $comments_show ne 'y'}tiki-index.php?page={$page|escape:"url"}&amp;comzone=show#comments{else}tiki-index.php?page={$page|escape:"url"}&amp;comzone=hide{/if}" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');{if $comments_show eq 'y'} return false;{/if}" class="tablink" style="background: #FFAAAA">{if $comments_cant eq 1}1&nbsp;{tr}comment{/tr}{else}{$comments_cant}&nbsp;{tr}comments{/tr}{/if}</a>
{else}
	<a href="{if $comments_show ne 'y'}tiki-index.php?page={$page|escape:"url"}&amp;comzone=show#comments{else}tiki-index.php?page={$page|escape:"url"}&amp;comzone=hide{/if}" onclick="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');{if $comments_show eq 'y'} return false;{/if}" class="tablink">{tr}comment{/tr}</a>
{/if}
</span>
{/if}

{* Attaching a file and viewing attachments are separate permissions! *}
{if $feature_wiki_attachments eq 'y' and $show_page eq 'y'}
  {if $tiki_p_wiki_attach_files eq 'y'}
    <span class="tabbut">
      <a href="#attachments" onclick="javascript:flip('attzone{if $atts_show eq 'y'}open{/if}');" class="tablink">
        {if $atts_count eq 0}
          {tr}attach file{/tr}
        {elseif $atts_count eq 1}
          1&nbsp;{tr}attachment{/tr}
        {else}
          {$atts_count}&nbsp;{tr}attachments{/tr}
        {/if}
      </a>
    </span>
  {elseif $tiki_p_wiki_view_attachments eq 'y' and $atts_count gt 0}
    <span class="tabbut">
      <a href="#attachments" onclick="javascript:flip('attzone{if $atts_show eq 'y'}open{/if}');" class="tablink">
        {if $atts_count eq 1}
          1&nbsp;{tr}attachment{/tr}
        {else}
          {$atts_count}&nbsp;{tr}attachments{/tr}
        {/if}
      </a>
    </span>
  {/if}
{/if}
{/if}

{if $feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
    <span class="tabbut">
      <a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="tablink">{tr}translation{/tr}</a>
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
{if $feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}{$pageNumUrlExtra}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}{$pageNumUrlExtra}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}{$pageNumUrlExtra}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}{$pageNumUrlExtra}">{html_image file='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}'}</a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

{if isset($wiki_authors_style) && $wiki_authors_style eq 'business'}
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
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'collaborative'}
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
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'none'}
{else}
<p class="editdate">
  {tr}Created by{/tr}: {$creator|userlink}
  {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
</p>
{/if}

{if $wiki_extras eq 'y'}
<br />
{include file=attachments.tpl}

{if $feature_wiki_comments eq 'y' and $tiki_p_wiki_view_comments eq 'y'}
{include file=comments.tpl}
{/if}
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}
