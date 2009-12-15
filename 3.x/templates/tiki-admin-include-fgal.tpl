{* $Id$ *}


{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}To create or remove file galleries, select{/tr} <a class="rbox-link" href="tiki-list_file_gallery.php">{tr}File Galleries{/tr}</a> {tr}from the application menu{/tr}.
<hr />
{tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
{tr}You have two options to accomplish this:<br /><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
{tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}
{/remarksbox}

<div class="cbox">
<form action="tiki-admin.php?page=fgal" method="post">
<table class="admin"><tr><td>
<div align="center" style="padding:1em"><input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Gallery Listing{/tr}|
				{tr}Search Indexing{/tr}
			{/strip}{/tabs}
{/if}

      {cycle name=content values="1,2,3" print=false advance=false reset=true}

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
	<div class="adminoptionlabel"><label for="ix">{tr}Home Gallery (main gallery){/tr}</label>
	<select name="home_file_gallery">
              {section name=ix loop=$file_galleries}
                <option value="{$file_galleries[ix].galleryId|escape}" {if $file_galleries[ix].galleryId eq $prefs.home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"...":true}</option>
			  {sectionelse}
			  <option value="">{tr}None{/tr}</option>
				{/section}
	</select>
	{if $file_galleries|@count ge '1'}
	<input type="submit" name="filegalset" value="{tr}Set{/tr}" />
	{else}
	{button href="tiki-list_file_gallery.php" _text="{tr}Create a Gallery{/tr}"}
	{/if}
	</div>
</div>


<div class="adminoptionbox">
	<div class="adminoptionlabel"><input type="radio" id="fgal_use_db1" name="fgal_use_db" value="y"
              {if $prefs.fgal_use_db eq 'y'}checked="checked"{/if} onclick="flip('storeinfile');" /><label for="fgal_use_db1">{tr}Store in database{/tr}.</label></div>
	<div class="adminoptionlabel"><input type="radio" id="fgal_use_db2" name="fgal_use_db" value="n"
            {if $prefs.fgal_use_db eq 'n'}checked="checked"{/if} onclick="flip('storeinfile');" /><label for="fgal_use_db2">{tr}Store in directory{/tr}.</label></div>

<div class="adminoptionboxchild" id="storeinfile" style="display:{if $prefs.fgal_use_db eq 'y'}none{else}block{/if};">
	<div class="adminoptionlabel"><label for="fgal_use_dir">{tr}Path{/tr}:</label> <input type="text" id="fgal_use_dir" name="fgal_use_dir" value="{$prefs.fgal_use_dir|escape}" size="50" />
	<br /><em>{tr}The server must be able to read/write the directory.{/tr} {tr}The directory can be outside the web space.{/tr}</em>
	</div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="fgal_podcast_dir">{tr}Podcast directory{/tr}: </label><input id="fgal_podcast_dir" type="text" name="fgal_podcast_dir" value="{$prefs.fgal_podcast_dir|escape}" size="50" /><br /><em>{tr}The server must be able to read/write the directory.{/tr} {tr}Required for podcasts.{/tr}</em></div>
</div>

<input type="hidden" name="filegalfeatures" />

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config"}{/if}</legend>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_file_galleries_rankings" name="feature_file_galleries_rankings" {if $prefs.feature_file_galleries_rankings eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_file_galleries_rankings">{tr}Rankings{/tr}</label></div>
</div>

<div class="adminoptionbox">
<input type="hidden" name="filegalcomprefs" />
	<div class="adminoption"><input type="checkbox" id="feature_file_galleries_comments" name="feature_file_galleries_comments"{if $prefs.feature_file_galleries_comments eq 'y'} checked="checked"{/if} onclick="flip('usecomments');" /></div>
	<div class="adminoptionlabel"><label for="feature_file_galleries_comments">{tr}Comments{/tr}</label></div>
<div class="adminoptionboxchild" id="usecomments" style="display:{if $prefs.feature_file_galleries_comments eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="file_galleries_comments_per_page">{tr}Default number per page{/tr}: </label><input size="5" type="text" name="file_galleries_comments_per_page" id="file_galleries_comments_per_page" value="{$prefs.file_galleries_comments_per_page|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="file_galleries_comments_default_ordering">{tr}Default Ordering{/tr}: </label>
	<select name="file_galleries_comments_default_ordering" id="file_galleries_comments_default_ordering">
              <option value="commentDate_desc" {if $prefs.file_galleries_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $prefs.file_galleries_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
	</div>
</div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="fgal_limit_hits_per_file" name="fgal_limit_hits_per_file"
              {if $prefs.fgal_limit_hits_per_file eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="fgal_limit_hits_per_file">{tr}Allow download limit per file{/tr}.</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="fgal_prevent_negative_score" name="fgal_prevent_negative_score"
              {if $prefs.fgal_prevent_negative_score eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="fgal_prevent_negative_score">{tr}Prevent download if score becomes negative{/tr}.</label>
	{if $prefs.feature_score ne 'y'}<br />{icon _id=information}{tr}Score is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="fgal_allow_duplicates">{tr}Allow same file to be uploaded more than once{/tr}:</label>
	<select name="fgal_allow_duplicates" id="fgal_allow_duplicates">
              <option value="n" {if $prefs.fgal_allow_duplicates eq 'n'}selected="selected"{/if}>{tr}Never{/tr}</option>
              <option value="y" {if $prefs.fgal_allow_duplicates eq 'y'}selected="selected"{/if}>{tr}Yes, even in the same gallery{/tr}</option>
              <option value="different_galleries" {if $prefs.fgal_allow_duplicates eq 'different_galleries'}selected="selected"{/if}>{tr}Only in different galleries{/tr}</option>
            </select>
	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_file_galleries_batch" name="feature_file_galleries_batch" {if $prefs.feature_file_galleries_batch eq 'y'}checked="checked"{/if} onclick="flip('usebatchload');" /></div>
	<div class="adminoptionlabel"><label for="feature_file_galleries_batch">{tr}Batch uploading{/tr}</label></div>

<div class="adminoptionboxchild" id="usebatchload" style="display:{if $prefs.feature_file_galleries_batch eq 'y'}block{else}none{/if};">
{tr}If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload files in that dir, either by scp, ftp, or other protocols{/tr}
	<div class="adminoptionlabel"><label for="fgal_batch_dir">{tr}Path{/tr}: </label><input type="text" id="fgal_batch_dir" name="fgal_batch_dir" value="{$prefs.fgal_batch_dir|escape}" size="50" />
	<br /><em>{tr}The server must be able to read the directory.{/tr} {tr}The directory can be outside the web space.{/tr}</em>
	</div>

</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_file_galleries_author" name="feature_file_galleries_author"
              {if $prefs.feature_file_galleries_author eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="feature_file_galleries_author">{tr}Require file author's name for anonymous uploads{/tr}.</label></div>
</div>

</fieldset>

<fieldset><legend>{tr}Upload Regex{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config#Filename_must_match:"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="fgal_match_regex">{tr}Must match{/tr}:</label> <input type="text" name="fgal_match_regex" id="fgal_match_regex" value="{$prefs.fgal_match_regex|escape}"/></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="fgal_nmatch_regex">{tr}Cannot match{/tr}:</label> <input type="text" name="fgal_nmatch_regex" id="fgal_nmatch_regex" value="{$prefs.fgal_nmatch_regex|escape}" /></div>
</div>

</fieldset>

     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Gallery Listings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}

{remarksbox title="Note"}{tr}Changing these settings will <em>not</em> affect existing file galleries. These changes will apply <em>only</em> to new file galleries{/tr}.{/remarksbox}

<input type="hidden" name="filegallistprefs" />
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="fgal_sortorder">{tr}Default sort order{/tr}:</label>
	<select name="fgal_sortorder" id="fgal_sortorder">
			{foreach from=$options_sortorder key=key item=item}
			<option value="{$item|escape}" {if $fgal_sortorder == $item} selected="selected"{/if}>{$key}</option>
			{/foreach}
	</select>
<div class="adminoptionboxchild">
	<div class="adminoptionlabel"><input type="radio" id="fgal_sortdirection1" name="fgal_sortdirection" value="desc" {if $fgal_sortdirection == 'desc'}checked="checked"{/if} /><label for="fgal_sortdirection1">{tr}Descending{/tr}</label></div>
	<div class="adminoptionlabel"><input type="radio" id="fgal_sortdirection2" name="fgal_sortdirection" value="asc" {if $fgal_sortdirection == 'asc'}checked="checked"{/if} /><label for="fgal_sortdirection2">{tr}Ascending{/tr}</label></div>
</div>	
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Select which items to display when listing galleries: {/tr}:</div>
        <table class="admin">
		{include file="fgal_listing_conf.tpl"}
		</table>
</div>

	      {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>
	
	
    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Search Indexing{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="fgal_enable_auto_indexing" name="fgal_enable_auto_indexing" {if $prefs.fgal_enable_auto_indexing eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="fgal_enable_auto_indexing">{tr}Automatically index files on upload or change{/tr}.</label></div>
</div>	  

<input name="filegalhandlers" type="hidden" />
<div class="adminoptionbox">
<fieldset><legend>{tr}Handlers{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config#File_galleries_search_indexing"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Add custom handlers to make your files &quot;searchable&quot; content{/tr}.
    <ul>
      <li>{tr}Use <strong>%1</strong> as the internal file name. For example, use <strong>strings %1</strong> to convert the document to text, using the Unix <strong>strings</strong> command{/tr}.</li>
	  <li>{tr}To delete a handler, leave the <strong>System Command</strong> field blank{/tr}.</li>
   </ul>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel">
	<table class="normal">
              <tr class="formcolor">
                <th>{tr}MIME Type{/tr}</th>
                <th>{tr}System Command{/tr}</th>
              </tr>
			   {cycle values="odd,even" print=false}
              {foreach  key=mime item=cmd from=$fgal_handlers}
              <tr>
                <td class="{cycle advance=false}">{$mime}</td>
                <td class="{cycle advance=true}">
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
	</div>
</div>
</fieldset>
	  
<div class="adminoptionbox">
	<div class="adminoptionlabel"><div align="center"><input type="submit" name="filegalredosearch" value="{tr}Reindex all files for search{/tr}"/></div></div>
</div>
	  
	      {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>
</div>

<div align="center" style="padding:1em"><input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" /></div>

</td></tr></table>
</form>
</div>

