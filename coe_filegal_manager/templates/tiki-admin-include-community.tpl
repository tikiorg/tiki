{* $Id$ *}

<div class="navbar">
	{button href="tiki-admingroups.php" _text="{tr}Admin Groups{/tr}"}
	{button href="tiki-adminusers.php" _text="{tr}Admin Users{/tr}"}
</div>

<form action="tiki-admin.php?page=community" method="post">
<div class="input_submit_container clear" style="text-align: right;">
	<input type="submit" value="{tr}Change preferences{/tr}" />
</div>

{tabset name="admin_community"}

{* --- User Features --- *}
{tab name="{tr}User features{/tr}"}
			<div class="admin featurelist">
				{preference name=feature_mytiki}
				{preference name=feature_minical}
				{preference name=feature_userPreferences}
				{preference name=feature_notepad}
				{preference name=feature_user_bookmarks}
				{preference name=feature_contacts}
				{preference name=feature_user_watches}
				{preference name=feature_group_watches}
				{preference name=feature_daily_report_watches}
				{preference name=feature_user_watches_translations}
				{preference name=feature_usermenu}
				{preference name=feature_tasks}
				{preference name=feature_messages}
				{preference name=feature_userfiles}
				{preference name=feature_userlevels}
				{preference name=feature_groupalert}
			</div>
{/tab}



	{tab name="{tr}General Settings{/tr}"}
	
			{preference name=user_show_realnames}

			<div class="adminoptionbox">
				<div class="adminoptionlabel">
					<label for="highlight_group">{tr}Highlight group{/tr}:</label>
					<select name="highlight_group" id="highlight_group">
						<option value="0">{tr}None{/tr}</option>
						{foreach key=g item=gr from=$listgroups}
							<option value="{$gr.groupName|escape}" {if $gr.groupName eq $prefs.highlight_group} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."}</option>
						{/foreach}
					</select>
					{if $prefs.feature_help eq 'y'} {help url="Groups"}{/if}
				</div>
			</div>

			{preference name=feature_display_my_to_others}
			
			{preference name=user_tracker_infos}
			<em>{tr}Use the format: trackerId, fieldId1, fieldId2, ...{/tr}</em>
	
	
