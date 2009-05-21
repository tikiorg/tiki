{* $Id$ *}
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove forums, look for "Admin forums" under "Forums" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_forums.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form method="post" action="tiki-admin.php?page=forums">
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Forums Listing{/tr}
			{/strip}{/tabs}
{/if}

{cycle name=content values="1,2" print=false advance=false reset=true}

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}General Settings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}


<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="home_forum">{tr}Home Forum (main forum){/tr}</label>
	<select name="home_forum" id="home_forum">
{section name=ix loop=$forums}
		<option value="{$forums[ix].forumId|escape}" {if $forums[ix].forumId eq $prefs.home_forum}selected="selected"{/if}>{$forums[ix].name|truncate:20:"...":true}</option>
{sectionelse}
		<option value="">{tr}None{/tr}</option>
{/section}
    </select>
{if $forums}<input type="submit" name="homeforumprefs" value="{tr}Set{/tr}" />
{else}<a href="tiki-admin_forums.php" class="button" title="{tr}Create a forum{/tr}"> {tr}Create a forum{/tr} </a> {/if}
	</div>
</div>

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Forum+Admin"}{/if}</legend>
<input type="hidden" name="forumprefs" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_rankings" name="feature_forum_rankings"
              {if $prefs.feature_forum_rankings eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_rankings">{tr}Rankings{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_parse" name="feature_forum_parse"
              {if $prefs.feature_forum_parse eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_parse">{tr}Accept wiki syntax{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Wiki+Syntax"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_topics_archiving" name="feature_forum_topics_archiving"
              {if $prefs.feature_forum_topics_archiving eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_topics_archiving">{tr}Topic archiving{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_quickjump" name="feature_forum_quickjump"
              {if $prefs.feature_forum_quickjump eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_quickjump">{tr}Quick jumps{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_replyempty" name="feature_forum_replyempty"
              {if $prefs.feature_forum_replyempty eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_replyempty">{tr}Replies are empty{/tr}.</label>
	<br /><em>{tr}If disabled, replies will quote the original post{/tr}.</em>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_comments_no_title_prefix" name="forum_comments_no_title_prefix"
              {if $prefs.forum_comments_no_title_prefix eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="forum_comments_no_title_prefix">{tr}Do not prefix messages titles by 'Re: '{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forums_allow_thread_titles" name="feature_forums_allow_thread_titles"
              {if $prefs.feature_forums_allow_thread_titles eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forums_allow_thread_titles">{tr}First post of a thread can have an empty body{/tr}.</label>
	<br /><em>{tr}Will be a thread title{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="forum_match_regex">{tr}Uploaded filenames must match regex:{/tr}</label> <input type="text" id="forum_match_regex" name="forum_match_regex" value="{$prefs.forum_match_regex|escape}" /></div>
</div>

</fieldset>

<fieldset><legend>{tr}Threads{/tr}</legend>
<input type="hidden" name="forumthreadprefs" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_thread_defaults_by_forum" name="forum_thread_defaults_by_forum"
		{if $prefs.forum_thread_defaults_by_forum eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_thread_defaults_by_forum">{tr}Manage thread defaults per-forum{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_thread_user_settings" name="forum_thread_user_settings"
		{if $prefs.forum_thread_user_settings eq 'y'}checked="checked"{/if} onclick="flip('useconfigurationbar');" /></div>
	<div class="adminoptionlabel"><label for="forum_thread_user_settings">{tr}Display thread configuration bar{/tr}.</label><br /><em>{tr}Allows users to override the defaults{/tr}.</em></div>
<div class="adminoptionboxchild" id="useconfigurationbar" style="display:{if $prefs.forum_thread_user_settings eq 'y'}block{else}none{/if};">
	<div class="adminoption"><input type="checkbox" id="forum_thread_user_settings_keep"name="forum_thread_user_settings_keep"
		{if $prefs.forum_thread_user_settings_keep eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_thread_user_settings_keep">{tr}Keep settings for all forums during the user session{/tr}.</label></div>
</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="forum_comments_per_page">{tr}Default number per page{/tr}</label>: <input size="5" type="text" id="forum_comments_per_page" name="forum_comments_per_page" value="{$prefs.forum_comments_per_page|escape}" /></div>
	
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="forum_thread_style">{tr}Default style{/tr}:</label> 
	<select name="forum_thread_style" id="forum_thread_style">
	  	<option value="commentStyle_plain" {if $prefs.forum_thread_style eq 'commentStyle_plain'}selected="selected"{/if}>{tr}Plain{/tr}</option>
		<option value="commentStyle_threaded" {if $prefs.forum_thread_style eq 'commentStyle_threaded'}selected="selected"{/if}>{tr}Threaded{/tr}</option>
		<option value="commentStyle_headers" {if $prefs.forum_thread_style eq 'commentStyle_headers'}selected="selected"{/if}>{tr}Headers Only{/tr}</option>
	      </select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="forum_thread_sort_mode">{tr}Default sort mode{/tr}: </label>
	<select name="forum_thread_sort_mode" id="forum_thread_sort_mode">
	      <option value="commentDate_desc" {if $prefs.forum_thread_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
	      <option value="commentDate_asc" {if $prefs.forum_thread_sort_mode eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
	      <option value="points_desc" {if $prefs.forum_thread_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
	      <option value="title_desc" {if $prefs.forum_thread_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title (desc){/tr}</option>
	      <option value="title_asc" {if $prefs.forum_thread_sort_mode eq 'title_asc'}selected="selected"{/if}>{tr}Title (asc){/tr}</option>
	</select>
	</div>
</div>

</fieldset>

<fieldset><legend>{tr}Searches{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forums_name_search" name="feature_forums_name_search"
              {if $prefs.feature_forums_name_search eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forums_name_search">{tr}Forum name search{/tr}</label><br /><em>{tr}When listing forums{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forums_search" name="feature_forums_search"
              {if $prefs.feature_forums_search eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forums_search">{tr}Forum content search{/tr}</label><br /><em>{tr}When listing forums{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input id="feature_forum_content_search" type="checkbox" name="feature_forum_content_search"
              {if $prefs.feature_forum_content_search eq 'y'}checked="checked" {/if}onclick="flip('usecontentsearch');" /></div>
	<div class="adminoptionlabel"><label for="feature_forum_content_search">{tr}Topic content search{/tr}</label>
{if $prefs.feature_search ne 'y'}<br />{icon _id=information} {tr}Search is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}</div>

<div class="adminoptionboxchild" id="usecontentsearch" style="display:{if $prefs.feature_forum_content_search eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_local_tiki_search" name="feature_forum_local_tiki_search"
              {if $prefs.feature_forum_local_tiki_search eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_local_tiki_search">{tr}Use Tiki (database-independent) search.{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_forum_local_search" name="feature_forum_local_search"
              {if $prefs.feature_forum_local_search eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_forum_local_search">{tr}Use database (full-text) search.{/tr}</label></div>
</div>


</div>

</div>





<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"><label for=""></label></div>
</div>
</fieldset>

     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Forums Listing{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}
<input type="hidden" name="forumlistprefs" />
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="forums_ordering">{tr}Default ordering{/tr}: </label>
	<select name="forums_ordering" id="forums_ordering">
              <option value="created_asc" {if $prefs.forums_ordering eq 'created_asc'}selected="selected"{/if}>{tr}Creation Date (asc){/tr}</option>
              <option value="created_desc" {if $prefs.forums_ordering eq 'created_desc'}selected="selected"{/if}>{tr}Creation Date (desc){/tr}</option>
              <option value="threads_desc" {if $prefs.forums_ordering eq 'threads_desc'}selected="selected"{/if}>{tr}Topics (desc){/tr}</option>
              <option value="comments_desc" {if $prefs.forums_ordering eq 'comments_desc'}selected="selected"{/if}>{tr}Threads (desc){/tr}</option>
              <option value="lastPost_desc" {if $prefs.forums_ordering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
              <option value="hits_desc" {if $prefs.forums_ordering eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="name_desc" {if $prefs.forums_ordering eq 'name_desc'}selected="selected"{/if}>{tr}Name (desc){/tr}</option>
              <option value="name_asc" {if $prefs.forums_ordering eq 'name_asc'}selected="selected"{/if}>{tr}Name (asc){/tr}</option>
    </select>
	</div>
</div>
<div class="adminoptionbox">
{tr}Select which items to display when listing forums{/tr}: 	  
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_topics" name="forum_list_topics"
              {if $prefs.forum_list_topics eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_list_topics">{tr}Topics{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_posts" name="forum_list_posts"
              {if $prefs.forum_list_posts eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_list_posts">{tr}Posts{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_ppd" name="forum_list_ppd"
              {if $prefs.forum_list_ppd eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_list_ppd">{tr}Posts per day{/tr} (PPD)</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_lastpost" name="forum_list_lastpost"
              {if $prefs.forum_list_lastpost eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_list_lastpost">{tr}Last post{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_visits" name="forum_list_visits"
              {if $prefs.forum_list_visits eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="forum_list_visits">{tr}Visits{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="forum_list_desc" name="forum_list_desc"
              {if $prefs.forum_list_desc eq 'y'}checked="checked"{/if} onclick="flip('descriptionlength');" /></div>
	<div class="adminoptionlabel"><label for="forum_list_desc">{tr}Description{/tr}</label>
<div class="adminoptionboxchild" id="descriptionlength" style="display:	{if $prefs.forum_list_desc eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="forum_list_description_len">{tr}Description length{/tr}: </label><input type="text" name="forum_list_description_len" id="forum_list_description_len" value="{$prefs.forum_list_description_len}" size="3" /></div>
</div>
	</div>
</div>
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
