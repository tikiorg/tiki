{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-main">
  {if $prefs.feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" id="tikimidtbl">
    <tr>
      {if $prefs.feature_left_column eq 'y'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">
      <br />
        <div class="cbox">
        <div class="cbox-title">
        {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />{$msg}
<form action="{$self}{if $query}?{$query|escape}{/if}" method="post">
{foreach key=k item=i from=$post}
<input type="hidden" name="{$k}" value="{$i|escape}" />
{/foreach}
<input type="submit" name="ticket_action_button" value="{tr}Click here to confirm your action{/tr}" />
</form><br /><br />
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br />
        <a href="{$prefs.tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      </div></td>
      {if $prefs.feature_right_column eq 'y'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $prefs.feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}

