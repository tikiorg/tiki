{* $Id$ *}

{title help="File+Galleries" admpage="fgal"}{if $editFileId}{tr}Edit File:{/tr} {$fileInfo.filename}{else}{tr}Upload File{/tr}{/if}{/title}

{if !empty($galleryId) or (count($galleries) > 0 and $tiki_p_list_file_galleries eq 'y') or count($uploads) > 0}
<div class="navbar">
	{if !empty($galleryId)}
		{if $filegals_manager neq ''}
			{assign var=fgmanager value=$filegals_manager|escape}
			{button href="tiki-list_file_gallery.php?galleryId=$galleryId&amp;filegals_manager=$fmanager" _text="{tr}Browse Gallery{/tr}"}
		{else}
			{button href="tiki-list_file_gallery.php?galleryId=$galleryId" _text="{tr}Browse Gallery{/tr}"}
		{/if}
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
</div>
{/if}

{if count($galleries) > 0 || $editFileId}

	{if count($errors) > 0}
		<div class="simplebox highlight">
		<h2>{tr}Errors detected{/tr}</h2>
		{section name=ix loop=$errors}
			{$errors[ix]}<br />
		{/section}
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
			{tr}The following file was successfully uploaded{/tr}:
		{else}
			{tr}The following files have been successfully uploaded{/tr}:
		{/if}
		</h2>

		<table border="0" cellspacing="4" cellpadding="4">
		{section name=ix loop=$uploads}
			<tr>
				<td class="{cycle values="odd,even"}" style="text-align: center">
					<img src="tiki-download_file.php?fileId={$uploads[ix].fileId}&amp;thumbnail=y" />
				</td>
				<td>
					<b>{$uploads[ix].name} ({$uploads[ix].size|kbsize})</b>
					{button href="#" _flip_id="uploadinfos`$uploads[ix].fileId`" _text="{tr}Additional Info{/tr}"}
					<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$uploads[ix].fileId}">
						{tr}You can download this file using{/tr}: <div class="code"><a class="link" href="{$uploads[ix].dllink}">{$uploads[ix].dllink}</a></div>
						{tr}You can link to the file from a Wiki page using{/tr}: <div class="code">[tiki-download_file.php?fileId={$uploads[ix].fileId}|{$uploads[ix].name} ({$uploads[ix].size|kbsize})]</div>
						{tr}You can display an image in a Wiki page using{/tr}: <div class="code">&#x7b;img src="tiki-download_file.php?fileId={$uploads[ix].fileId}&amp;preview" link="{$uploads[ix].dllink}" alt="{$uploads[ix].name} ({$uploads[ix].size|kbsize})"}</div>
						{tr}You can link to the file from an HTML page using{/tr}: <div class="code">&lt;a href="{$uploads[ix].dllink}"&gt;{$uploads[ix].name} ({$uploads[ix].size|kbsize})&lt;/a&gt;</div>
					</div>
				</td>
			</tr>
		{/section}
		</table>
		<br />

		<h2>{tr}Upload File{/tr}</h2>
	{elseif $fileChangedMessage}
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
				<tr>
					<td>{tr}File Title:{/tr}</td>
					<td width="80%">
						<input style="width:100%" type="text" name="name[]" {if $fileInfo.name}value="{$fileInfo.name}"{/if} size="40" /> {if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"} ({tr}required field for podcasts{/tr}){/if}
					</td>
				</tr>
				<tr>
					<td>{tr}File Description:{/tr}</td>
					<td>
						<textarea style="width:100%" rows="2" cols="40" name="description[]">{if $fileInfo.description}{$fileInfo.description}{/if}</textarea>
					{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"} ({tr}required field for podcasts{/tr}){/if}
					</td>
				</tr>
				<tr>
		{* File replacement is only here when the javascript upload action is not
		available in the file listing.
		This may be moved later in another specific place (e.g. simple popup) for
		non-javascript browsers since it is not really a "Property" of the file *}
				{if $prefs.javascript_enabled neq 'y' || !$editFileId}
					<td>{tr}Upload from disk:{/tr}</td>
					<td>
						{if $editFileId}{$fileInfo.filename|escape}{/if}
						<input name="userfile[]" type="file" size="15"/>
					</td>
					{/if}
				</tr>
			</table>
		</div>
		<div class="fgal_file_c2">
		<table width="100%">
		{if !$editFileId and $tiki_p_batch_upload_files eq 'y'}
			<tr><td>
				{tr}Unzip zip files{/tr}
			</td><td width="80%">
				<input type="checkbox" name="isbatch[]" />
			</td></tr>
		{/if}

			{if $editFileId}
				<input type="hidden" name="galleryId" value="{$galleryId}"/>
				<input type="hidden" name="fileId" value="{$editFileId}"/>
				<input type="hidden" name="lockedby" value="{$fileInfo.lockedby|escape}" \>
			{else}
				{if $groupforalert eq ''}
				<tr><td>
					{tr}File Gallery:{/tr}
				</td><td width="80%">
					<select name="galleryId[]">
					{section name=idx loop=$galleries}
						{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_upload_files eq 'y')}
						<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name|escape}</option>
						{/if}
					{/section}
					</select>
				</td></tr>
				{else}
					<input type="hidden" name="galleryId" value="{$galleryId}"/>
				{/if}
			{/if}
			{if $tiki_p_admin_file_galleries eq 'y'}
				<tr><td>
					{tr}Creator:{/tr}
				</td><td width="80%">
					<select name="user[]">
					{section name=ix loop=$users}
						<option value="{$users[ix].login|escape}"{if (isset($fileInfo) and $fileInfo.user eq $users[ix].login) or (!isset($fileInfo) and $user == $users[ix].login)}  selected="selected"{/if}>{$users[ix].login|username|escape}</option>
					{/section}
					</select>
				</td></tr>
			{/if}

			{if $prefs.feature_file_galleries_author eq 'y'}
				<tr><td>
					{tr}Author if not the file creator:{/tr}
				</td><td width="80%">
					<input type="text" name="author[]" value="{$fileInfo.author|escape}" />
				</td></tr>
			{/if}
			{if $groupforalert ne ''}
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
				{tr}Maximum amount of downloads:{/tr}
				<input type="text" name="hit_limit[]" value="{$hit_limit|default:0}"/>
				{tr}0 for no limit{/tr}
			</label>
			<br/>
		{/if}

		{* We want comments only on updated files *}
		{if $prefs.javascript_enabled neq 'y' && $editFileId}
			<label>
				{tr}Comment:{/tr}
				<input type="text" name="comment[]" value="" size="40" />
			</label>
			<br/>
		{/if}
	</div>
	</div>
	{if $prefs.javascript_enabled eq 'y' and !$editFileId}
		{include file=categorize.tpl notable='y'}<br/>
	{/if}
	{if $prefs.javascript_enabled eq 'y'}
	<input type="hidden" name="upload" />
	{/if}
	{/capture}
	<div id="form">
	<form {if $prefs.javascript_enabled eq 'y' and !$editFileId}onsubmit='return false' target='upload_progress_0'{/if} id='file_0' name='file_0' action='tiki-upload_file.php' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>
	<input type="hidden" name="formId" value="0"/>
	{$upload_str}
	{if $editFileId}
		{include file=categorize.tpl notable='y'}<br/>
		<input name="upload" type="submit" value="{tr}Save{/tr}"/>
	{/if}
	{if $prefs.javascript_enabled neq 'y' and !$editFileId}
	{$upload_str}
	{$upload_str}
	{include file=categorize.tpl notable='y'}<br/>
	<hr />
	<input type="submit" name="upload" value="{if $editFileId}{tr}Save{/tr}{else}{tr}Upload{/tr}{/if}"/>
	{/if}
	</form>
	<div id="multi_1">
	</div>
	<hr class="clear"/>
	<div id="page_bar">
	{if $prefs.javascript_enabled eq 'y'  and  !$editFileId}
			<input type="button" onclick="upload('0', 'loader_0')" value="{tr}Upload{/tr}"/>
			<input type="button" onclick="javascript:add_upload_file('multiple_upload')" value="{tr lang=$lang}Add File{/tr}"/>
	{/if}
	</div>
	</div>
	{if !empty($fileInfo.lockedby) and $user ne $fileInfo.lockedby}
		{icon _id="lock" class="" alt=""}
		<span class="attention">{tr}The file is locked by {$fileInfo.lockedby}{/tr}</span>
	{/if}
	<br />
	{remarksbox type="note"}
		{tr}Maximum file size is limited to:{/tr}
		{if $tiki_p_admin eq 'y'}<a title="{$max_upload_size_comment}">{/if}
			{$max_upload_size|kbsize:true:0}
		{if $tiki_p_admin eq 'y'}</a>{/if}
	{/remarksbox}

	</div>
{else}
	{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle;"}
	{tr}No gallery available.{/tr}
	{tr}You have to create a gallery first!{/tr}
	<p><a href="tiki-list_file_gallery.php{if $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}">{tr}Create New Gallery{/tr}</a></p>
{/if}
	{if $prefs.javascript_enabled neq 'y' || ! $editFileId}
		<script type="text/javascript">
		<!--//--><![CDATA[//><!--
		{literal}
		var nb_upload = 1;
		function add_upload_file() {
			tmp = "<form onsubmit='return false' id='file_"+nb_upload+"' name='file_"+nb_upload+"' action='tiki-upload_file.php' target='upload_progress_"+nb_upload+"' enctype='multipart/form-data' method='post' style='margin:0px; padding:0px'>";
			{/literal}
			tmp += '<input type="hidden" name="formId" value="'+nb_upload+'"/>';
			tmp += '{$upload_str|strip|escape:'javascript'}';
			{literal}
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
			{/literal}
				progress(n,"<img src='lib/shadowbox/images/loading.gif'>{tr}Uploading file...{/tr}");
			{literal}
				document.getElementById('file_'+n).submit();
				document.getElementById('file_'+n).reset();
			} else {
				progress(n,"{tr}No File to Upload...{/tr}");
			}
		}

		function upload(form, loader){
			//only do this if the form exists
			n=0;
			while (document.forms['file_'+n]){
				do_submit(n);
				n++;
			}
			hide('form');
		}
		{/literal}
		//--><!]]>
		</script>
	{/if}

