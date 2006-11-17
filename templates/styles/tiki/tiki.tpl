{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/tiki/tiki.tpl,v 1.14 2006-11-17 11:44:31 mose Exp $ *}
{include file="header.tpl"}

{if $feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}

{assign var=cols value=1}
{assign var=rcol value="n"}
{assign var=lcol value="n"}
{if $feature_left_column ne 'n' and count($left_modules) gt 0}
{assign var=cols value=$cols+1}
{assign var=lcol value="y"}
{/if}
{if $feature_right_column ne 'n' and count($right_modules) gt 0}
{assign var=cols value=$cols+1}
{assign var=rcol value="y"}
{/if}
<table {if $feature_bidi eq 'y'}dir="rtl"{/if} cellpadding="0" cellspacing="0" border="0" width="100%">
{if $feature_top_bar eq 'y'}
<tr><td {if $cols gt 1}colspan="{$cols}"{/if}>
<div id="tiki-top">{include file="tiki-top_tiki_bar.tpl"}</div>
</td></tr>
{/if}
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
{if $lcol eq "y"}
<td valign="top" id="leftcolumn">
<div>{section name=homeix loop=$left_modules}{$left_modules[homeix].data}{/section}
          {if $feature_left_column eq 'user'}
            <img src="images/none.gif" width="100%" height="0" />
            {literal}
              <script type="text/javascript">
                setfolderstate("leftcolumn");
              </script>
            {/literal}
          {/if}
</div>
</td>
{/if}

<td valign="top" id="tiki-mid">
{/if}
<div id="tiki-center">{$mid_data}</div>
{if $feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}

</td>

{if $rcol eq "y"}
<td valign="top" id="rightcolumn">
<div>{section name=homeix loop=$right_modules}{$right_modules[homeix].data}{/section}
          {if $feature_right_column eq 'user'}
            <img src="images/none.gif" width="100%" height="0" />
            {literal}
              <script type="text/javascript"> 
                setfolderstate("rightcolumn");
              </script>
            {/literal}
          {/if}
</div>
</td>
{/if}

</tr>

{if $feature_bot_bar eq 'y'}
<tr><td {if $cols gt 1}colspan="{$cols}"{/if}>
<div id="tiki-bot">{include file="tiki-bot_bar.tpl"}</div>
</td></tr>
{/if}

</table>
{/if}
{include file="footer.tpl"}
