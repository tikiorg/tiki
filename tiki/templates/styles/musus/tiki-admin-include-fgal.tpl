<div class="tiki">
  <div class="tiki-title">{tr}File galleries{/tr}</div>
  <div class="tiki-content">
    <div class="simplebox">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td><label>{tr}Home Gallery (main gallery){/tr}: </label></td>
          <td><select name="homeFileGallery">
              {section name=ix loop=$file_galleries}
                <option value="{$file_galleries[ix].galleryId|escape}" {if $file_galleries[ix].galleryId eq $home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"...":true}</option>
              {/section}
              </select></td>
			<td><input type="submit" name="filegalset" value="{tr}ok{/tr}" /></td>
        </tr></table>
      </form>
    </div>
    <div class="simplebox">{tr}Galleries features{/tr}<br />
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td><label>{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_file_galleries_rankings" id="" 
              {if $feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_file_galleries_comments" id="" 
              {if $feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Use database to store files{/tr}:</label></td>
          <td><input type="radio" name="fgal_use_db" value="y" id="" 
              {if $fgal_use_db eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td><label>{tr}Use a directory to store files{/tr}:</label></td>
          <td><input type="radio" name="fgal_use_db" id="" value="n"
              {if $fgal_use_db eq 'n'}checked="checked"{/if}/>
              {tr}Directory path{/tr}:<br /><input type="text" name="fgal_use_dir" id="" value="{$fgal_use_dir|escape}" size="50" /></td>
        </tr><tr>
          <td><label>{tr}Uploaded filenames must match the regular expression{/tr}:</label></td>
          <td><input type="text" name="fgal_match_regex" id="" value="{$fgal_match_regex|escape}"/></td>
        </tr><tr>
          <td><label>{tr}Uploaded filenames cannot match the regular expression{/tr}:</label></td>
          <td><input type="text" name="fgal_nmatch_regex" id="" 
              value="{$fgal_nmatch_regex|escape}"/><a {popup sticky="true"
              trigger="onClick" caption="Storing files in a directory"
              text="If you decide to store files in a directory you must ensure
that the user cannot access directly to the directory. You have two options to
accomplish this:<br />
<ul><li>Use a directory outside your document root, make sure your php script can
read and write to that directory.</li>
<li>Use a directory inside the document root and use and .htaccess to prevent the
user from listing the directory contents.</li></ul>
To configure the directory path use UNIX like paths for example files/ or
c:/foo/files or /www/files/"}>{tr}please read{/tr}.</a></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegalfeatures" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
    <div class="simplebox">{tr}Gallery listing configuration{/tr}
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin"><tr>
          <td><label>{tr}Name{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_name" id="" {if $fgal_list_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}Description{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_description" id="" {if $fgal_list_description eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}Created{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_created" id="" {if $fgal_list_created eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}Last modified{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_lastmodif" id="" {if $fgal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}User{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_user" id="" {if $fgal_list_user eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}Files{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_files" id="" {if $fgal_list_files eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label>{tr}Hits{/tr}</label></td>
          <td><input type="checkbox" name="fgal_list_hits" id="" {if $fgal_list_hits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegallistprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
    <div class="simplebox">{tr}File galleries comments settings{/tr}
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin"><tr>
          <td><label>{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="file_galleries_comments_per_page" id="" 
               value="{$file_galleries_comments_per_page|escape}" /></td>
        </tr><tr>
          <td><label>{tr}Comments default ordering{/tr}</label></td>
          <td><select name="file_galleries_comments_default_ordering" id="">
              <option value="commentDate_desc" {if $file_galleries_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $file_galleries_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
          </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegalcomprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>