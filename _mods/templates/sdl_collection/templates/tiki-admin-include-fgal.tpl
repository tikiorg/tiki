<div class="cbox">
  <div class="cbox-title">{tr}File Galleries{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Home Gallery (main gallery){/tr}</td>
          <td><select name="homeFileGallery">
              {section name=ix loop=$file_galleries}
                <option value="{$file_galleries[ix].galleryId|escape}" {if $file_galleries[ix].galleryId eq $home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"...":true}</option>
              {/section}
              </select></td>
			<td><input type="submit" name="filegalset"
              value="{tr}Set{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Galleries features{/tr}<br />
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Rankings{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_rankings" 
              {if $feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Comments{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_comments"
              {if $feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Use database to store files{/tr}:</td>
          <td><input type="radio" name="fgal_use_db" value="y"
              {if $fgal_use_db eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Use a directory to store files{/tr}:</td>
          <td class="form"><input type="radio" name="fgal_use_db" value="n"
              {if $fgal_use_db eq 'n'}checked="checked"{/if}/>
              {tr}Directory path{/tr}:<br><input type="text" name="fgal_use_dir"
              value="{$fgal_use_dir|escape}" size="50" /></td>
        </tr><tr>
          <td class="form">{tr}Uploaded filenames must match regex{/tr}:</td>
          <td><input type="text" name="fgal_match_regex"
               value="{$fgal_match_regex|escape}"/></td>
        </tr><tr>
          <td class="form">{tr}Uploaded filenames cannot match regex{/tr}:</td>
          <td><input type="text" name="fgal_nmatch_regex"
              value="{$fgal_nmatch_regex|escape}"/><a class="link" {popup sticky="true"
              trigger="onClick" caption="Storing files in a directory"
              text="If you decide to store files in a directory you must ensure
that the user cannot access directly to the directory. You have two options to
accomplish this:<br />
<ul><li>Use a directory ourside your document root, make sure your php script can
read and write to that directory</li>
<li>Use a directory inside the document root and use and .htaccess to prevent the
user from listing the directory contents</li></ul>
To configure the directory path use UNIX like paths for example files/ or
c:/foo/files or /www/files/"}>{tr}please read{/tr}</a></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegalfeatures"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Gallery listing configuration{/tr}
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin"><tr>
          <td class="form">{tr}Name{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_name"
              {if $fgal_list_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}Description{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_description"
              {if $fgal_list_description eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}Created{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_created"
              {if $fgal_list_created eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}Last Modified{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_lastmodif"
              {if $fgal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}User{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_user"
              {if $fgal_list_user eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}Files{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_files"
              {if $fgal_list_files eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form">{tr}Hits{/tr}</td>
          <td class="form"><input type="checkbox" name="fgal_list_hits"
              {if $fgal_list_hits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit"
              name="filegallistprefs" value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}File galleries comments settings{/tr}
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin"><tr>
          <td class="form">{tr}Default number of comments per page{/tr}: </td>
          <td><input size="5" type="text" name="file_galleries_comments_per_page"
               value="{$file_galleries_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form">{tr}Comments default ordering{/tr}</td>
          <td><select name="file_galleries_comments_default_ordering">
              <option value="commentDate_desc" {if $file_galleries_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $file_galleries_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegalcomprefs"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>
