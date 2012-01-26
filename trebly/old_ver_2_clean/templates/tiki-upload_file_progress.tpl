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
{capture name=msg assign=msg}
<table border="0" cellspacing="4" cellpadding="4">
	<tr>
		<td style="text-align: center">
			<a {$alink}><img src="{$fileId|sefurl:thumbnail}" /></a>
		</td>
		<td>
			{if !empty($filegals_manager)}
				<a {$alink}>{$name} ({$size|kbsize})</a>
			{else}
				<b>{$name} ({$size|kbsize})</b>
			{/if}
			{if $feedback_message != ''}
				<div class="upload_note">
					{$feedback_message}
				</div>
			{/if}
			{if empty($filegals_manager)}
				<div>
					{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Additional Info{/tr}"}
					<span id="close_uploadinfos{$fileId}" style="display:none">
				  		{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
					</span>
				</div>
				<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
					<div style="font-weight:bold; font-style: italic; ">
						{tr} Syntax tips:{/tr}
					</div>
					<span style="line-height: 150%">
					{tr}Link to file from a Wiki page:{/tr}
					</span><br/>
					<table>
						<tr>
							<td width="6px">
							</td>
							<td class="inline_syntax">
								[{$fileId|sefurl:file}|{$name}]
							</td>
						</tr>
					</table>
					<div style="font-weight:bold; font-style: italic; margin-top: 10px">
						{tr}In addition, for image files:{/tr}
					</div>
					<span style="line-height: 150%">
						{tr}To display in a Wiki page:{/tr}</span><br/>
					<table>
						<tr>
							<td width="6px">
							</td>
							<td class="inline_syntax">
								&#x7b;img fileId={$fileId}}
							</td>
						</tr>
					</table>
					{if $prefs.feature_shadowbox eq 'y'}
						<span style="line-height: 200%">{tr}Display thumbnail that enlarges:{/tr} 
						</span><br/>
						<table>
							<tr>
								<td width="6px">
								</td>
								<td class="inline_syntax">
									&#x7b;img fileId={$fileId} thumb=y rel=box[g]}
								</td>
							</tr>
						</table>
					{/if}
				</div>
			{/if}
		</td>
	</tr>
</table>
{/capture}
<script type='text/javascript'><!--//--><![CDATA[//><!--
	parent.progress('{$FormId}','{$msg|escape:"javascript"}');
//--><!]]></script>
