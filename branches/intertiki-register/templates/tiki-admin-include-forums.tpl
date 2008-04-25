{* $Id$ *}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove forums, look for "Admin forums" under "Forums" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_forums.php">{tr}Click Here{/tr}</a>.</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}Home Forum{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=forums">
        <table class="admin"><tr class="form">
          <td><label>{tr}Home Forum (main forum){/tr}</label></td>
          <td><select name="home_forum">
          		<option value="">-</option>
              {section name=ix loop=$forums}
                <option value="{$forums[ix].forumId|escape}" {if $forums[ix].forumId eq $prefs.home_forum}selected="selected"{/if}>{$forums[ix].name|truncate:20:"...":true}</option>
              {/section}
              </select></td>
          <td><input type="submit" name="homeforumprefs"
              value="{tr}OK{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Forums features{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=forums">
        <table class="admin"><tr class="form">
          <td><label>{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_forum_rankings"
              {if $prefs.feature_forum_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Accept wiki syntax{/tr}:</label></td>
          <td><input type="checkbox" name="feature_forum_parse"
              {if $prefs.feature_forum_parse eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Enable topics archiving{/tr}:</label></td>
          <td><input type="checkbox" name="feature_forum_topics_archiving"
              {if $prefs.feature_forum_topics_archiving eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Allow first posts of a thread to have an empty body (will be a thread title){/tr}:</label></td>
          <td><input type="checkbox" name="feature_forums_allow_thread_titles"
              {if $prefs.feature_forums_allow_thread_titles eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Reply posts are empty{/tr}:</label></td>
          <td><input type="checkbox" name="feature_forum_replyempty"
              {if $prefs.feature_forum_replyempty eq 'y'}checked="checked"{/if}/></td>
        </tr>
	  <tr class="form">
          <td><label>{tr}Forum quick jumps{/tr}:</label></td>
          <td><input type="checkbox" name="feature_forum_quickjump"
              {if $prefs.feature_forum_quickjump eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Ordering for forums in the forum listing{/tr}</label></td>
          <td><select name="forums_ordering">
              <option value="created_asc" {if $prefs.forums_ordering eq 'created_asc'}selected="selected"{/if}>{tr}Creation Date (asc){/tr}</option>
              <option value="created_desc" {if $prefs.forums_ordering eq 'created_desc'}selected="selected"{/if}>{tr}Creation Date (desc){/tr}</option>
              <option value="threads_desc" {if $prefs.forums_ordering eq 'threads_desc'}selected="selected"{/if}>{tr}Topics (desc){/tr}</option>
              <option value="comments_desc" {if $prefs.forums_ordering eq 'comments_desc'}selected="selected"{/if}>{tr}Threads (desc){/tr}</option>
              <option value="lastPost_desc" {if $prefs.forums_ordering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
              <option value="hits_desc" {if $prefs.forums_ordering eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="name_desc" {if $prefs.forums_ordering eq 'name_desc'}selected="selected"{/if}>{tr}Name (desc){/tr}</option>
              <option value="name_asc" {if $prefs.forums_ordering eq 'name_asc'}selected="selected"{/if}>{tr}Name (asc){/tr}</option>
              </select></td>
        </tr><tr class="form">
          <td><label>{tr}Search some forums by name (on "forum list"){/tr}</label></td>
          <td><input type="checkbox" name="feature_forums_name_search"
              {if $prefs.feature_forums_name_search eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Search some forums by content (on "forum list"){/tr}</label></td>
          <td><input type="checkbox" name="feature_forums_search"
              {if $prefs.feature_forums_search eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Search in topics content on forum page{/tr}</label></td>
          <td><input type="checkbox" name="feature_forum_content_search"
              {if $prefs.feature_forum_content_search eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Search method when searching in content: Tiki search local to a forum{/tr}</label></td>
          <td><input type="checkbox" name="feature_forum_local_tiki_search"
              {if $prefs.feature_forum_local_tiki_search eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td><label>{tr}Search method when searching in content: Non-Tiki search local to a forum{/tr}</label></td>
          <td><input type="checkbox" name="feature_forum_local_search"
              {if $prefs.feature_forum_local_search eq 'y'}checked="checked"{/if}/></td>			
        </tr><tr class="form">
          <td><label>{tr}Do not prefix messages titles by 'Re: '{/tr}</label></td>
          <td><input type="checkbox" name="forum_comments_no_title_prefix"
              {if $prefs.forum_comments_no_title_prefix eq 'y'}checked="checked"{/if}/></td>			
        </tr><tr class="form">
          <td colspan="2" class="button"><input type="submit" name="forumprefs"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Forum listing configuration{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=forums">
        <table class="admin"><tr class="form">
          <td><label>{tr}Topics{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_topics"
              {if $prefs.forum_list_topics eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td><label>{tr}Posts{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_posts"
              {if $prefs.forum_list_posts eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td><label>{tr}Posts per day{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_ppd"
              {if $prefs.forum_list_ppd eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td><label>{tr}Last post{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_lastpost"
              {if $prefs.forum_list_lastpost eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td><label>{tr}Visits{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_visits"
              {if $prefs.forum_list_visits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td><label>{tr}Description{/tr}</label></td>
          <td><input type="checkbox" name="forum_list_desc"
              {if $prefs.forum_list_desc eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="forumlistprefs"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>    

<div class="cbox">
  <div class="cbox-title">{tr}Threads default preferences{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=forums">
        <table class="admin">
        
        <tr class="form">
          <td><label>{tr}Allow to manage thread defaults in each forum configuration{/tr}</label></td>
	  <td><input type="checkbox" name="forum_thread_defaults_by_forum"
		{if $prefs.forum_thread_defaults_by_forum eq 'y'}checked="checked"{/if} /></td>
        </tr>
        
        <tr class="form">
          <td><label>{tr}Display thread configuration bar to override defaults{/tr}</label></td>
	  <td><input type="checkbox" name="forum_thread_user_settings"
		{if $prefs.forum_thread_user_settings eq 'y'}checked="checked"{/if} />
	  </td>
	</tr>
        
        <tr class="form">
          <td><label>{tr}Configuration bar settings are kept for all forums during the user session:{/tr}</label></td>
	  <td><input type="checkbox" name="forum_thread_user_settings_keep"
		{if $prefs.forum_thread_user_settings_keep eq 'y'}checked="checked"{/if} />
	  </td>
        </tr>
        
        <tr class="form">
          <td><label>{tr}Default number of comments per page{/tr}</label></td>
	  <td><input size="5" type="text" name="forum_comments_per_page" value="{$prefs.forum_comments_per_page|escape}" /></td>
        </tr>

        <tr class="form">
          <td><label>{tr}Default thread style{/tr}</label></td>
	  <td><select name="forum_thread_style">
	  	<option value="commentStyle_plain" {if $prefs.forum_thread_style eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
		<option value="commentStyle_threaded" {if $prefs.forum_thread_style eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
		<option value="commentStyle_headers" {if $prefs.forum_thread_style eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
	      </select>
          </td>
        </tr>
        
        <tr class="form">
          <td><label>{tr}Default thread sort mode{/tr}</label></td>
	  <td>
            <select name="forum_thread_sort_mode">
	      <option value="commentDate_desc" {if $prefs.forum_thread_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
	      <option value="commentDate_asc" {if $prefs.forum_thread_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
	      <option value="points_desc" {if $prefs.forum_thread_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
	      <option value="title_desc" {if $prefs.forum_thread_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
	      <option value="title_asc" {if $prefs.forum_thread_sort_mode eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
	    </select>
          </td>
        </tr>
        
        <tr>
          <td colspan="2" class="button">
            <input type="submit" name="forumthreadprefs" value="{tr}Change preferences{/tr}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>    
