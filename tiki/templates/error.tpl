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
    <table border="0" cellpadding="0" cellspacing="0" id="tikimidtbl">
    <tr>
      {if $feature_left_column eq 'y'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">
      {if ($errortype eq "402")}
      <center>
      {include file=tiki-login.tpl}
      </center>
      {else}
        <br />
        <div class="cbox">
        <div class="cbox-title">
        {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />
        {if ($errortype eq "404")}
          {if $feature_likePages eq 'y'}
          {tr}Perhaps you were looking for:{/tr}
          <ul>
          {section name=back loop=$likepages}
          <li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
          {sectionelse}
          <li>{tr}There are no wiki pages similar to '{$page}'{/tr}</li>
          {/section}
          </ul>
          <br />
          {/if}
          {include
            file="tiki-searchindex.tpl"
            searchNoResults="true"                 
            searchStyle="menu"
            searchOrientation="horiz"
            words="$page"
          }
          <br />
        {else}
        {$msg}
        <br /><br />
        {/if}
        {if $page and $create eq 'y' and ($tiki_p_admin eq 'y' or $tiki_p_admin_wiki eq 'y'  or $tiki_p_edit eq 'y')}<a href="tiki-editpage.php?page={$page}" class="linkmenu">{tr}Create this page{/tr}</a> {tr}(page will be orphaned){/tr}<br /><br />{/if}
        <a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
        <a href="{$tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
        </div>
        </div>
      {/if}
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
