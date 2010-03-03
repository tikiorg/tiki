{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}
{if !empty($filegals_manager)}
	{assign var=seturl value=$fileId|sefurl:display}
	{capture name=alink assign=alink}href="javascript:if (typeof window.opener.SetMyUrl != 'undefined') window.opener.SetMyUrl('{$filegals_manager|escape}','{$seturl}'); else window.opener.SetUrl('{$tikiroot}{$seturl}'); if (typeof checkClose != 'undefined') checkClose(); else window.close();" title="{tr}Click Here to Insert in Wiki Syntax{/tr}" class="tips"{/capture}
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
			<div class="upload_note">{$feedback_message}</div>
		{/if}
			{if empty($filegals_manager)}<div>
			{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Additional Info{/tr}"}
			<span id="close_uploadinfos{$fileId}" style="display:none">
				  {button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
			</span>
			</div>
			<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
				<div style="font-style: italic; ">{tr} Syntax tips:{/tr}</div>
				<span style="line-height: 150%">{tr}Link to file from a Wiki page{/tr}:</span><br/>&nbsp;&nbsp;&nbsp;<tt style="color: brown;">[{$fileId|sefurl:file}|{$name}]</tt><br/>
				<div style="font-style: italic; margin-top: 10px">{tr}For image files:{/tr}</div>
				<span style="line-height: 150%">{tr}To display in a Wiki page{/tr}:</span><br/>&nbsp;&nbsp;&nbsp;<tt style="color: brown;">&#x7b;img fileId={$fileId}}</tt><br/>
				{if $prefs.feature_shadowbox eq 'y'}
					<span style="line-height: 200%">{tr}Display thumbnail that enlarges{/tr}: </span><br/>&nbsp;&nbsp;&nbsp;<tt style="color: brown;">&#x7b;img fileId={$fileId} thumb=y rel=box[g]}</tt><br/>
				{/if}
			</div>{/if}
		</td>
	</tr>
</table>
{/capture}
<script type='text/javascript'><!--//--><![CDATA[//><!--
	parent.progress('{$FormId}','{$msg|escape:"javascript"}');
//--><!]]></script>
