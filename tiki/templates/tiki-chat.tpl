<br /><br /><br /><br />
<div align="center">
<h1>{tr}Welcome to the Tiki Chat Rooms{/tr}</h1>
{if $channels[0] ne ""}
<h2>{tr}Please select a chat channel{/tr}</h2>
<form action="tiki-chatroom.php" method="post">
<select name="channelId">
{section name=ix loop=$channels}
<option value="{$channels[ix].channelId|escape}">{$channels[ix].name}</option>
{/section}
</select><br /><br />
{if !$user}
{tr}Nickname{/tr}: <input type="text" name="nickname" /><br /><br />
{/if}
<input type="submit" name="enter" value="{tr}enter chat room{/tr}" />
</form>
{else}
<h3>{tr}There are no channels setup, please contact a site admin{/tr}</h3>
{/if}
</div>
