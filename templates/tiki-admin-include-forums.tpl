<a name="forums"></a>
{include file="tiki-admin-include-anchors-empty.tpl"}
<div class="cbox">
<div class="cbox-title">{tr}Forums{/tr}</div>
<div class="cbox-data">
    {tr}Forums settings{/tr}
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Home Forum (main forum){/tr}</td><td>
    <select name="homeForum">
    {section name=ix loop=$forums}
    <option value="{$forums[ix].forumId}" {if $forums[ix].forumId eq $home_forum}selected="selected"{/if}>{$forums[ix].name|truncate:20:"(...)":true}</option>
    {/section}
    </select>
    </td></tr>
        <tr><td align="center" colspan="2"><input type="submit" name="homeforumprefs" value="{tr}Set home forum{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_forum_rankings" {if $feature_forum_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Accept wiki syntax{/tr}:</td><td><input type="checkbox" name="feature_forum_parse" {if $feature_forum_parse eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Ordering for forums in the forum listing{/tr}
    </td><td>
    <select name="forums_ordering">
    <option value="created_desc" {if $forums_ordering eq 'created_desc'}selected="selected"{/if}>{tr}Creation Date (desc){/tr}</option>
    <option value="threads_desc" {if $forums_ordering eq 'threads_desc'}selected="selected"{/if}>{tr}Topics (desc){/tr}</option>
    <option value="comments_desc" {if $forums_ordering eq 'comments_desc'}selected="selected"{/if}>{tr}Threads (desc){/tr}</option>
    <option value="lastPost_desc" {if $forums_ordering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
    <option value="hits_desc" {if $forums_ordering eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
    <option value="name_desc" {if $forums_ordering eq 'name_desc'}selected="selected"{/if}>{tr}Name (desc){/tr}</option>
    <option value="name_asc" {if $forums_ordering eq 'name_asc'}selected="selected"{/if}>{tr}Name (asc){/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="forumprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    {tr}Forum listing configuration{/tr}
    <table>
    <tr>
		<td class="form">{tr}Topics{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_topics" {if $forum_list_topics eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Posts{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_posts" {if $forum_list_posts eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Posts per day{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_ppd" {if $forum_list_ppd eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Last post{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_lastpost" {if $forum_list_lastpost eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Visits{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_visits" {if $forum_list_visits eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Description{/tr}</td>
		<td class="form"><input type="checkbox" name="forum_list_desc" {if $forum_list_desc eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr><td align="center" colspan="2"><input type="submit" name="forumlistprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

    
</div>
</div>    


