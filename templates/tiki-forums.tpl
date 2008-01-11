{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-forums.tpl,v 1.37.2.3 2008-01-11 15:47:49 nyloth Exp $ *}

<h1><a class="pagetitle" href="tiki-forums.php">{tr}Forums{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=forums" title="{tr}Admin Feature{/tr}">{html_image file='pics/icons/wrench.png' border='0'  alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

<div class="navbar">
{if $tiki_p_admin_forum eq 'y'}
<span class="button2"><a href="tiki-admin_forums.php" class="linkbut">{tr}Admin forums{/tr}</a></span>
{/if}
</div>

{if $prefs.feature_forums_search eq 'y' or $prefs.feature_forums_name_search eq 'y'}
  <table class="findtable">
    <tr>
      <td class="findtable">{tr}Find{/tr}</td>
      
      {if $prefs.feature_forums_name_search eq 'y'}
        <td class="findtable">
          <form method="get" action="tiki-forums.php">
            <input type="text" name="find" value="{$find|escape}" />
            <input type="submit" value="{tr}Search by name{/tr}" name="search" />
            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
          </form>
        </td>
      {/if}

      {if $prefs.feature_forums_search eq 'y'}
        <td>
          <form  class="forms" method="get" action="{if $prefs.feature_forum_local_tiki_search eq 'y'}tiki-searchindex.php{else}tiki-searchresults.php{/if}">
            <input name="highlight" size="30" type="text" />
            <input type="hidden" name="where" value="forums" />
            <input type="submit" class="wikiaction" name="search" value="{tr}Search in content{/tr}"/>
          </form>
        </td>
      {/if}
    </tr>
  </table>
{/if}  

<table class="normal">
<tr>
<td  class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{if $prefs.forum_list_topics eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'threads_desc'}threads_asc{else}threads_desc{/if}">{tr}Topics{/tr}</a></td>
{/if}	
{if $prefs.forum_list_posts eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comments_desc'}comments_asc{else}comments_desc{/if}">{tr}Posts{/tr}</a></td>
{/if}	
{if $prefs.forum_list_ppd eq 'y'}
	<td class="heading">{tr}PPD{/tr}</td>
{/if}	
{if $prefs.forum_list_lastpost eq 'y'}	
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}Last Post{/tr}</a></td>
{/if}
{if $prefs.forum_list_visits eq 'y'}
	<td class="heading"><a class="tableheading" href="tiki-forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
{/if}	
</tr>
{assign var=section_old value=""}
{section name=user loop=$channels}
{cycle values="odd,even" print=false}
{assign var=section value=$channels[user].section}
{if $section ne $section_old}
  {assign var=section_old value=$section}
  <tr><td class="third" colspan="6"><div align="center">{$section}</div></td></tr>
{/if}
<tr>
<td class="{cycle advance=false}"><span style="float:left">
{if ($channels[user].individual eq 'n') or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
<a class="forumname" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name}</a>
{else}
{$channels[user].name}
{/if}
</span>
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
<span style="float:right">
<a class="admlink" title="{tr}configure forum{/tr}" href="tiki-admin_forums.php?forumId={$channels[user].forumId}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}Edit{/tr}' /></a>
</span>
{/if}{if $prefs.forum_list_desc eq 'y'}<br />
<small><i>{$channels[user].description|truncate:240:"...":true}</i></small>{/if}
</td>
{if $prefs.forum_list_topics eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].threads}</td>
{/if}
{if $prefs.forum_list_posts eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].comments}</td>
{/if}
{if $prefs.forum_list_ppd eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
{/if}
{if $prefs.forum_list_lastpost eq 'y'}	
<td class="{cycle advance=false}">
{$channels[user].lastPost|tiki_short_datetime}<br />
<small><i>{$channels[user].lastPostData.title}</i> {tr}by{/tr} {$channels[user].lastPostData.userName}</small>
</td>
{/if}
{if $prefs.forum_list_visits eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].hits}</td>
{/if}	
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="forumprevnext" href="tiki-forums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="forumprevnext" href="tiki-forums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-forums.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

