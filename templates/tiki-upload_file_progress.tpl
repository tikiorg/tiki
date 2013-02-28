{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}
{if !empty($filegals_manager)}
	{assign var=seturl value=$fileId|sefurl:display}
	{capture name=alink assign=alink}href="#" onclick="window.opener.insertAt('{$filegals_manager}','{$syntax|escape}');checkClose();return false;" title="{tr}Click Here to Insert in Wiki Syntax{/tr}" class="tips"{/capture}
{else}
	{assign var=alink value=''}
{/if}
<table border="0" cellspacing="4" cellpadding="4">
	<tr>
		{if $view neq 'page'}
			<td style="text-align: center">
				{if !empty($filegals_manager)}
					<a {$alink}><img src="{$fileId|sefurl:thumbnail}" /><br><span class="thumbcaption">{tr}Click Here to Insert in Wiki Syntax{/tr}</span></a>
				{else}
					<img src="{$fileId|sefurl:thumbnail}" />
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
						<td class="inline_syntax">
							[{$fileId|sefurl:file}|{$name|escape}]
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
						<td class="inline_syntax">
							&#x7b;img fileId="{$fileId}"}
						</td>
					</tr>
					{if $prefs.feature_shadowbox eq 'y'}
						<tr>
							<td style="text-align:right">
								{tr}Display thumbnail that enlarges:{/tr}
							</td>
							<td class="inline_syntax">
								&#x7b;img fileId="{$fileId}" thumb="y" rel="box[g]"}
							</td>
						</tr>
					{/if}
				</table>
			</div>
		</td>
	</tr>
</table>