<input type="hidden" name="userfeatures" />
<fieldset><legend>{tr}Community{/tr}{if $prefs.feature_help eq 'y'} {help url="Community"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_gender" id="community-gender" {if $prefs.feature_community_gender eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="community-gender">{tr}Users can choose to show their gender{/tr}.</label>{if $prefs.feature_help eq 'y'}<br /><em>{tr}Requires User Preferences feature{/tr}.</em> {help url="User+Preferences"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" onclick="flip('userinformation');" name="feature_community_mouseover" id="community-mouseover" {if $prefs.feature_community_mouseover eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="community-mouseover">{tr}Show user's information on mouseover{/tr}.</label>{if $prefs.feature_help eq 'y'}<br /><em>{tr}Requires user's information to be public{/tr}.</em> {help url="User+Preferences"}{/if}
	
<div id="userinformation" class="adminoptionboxchild" style="display:{if $prefs.feature_community_mouseover eq 'y'}block{else}none{/if};">

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_name" id="community-mouseover-name"{if $prefs.feature_community_mouseover_name eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-name">{tr}Real name{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_gender" id="community-mouseover-gender"{if $prefs.feature_community_mouseover_gender eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-gender">{tr}Gender{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_picture" id="community-mouseover-picture" {if $prefs.feature_community_mouseover_picture eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-picture">{tr}Picture{/tr} ({tr}avatar{/tr})</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input {if $prefs.feature_friends ne 'y'}disabled="disabled" {/if}type="checkbox" name="feature_community_mouseover_friends" id="community-mouseover-friends"{if $prefs.feature_community_mouseover_friends eq 'y'} checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-friends">{tr}Number of friends{/tr}{if $prefs.feature_friends ne 'y'}<br />{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a>{/if}	
	{if $prefs.feature_help eq 'y'} {help url="Friendship+Network"}{/if}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_score" id="community-mouseover-score" {if $prefs.feature_community_mouseover_score eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-score">{tr}Score{/tr}</label>
	{if $prefs.feature_help eq 'y'}{help url="Score"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_country" id="community-mouseover-country" {if $prefs.feature_community_mouseover_country eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-country">{tr}Country{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_email" id="community-mouseover-email" {if $prefs.feature_community_mouseover_email eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-email">{tr}E-mail{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_lastlogin" id="community-mouseover-lastlogin" {if $prefs.feature_community_mouseover_lastlogin eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-lastlogin">{tr}Last login{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_mouseover_distance" id="community-mouseover-distance" {if $prefs.feature_community_mouseover_distance eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-mouseover-distance">{tr}Distance{/tr}</label></div>
</div>
</div>	
	
	</div>
</div>
</fieldset>
<input type="hidden" name="users_defaults" />
{* ************ Users Default Preferences *}
<fieldset><legend>{tr}Default user preferences{/tr}
{if $prefs.feature_help eq 'y'} {help url="UsersDefaultPrefs" desc="{tr}Users Default Preferences{/tr}"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_mailCharset">{tr}Character set for mail{/tr}: </label>
	<select name="users_prefs_mailCharset" id="users_prefs_mailCharset">
  <option value=''>{tr}default{/tr}</option>
   {section name=ix loop=$mailCharsets}
      <option value="{$mailCharsets[ix]|escape}" {if $prefs.users_prefs_mailCharset eq $mailCharsets[ix]|escape}selected="selected"{/if}>{$mailCharsets[ix]}</option>
   {/section}
  </select>
	</div>
</div>
{if $prefs.change_theme eq 'y'}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_theme">{tr}Theme{/tr}:</label> 
	<select name="users_prefs_theme" id="users_prefs_theme">
<option value='' >{tr}default{/tr}</option>
{section name=ix loop=$styles}
	{if count($prefs.available_styles) == 0 || in_array($styles[ix], $prefs.available_styles)}
        <option value="{$styles[ix]|escape}" {if $users_prefs_theme eq $styles[ix]|escape}selected="selected"{/if}>{$styles[ix]}</option>
	{/if}
{/section}
</select>
</div>
</div>
{/if}
{if $prefs.change_language eq 'y'}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_language">{tr}Language{/tr}:</label> 
	<select name="users_prefs_language" id="users_prefs_language">
	<option value=''>{tr}default{/tr}</option>
	{section name=ix loop=$languages}
	{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
        <option value="{$languages[ix].value|escape}"
		{if $users_prefs_language eq $languages[ix].value}selected="selected"{/if}>
		{$languages[ix].name}
        </option>
	{/if}
	{/section}
</select>
	</div>
</div>
{/if}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_userbreadCrumb">{tr}Number of visited pages to remember{/tr}:</label>
	<select name="users_prefs_userbreadCrumb" id="users_prefs_userbreadCrumb">
<option value="1" {if $prefs.users_prefs_userbreadCrumb eq 1}selected="selected"{/if}>1</option>
<option value="2" {if $prefs.users_prefs_userbreadCrumb eq 2}selected="selected"{/if}>2</option>
<option value="3" {if $prefs.users_prefs_userbreadCrumb eq 3}selected="selected"{/if}>3</option>
<option value="4" {if $prefs.users_prefs_userbreadCrumb eq 4}selected="selected"{/if}>4</option>
<option value="5" {if $prefs.users_prefs_userbreadCrumb eq 5}selected="selected"{/if}>5</option>
<option value="10" {if $prefs.users_prefs_userbreadCrumb eq 10}selected="selected"{/if}>10</option>
</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Displayed time zone{/tr}:<label></label></div>
<div class="adminoptionboxchild">	
	<div class="adminoptionlabel"><input type="radio" name="users_prefs_display_timezone" id="users_prefs_display_timezone1" value="Site" {if $prefs.users_prefs_display_timezone eq 'Site'}checked="checked"{/if}/><label for="users_prefs_display_timezone1">{tr}Site default{/tr}</label></div>
	<div class="adminoptionlabel"><input type="radio" name="users_prefs_display_timezone" id="users_prefs_display_timezone2" value="Local" {if $prefs.users_prefs_display_timezone ne 'Site'}checked="checked"{/if}/><label for="users_prefs_display_timezone2">{tr}Detect user timezone if browser allows, otherwise site default{/tr}</label></div>
</div>
</div>	
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_user_information">{tr}User information{/tr}:</label> 
	<select name="users_prefs_user_information" id="users_prefs_user_information">
<option value='private' {if $prefs.users_prefs_user_information eq 'private'}selected="selected"{/if}>{tr}Private{/tr}</option>
<option value='public' {if $prefs.users_prefs_user_information eq 'public'}selected="selected"{/if}>{tr}public{/tr}</option>
</select>
	</div>
</div>
{if $prefs.feature_wiki eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_user_dbl" name="users_prefs_user_dbl" {if $prefs.users_prefs_user_dbl eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_user_dbl">{tr}Use double-click to edit pages{/tr}</label></div>
</div>
{* not used {if $prefs.feature_history eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_diff_versions" name="users_prefs_diff_versions" {if $prefs.users_prefs_diff_versions eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_diff_versions">Use new diff any version interface</label></div>
</div>
{/if}
*}
{/if}
{if $prefs.feature_community_mouseover eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_show_mouseover_user_info"  name="users_prefs_show_mouseover_user_info" {if $prefs.users_prefs_show_mouseover_user_info eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_show_mouseover_user_info">{tr}Show user's info on mouseover{/tr}.</label></div>
</div>
{/if}

{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
{* *** User Tasks *** *}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_tasks_maxRecords">{tr}Tasks per page{/tr}: </label>
	<select name="users_prefs_tasks_maxRecords" id="users_prefs_tasks_maxRecords">
      <option value="2" {if $prefs.users_prefs_tasks_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_tasks_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_tasks_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_tasks_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_tasks_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_tasks_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_tasks_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>{if $prefs.feature_help eq 'y'} {help url="Task"}{/if}
	</div>
</div>
{/if}
</fieldset>

{* *** User Messages *** *}

<fieldset><legend>{tr}User messages{/tr}{if $prefs.feature_help eq 'y'} {help url="Inter-User+Messages"}{/if}</legend>

{preference name=feature_messages}

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_mess_maxRecords">{tr}Messages per page{/tr}:</label> 
	<select name="users_prefs_mess_maxRecords" id="users_prefs_mess_maxRecords">
      <option value="2" {if $prefs.users_prefs_mess_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_mess_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_mess_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_mess_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_mess_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_mess_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_mess_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_allowMsgs" name="users_prefs_allowMsgs" {if $prefs.users_prefs_allowMsgs eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="users_prefs_allowMsgs">{tr}Allow messages from other users{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mess_sendReadStatus" name="users_prefs_mess_sendReadStatus" {if $prefs.users_prefs_mess_sendReadStatus eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="users_prefs_mess_sendReadStatus">{tr}Notify sender when reading mail{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_minPrio">{tr}Send me an email for messages with priority equal or greater than{/tr}:</label>
	<select name="users_prefs_minPrio" id="users_prefs_minPrio">
      <option value="1" {if $prefs.users_prefs_minPrio eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $prefs.users_prefs_minPrio eq 2}selected="selected"{/if}>2</option>
      <option value="3" {if $prefs.users_prefs_minPrio eq 3}selected="selected"{/if}>3</option>
      <option value="4" {if $prefs.users_prefs_minPrio eq 4}selected="selected"{/if}>4</option>
      <option value="5" {if $prefs.users_prefs_minPrio eq 5}selected="selected"{/if}>5</option>
      <option value="6" {if $prefs.users_prefs_minPrio eq 6}selected="selected"{/if}>{tr}none{/tr}</option>
    </select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="users_prefs_mess_archiveAfter">{tr}Auto-archive read messages after x days{/tr}:</label>
	<select name="users_prefs_mess_archiveAfter" id="users_prefs_mess_archiveAfter">
      <option value="0" {if $prefs.users_prefs_mess_archiveAfter eq 0}selected="selected"{/if}>{tr}never{/tr}</option>
      <option value="1" {if $prefs.users_prefs_mess_archiveAfter eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $prefs.users_prefs_mess_archiveAfter eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $prefs.users_prefs_mess_archiveAfter eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $prefs.users_prefs_mess_archiveAfter eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $prefs.users_prefs_mess_archiveAfter eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $prefs.users_prefs_mess_archiveAfter eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $prefs.users_prefs_mess_archiveAfter eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $prefs.users_prefs_mess_archiveAfter eq 50}selected="selected"{/if}>50</option>
      <option value="60" {if $prefs.users_prefs_mess_archiveAfter eq 60}selected="selected"{/if}>60</option>
    </select>
	</div>
</div>
</fieldset>
{* *** My Tiki *** *}
<fieldset><legend>{tr}My Tiki{/tr}</legend>
{if $prefs.feature_wiki eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_pages" name="users_prefs_mytiki_pages" {if $prefs.users_prefs_mytiki_pages eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_pages">{tr}My pages{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_blogs eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_blogs" name="users_prefs_mytiki_blogs" {if $prefs.users_prefs_mytiki_blogs eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_blogs">{tr}My blogs{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_galleries eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_gals" name="users_prefs_mytiki_gals" {if $prefs.users_prefs_mytiki_gals eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_gals">{tr}My galleries{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_msgs" name="users_prefs_mytiki_msgs" {if $prefs.users_prefs_mytiki_msgs eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_msgs">{tr}My messages{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_tasks" name="users_prefs_mytiki_tasks" {if $prefs.users_prefs_mytiki_tasks eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_tasks">{tr}My tasks{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_forums eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_forum_topics" name="users_prefs_mytiki_forum_topics" {if $prefs.users_prefs_mytiki_forum_topics eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_forum_topics">{tr}My forum topics{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_forum_replies" name="users_prefs_mytiki_forum_replies" {if $prefs.users_prefs_mytiki_forum_replies eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_forum_replies">{tr}My forum replies{/tr}</label></div>
</div>
{/if}
{if $prefs.feature_trackers eq 'y'}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="users_prefs_mytiki_items" name="users_prefs_mytiki_items" {if $prefs.users_prefs_mytiki_items eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="users_prefs_mytiki_items">{tr}My items{/tr}</label></div>
</div>
{/if}
</fieldset>
	{/tab}

	{tab name="{tr}Friendship Network{/tr}"}

						{preference name=feature_friends}
	
{if $prefs.feature_friends eq 'y'}
<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Select which items to display when listing users{/tr}.
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="user_list_order">{tr}Sort order{/tr}:</label>
	<select name="user_list_order" id="user_list_order">
{if $prefs.feature_community_list_score eq 'y'}	
        	<option value="score_asc" {if $prefs.user_list_order=="score_asc"}selected="selected"{/if}>{tr}Score ascending{/tr}</option>
            <option value="score_desc" {if $prefs.user_list_order=="score_desc"}selected="selected"{/if}>{tr}Score descending{/tr}</option>
{/if}
{if $prefs.feature_community_list_name eq 'y'}
            <option value="pref:realName_asc" {if $prefs.user_list_order=="pref:realName_asc"}selected="selected"{/if}>{tr}Name ascending{/tr}</option>
            <option value="pref:realName_desc" {if $prefs.user_list_order=="pref:realName_desc"}selected="selected"{/if}>{tr}Name descending{/tr}</option>
{/if}
            <option value="login_asc" {if $prefs.user_list_order=="login_asc"}selected="selected"{/if}>{tr}Login ascending{/tr}</option>
            <option value="login_desc" {if $prefs.user_list_order=="login_desc"}selected="selected"{/if}>{tr}Login descending{/tr}</option>
          </select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_list_name" id="community-list-name" {if $prefs.feature_community_list_name eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-list-name">{tr}Name{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_list_score" id="community-list-score" {if $prefs.feature_community_list_score eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-list-score">{tr}Score{/tr}</label>{if $prefs.feature_help eq 'y'}{help url="Score"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_list_country" id="community-list-country" {if $prefs.feature_community_list_country eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-list-country">{tr}Country{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_community_list_distance" id="community-list-distance" {if $prefs.feature_community_list_distance eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="community-list-distance">{tr}Distance{/tr}</label></div>
</div>
{/if}

	{/tab}
{/tabset}
<div class="input_submit_container clear" style="text-align: center;">
	<input type="submit" value="{tr}Change preferences{/tr}" />
</div>
</form>

{* This is desired feature for future
<div class="cbox">
  <div class="cbox-title">{tr}Friendship network{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=community" method="post">
        <table class="admin"><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-friends-permission">{tr}Allow permissions for friendship network{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_friends_permission" id="community-friends-permission"
              {if $prefs.feature_community_friends_permission eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-friends-permission-de">{tr}Max friendship distance{/tr}:</label></td>
          <td><input type="text" size="1" maxlength="1" name="feature_community_friends_permission_dep" id="community-friends-permission-dep"
              value="{$prefs.feature_community_friends_permission_dep}" /></td>
        </tr><tr>
          <td colspan="2" class="input_submit_container"><input type="submit" name="friendshipfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
*}
