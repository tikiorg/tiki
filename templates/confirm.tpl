{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" >
    <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">
      <br />
        <div class="cbox">
        <br />
        <div class="cbox-data">
				<form action="{$confirmaction}" method="post">
{if $ticket}<input value="{$ticket}" name="ticket" type="hidden">{/if}
				<input type="submit" name="daconfirm" value="{tr}Click here to confirm your action{/tr}" />
        <span class="button2"><a href="javascript:history.back()" class="linkbut">{tr}Go back{/tr}</a></span>
        <span class="button2"><a href="{$tikiIndex}" class="linkbut">{tr}Return to home page{/tr}</a></span>
				</form>
        </div>
        </div><br />

      </div></td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
