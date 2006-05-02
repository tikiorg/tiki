{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}
<div dir="rtl">
{/if}
<div id="tiki-main">
  {if $feature_top_bar eq 'y'}
  <div id="tiki-top">
    {include file="tiki-top_menu.tpl"}
  </div>
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
    <tr id="contentcols">
    <td id="sidecol">
    <div class="logobox" id="tikipedia-logo">
    				<a href="index.php" title="Home"></a>
    				</div>
    				<div id="tiki-top_bar">
    				{include file="tiki-top_bar.tpl"}
    				</div>
    {if $page} {* displays toolbox (watch, backlinks, etc.) on wiki pages *}
    {include file="mod-toolbox.tpl"}
    {/if}
    {if $feature_left_column ne 'n'}
          	<div id="leftcolumn" valign="top">
          	          		{section name=homeix loop=$left_modules}
          		{$left_modules[homeix].data}
          		{/section}
              {if $feature_left_column eq 'user'}
                <img src="blank.gif" width="100%" height="0px">
                {literal}
                  <script language="Javascript" type="text/javascript">
                    setfolderstate("leftcolumn");
                  </script>
                {/literal}
              {/if}</div>
         {/if}
           {if $feature_right_column ne 'n'}
                <div id="rightcolumn" valign="top">
                {section name=homeix loop=$right_modules}
                {$right_modules[homeix].data}
                {/section}
                    {if $feature_right_column eq 'user'}
                      <img src="blank.gif" width="100%" height="0px">
                      {literal}
                        <script language="Javascript" type="text/javascript"> 
                          setfolderstate("rightcolumn");
                        </script>
                      {/literal}
                    {/if}
                </div>
      {/if}
      </td>
      <td id="centercolumn" valign="top"><div id="tiki-center">
      <br />
        <div class="cbox">
        <div class="cbox-title">
        {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />
        {if ($errortype eq "404")}
           {if $likepages}
          {tr}Perhaps you were looking for:{/tr}
          <ul>
          {section name=back loop=$likepages}
          <li><a  href="tiki-index.php?page={$likepages[back]|escape:"url"}" class="wiki">{$likepages[back]}</a></li>
          {/section}
          </ul>
          {else}
          {tr}There are no wiki pages similar to '{$page}'{/tr}
          <br /><br />
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
      </div>
      </td>
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
