{if $feature_shoutbox eq 'y' and $tiki_p_view_shoutbox eq 'y'}
<div class="box">
<div class="box-title">
{tr}ShoutBox{/tr}
</div>
<div class="box-data">
{if $tiki_p_post_shoutbox eq 'y'}
<form action="{$shout_ownurl}" method="post">
<div align="center">
<textarea rows="3" class="tshoutbox"  name="shout_msg" maxlength="250"></textarea>
<br/>
<input type="submit" name="shout_send" value="{tr}send{/tr}" /></div><br/>
</form>
{/if}
{section loop=$shout_msgs name=ix}
<div class="shoutboxmodmsg">
<b>{$shout_msgs[ix].user|userlink:"linkmodule"}</b> at {$shout_msgs[ix].timestamp|tiki_long_time}
<br/>
{$shout_msgs[ix].message}{if $tiki_p_admin_shoutbox eq 'y'} [<a href="{$shout_ownurl}shout_remove={$shout_msgs[ix].msgId}" class="linkmodule">x</a>|<a href="tiki-shoutbox.php?msgId={$shout_msgs[ix].msgId}" class="linkmodule">e</a>]{/if}
</div>
{/section}
</div>
</div>
{/if}
