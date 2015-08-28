{* $Id$ *}

{title}
	{if $user ne $userwatch}
		{tr}Profile picture:{/tr} {$userwatch}
	{else}
		{tr}Pick your profile picture{/tr}
	{/if}
{/title}


{if $user eq $userwatch}
	{include file='tiki-mytiki_bar.tpl'}
{else}
	<div class="t_navbar">
		{assign var=thisuserwatch value=$userwatch|escape}
		{button href="tiki-user_preferences.php?view_user=$thisuserwatch" class="btn btn-default" _text="{tr}User Preferences{/tr}"}
	</div>
{/if}

<h2>{if $user eq $userwatch}{tr}Your current profile picture{/tr}{else}{tr}Profile picture{/tr}{/if}</h2>
{if $avatar}
	<div>
		{if isset($user_picture_id)}{tr}Thumbnail{/tr}<br>{/if}
		{$avatar}
	</div>
	{if isset($user_picture_id)}
		<div>{tr}Full size{/tr}<br>
			<img src="tiki-download_file.php?fileId={$user_picture_id|escape}&amp;display=y">
		</div>
	{/if}
{else}
	{tr}no profile picture{/tr}
{/if}

{if sizeof($avatars) eq 0 and $avatar}
	<a class="link tips" href="tiki-pick_avatar.php?reset=y&amp;view_user{$userwatch|escape}" title=":{tr}Reset{/tr}">
		{icon name='remove'}
	</a>
{/if}

{if sizeof($avatars) > 0}

	{if $showall eq 'y'}
		<h2>{if $user eq $userwatch}{tr}Pick user profile picture from the library{/tr}{else}{tr}Pick user profile picture{/tr}{/if} <a href="tiki-pick_avatar.php?showall=n">{tr}Hide all{/tr}</a> {$numav} {tr}icons{/tr}</h2>
		<div class="table normal">
			{section name=im loop=$avatars}
				<a href="tiki-pick_avatar.php?showall=n&amp;avatar={$avatars[im]|escape:"url"}&amp;uselib=use"><img src="{$avatars[im]}" alt=''></a>
			{/section}
		</div>
	{else}

		{jq}
			var avatars = new Array();
			{{section name=ix loop=$avatars}
				avatars[{$smarty.section.ix.index}] = '{$avatars[ix]}';
				{if $smarty.section.ix.index eq $yours}
					{assign var="yours" value=$avatars[ix]}
				{/if}
			{/section}}
			var pepe=1;
			function addavt() {
				pepe++;
				if(pepe > avatars.length-1) {
					pepe =0;
				}
				document.getElementById('avtimg').src=avatars[pepe];
				document.getElementById('avatar').value=avatars[pepe];
			}

			function subavt() {
				pepe--;
				if(pepe < 0 ) {
					pepe=avatars.length-1
				}
				document.getElementById('avtimg').src=avatars[pepe];
				document.getElementById('avatar').value=avatars[pepe];
			}
		{/jq}

		<h2>{tr}Pick user profile picture from the library{/tr} <a href="tiki-pick_avatar.php?showall=y">{tr}Show all{/tr}</a> {$numav} {tr}Items{/tr}</h2>
		<form action="tiki-pick_avatar.php" method="post">
			<input id="avatar" type="hidden" name="avatar" value="{$yours|escape}">
			{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}">{/if}
			<table class="formcolor">
				<tr>
					<td>
						<div align="center">
							<a class="link" href="javascript:subavt();">{tr}Prev{/tr}</a>
							<img id='avtimg' src="{$yours}" alt="{tr}Profile picture{/tr}">
							<a class="link" href="javascript:addavt();">{tr}Next{/tr}</a>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div align="center">
							<input type="submit" class="btn btn-default btn-sm" name="rand" value="{tr}random{/tr}">
							<input type="submit" class="btn btn-default btn-sm" name="uselib" value="{tr}Use{/tr}">
							<input type="submit" class="btn btn-default btn-sm" name="reset" value="{tr}no profile picture{/tr}">
						</div>
					</td>
				</tr>
			</table>
		</form>
	{/if}
{/if}

<div class="table normal">
	<form enctype="multipart/form-data" action="tiki-pick_avatar.php" method="post" class="form-horizontal">
		<legend><strong>{tr}Upload your own profile picture{/tr}</strong></legend>
		{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}">{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Select your profile picture{/tr}</label>
			<div class="col-sm-7">
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
				<input id="userfile1" name="userfile1" type="file">
				<div class="help-block">
					{if $prefs.user_store_file_gallery_picture neq 'y'}{tr}File (only .gif, .jpg and .png images approximately 45px × 45px){/tr}{else}{tr}File (only .gif, .jpg and .png images){/tr}{/if}:
				</div>
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-7">
				<input type="submit" class="btn btn-primary btn-sm" name="upload" value="{tr}Upload{/tr}">
		    </div>
	    </div>
	</form>
</div>
