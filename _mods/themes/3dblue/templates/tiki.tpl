{include file="header.tpl"}
{* Index we display a wiki page here *}
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
      {if $left_modules[homeix].data ne ''}<table cellspacing="0" width="100%" cellpadding="0" ><tr><td width="100%"><img hspace="4" alt="left shadow" src="styles/3dblue/modl.gif" /></td></tr></table>{/if}
      {/section}
      
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">{include file=$mid}
      {if $show_page_bar eq 'y'}
      {include file="tiki-page_bar.tpl"}
      {/if}
      </div>
      </td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {if $right_modules[homeix].data|strip ne " "}<table cellspacing="0" cellpadding="0" width="100%" ><tr><td width="100%"><div align="right"><img hspace="4" alt="right shadow" src="styles/3dblue/modr.gif" /></div></td></tr></table>{/if}
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
