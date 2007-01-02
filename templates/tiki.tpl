{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki.tpl,v 1.32 2007-01-02 08:19:45 mose Exp $ *}{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<div dir="rtl">
{/if}
{if $feature_ajax eq 'y'}
{include file="tiki-ajax_header.tpl"}
{/if}
{if $feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}
  <div id="tiki-mid">
  {if $feature_left_column eq 'user' or $feature_right_column eq 'user'}
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
      {if $feature_left_column eq 'user'}
				<td align="left"><a class="flip" href="javascript:flip('leftcolumn','table-cell');">
        <img  align="left" name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a></td>
      {/if}
      {if $feature_right_column eq 'user'}
        <td align="right" style="text-align:right"><a class="flip" href="javascript:flip('rightcolumn','table-cell');">
        <img align="right" name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;</a></td>
      {/if}
			</td></tr>
	</table>
  {/if}
  <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      {if $feature_left_column ne 'n'}
      <td id="leftcolumn" valign="top"
			{if $feature_left_column eq 'user'} 
			style="display:{if isset($cookie.show_leftcolumn) and $cookie.show_leftcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
			{/if}>
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn" valign="top">
			{/if}

			<div id="tiki-center">
			{$mid_data}
      </div>
			
			{if $feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      </td>
      {if $feature_right_column ne 'n'}
      <td id="rightcolumn" valign="top" 
			{if $feature_right_column eq 'user'} 
			style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
			{/if}>
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
{/if}

{if $feature_bidi eq 'y'}
</div>
{/if}
{include file="footer.tpl"}
