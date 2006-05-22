
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove file galleries, go to "File Galleries" on the application menu, or{/tr} <a class="rbox-link" href="tiki-file_galleries.php">{tr}click here{/tr}</a>.</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}Home Gallery{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Home Gallery (main gallery){/tr}</td>
          <td><select name="home_file_gallery">
              {section name=ix loop=$file_galleries}
                <option value="{$file_galleries[ix].galleryId|escape}" {if $file_galleries[ix].galleryId eq $home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"...":true}</option>
              {/section}
              </select></td>
			<td><input type="submit" name="filegalset"
              value="{tr}ok{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Galleries features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr>
          <td class="form">{tr}Rankings{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_rankings" 
              {if $feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Comments{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_comments"
              {if $feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td>{tr}Allow same file to be uploaded more than once{/tr}:</td>
          <td><input type="checkbox" name="fgal_allow_duplicates"
              {if $fgal_allow_duplicates eq 'y'}checked="checked"{/if}/></td>
         </tr><tr>
          <td class="form">{tr}Use database to store files{/tr}:</td>
          <td><input type="radio" name="fgal_use_db" value="y"
              {if $fgal_use_db eq 'y'}checked="checked"{/if}/></td>
        </tr><tr class="form">
          <td>{tr}Use a directory to store files{/tr}:</td>
          <td><input type="radio" name="fgal_use_db" value="n"
              {if $fgal_use_db eq 'n'}checked="checked"{/if}/>
              {tr}Directory path{/tr}:<br /><input type="text" name="fgal_use_dir"
              value="{$fgal_use_dir|escape}" size="50" /></td>
        </tr><tr>
          <td class="form">{tr}Uploaded filenames must match regex{/tr}:</td>
          <td><input type="text" name="fgal_match_regex"
               value="{$fgal_match_regex|escape}"/></td>
        </tr><tr>
          <td class="form">{tr}Uploaded filenames cannot match regex{/tr}:</td>
          <td><input type="text" name="fgal_nmatch_regex"
              value="{$fgal_nmatch_regex|escape}"/><a class="link" {popup sticky="true"
              trigger="onclick" caption="{tr}Storing files in a directory{/tr}"
              text="{tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
              {tr}You have two options to accomplish this:<br /><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
              {tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}"}>
              {tr}please read{/tr}</a></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="filegalfeatures"
              value="{tr}Set features{/tr}" /></td>
       </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Gallery listing configuration{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin"><tr class="form">
          <td>{tr}Name{/tr}</td>
          <td><input type="checkbox" name="fgal_list_name"
              {if $fgal_list_name eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}Description{/tr}</td>
          <td><input type="checkbox" name="fgal_list_description"
              {if $fgal_list_description eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}Created{/tr}</td>
          <td><input type="checkbox" name="fgal_list_created"
              {if $fgal_list_created eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}Last modified{/tr}</td>
          <td><input type="checkbox" name="fgal_list_lastmodif"
              {if $fgal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}User{/tr}</td>
          <td><input type="checkbox" name="fgal_list_user"
              {if $fgal_list_user eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}Files{/tr}</td>
          <td><input type="checkbox" name="fgal_list_files"
              {if $fgal_list_files eq 'y'}checked="checked"{/if} /></td>
        </tr><tr class="form">
          <td>{tr}Hits{/tr}</td>
          <td><input type="checkbox" name="fgal_list_hits"
              {if $fgal_list_hits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit"
              name="filegallistprefs" value="{tr}Change configuration{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}File galleries comments settings{/tr}</div>
  <div class="cbox-data">
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
              value="{tr}Change settings{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}File galleries search indexing{/tr}</div>
  <div class="cbox-data">
  		<ul><li>{tr}Leave command blank to delete handler{/tr}</li><li>
    	{tr}Use %1 for where internal file name should be substituted (example: "strings %1" to convert the document to text using the unix strings command){/tr}</li></ul>
    	<form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin">
		<tr class="form">
          <td>{tr}Enable auto indexing on file upload or change{/tr}</td>
          <td><input type="checkbox" name="fgal_enable_auto_indexing"
              {if $fgal_enable_auto_indexing eq 'y'}checked="checked"{/if} /></td>
        </tr>
        <tr><td colspan=2><table class="normal">
        <tr><td class="heading">{tr}MIME Type{/tr}</td><td class="heading">{tr}System command{/tr}</td></tr>
        {foreach  key=mime item=cmd from=$fgal_handlers}
        <tr><td class="odd">{$mime}</td><td class="odd"><input name="mimes[{$mime}]" type="text" value="{$cmd|escape:html}" size="30"/></td></tr>
        {/foreach}
        <tr><td class="odd"><input name="newMime" type="text" size="30"/></td><td class="odd"><input name=newCmd type="text" size="30"/></td></tr>
        </table></td></tr>
        <tr><td colspan="2" align="left"><input type="submit" name="filegalredosearch" value="{tr}Reindex all files for search{/tr}"/></td></tr>
           <tr><td colspan="2" class="button"><input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" /></td></tr>
        </table>
    	</form>
  </div>
</div>
