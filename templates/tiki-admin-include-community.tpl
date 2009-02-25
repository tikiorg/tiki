<form action="tiki-admin.php?page=community" method="post">

<table class="admin"><tr><td>

<div align="center" style="margin:1em;"><input type="submit" value=" {tr}Change Preferences{/tr} " name="mouseoverfeatures" /></div>

<fieldset><legend>{tr}User Information{/tr}</legend>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" onclick="flip('userinformation');" name="feature_community_mouseover" id="community-mouseover" {if $prefs.feature_community_mouseover eq 'y'}checked="checked" {/if}/></div>
	<div><label for="community-mouseover">{tr}Show user's information on mouseover{/tr}.</label>
	{if $prefs.feature_help eq 'y'}<br /><em>{tr}Requires user's information to be public{/tr}.</em> {help url="User+Preferences"}{/if}
	</div>
<div id="userinformation" style="display:{if $prefs.feature_community_mouseover eq 'y'}block{else}none{/if};margin-left:2.5em;">
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_name" id="community-mouseover-name"{if $prefs.feature_community_mouseover_name eq 'y'} checked="checked"{/if} /></div>
	<div><label for="community-mouseover-name">{tr}Real name{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_picture" id="community-mouseover-picture" {if $prefs.feature_community_mouseover_picture eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-picture">{tr}Picture{/tr} ({tr}avatar{/tr})</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input {if $prefs.feature_friends ne 'y'}disabled="disabled" {/if}type="checkbox" name="feature_community_mouseover_friends" id="community-mouseover-friends"{if $prefs.feature_community_mouseover_friends eq 'y'} checked="checked"{/if} /></div>
	<div><label for="community-mouseover-friends">{tr}Number of friends{/tr}</label>
	{if $prefs.feature_friends ne 'y'}<br />{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a>{/if}	
	{if $prefs.feature_help eq 'y'}{help url="Friendship+Network"}{/if}
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_score" id="community-mouseover-score" {if $prefs.feature_community_mouseover_score eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-score">{tr}Score{/tr}</label>
	{if $prefs.feature_help eq 'y'}{help url="Score"}{/if}
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_country" id="community-mouseover-country" {if $prefs.feature_community_mouseover_country eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-country">{tr}Country{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_email" id="community-mouseover-email" {if $prefs.feature_community_mouseover_email eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-email">{tr}E-mail{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_lastlogin" id="community-mouseover-lastlogin" {if $prefs.feature_community_mouseover_lastlogin eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-lastlogin">{tr}Last login{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_mouseover_distance" id="community-mouseover-distance" {if $prefs.feature_community_mouseover_distance eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-mouseover-distance">{tr}Distance{/tr}</label></div>
</div>
</div>
</div>
</fieldset>
<fieldset><legend>{tr}User List{/tr}</legend>
<div style="padding:0.5em;clear:both">Select which fields appear when <a href="tiki-list_users.php" title="{tr}User List{/tr}">listing users</a>.</div>
{if $prefs.feature_friends ne 'y'}<div style="padding:0.5em;clear:both">{icon _id=information} {tr}Feature is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}.</a></div>
{else}
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_list_name" id="community-list-name" {if $prefs.feature_community_list_name eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-list-name">{tr}Name{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_list_score" id="community-list-score" {if $prefs.feature_community_list_score eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-list-score">{tr}Score{/tr}</label>
	{if $prefs.feature_help eq 'y'}{help url="Score"}{/if}</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_list_country" id="community-list-country" {if $prefs.feature_community_list_country eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-list-country">{tr}Country{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;"><input type="checkbox" name="feature_community_list_distance" id="community-list-distance" {if $prefs.feature_community_list_distance eq 'y'}checked="checked"{/if} /></div>
	<div><label for="community-list-distance">{tr}Distance{/tr}</label></div>
</div>
<div style="padding:0.5em;clear:both">
	<div><label for="user_list_order">{tr}Sort order{/tr}:</label>
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
{/if}
</fieldset>

<div align="center" style="margin:1em;"><input type="submit" name="listfeatures" value=" {tr}Change Preferences{/tr} " class="button" /></div>

</td></tr></table>

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
