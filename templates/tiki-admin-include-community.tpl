<div class="cbox">
  <div class="cbox-title">{tr}User identity features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=community" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="community-mouseover">{tr}Show user's info on mouseover{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover" id="community-mouseover"
              {if $feature_community_mouseover eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-name">{tr}Name{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_name" id="community-mouseover-name"
              {if $feature_community_mouseover_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-picture">{tr}Picture{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_picture" id="community-mouseover-picture"
              {if $feature_community_mouseover_picture eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-friends">{tr}Number of friends{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_friends" id="community-mouseover-friends"
              {if $feature_community_mouseover_friends eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-score">{tr}Score{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_score" id="community-mouseover-score"
              {if $feature_community_mouseover_score eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-country">{tr}Country{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_country" id="community-mouseover-country"
              {if $feature_community_mouseover_country eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-email">{tr}E-mail{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_email" id="community-mouseover-email"
              {if $feature_community_mouseover_email eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-lastlogin">{tr}Last login{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_lastlogin" id="community-mouseover-lastlogin"
              {if $feature_community_mouseover_lastlogin eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-mouseover-distance">{tr}Distance{/tr}:</label></td>
          <td><input type="checkbox" name="feature_community_mouseover_distance" id="community-mouseover-distance"
              {if $feature_community_mouseover_distance eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="mouseoverfeatures"
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
              {if $feature_community_friends_permission eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">&nbsp;&nbsp;<label for="community-friends-permission-de">{tr}Max friendship distance{/tr}:</label></td>
          <td><input type="text" size="1" maxlength="1" name="feature_community_friends_permission_dep" id="community-friends-permission-dep"
              value="{$feature_community_friends_permission_dep}" /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="friendshipfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
*}