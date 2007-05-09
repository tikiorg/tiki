{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki.tpl,v 1.34 2007-05-09 14:59:30 pkdille Exp $ *}{include file="header.tpl"}
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
  <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      {if $feature_left_column ne 'n' && $left_modules|@count > 0}
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
      {if $feature_left_column eq 'user' or $feature_right_column eq 'user'}
        <div>
      {if $feature_left_column eq 'user' && $left_modules|@count > 0}
	<div style="text-align:left;float:left;position:absolute;"><a class="flip" href="javascript:flip('leftcolumn','table-cell');">
        <img  align="left" name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a></div>
      {/if}
      {if $feature_right_column eq 'user'&& $right_modules|@count > 0}
        <div style="text-align:right;float:right;"><a class="flip" href="javascript:flip('rightcolumn','table-cell');" align="right">
        <img align="right" name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;</a></div>
      {/if}
        <br clear="both">
        </div>
      {/if}

			<div id="tiki-center">
			{$mid_data}
      </div>
			
			{if $feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      </td>
      {if $feature_right_column ne 'n' && $right_modules|@count > 0}
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
