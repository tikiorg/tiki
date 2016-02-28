{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}
{if !empty($filegals_manager)}
	{assign var=seturl value=$fileId|sefurl:display}
	{capture name=alink assign=alink}href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$syntax|escape}');checkClose();return false;" title="{tr}Click here to use the file{/tr}" class="tips"{/capture}
{else}
	{assign var=alink value=''}
{/if}
<table border="0" cellspacing="4" cellpadding="4">
	<tr>
		{if $view neq 'page'}
			{$type = $name|iconify:null:null:null:'filetype'}
			{if $type eq 'image/png' or $type eq 'image/jpeg'or $type eq 'image/jpg'
				or $type eq 'image/gif' or $type eq 'image/x-ms-bmp'}
					{$imagetypes = 'y'}
			{else}
				{$imagetypes = 'n'}
			{/if}
			<td style="text-align: center">
				{if $imagetypes eq 'y' or $prefs.theme_iconset eq 'legacy'}
					{if !empty($filegals_manager)}
						<a {$alink}>
							<img src="{$fileId|sefurl:thumbnail}"><br>
							<span class="thumbcaption">
								{tr}Click here to use the file{/tr}
							</span>
						</a>
					{else}
						<img src="{$fileId|sefurl:thumbnail}">
					{/if}
				{else}
					{$name|iconify:$type:null:3}
				{/if}
			</td>
			<td>
				{if !empty($filegals_manager)}
					<a {$alink}>{$name|escape} ({$size|kbsize})</a>
				{else}
					<b>{$name|escape} ({$size|kbsize})</b>
				{/if}
				{if $feedback_message != ''}
					<div class="upload_note">
						{$feedback_message}
					</div>
				{/if}
		{else}
			<td>
		{/if}
			<div>
				{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Syntax Tips{/tr}"}
				<span id="close_uploadinfos{$fileId}" style="display:none">
					{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
				</span>
			</div>
			<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
				<table>
					<tr>
						<td style="text-align:right">
							{tr}Link to file from a Wiki page:{/tr}
						</td>
						<td>
							<code>[{$fileId|sefurl:file}|{$name|escape}]</code>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span style="font-weight:bold; font-style: italic">{tr}For image files:{/tr}</span>
						</td>
					</tr>
					<tr>
						<td style="text-align:right">
							{tr}Display full size:{/tr}
						</td>
						<td>
							<code>&#x7b;img fileId="{$fileId}"}</code>
						</td>
					</tr>
					{if $prefs.feature_shadowbox eq 'y'}
						<tr>
							<td style="text-align:right">
								{tr}Display thumbnail that enlarges:{/tr}
							</td>
							<td>
								<code>{rdelim}img fileId="{$fileId}" thumb="box"{ldelim}</code>
							</td>
						</tr>
					{/if}
				</table>
			</div>
		</td>
	</tr>
</table>
