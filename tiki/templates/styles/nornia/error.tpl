{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-main">
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" >
    <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn" valign="top">
			<div style="text-align: center;">
			<a href="http://nornia.org"><tt>( - &gt; + )</tt><br />N O R N I A</a><br clear="both" /><br />
			</div>
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
      <td id="centercolumn" valign="top"><div id="tiki-center">
        <div class="cbox">
        <div class="cbox-title">
        {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />{$msg}
        <br /><br />
	{if $page and !$nocreate and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Click here to create it{/tr}</a><br />{/if}
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br />
        <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      </div>
      </td>
      {if $feature_right_column eq 'y'}
      <td id="rightcolumn" valign="top">
			<div style="text-align: center;">
			<a href="http://nornia.org"><tt>D I R E C T</tt><br />Democracy</a><br clear="both" /><br />
			</div>
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $feature_top_bar eq 'y'}<div align="center"><div id="tiki-top">{include file="tiki-top_bar.tpl"}</div></div>{/if}
  {if $feature_bot_bar eq 'y'}<div align="center"><div id="tiki-bot">{include file="tiki-bot_bar.tpl"}</div></div>{/if}
</div>

{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
