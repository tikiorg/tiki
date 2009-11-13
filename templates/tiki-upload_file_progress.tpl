{if !empty($filegals_manager) and !isset($smarty.request.simpleMode)}
	{assign var=simpleMode value='y'}
{else}
	{assign var=simpleMode value='n'}
{/if}
{if !empty($filegals_manager)}
	{assign var=seturl value=$fileId|sefurl:display}
	{capture name=alink assign=alink}href="javascript:if (typeof window.opener.SetMyUrl != 'undefined') window.opener.SetMyUrl('{$filegals_manager|escape}','{$seturl}'); else window.opener.SetUrl('{$tikiroot}{$seturl}'); if (typeof checkClose != 'undefined') checkClose(); else window.close();" title="{tr}Click Here to Insert in Wiki Syntax{/tr}" class="tips"{/capture}
{else}
{assign var=alink value''}
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
			{if empty($filegals_manager)}<div>
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
			</div>{/if}
		</td>
	</tr>
</table>
{/capture}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	parent.progress('{$FormId}','{$msg|escape:"javascript"}');
//--><!]]>
</script>



