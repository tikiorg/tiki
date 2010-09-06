{* $Id$ *}

{title}
  {if $user ne $userwatch}
    {tr}Avatar:{/tr} {$userwatch}
  {else}
    {tr}Pick your avatar{/tr}
  {/if}
{/title}


{if $user eq $userwatch}
	{include file='tiki-mytiki_bar.tpl'}
{else}
	<div class="navbar">
		{assign var=thisuserwatch value=$userwatch|escape}
		{button href="tiki-user_preferences.php?view_user=$thisuserwatch" _text="{tr}User Preferences{/tr}"}
	</div>
{/if}
<h2>{if $user eq $userwatch}{tr}Your current avatar{/tr}{else}{tr}Avatar{/tr}{/if}</h2>
{if $avatar}{$avatar}
{if $user_picture_id}
{wikiplugin _name="img" thumb="y" fileId="$user_picture_id"}{/wikiplugin}
{/if}
{else}{tr}no avatar{/tr}{/if}
{if sizeof($avatars) eq 0 and $avatar}
<a class="link" href="tiki-pick_avatar.php?reset=y&amp;view_user{$userwatch|escape}" title="{tr}reset{/tr}">{icon _id='cross' alt="{tr}reset{/tr}"}</a>
{/if}

{if sizeof($avatars) > 0}

{if $showall eq 'y'}
<h2>{if $user eq $userwatch}{tr}Pick avatar from the library{/tr}{else}{tr}Pick user Avatar{/tr}{/if} <a href="tiki-pick_avatar.php?showall=n">{tr}Hide all{/tr}</a> {$numav} {tr}icons{/tr}</h2>
<div class="normal">
{section name=im loop=$avatars}
<a href="tiki-pick_avatar.php?showall=n&amp;avatar={$avatars[im]|escape:"url"}&amp;uselib=use"><img src="{$avatars[im]}" alt=''/></a>
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

<h2>{tr}Pick avatar from the library{/tr} <a href="tiki-pick_avatar.php?showall=y">{tr}Show all{/tr}</a> {$numav} {tr}Items{/tr}</h2>
<form action="tiki-pick_avatar.php" method="post">
<input id="avatar" type="hidden" name="avatar" value="{$yours|escape}" />
{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}" />{/if}
<table class="formcolor">
<tr>
 <td>
 <div align="center">
<a class="link" href="javascript:subavt();">{tr}Prev{/tr}</a>
<img id='avtimg' src="{$yours}" alt="{tr}Avatar Image{/tr}"/>
<a class="link" href="javascript:addavt();">{tr}Next{/tr}</a>
</div>
 </td>
</tr>
<tr>
 <td>
   <div align="center">
	 <input type="submit" name="rand" value="{tr}random{/tr}" />
	 <input type="submit" name="uselib" value="{tr}Use{/tr}" /> 
	 <input type="submit" name="reset" value="{tr}no avatar{/tr}" /> 
	 </div>
 </td>
</tr>
</table>
</form>
{/if}

{/if}

<div class="normal">
<form enctype="multipart/form-data" action="tiki-pick_avatar.php" method="post">
<fieldset>
<legend><strong>{tr}Upload your own avatar{/tr}</strong></legend>
{if $user ne $userwatch}<input type="hidden" name="view_user" value="{$userwatch|escape}" />{/if}
<label for="userfile1">{if $prefs.user_store_file_gallery_picture neq 'y'}{tr}File (only .gif, .jpg and .png images approximately 45px × 45px){/tr}{else}{tr}File (only .gif, .jpg and .png images){/tr}{/if}:</label>
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
<input id="userfile1" name="userfile1" type="file" />
<input type="submit" name="upload" value="{tr}Upload{/tr}" />
</fieldset>
</form>
</div>
