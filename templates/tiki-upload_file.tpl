{* $Id$ *}

{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}

{title help="File+Galleries" admpage="fgal"}{if $editFileId}{tr}Edit File:{/tr} {$fileInfo.filename}{else}{tr}Upload File{/tr}{/if}{/title}

{if !empty($galleryId) or (count($galleries) > 0 and $tiki_p_list_file_galleries eq 'y') or count($uploads) > 0}
<div class="navbar">
	{if !empty($galleryId)}
		{button galleryId="$galleryId" href="tiki-list_file_gallery.php" _text="{tr}Browse Gallery{/tr}"}
	{/if}

	{if count($galleries) > 0 and $tiki_p_list_file_galleries eq 'y'}
		{if $filegals_manager neq ''}
			{assign var=fgmanager value=$filegals_manager|escape}
			{button href="tiki-list_file_gallery.php?filegals_manager=$fgmanager" _text="{tr}List Galleries{/tr}"}
		{else}
			{button href="tiki-list_file_gallery.php" _text="{tr}List Galleries{/tr}"}
		{/if}
	{/if}
	{if count($uploads) > 0}
		{button href="#upload" _text="{tr}Upload File{/tr}"}
	{/if}
	{if isset($filegals_manager)}
		{if $simpleMode eq 'y'}{button simpleMode='n' galleryId=$galleryId href="" _text="{tr}Advanced mode{/tr}" _ajax="n"}{else}{button galleryId=$galleryId href="" _text="{tr}Simple mode{/tr}" _ajax="n"}{/if}
		<span{if $simpleMode eq 'y'} style="display:none;"{/if}>
			<label for="keepOpenCbx">{tr}Keep gallery window open{/tr}</label>
			<input type="checkbox" id="keepOpenCbx" checked="checked">
		</span>
	{/if}
</div>
{/if}

{if count($errors) > 0}
	<div class="simplebox highlight">
	<h2>{tr}Errors detected{/tr}</h2>
	{section name=ix loop=$errors}
		{$errors[ix]}<br />
	{/section}
	{button href="#upload" _text="{tr}Retry{/tr}"}
	</div>
{/if}


{if $prefs.javascript_enabled eq 'y'}
<div id="upload_progress">
<iframe id="upload_progress_0" name="upload_progress_0" height="1" width="1" style="border:0px none"></iframe>
</div>
<div id='progress'>
<div id='progress_0'></div>
</div>
{/if}

{if count($uploads) > 0}
	<h2>
	{if count($uploads) eq 1}
		{tr}The following file was successfully uploaded:{/tr}
	{else}
		{tr}The following files have been successfully uploaded:{/tr}
	{/if}
	</h2>

	<table border="0" cellspacing="4" cellpadding="4">
	{section name=ix loop=$uploads}
		<tr class="{cycle values="odd,even"}">
			<td style="text-align: center">
				<img src="{$uploads[ix].fileId|sefurl:thumbnail}" />
			</td>
			<td>
				{if $filegals_manager neq ''}
					<a href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$files[changes].wiki_syntax|escape}');checkClose();return false;" title="{tr}Click Here to Insert in Wiki Syntax{/tr}">{$uploads[ix].name} ({$uploads[ix].size|kbsize})</a>
				{else}
					<b>{$uploads[ix].name} ({$uploads[ix].size|kbsize})</b>
				{/if}
				{button href="#" _flip_id="uploadinfos`$uploads[ix].fileId`" _text="{tr}Additional Info{/tr}"}
				<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$uploads[ix].fileId}">
					{tr}You can download this file using:{/tr} <div class="code"><a class="link" href="{$uploads[ix].dllink}">{$uploads[ix].fileId|sefurl:file}</a></div>
					{tr}You can link to the file from a Wiki page using:{/tr} <div class="code">[{$uploads[ix].fileId|sefurl:file}|{$uploads[ix].name} ({$uploads[ix].size|kbsize})]</div>
					{tr}You can display an image in a Wiki page using:{/tr} <div class="code">&#x7b;img src="{$uploads[ix].fileId|sefurl:preview}" link="{$uploads[ix].fileId|sefurl:file}" alt="{$uploads[ix].name} ({$uploads[ix].size|kbsize})"}</div>
					{if $prefs.feature_shadowbox eq 'y'}
						{tr}Or using as a thumbnail with ShadowBox:{/tr} <div class="code">&#x7b;img src="{$uploads[ix].fileId|sefurl:thumbnail}" link="{$uploads[ix].fileId|sefurl:preview}" rel="shadowbox[gallery];type=img" alt="{$name} ({$uploads[ix].size|kbsize})"}</div>
					{/if}
					{tr}You can link to the file from an HTML page using:{/tr} <div class="code">&lt;a href="{$uploads[ix].dllink}"&gt;{$uploads[ix].name} ({$uploads[ix].size|kbsize})&lt;/a&gt;</div>
				</div>
			</td>
		</tr>
	{/section}
	</table>
	<br />

	<h2>{tr}Upload File{/tr}</h2>
{elseif isset($fileChangedMessage)}
	<div align="center">
		<div class="wikitext">
			{$fileChangedMessage}
		</div>
	</div>
{/if}

