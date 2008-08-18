{capture name=msg assign=msg}
<table border="0" cellspacing="4" cellpadding="4">
	<tr>
		<td style="text-align: center">
			<img src="tiki-download_file.php?fileId={$fileId}&amp;thumbnail=y" />
		</td>
		<td>
			<b>{$name} ({$size|kbsize})</b>
			<div class="button2">
				<a href="#" onclick="javascript:flip('uploadinfos{$fileId}');flip('uploadinfos{$fileId}_close','inline');return false;" class="linkbut">
				{tr}Additional Info{/tr}
				<span id="uploadinfos{$fileId}_close" style="display:none">({tr}Hide{/tr})</span>
				</a>
			</div>
			<div style="display:none;" id="uploadinfos{$fileId}">
				{tr}You can download this file using{/tr}: <a class="link" href="{$dllink}">{$dllink}</a><br />
				{tr}You can link to the file from a Wiki page using{/tr}: <div class="code">[tiki-download_file.php?fileId={$fileId}|{$name} ({$size|kbsize})]</div>
				{tr}You can display an image in a Wiki page using{/tr}: <div class="code">&#x7b;img src="{$dllink}" alt="{$name} ({$size|kbsize})"}</div>
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



