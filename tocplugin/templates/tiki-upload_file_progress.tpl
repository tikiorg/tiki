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
<div class="media">
	{if $view neq 'page'}
	{$type = $name|iconify:null:null:null:'filetype'}
	{if $type eq 'image/png' or $type eq 'image/jpeg'or $type eq 'image/jpg'
	or $type eq 'image/gif' or $type eq 'image/x-ms-bmp'}
		{$imagetypes = 'y'}
	{else}
		{$imagetypes = 'n'}
	{/if}
	<div class="media-left">
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
	</div>
	<div class="media-body">
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
		<div>
		{/if}
			<div class="margin-bottom-sm" style="margin-top: 1em;">
			{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="{tr}Syntax Tips{/tr}"}
			<span id="close_uploadinfos{$fileId}" style="display:none">
				{button href="#" _onclick="javascript:flip('uploadinfos$fileId');flip('close_uploadinfos$fileId','inline');return false;" _text="({tr}Hide{/tr})"}
			</span>
		</div>
		<div style="{if $prefs.javascript_enabled eq 'y'}display:none;{/if}" id="uploadinfos{$fileId}">
			<div class="row">
				<div class="col-sm-6 text-right">
					{tr}Link to file from a Wiki page:{/tr}
				</div>
				<div class="col-sm-6">
					<code>[{$fileId|sefurl:file}|{$name|escape}]</code>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 text-right">
					<strong><em>{tr}For image files:{/tr}</em></strong>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 text-right">
					{tr}Display full size:{/tr}
				</div>
				<div class="col-sm-6">
					<code>{ldelim}img fileId="{$fileId}"{rdelim}</code>
				</div>
			</div>
			{if $prefs.feature_shadowbox eq 'y'}
				<div class="row">
					<div class="col-sm-6 text-right">
						{tr}Display thumbnail that enlarges:{/tr}
					</div>
					<div class="col-sm-6">
						<code>{ldelim}img fileId="{$fileId}" thumb="box"{rdelim}</code>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>