{if $editFileId and isset($fileInfo.lockedby) and $fileInfo.lockedby neq ''}
	{remarksbox type="note" title="{tr}Info{/tr}" icon="lock"}
	{if $user eq $fileInfo.lockedby}
		{tr}You locked the file{/tr}
	{else}
		{tr}The file is locked by {$fileInfo.lockedby}{/tr}
	{/if}
	{/remarksbox}
{/if}

<div>
	{capture name=upload_file assign=upload_str}
	<hr class="clear"/>



	<div class="fgal_file">
		<div class="fgal_file_c1">
		<table width="100%">
			{if $simpleMode neq 'y'}<tr>
				<td><label for="name">{tr}File title:{/tr}</label></td>
				<td width="80%">
					<input style="width:100%" type="text" id="name" name="name[]" {if isset($fileInfo) and $fileInfo.name}value="{$fileInfo.name}"{/if} size="40" /> {if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"} ({tr}required field for podcasts{/tr}){/if}
				</td>
			</tr>
			<tr>
				<td><label for="description">{tr}File description:{/tr}</label></td>
				<td>
					<textarea style="width:100%" rows="2" cols="40" id="description" name="description[]">{if isset($fileInfo) and $fileInfo.description}{$fileInfo.description}{/if}</textarea>
				{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}<br /><em>{tr}Required for podcasts{/tr}.</em>{/if}
				</td>
			</tr>{/if}
			<tr>
	{* File replacement is only here when the javascript upload action is not
	available in the file listing.
	This may be moved later in another specific place (e.g. simple popup) for
	non-javascript browsers since it is not really a "Property" of the file *}
			{if $prefs.javascript_enabled neq 'y' || !$editFileId}
				<td><label for="userfile">{tr}Upload from disk:{/tr}</label></td>
				<td>
					{if $editFileId}{$fileInfo.filename|escape}{/if}
					<input id="userfile" name="userfile[]" type="file" size="40"/>
				</td>
				{/if}
			</tr>
		</table>
	</div>
	{if $simpleMode neq 'y'}<div class="fgal_file_c2">
	<table width="100%">
	{if !$editFileId and $tiki_p_batch_upload_files eq 'y'}
		<tr><td>
			<label for="isbatch">{tr}Unzip zip files:{/tr}</label>
		</td><td width="80%">
			<input type="checkbox" id="isbatch" name="isbatch[]" />
		</td></tr>
	{/if}

	{if $prefs.fgal_delete_after eq 'y'}
		<tr><td>
			<label for="deleteAfter">{tr}File can be deleted after:{/tr}</label>
		</td><td width="80%">
			{if $editFileId}
				{html_select_duration prefix='deleteAfter' default_value=$fileInfo.deleteAfter}
			{else}
				{html_select_duration prefix='deleteAfter[]' default_unit=week}
			{/if}
		</td></tr>
	{/if}

		{if $editFileId}
			<input type="hidden" name="galleryId" value="{$galleryId}"/>
			<input type="hidden" name="fileId" value="{$editFileId}"/>
			<input type="hidden" name="lockedby" value="{$fileInfo.lockedby|escape}" \>
		{else}
			{if count($galleries) eq 0}
				<input type="hidden" name="galleryId" value="{$treeRootId}"/>
			{elseif empty($groupforalert)}
			<tr><td>
				<label for="galleryId">{tr}File gallery:{/tr}</label>
			</td><td width="80%">
				<select id="galleryId" name="galleryId[]">
					<option value="{$treeRootId}" {if $treeRootId eq $galleryId}selected="selected"{/if} style="font-style:italic; border-bottom:1px dashed #666;">{tr}Root{/tr}</option>
				{section name=idx loop=$galleries}
					{if $galleries[idx].id neq $treeRootId and $galleries[idx].perms.tiki_p_upload_files eq 'y'}
					<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name|escape}</option>
					{/if}
				{/section}
				</select>
			</td></tr>
			{else}
				<input type="hidden" name="galleryId" value="{$galleryId}"/>
			{/if}
		{/if}
		<tr><td>
			<label for="user">{tr}Uploaded by:{/tr}</label>
		</td><td width="80%">
			{user_selector id='user' name='user[]' select=$fileInfo.user editable=$tiki_p_admin_file_galleries}
		</td></tr>

		{if $prefs.feature_file_galleries_author eq 'y'}
			<tr><td>
				<label for="author">{tr}Creator of file, if different from the 'Uploaded by' user:{/tr}</label>
			</td><td width="80%">
				<input type="text" id="author"name="author[]" value="{$fileInfo.author|escape}" />
			</td></tr>
		{/if}
		{if !empty($groupforalert)}
			{if $showeachuser eq 'y' }
				<tr><td>
					{tr}Choose users to alert{/tr}
				</td><td width="80%">
			{/if}
			{section name=idx loop=$listusertoalert}
				{if $showeachuser eq 'n' }
					<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
				{else}
					<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
				{/if}
			{/section}
			{if $showeachuser eq 'y' }
				</td></tr>
			{/if}
		{/if}
		</table>
	</div>
	<div class="fgal_file_c3">
	{if $prefs.fgal_limit_hits_per_file eq 'y'}
		<label>
			<label for="hit_limit">{tr}Maximum number of downloads:{/tr}</label>
			<input type="text" id="hit_limit" name="hit_limit[]" value="{$hit_limit|default:0}"/>
			<br /><em>{tr}Use{/tr} {tr}0 for no limit{/tr}.</em>
		</label>
		<br/>
	{/if}

	{* We want comments only on updated files *}
	{if $prefs.javascript_enabled neq 'y' && $editFileId}
		<label>
			<label for="comment">{tr}Comment:{/tr}</label>
			<input type="text" id="comment" name="comment[]" value="" size="40" />
		</label>
		<br/>
	{/if}
