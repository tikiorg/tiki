{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}
	<div id="upload-avatar" class="clearfix">
		<div class="user-avatar user-avatar-preview pull-left" style="width: {$prefs.user_small_avatar_size}px">{$userwatch|avatarize:"":"":false}</div>
		<form method="post" enctype="multipart/form-data" action="{service controller=user action=upload_avatar user={$userwatch|escape}}" id="uploadAvatarForm">
			<input id="userfile" name="userfile" type="file">
			<div class="submit">
				<button href="{service controller=user action=upload_avatar user={$userwatch|escape} reset=y}" class="pull-left btn btn-default">Remove Avatar</button>
				<button type="submit" class="btn btn-primary btn-upload-avatar disabled">Upload Avatar</button>
			</div>
		</form>
	</div>
{/block}