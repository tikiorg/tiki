{* $Id$ *}

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}To add/remove file galleries, go to "File Galleries" on the application menu, or{/tr} <a class="rbox-link" href="tiki-file_galleries.php">{tr}Click Here{/tr}</a>.</div>
  <div class="rbox-data" name="tip">
    {tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
    {tr}You have two options to accomplish this:<br /><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
    {tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}
  </div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}Home Gallery{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin"><tr class="formcolor">
          <td>{tr}Home Gallery (main gallery){/tr}</td>
          <td><select name="home_file_gallery">
              {section name=ix loop=$file_galleries}
                <option value="{$file_galleries[ix].galleryId|escape}" {if $file_galleries[ix].galleryId eq $prefs.home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"...":true}</option>
              {/section}
              </select></td>
			<td><input type="submit" name="filegalset"
              value="{tr}OK{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Galleries features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=fgal" method="post">
        <table class="admin">
        <tr class="formcolor">
          <td>{tr}Rankings{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_rankings" 
              {if $prefs.feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Comments{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_comments"
              {if $prefs.feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Allow download limit per file{/tr}:</td>
          <td><input type="checkbox" name="fgal_limit_hits_per_file"
              {if $prefs.fgal_limit_hits_per_file eq 'y'}checked="checked"{/if}/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Prevent download if score becomes negative{/tr}:</td>
          <td><input type="checkbox" name="fgal_prevent_negative_score"
              {if $prefs.fgal_prevent_negative_score eq 'y'}checked="checked"{/if}/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}File author{/tr}:</td>
          <td><input type="checkbox" name="feature_file_galleries_author"
              {if $prefs.feature_file_galleries_author eq 'y'}checked="checked"{/if}/><i>{tr}For not registered author{/tr}</i></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Allow same file to be uploaded more than once{/tr}:</td>
          <td>
	    <select name="fgal_allow_duplicates">
              <option value="n" {if $prefs.fgal_allow_duplicates eq 'n'}selected="selected"{/if}>{tr}Never{/tr}</option>
              <option value="y" {if $prefs.fgal_allow_duplicates eq 'y'}selected="selected"{/if}>{tr}Yes, even in the same gallery{/tr}</option>
              <option value="different_galleries" {if $prefs.fgal_allow_duplicates eq 'different_galleries'}selected="selected"{/if}>{tr}Only in different galleries{/tr}</option>
            </select>
	  </td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Use database to store files{/tr}:</td>
          <td><input type="radio" name="fgal_use_db" value="y"
              {if $prefs.fgal_use_db eq 'y'}checked="checked"{/if}/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Use a directory to store files{/tr}:</td>
          <td>
            <input type="radio" name="fgal_use_db" value="n"
            {if $prefs.fgal_use_db eq 'n'}checked="checked"{/if} />
            {tr}Directory path{/tr}:
            <br />
            <input type="text" name="fgal_use_dir" value="{$prefs.fgal_use_dir|escape}" size="50" />
            <br />
            <i>{tr}The server must be able to read/write the directory.{/tr}<br />{tr}The directory can be outside the web space.{/tr}</i>
          </td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}PodCast directory (must be web accessible):{/tr}</td>
          <td>
            {tr}Directory path{/tr}:
            <br />
            <input type="text" name="fgal_podcast_dir" value="{$prefs.fgal_podcast_dir|escape}" size="50" />
            <br />
            <i>({tr}required field for podcasts{/tr})</i>
          </td>
        </tr>
        
        <tr class="formcolor">
	  <td colspan="2">
            <b>{tr}Directory Batch Loading{/tr}</b>
            <br />
	    {tr}If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload files in that dir, either by scp, ftp, or other protocols{/tr}
          </td>
        </tr>
    	
        <tr class="formcolor">
          <td>
            <label>{tr}Enable directory batch loading{/tr}:</label></td>
          <td>
            <input type="checkbox" name="feature_file_galleries_batch" {if $prefs.feature_file_galleries_batch eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
    	<tr class="formcolor">
          <td>
            <label>{tr}Batch loading directory{/tr}:</label>
          </td>
          <td>
            <input type="text" name="fgal_batch_dir" value="{$prefs.fgal_batch_dir|escape}" size="50" />
            <br />
            <i>{tr}The server must be able to read the directory.{/tr}
            <br />
            {tr}The directory can be outside the web space.{/tr}
            </i>
          </td>
        </tr>
	
        <tr class="formcolor">
          <td>{tr}Uploaded filenames must match regex{/tr}:</td>
          <td>
            <input type="text" name="fgal_match_regex" value="{$prefs.fgal_match_regex|escape}"/></td>
        </tr>
        
        <tr class="formcolor">
          <td>{tr}Uploaded filenames cannot match regex{/tr}:</td>
          <td>
            <input type="text" name="fgal_nmatch_regex" value="{$prefs.fgal_nmatch_regex|escape}" />
          </td>
        </tr>
        
        <tr class="formcolor">
          <td colspan="2" class="button">
            <input type="submit" name="filegalfeatures" value="{tr}Set features{/tr}" /></td>
       </tr>
      </table>
    </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Gallery listing configuration{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=fgal">
        <table class="admin">
		{include file="fgal_listing_conf.tpl"}
		<tr class="formcolor">
		<td>{tr}Default sort order{/tr}</td>
		<td><select name="fgal_sortorder">
			{foreach from=$options_sortorder key=key item=item}
			<option value="{$item|escape}" {if $fgal_sortorder == $item} selected="selected"{/if}>{$key}</option>
			{/foreach}
			</select>
			<input type="radio" name="fgal_sortdirection" value="desc" {if $fgal_sortdirection == 'desc'}checked="checked"{/if} />{tr}descending{/tr}
			<input type="radio" name="fgal_sortdirection" value="asc" {if $fgal_sortdirection == 'asc'}checked="checked"{/if} />{tr}ascending{/tr}
		</td>
		</tr>
        <tr class="formcolor">
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
        <table class="admin"><tr class="formcolor">
          <td>{tr}Default number of comments per page{/tr}: </td>
          <td><input size="5" type="text" name="file_galleries_comments_per_page"
               value="{$prefs.file_galleries_comments_per_page|escape}" /></td>
        </tr><tr class="formcolor">
          <td>{tr}Comments default ordering{/tr}</td>
          <td><select name="file_galleries_comments_default_ordering">
              <option value="commentDate_desc" {if $prefs.file_galleries_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $prefs.file_galleries_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr class="formcolor">
          <td colspan="2" class="button"><input type="submit" name="filegalcomprefs"
              value="{tr}Change settings{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}File galleries search indexing{/tr}</div>
  <div class="cbox-data">
    <ul>
      <li>{tr}Leave command blank to delete handler{/tr}</li>
      <li>
        {tr}Use %1 for where internal file name should be substituted (example: "strings %1" to convert the document to text using the unix strings command){/tr}
      </li>
    </ul>
    <form method="post" action="tiki-admin.php?page=fgal">
      <table class="admin">
        <tr class="formcolor">
          <td>{tr}Enable auto indexing on file upload or change{/tr}</td>
          <td><input type="checkbox" name="fgal_enable_auto_indexing"
              {if $prefs.fgal_enable_auto_indexing eq 'y'}checked="checked"{/if} /></td>
        </tr>
        <tr class="formcolor">
          <td colspan="2">
            <table class="normal">
              <tr class="formcolor">
                <td class="heading">{tr}MIME Type{/tr}</td>
                <td class="heading">{tr}System command{/tr}</td>
              </tr>
              {foreach  key=mime item=cmd from=$fgal_handlers}
              <tr>
                <td class="odd">{$mime}</td>
                <td class="odd">
                  <input name="mimes[{$mime}]" type="text" value="{$cmd|escape:html}" size="30"/></td>
              </tr>
              {/foreach}
              <tr>
                <td class="odd">
                  <input name="newMime" type="text" size="30" /></td>
                <td class="odd">
                  <input name="newCmd" type="text" size="30"/></td>
              </tr>
            </table>
          </td>
        </tr>
      
        <tr class="formcolor">
          <td colspan="2" align="left">
            <input type="submit" name="filegalredosearch" value="{tr}Reindex all files for search{/tr}"/>
          </td>
        </tr>
    
        <tr class="formcolor">
          <td colspan="2" class="button">
            <input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