</div>
</div><br/>
{if $prefs.javascript_enabled eq 'y' and !$editFileId}
	{include file='categorize.tpl' notable='y'}<br/>
{/if}
{else}
	<input type="hidden" name="galleryId" value="{$galleryId}"/>
{/if}{* end if $simpleMode neq 'y'*}
{if $prefs.javascript_enabled eq 'y'}
<input type="hidden" name="upload" />
{/if}
{/capture}
<div id="form">
<form {if $prefs.javascript_enabled eq 'y' and !$editFileId}onsubmit='return false' target='upload_progress_0'{/if} id='file_0' name='file_0' action='tiki-upload_file.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
<input type="hidden" name="formId" value="0"/>
{if $filegals_manager neq ''}
	<input type="hidden" name="filegals_manager" value="{$filegals_manager}"/>
{/if}
<input type="hidden" name="simpleMode" value="{$simpleMode}"/>
{$upload_str}
{if $editFileId}
	{include file='categorize.tpl' notable='y'}<br/>
	<input name="upload" type="submit" value="{tr}Save{/tr}"/>
{/if}
{if $prefs.javascript_enabled neq 'y' and !$editFileId}
{$upload_str}
{$upload_str}
{include file='categorize.tpl' notable='y'}<br/>
<hr />
<input type="submit" name="upload" value="{if $editFileId}{tr}Save{/tr}{else}{tr}Upload{/tr}{/if}"/>
{/if}
</form>
<div id="multi_1">
</div>
<hr class="clear"/>
<div id="page_bar">
{if $prefs.javascript_enabled eq 'y'  and  !$editFileId}
		<input type="button" onclick="upload_files('0', 'loader_0')" value="{tr}Upload{/tr}"/>
		{if $simpleMode neq 'y'}<input type="button" onclick="javascript:add_upload_file('multiple_upload')" value="{tr}Add File{/tr}"/>{/if}
{/if}
</div>
</div>
{if !empty($fileInfo.lockedby) and $user ne $fileInfo.lockedby}
	{icon _id="lock" class="" alt=""}
	<span class="attention">{tr}The file is locked by {$fileInfo.lockedby}{/tr}</span>
{/if}
<br />
{remarksbox type="note"}
	{tr}Maximum file size is around:{/tr}
	{if $tiki_p_admin eq 'y'}<a title="{$max_upload_size_comment}">{/if}
		{$max_upload_size|kbsize:true:0}
	{if $tiki_p_admin eq 'y'}</a>{/if}
{/remarksbox}

