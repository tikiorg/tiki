{if $feature_left_column eq 'n' and $feature_right_column eq 'n'}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
  <td width="30%">
    <a class="pagetitle" href="tiki-list_games.php">{tr}Games{/tr}</a>
  </td>
  <td align="center">
    <a class="pagetitle" href="/">{tr}/{/tr}</a>
  </td>
  <td align="right" width="30%">
    <a class="pagetitle" href="tiki-index.php">{tr}Home{/tr}</a>
  </td>
  </tr>
</table><br />
{else}
<a class="pagetitle" href="tiki-list_games.php">{tr}Games{/tr}</a><br /><br />
{/if}

<small>{tr}All games are from{/tr} <a class="link" href="http://www.miniclip.com">www.miniclip.com</a>. {tr}visit the site for more games and fun{/tr}</small><br /><br />
{if $tiki_p_admin_games eq 'y'}
<a href="tiki-list_games.php?uploadform=1" class="link">{tr}Upload a game{/tr}</a><br /><br />
{/if}

{if $uploadform eq 'y'}
<h2>{tr}Upload a new game{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-list_games.php" method="post">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
<table class="normal">
  <tr><td class="formcolor">{tr}Flash binary (.sqf or .dcr){/tr}:</td>
      <td class="formcolor">
        <input name="userfile1" type="file" />
      </td>
  </tr>
  <tr><td class="formcolor">{tr}Thumbnail (if the game is foo.swf the thumbnail must be named foo.swf.gif or foo.swf.png or foo.swf.jpg){/tr}:</td>
      <td class="formcolor">
        <input name="userfile2" type="file" />
      </td>
  </tr>
  <tr><td class="formcolor">{tr}description{/tr}:</td>
      <td class="formcolor"><textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
  </tr>
  <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="upload" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>
<br />
{/if}

{if $editgame eq 'y'}
<h2>{tr}Edit game{/tr}</h2>
<form action="tiki-list_games.php" method="post">
<input type="hidden" name="editable" value="{$editable|escape}" />
<table class="normal">
  <tr><td class="formcolor">{tr}description{/tr}</td>
      <td class="formcolor"><textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
  </tr>
  <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>
<br />
{/if}

{if $play eq 'n'}

<table class="normal">
{section name=ix loop=$games}
   <tr><td class="even">
      <a href="tiki-list_games.php?game={$games[ix].game}"><img border='0' src="games/thumbs/{$games[ix].game}" alt=''/></a>

  {if $tiki_p_admin_games eq 'y'}
  <div align="center"><small>
  [<a href="tiki-list_games.php?remove={$games[ix].game}" class="link">{tr}x{/tr}</a>
  |<a href="tiki-list_games.php?edit={$games[ix].game}" class="link">{tr}edit{/tr}</a>]
  </small></div>
  {/if}

       </td>
       <td class="even" > <small>{$games[ix].desc}</small> </td>
       <td class="even" > {tr}Played{/tr} {$games[ix].hits} {tr}times{/tr} </td>
   </tr>
   <tr rowspan="3"><td>&nbsp;</td></tr>
{/section}
</table>

{else}
<div align="center">
<embed src="{$source}" width="583" height="385" pluginspage="http://www.macromedia.com/shockwave/download/" quality="high"></embed>
</div>
<br />
{tr}If you can't see the game then you need a flash plugin for your browser{/tr}
{/if}
