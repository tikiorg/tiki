<div class="cbox">
  <div class="cbox-title">{tr}Jukebox settings{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=jukebox" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="jukebox-files">{tr}Jukebox tracks location{/tr}</label></td>
          <td><input type="text" name="feature_jukebox_files" id="jukebox-files" value="{$feature_jukebox_files|escape}" />
              </td>
          <td><input type="submit" name="filesset" value="{tr}ok{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Jukebox features{/tr}<br />
      <form action="tiki-admin.php?page=jukebox" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="jukebox-order">{tr}Default ordering for album listing{/tr}:</label></td>
          <td><select name="jukebox_list_order" id="jukebox-order">
              <option value="created_desc" {if $jukebox_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
              <option value="lastModif_desc" {if $jukebox_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
              <option value="title_asc" {if $jukebox_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Album title (asc){/tr}</option>
              <option value="tracks_desc" {if $jukebox_list_order eq 'tracks_desc'}selected="selected"{/if}>{tr}Number of tracks (desc){/tr}</option>
              <option value="hits_desc" {if $jukebox_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              </select></td>
        </tr><tr>
          <td class="form"><label for="jukebox-listinguser">{tr}In album listing show user as{/tr}:</label></td>
          <td><select name="jukebox_list_user" id="jukebox-listinguser">
              <option value="text" {if $jukebox_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
              <option value="link" {if $jukebox_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
              <option value="avatar" {if $jukebox_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="jukeboxfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Jukebox listing configuration (when listing available albums){/tr}
      <form method="post" action="tiki-admin.php?page=jukebox">
        <table class="admin"><tr>
          <td class="form"><label for="jukebox-album-title">{tr}Title{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_title" id="jukebox-album-title"
              {if $jukebox_album_list_title eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-desc">{tr}Description{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_description" id="jukebox-album-desc"
              {if $jukebox_album_list_description eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-creation">{tr}Creation date{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_created" id="jukebox-album-creation"
              {if $jukebox_album_list_created eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-lastmod">{tr}Last modification time{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_lastmodif" id="jukebox-album-lastmod"
              {if $jukebox_album_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-user">{tr}User{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_user" id="jukebox-album-user"
              {if $jukebox_album_list_user eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-tracks">{tr}Tracks{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_tracks" id="jukebox-album-tracks"
              {if $jukebox_album_list_tracks eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="jukebox-album-visits">{tr}Visits{/tr}</label></td>
          <td class="form"><input type="checkbox" name="jukebox_album_list_visits" id="jukebox-album-visits"
              {if $jukebox_album_list_visits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
	  <td class="form"><label for="jukebox-album-genre">{tr}Genre{/tr}</label></td>
	  <td class="form"><input type="checkbox" name="jukebox_album_list_genre" id="jukebox-album-genre"
	      {if $jukebox_album_list_genre eq 'y'}checked="checked"{/if} /></td>
	</tr><tr>
          <td colspan="2" class="button"><input type="submit" name="jukeboxalbumlistconf"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

  </div>
</div>
