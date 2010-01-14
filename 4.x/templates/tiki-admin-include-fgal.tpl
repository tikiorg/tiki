{* $Id$ *}
{if !empty($errors)}
	{remarksbox type="errors" title="{tr}Errors{/tr}"}
	{foreach from=$errors item=error}
		{$error|escape}<br />
	{/foreach}
	{/remarksbox}
{/if}
{if !empty($feedbacks)}
	{remarksbox type="comment" title="{tr}Feedbacks{/tr}"}
	{foreach from=$feedbacks item=feedback}
		{$feedback|escape}<br />
	{/foreach}
	{/remarksbox}
{/if}
{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}To create or remove file galleries, select{/tr} <a class="rbox-link" href="tiki-list_file_gallery.php">{tr}File Galleries{/tr}</a> {tr}from the application menu{/tr}.
<hr />
{tr}If you decide to store files in a directory you must ensure that the user cannot access directly to the directory.{/tr}
{tr}You have two options to accomplish this:<br /><ul><li>Use a directory outside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use .htaccess to prevent the user from listing the directory contents</li></ul>{/tr}
{tr}To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/{/tr}
{/remarksbox}

<form action="tiki-admin.php?page=fgal" method="post">
<div class="heading input_submit_container" style="text-align: right">
	<input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" />
</div>

{tabset name="fgal_admin"}
	{tab name="{tr}General Settings{/tr}"}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label>{tr}Home Gallery (main gallery){/tr}</label>
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

{*
	{preference name='fgal_use_db'}
	<div class="adminoptionboxchild" id="fgal_use_db_childcontainer_1">
		 {preference name='fgal_use_dir'}
	</div>
*}
	{button href="tiki-admin.php?page=fgal&amp;move=to_fs" _text="{tr}Move files from database to directory{/tr}"}
	{button href="tiki-admin.php?page=fgal&amp;move=to_db" _text="{tr}Move files from directory to database{/tr}"}

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

	{preference name='fgal_podcast_dir'}

<input type="hidden" name="filegalfeatures" />

<fieldset>
	<legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config"}{/if}</legend>

	{preference name='feature_file_galleries_rankings'}

	{preference name='feature_file_galleries_comments'}
	<div class="adminoptionboxchild" id="feature_file_galleries_comments_childcontainer">
		 {preference name='file_galleries_comments_per_page'}
		 {preference name='file_galleries_comments_default_ordering'}
	</div>

	{preference name='fgal_limit_hits_per_file'}

	{preference name='fgal_prevent_negative_score'}
	<div class="adminoptionboxchild" id="fgal_prevent_negative_score_childcontainer">
		{if $prefs.feature_score ne 'y'}<br />{icon _id=information}{tr}Score is disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>.{/if}
	</div>

	{preference name='fgal_allow_duplicates'}

	{preference name='feature_file_galleries_batch'}
	<div class="adminoptionboxchild" id="feature_file_galleries_batch_childcontainer">
		{preference name='fgal_batch_dir'}
	</div>

	{preference name='feature_file_galleries_author'}
</fieldset>

<fieldset>
	<legend>{tr}Quota{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config#Quota"}{/if}</legend>
	{preference name='fgal_quota'}{tr}Used:{/tr} {$usedSize|kbsize}
	<div class="adminoptionboxchild" id="fgal_quota_childcontainer">
		{if !empty($prefs.fgal_quota)}
			{capture name='use'}{math equation="round((100*x)/(1024*1024*y))" x=$usedSize y=$prefs.fgal_quota}{/capture}
			{quotabar length='100' value='$smarty.capture.use'}
		{/if}
	</div>
	{preference name='fgal_quota_per_fgal'}
	<div class="adminoptionboxchild" id="fgal_quota_per_fgal_childcontainer">
		 {preference name='fgal_quota_default'}
	</div>
</fieldset>

<fieldset>
	<legend>{tr}Upload Regex{/tr}{if $prefs.feature_help eq 'y'} {help url="File+Gallery+Config#Filename_must_match:"}{/if}</legend>
	{preference name='fgal_match_regex'}
	{preference name='fgal_nmatch_regex'}
</fieldset>

	{/tab}

	{tab name="{tr}Gallery Listings{/tr}"}

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
	{preference name='fgal_quota_show'}

<div class="adminoptionbox">
	<div class="adminoptionlabel">{tr}Select which items to display when listing galleries: {/tr}:</div>
        <table class="admin">
		{include file='fgal_listing_conf.tpl'}
		</table>
</div>
	{/tab}

	{tab name="{tr}Search Indexing{/tr}"}
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
	  
</div>
	{/tab}
{/tabset}

<div class="input_submit_container clear" style="text-align: center">
	<input type="submit" name="filegalhandlers" value="{tr}Change preferences{/tr}" />
</div>
</form>
