{capture name=msg assign=msg}
<table border="0" cellspacing="4" cellpadding="4">
	<tr>
		<td style="text-align: center">
			<img src="tiki-download_file.php?fileId={$fileId}&amp;thumbnail=y" />
		</td>
		<td>
			<b>{$name} ({$size|kbsize})</b>
			<div>
			{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Additional Info{/tr}"}
			<span id="close_uploadinfos{$fileId}" style="display:none">
				  {button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
			</span>
			</div>
			<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
				{tr}You can download this file using{/tr}: <div class="code"><a class="link" href="{$dllink}">{$dllink}</a></div>
				{tr}You can link to the file from a Wiki page using{/tr}: <div class="code">[tiki-download_file.php?fileId={$fileId}|{$name} ({$size|kbsize})]</div>
				{tr}You can display an image in a Wiki page using{/tr}: <div class="code">&#x7b;img src="tiki-download_file.php?fileId={$fileId}&amp;preview" link="{$dllink}" alt="{$name} ({$size|kbsize})"}</div>
				{tr}You can link to the file from an HTML page using{/tr}: <div class="code">&lt;a href="{$dllink}"&gt;{$name} ({$size|kbsize})&lt;/a&gt;</div>
			</div>
		</td>
	</tr>
</table>
{/capture}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	parent.progress('{$FormId}','{$msg|escape:"javascript"}');
//--><!]]>
</script>



