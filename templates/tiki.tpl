{* $Id$ *}{include file="header.tpl"}
{* TikiTest ToolBar *}
{if $prefs.feature_tikitests eq 'y' and $tikitest_state neq 0}
{include file="tiki-tests_topbar.tpl"}
{/if}
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
{/if}

{if $prefs.feature_ajax eq 'y'}
{include file="tiki-ajax_header.tpl"}
{/if}

<div id="tiki-main">
  {if $prefs.feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  {/if}

  <div id="tiki-mid">
  <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
	{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      {if $prefs.feature_left_column ne 'n' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
      <td id="leftcolumn" valign="top"
			{if $prefs.feature_left_column eq 'user'} 
			style="display:{if isset($cookie.show_leftcolumn) and $cookie.show_leftcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
			{/if}>
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
	{/if}
	
		<td id="centercolumn" valign="top">

	{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      {if $prefs.feature_left_column eq 'user' or $prefs.feature_right_column eq 'user'}
			<div id="showhide_columns">
      {if $prefs.feature_left_column eq 'user' && $left_modules|@count > 0 && $show_columns.left_modules ne 'n'}
				<div style="text-align:left;float:left;"><a class="flip" href="javascript:flip('leftcolumn','table-cell');">
        <img name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a></div>
      {/if}
      {if $prefs.feature_right_column eq 'user'&& $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
				<div style="text-align:right;float:right;"><a class="flip" href="javascript:flip('rightcolumn','table-cell');">
        &nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;<img name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" /></a></div>
      {/if}
			</div>
      {/if}
	{/if}

			<div id="tiki-center" style="clear: both">
			{$mid_data}
			</div>
		</td>

		{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
			{if $prefs.feature_right_column ne 'n' && $right_modules|@count > 0 && $show_columns.right_modules ne 'n'}
      <td id="rightcolumn" valign="top" 
				{if $prefs.feature_right_column eq 'user'} 
			style="display:{if isset($cookie.show_rightcolumn) and $cookie.show_rightcolumn ne 'y'}none{else}table-cell;_display:block{/if};"
				{/if}>
      			{section name=homeix loop=$right_modules}
      				{$right_modules[homeix].data}
      			{/section}
      </td>
      		{/if}
		{/if}      	
    </tr>
   </table>
  </div><!-- END of tiki-mid -->
  
  {if $prefs.feature_bot_bar eq 'y' and ($prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y')}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
  
</div><!-- END of tiki-main -->

{if $prefs.feature_bidi eq 'y'}
</div>
{/if}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}{include file="footer.tpl"}{/if}
