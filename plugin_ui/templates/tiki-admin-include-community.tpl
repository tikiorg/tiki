<div class="cbox">
  <div class="cbox-title">{tr}User identity features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=community" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="community-mouseover">{tr}Show user's info on mouseover{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover" id="community-mouseover"
              {if $prefs.feature_community_mouseover eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-name">{tr}Name{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_name" id="community-mouseover-name"
              {if $prefs.feature_community_mouseover_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-picture">{tr}Picture{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_picture" id="community-mouseover-picture"
              {if $prefs.feature_community_mouseover_picture eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-friends">{tr}Number of friends{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_friends" id="community-mouseover-friends"
              {if $prefs.feature_community_mouseover_friends eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-score">{tr}Score{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_score" id="community-mouseover-score"
              {if $prefs.feature_community_mouseover_score eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-country">{tr}Country{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_country" id="community-mouseover-country"
              {if $prefs.feature_community_mouseover_country eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-email">{tr}E-mail{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_email" id="community-mouseover-email"
              {if $prefs.feature_community_mouseover_email eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-lastlogin">{tr}Last login{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_lastlogin" id="community-mouseover-lastlogin"
              {if $prefs.feature_community_mouseover_lastlogin eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-distance">{tr}Distance{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_distance" id="community-mouseover-distance"
              {if $prefs.feature_community_mouseover_distance eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="mouseoverfeatures"
              value="{tr}Set features{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}User List{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=community" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="community-list-name">{tr}Name{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_list_name" id="community-list-name"
              {if $prefs.feature_community_list_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="community-list-score">{tr}Score{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_list_score" id="community-list-score"
              {if $prefs.feature_community_list_score eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="community-list-country">{tr}Country{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_list_country" id="community-list-country"
              {if $prefs.feature_community_list_country eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="community-list-distance">{tr}Distance{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_list_distance" id="community-list-distance"
              {if $prefs.feature_community_list_distance eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form" ><label for="user_list_order">{tr}Users sort order{/tr}:</label></td>
          <td ><select name="user_list_order" id="user_list_order">
        	<option value="score_asc" {if $prefs.user_list_order=="score_asc"}selected="selected"{/if}>{tr}Score ascending{/tr}</option>
            <option value="score_desc" {if $prefs.user_list_order=="score_desc"}selected="selected"{/if}>{tr}Score descending{/tr}</option>
            <option value="pref:realName_asc" {if $prefs.user_list_order=="pref:realName_asc"}selected="selected"{/if}>{tr}Name ascending{/tr}</option>
            <option value="pref:realName_desc" {if $prefs.user_list_order=="pref:realName_desc"}selected="selected"{/if}>{tr}Name descending{/tr}</option>
            <option value="login_asc" {if $prefs.user_list_order=="login_asc"}selected="selected"{/if}>{tr}Login ascending{/tr}</option>
            <option value="login_desc" {if $prefs.user_list_order=="login_desc"}selected="selected"{/if}>{tr}Login descending{/tr}</option>
          </select></td>
       </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="listfeatures"
              value="{tr}Set features{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

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
          <td colspan="2" class="button"><input type="submit" name="friendshipfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
*}