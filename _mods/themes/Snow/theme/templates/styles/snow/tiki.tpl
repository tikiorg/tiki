{* 1.9.2 modified for Snow theme by the addition of phplayers id=43 type=horiz at about line 11 *}
{include file="header.tpl"} {* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<div dir="rtl">
{/if}
<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_bar.tpl"}
  </div>
  <div id="horiz_menu">{phplayers id=43 type=horiz}</div>
  {/if}
  <div id="tiki-mid">
  <table id="tiki-midtbl" border="0" cellpadding="0" cellspacing="0" >
  {if $feature_left_column eq 'user' or $feature_right_column eq 'user'}
    <tr><td id="tiki-columns" colspan="0" width="100%">
      {if $feature_left_column eq 'user'}
        <span style="float: left"><a class="flip" href="javascript:icntoggle('leftcolumn');">
        <img name="leftcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Left Menus{/tr}&nbsp;</a>
        </span>
      {/if}
      {if $feature_right_column eq 'user'}
        <span style="float: right"><a class="flip" href="javascript:icntoggle('rightcolumn');">
        <img name="rightcolumnicn" class="colflip" src="img/icons/ofo.gif" border="0" alt="+/-" />&nbsp;{tr}Show/Hide Right Menus{/tr}&nbsp;</a>
        </span>
      {/if}
      <br />
    </td></tr>
  {/if}
    <tr>
      {if $feature_left_column ne 'n'}
      <td id="leftcolumn" valign="top">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
          {if $feature_left_column eq 'user'}
            <img src="images/none.gif" width="100%" height="0" />
            {literal}
              <script language="Javascript" type="text/javascript">
                setfolderstate("leftcolumn");
              </script>
            {/literal}
          {/if}
      </td>
      {/if}
      <td id="centercolumn" valign="top"><div id="tiki-center">{include file=$mid}
      {if $show_page_bar eq 'y'}
      {include file="tiki-page_bar.tpl"}
      {/if}
      </div>
      </td>
      {if $feature_right_column ne 'n'}
      <td id="rightcolumn" valign="top">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
          {if $feature_right_column eq 'user'}
            <img src="images/none.gif" width="100%" height="0" />
            {literal}
              <script language="Javascript" type="text/javascript">
                setfolderstate("rightcolumn");
              </script>
            {/literal}
          {/if}
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
</div>
{/if}
{include file="footer.tpl"}
