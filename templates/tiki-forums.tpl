{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-forums.tpl,v 1.37.2.10 2008-03-10 15:05:41 nyloth Exp $ *}

<h1><a class="pagetitle" href="tiki-forums.php">{tr}Forums{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=forums" title="{tr}Admin Feature{/tr}">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
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
    <td  class="heading">{self_link _class="tableheading" _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</td>
    
    {if $prefs.forum_list_topics eq 'y'}
	    <td class="heading">{self_link _class="tableheading" _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}</td>
    {/if}	

    {if $prefs.forum_list_posts eq 'y'}
	    <td class="heading">{self_link _class="tableheading" _sort_arg='sort_mode' _sort_field='comments'}{tr}Posts{/tr}{/self_link}</td>
    {/if}	

    {if $prefs.forum_list_ppd eq 'y'}
	    <td class="heading">{tr}PPD{/tr}</td>
    {/if}	

    {if $prefs.forum_list_lastpost eq 'y'}	
	    <td class="heading">{self_link _class="tableheading" _sort_arg='sort_mode' _sort_field='lastPost'}{tr}Last Post{/tr}{/self_link}</td>
    {/if}

    {if $prefs.forum_list_visits eq 'y'}
	    <td class="heading">{self_link _class="tableheading" _sort_arg='sort_mode' _sort_field='hits'}{tr}Visits{/tr}{/self_link}</td>
    {/if}	
  </tr>

{assign var=section_old value=""}
{section name=user loop=$channels}
{cycle values="odd,even" print=false}
{assign var=section value=$channels[user].section}
{if $section ne $section_old}
  {assign var=section_old value=$section}
  <tr><td class="third" colspan="6">{$section}</td></tr>
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
<a class="admlink" title="{tr}Configure Forum{/tr}" href="tiki-admin_forums.php?forumId={$channels[user].forumId}">{icon _id='page_edit'}</a>
</span>
{/if}{if $prefs.forum_list_desc eq 'y'}<br />
<div class="subcomment">{$channels[user].description|truncate:240:"...":true}</div>{/if}
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
{if isset($channels[user].lastPost)}
{$channels[user].lastPost|tiki_short_datetime}<br />
<small><i>{$channels[user].lastPostData.title}</i> {tr}by{/tr} {$channels[user].lastPostData.userName}</small>
{/if}
</td>
{/if}
{if $prefs.forum_list_visits eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].hits}</td>
{/if}	
</tr>
{/section}
</table>
<br />

{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}

