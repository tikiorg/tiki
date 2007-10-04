{if $prefs.feature_left_column eq 'n' and $prefs.feature_right_column eq 'n'}
  {assign var=xtitle value=1}
{else} {assign var=xtitle value=0} {/if}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
  <td{if $xtitle} width="30%"{/if}>
    <h1><a class="pagetitle" href="tiki-list_games.php">{tr}Games{/tr}</a>

      {if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Games" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Games{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}Help{/tr}" /></a>{/if}


      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_games.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}games tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

    
  </td>
{if $xtitle}
  <td align="center">
    <h1><a class="pagetitle" href="/">{tr}/{/tr}</a></h1>
  </td>
  <td align="right" width="30%">
    <h1><a class="pagetitle" href="tiki-index.php">{tr}Home{/tr}</a></h1>
  </td>
{/if}
  </tr>
</table><br />

<small>{tr}All games are from{/tr} <a class="link" href="http://www.miniclip.com">www.miniclip.com</a>. {tr}visit the site for more games and fun{/tr}</small><br /><br />
{if $tiki_p_admin_games eq 'y'}
<a href="tiki-list_games.php?uploadform=1" class="link">{tr}Upload a game{/tr}</a><br /><br />
{/if}

{if $uploadform eq 'y'}
<h2>{tr}Upload a new game{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-list_games.php" method="post">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
<table class="normal">
  <tr><td class="formcolor">{tr}Flash binary (.swf or .dcr){/tr}:</td>
      <td class="formcolor">
        <input name="flashfile" type="file" />
      </td>
  </tr>
  <tr><td class="formcolor">{tr}Thumbnail (if the game is foo.swf the thumbnail must be named foo.swf.gif or foo.swf.png or foo.swf.jpg){/tr}:</td>
      <td class="formcolor">
        <input name="imagefile" type="file" />
      </td>
  </tr>
  <tr><td class="formcolor">{tr}Description{/tr}:</td>
      <td class="formcolor"><textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
  </tr>
  <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="upload" value="{tr}Save{/tr}" /></td>
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
  <tr><td class="formcolor" style="text-align:center; vertical-align:bottom">
        <a href="tiki-list_games.php?edit={$games[$editable].game}"><img border='0' src="games/thumbs/{$games[$editable].game}" alt=''/></td>
      <td class="formcolor">{tr}Description{/tr}<br />
      <textarea name="description" rows="4" cols="40">{$data|escape}</textarea></td>
  </tr>
  <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>
</form>
<br />
{/if}

{if $play eq 'n'}

<table class="normal">
{foreach from=$games item=ix}
   <tr><td class="even">
      <a href="tiki-list_games.php?game={$ix.game}"><img border='0' src="games/thumbs/{$ix.game}" alt=''/></a>

  {if $tiki_p_admin_games eq 'y'}
  <div align="center"><small>
  [<a href="tiki-list_games.php?remove={$ix.game}" class="link">{tr}x{/tr}</a>
  |<a href="tiki-list_games.php?edit={$ix.game}" class="link">{tr}Edit{/tr}</a>]
  </small></div>
  {/if}

       </td>
       <td class="even" > <small>{$ix.desc}</small> </td>
       <td class="even" > {tr}Played{/tr} {$ix.hits} {tr}times{/tr} </td>
   </tr>
   <tr><td colspan="3">&nbsp;</td></tr>
{/foreach}
</table>

{else}
<div align="center">
<embed src="{$source}" width="583" height="385" pluginspage="http://www.macromedia.com/shockwave/download/" quality="high"></embed>
</div>
<br />
{tr}If you can't see the game then you need a flash plugin for your browser{/tr}
{/if}