</div>

{if $prefs.javascript_enabled neq 'y' || ! $editFileId}
	{jq notonready=true}
		var nb_upload = 1;
		function add_upload_file() {
			tmp = "<form onsubmit='return false' id='file_"+nb_upload+"' name='file_"+nb_upload+"' action='tiki-upload_file.php' target='upload_progress_"+nb_upload+"' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>";
			{{if $filegals_manager neq ''}}
			tmp += '<input type="hidden" name="filegals_manager" value="{$filegals_manager}"/>';
			{{/if}}
			tmp += '<input type="hidden" name="formId" value="'+nb_upload+'"/>';
			tmp += '{{$upload_str|strip|escape:'javascript'}}';
			tmp += '</form><div id="multi_'+(nb_upload+1)+'"></div>';
			//tmp += '<div id="multi_'+(nb_upload+1)+'"></div>';
			document.getElementById('multi_'+nb_upload).innerHTML = tmp;
			document.getElementById('progress').innerHTML += "<div id='progress_"+nb_upload+"'></div>";
			document.getElementById('upload_progress').innerHTML += "<iframe id='upload_progress_"+nb_upload+"' name='upload_progress_"+nb_upload+"' height='1' width='1' style='border:0px none'></iframe>";
			nb_upload += 1;
		}

		function progress(id,msg) {
//			alert ('progress_'+id);
			document.getElementById('progress_'+id).innerHTML = msg;
		}

		function do_submit(n) {
//				alert(document.getElementById('file_'+n).name);
			if (document.forms['file_'+n].elements['userfile[]'].value != '') {
				progress(n,"<img src='img/spinner.gif'>{tr}Uploading file...{/tr}");
				document.getElementById('file_'+n).submit();
				document.getElementById('file_'+n).reset();
			} else {
				progress(n,"{tr}No File to Upload...{/tr} <span class='button'><a href='#' onclick='location.replace(location.href);return false;'>{tr}Retry{/tr}</a></span>");
			}
		}

		function upload_files(form, loader){
			//only do this if the form exists
			n=0;
			while (document.forms['file_'+n]){
				do_submit(n);
				n++;
			}
			hide('form');
		}
	{/jq}
{/if}

