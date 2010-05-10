{capture name=msg assign=msg} 
<!--table border="0" cellspacing="4" cellpadding="4">
	<tr>
		<td style="text-align: center">
			<img src="{$fileId|sefurl:thumbnail}" />
		</td>
		<td>
		{if $filegals_manager neq ''}
			{assign var=seturl value=$fileId|sefurl:display}

			{* Note: When using this code inside FCKeditor, SetMyUrl function is not defined and we use FCKeditor SetUrl native function *}
			<a href="javascript:if (typeof window.opener.SetMyUrl != 'undefined') window.opener.SetMyUrl('{$filegals_manager|escape}','{$seturl}'); else window.opener.SetUrl('{$tikiroot}{$seturl}'); checkClose();" title="{tr}Click Here to Insert in Wiki Syntax{/tr}">{$name} ({$size|kbsize})</a>
		{else}
			<b>{$name} ({$size|kbsize})</b>
		{/if}
			<div>
			{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Additional Info{/tr}"}
			<span id="close_uploadinfos{$fileId}" style="display:none">
				  {button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
			</span>
			</div>
			<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
				{tr}You can download this file using{/tr}: <div class="code"><a class="link" href="{$fileId|sefurl:file}">{$fileId|sefurl:file}</a></div>
				{tr}You can link to the file from a Wiki page using{/tr}: <div class="code">[{$fileId|sefurl:file}|{$name} ({$size|kbsize})]</div>
				{tr}You can display an image in a Wiki page using{/tr}: <div class="code">&#x7b;img src="{$fileId|sefurl:preview}" link="{$fileId|sefurl:file}" alt="{$name} ({$size|kbsize})"}</div>
				{if $prefs.feature_shadowbox eq 'y'}
					{tr}Or using as a thumbnail with ShadowBox{/tr}: <div class="code">&#x7b;img src="{$fileId|sefurl:thumbnail}" link="{$fileId|sefurl:preview}" rel="shadowbox[gallery];type=img" alt="{$name} ({$size|kbsize})"}</div>
				{/if}
				{tr}You can link to the file from an HTML page using{/tr}: <div class="code">&lt;a href="{$fileId|sefurl:file}"&gt;{$name} ({$size|kbsize})&lt;/a&gt;</div>
			</div>
		</td>
	</tr>
</table-->
{/capture}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	parent.FileGallery.upload.progress('{$FormId}','{$msg|escape:"javascript"}');
//--><!]]>
</script>



{assign var=seturl value=$fileId|sefurl:display}
<script>
{if $filegals_manager neq ''}
	if (parent.FileGallery.upload.asimage)
		parent.FileGallery.upload.insertImage('{$fileId}',parent.FileGallery.upload.dimoriginal,parent.FileGallery.upload.dimwidth,parent.FileGallery.upload.dimheight);
	else if (parent.FileGallery.upload.aslink)
		parent.FileGallery.upload.insertLink('{$fileId}',parent.FileGallery.upload.linktitle);
	else
		parent.FileGallery.upload.insert('{$fileId}');
	if (!parent.document.getElementById("fg-insert-as-image"))
		parent.FileGallery.open('tiki-list_file_gallery.php?galleryId={$galleryId}&filegals_manager={$filegals_manager}');
{else}
	{if $fgspecial neq ''}
		parent.FileGallery.open('tiki-list_file_gallery.php?galleryId={$galleryId}&filegals_manager={$filegals_manager}');
		parent.FileGallery.upload.close();
	{else}
		parent.location = 'tiki-list_file_gallery.php?galleryId={$galleryId}';
	{/if}
{/if}
</script>
