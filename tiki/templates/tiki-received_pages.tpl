{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-received_pages.tpl,v 1.31.2.4 2008-01-30 15:33:51 nyloth Exp $ *}
<h1><a class="pagetitle" href="tiki-received_pages.php">{tr}Received Pages{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/CommunicationsCenterDoc" target="tikihelp" class="tikihelp" title="{tr}Help on Communication Center{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-received_pages.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}received pages tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Tpl{/tr}'}</a>{/if}</h1>

{if !empty($errors)}
<div class="simplebox highlight">
{foreach item=error from=$errors}
{tr}{$error.error}{/tr} {$error.param}<br />
{/foreach}
</div>
{/if}

{if $receivedPageId > 0 or $view eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$parsed}</div>
{/if}
{if $receivedPageId > 0}
<h2>{tr}Edit Received Page{/tr}</h2>
<form action="tiki-received_pages.php" method="post">
<input type="hidden" name="receivedPageId" value="{$receivedPageId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="pageName" value="{$pageName|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Data{/tr}:</td><td class="formcolor"><textarea name="data" rows="10" cols="60">{$data|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor">
<input type="text" name="comment" value="{$comment|escape}" />
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Received Pages{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_pages.php">
     <input type="text" name="find" />
     <input type="submit" name="search" value="{tr}Find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	 <input type="hidden" name="sort_modes" value="{$sort_modes|escape}" />
   </form>
   </td>
</tr>
</table>
{if $channels|@count > 0}<p><span class="highlight">{tr}The highlight pages already exist.{/tr}</span> {tr}Please, change the name if you want the page to be uploaded.{/tr}</p>{/if}
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Name{/tr}</a></td>
<!--<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}Comment{/tr}</a></td>-->
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].receivedPageId}</td>
{if $channels[user].pageExists ne ''}
<td class="{cycle advance=false}"><span class="highlight">{$channels[user].pageName}</span></td>
{else}
<td class="{cycle advance=false}">{$channels[user].pageName}</td>
{/if}
<!--<td class="{cycle advance=false}">{$channels[user].comment}</td>-->
<td class="{cycle advance=false}">{$channels[user].receivedDate|tiki_short_date}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromSite}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromUser}</td>
<td class="{cycle advance=false}">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$channels[user].receivedPageId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedPageId}">{icon _id='magnifier' alt='{tr}View{/tr}'}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedPageId}">{icon _id='accept'}</a> &nbsp;
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedPageId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-received_pages.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-received_pages.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-received_pages.php?offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<h2>{tr}Received Structures{/tr}</h2>
{if $structures|@count > 0}<p><span class="highlight">{tr}The highlight pages already exist.{/tr}</span> {tr}Please, change the name if you want the page to be uploaded.{/tr}</p>{/if}
<form>
<table class="normal">
<tr>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'structureName_desc'}structureName_asc{else}structureName_desc{/if}">{tr}Structure{/tr}</a></td>
<td class="heading">{tr}Page{/tr}</td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$structures}
{if $structures[user].structureName eq $structures[user].pageName}
<tr>
<td class="{cycle advance=false}">&nbsp;</td>
<td class="{cycle advance=false}">{$structures[user].receivedPageId}</td>
<td class="{cycle advance=false}">{$structures[user].pageName}</td>
<td class="{cycle advance=false}">&nbsp;</td>
<td class="{cycle advance=false}">{$structures[user].receivedDate|tiki_short_date}</td>
<td class="{cycle advance=false}">{$structures[user].receivedFromSite}</td>
<td class="{cycle advance=false}">{$structures[user].receivedFromUser}</td>
<td class="{cycle advance=false}">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$structures[user].receivedPageId}">{icon _id='accept'}</a> &nbsp;
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$structures[user].receivedPageId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{section name=ix loop=$structures}
{if $structures[ix].structureName eq $structures[user].structureName}
<tr>
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$structures[ix].pageName|escape}" </td>
<td class="{cycle advance=false}">{$structures[ix].receivedPageId}</td>
<td class="{cycle advance=false}">&nbsp;</td>
{if $structures[ix].pageExists ne ''}
<td class="{cycle advance=false}"><span class="highlight">{$structures[ix].pageName}</span></td>
{else}
<td class="{cycle advance=false}">{$structures[ix].pageName}</td>
{/if}
<td class="{cycle advance=false}">{$structures[ix].receivedDate|tiki_short_date}</td>
<td class="{cycle advance=false}">{$structures[ix].receivedFromSite}</td>
<td class="{cycle advance=false}">{$structures[ix].receivedFromUser}</td>
<td class="{cycle advance=false}">
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$structures[ix].receivedPageId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$structures[ix].receivedPageId}">{icon _id='magnifier' alt='{tr}View{/tr}'}</a>
</td>
</tr>
{/if}
{/section}
{/if}
{/section}
<script type="text/javascript"> /* <![CDATA[ */
	document.write('<tr><td colspan="8"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'checked[]\',this.checked)"/>');
	document.write('<label for="clickall">{tr}select all{/tr}</label></td></tr>');
	/* ]]> */</script>
</table>
{tr}Prefix the checked:{/tr}<input type="text" name="prefix" />{tr}Postfix the checked:{/tr}<input type="text" name="postfix" />&nbsp;<input type="submit" value="{tr}OK{/tr}" />
</form>