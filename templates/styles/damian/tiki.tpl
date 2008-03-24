{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="tiki-main">
  {if $prefs.feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" >
    <tr>
      {if $prefs.feature_left_column ne 'n'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
      <td id="centercolumn">
			{/if}
			<div id="tiki-center">
			{$mid_data}
      </div>
      {if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
			</td>
      {if $prefs.feature_right_column ne 'n'}
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
{/if}
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
