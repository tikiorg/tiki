<a class="pagetitle" href="tiki-list_games.php">{tr}Games{/tr}</a><br /><br />
<small>{tr}All games are from{/tr} <a href="http://www.miniclip.com">www.miniclip.com</a>. {tr}visit the site for more games and fun{/tr}</small><br /><br />
{if $tiki_p_admin_games eq 'y'}
<a href="tiki-list_games.php?uploadform=1">{tr}Upload a game{/tr}</a><br /><br />
{/if}
{if $uploadform eq 'y'}
<h2>{tr}Upload a new game{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-list_games.php" method="post">
<table>
<tr><td>{tr}Flash binary (.sqf or .dcr){/tr}:</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input name="userfile1" type="file" /></td></tr>
<tr><td>{tr}Thumbnail (if the game is foo.swf the thumbnail must be named foo.swf.gif or foo.swf.png or foo.swf.jpg){/tr}:</td><td>
<input name="userfile2" type="file"></td></tr>
<tr><td>{tr}description{/tr}:</td>
    <td><textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
</tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="upload" value="{tr}save{/tr}" /></td>
</table>
</form>
<br />
{/if}
{if $editgame eq 'y'}
<h2>{tr}Edit game{/tr}</h2>
<form action="tiki-list_games.php" method="post">
<input type="hidden" name="editable" value="{$editable|escape}" />
<table>
<tr><td>{tr}description{/tr}</td>
    <td><textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
</tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" value="{tr}save{/tr}" /></td>
</table>
</form>
<br />
{/if}
{if $play eq 'n'}
{section name=ix loop=$games}
<table>
<tr><td  class="even">
<a href="tiki-list_games.php?game={$games[ix].game}"><img border='0' src="games/thumbs/{$games[ix].game}" alt=''/></a>
{if $tiki_p_admin_games eq 'y'}
<div align="center"><small>
[<a href="tiki-list_games.php?remove={$games[ix].game}">{tr}x{/tr}</a>
|<a href="tiki-list_games.php?edit={$games[ix].game}">{tr}edit{/tr}</a>]
</small></div>
{/if}
</td><td class="even" >
<small>{$games[ix].desc}</small>
</td>
<td class="even" >
{tr}Played{/tr} {$games[ix].hits} {tr}times{/tr}
</td>
</tr>
</table>
<br />
{/section}
{else}
<a href="tiki-list_games.php">List Games</a><br /><br />
<div align="center">
<embed src="{$source}" width="583" height="385" pluginspage="http://www.macromedia.com/shockwave/download/" quality="high"></embed>
</div>
<br />
{tr}If you can't see the game then you need a flash plugin for your browser{/tr}
{/if}
