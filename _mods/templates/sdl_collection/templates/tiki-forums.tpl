{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-forums.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a class="pagetitle" href="tiki-forums.php">{tr}Forums{/tr}</a>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=forums"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
<br /><br />
{/if}
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-forums.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td  class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{if $forum_list_topics eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'threads_desc'}threads_asc{else}threads_desc{/if}">{tr}Topics{/tr}</a></td>
{/if}	
{if $forum_list_posts eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comments_desc'}comments_asc{else}comments_desc{/if}">{tr}Posts{/tr}</a></td>
{/if}	
<!--<td class="heading">{tr}users{/tr}</td>-->
<!--<td class="heading">{tr}age{/tr}</td>-->
{if $forum_list_ppd eq 'y'}
	<td class="heading" title="Posts per Day">{tr}PPD{/tr}</td>
{/if}	
{if $forum_list_lastpost eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}Last Post{/tr}</a></td>
{/if}
{if $forum_list_visits eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
{/if}	
</tr>
{assign var=section_old value=""}
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
{assign var=section value=$channels[user].section}
{if $section ne $section_old}
  {assign var=section_old value=$section}
  <tr><td class="third" colspan="6"><div align="center">{$section}</div></td></tr>
{/if}
<tr>
{if ($channels[user].individual eq 'n') or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
<td class="{cycle advance=false}"><a class="forumname" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name}</a>
{else}
<td class="{cycle advance=false}">{$channels[user].name}
{/if}
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
<a class="admlink" href="tiki-admin_forums.php?forumId={$channels[user].forumId}">{tr}Admin{/tr}</a>
{/if}{if $forum_list_desc eq 'y'}<br />
<small><i>{$channels[user].description|truncate:240:"...":true}</i></small>{/if}
</td>
{if $forum_list_topics eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].threads}</td>
{/if}
{if $forum_list_posts eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].comments}</td>
{/if}
<!--<td class="inf{cycle advance=false}">{$channels[user].users}</td> -->
<!--<td class="{cycle advance=false}">{$channels[user].age}</td> -->
{if $forum_list_ppd eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
{/if}
{if $forum_list_lastpost eq 'y'}	
<td class="{cycle advance=false}">
{$channels[user].lastPost|tiki_short_datetime}<br />
<small><i>{$channels[user].lastPostData.title}</i> {tr}by{/tr} {$channels[user].lastPostData.userName}</small>
</td>
{/if}
{if $forum_list_visits eq 'y'}
	<td style="text-align:right;" class="{cycle}">{$channels[user].hits}</td>
{/if}	
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="forumprevnext" href="tiki-forums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="forumprevnext" href="tiki-forums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-forums.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

