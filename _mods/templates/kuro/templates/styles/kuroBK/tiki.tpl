{* Index we display a wiki page here *}{include file="header.tpl"}
{include file="reveal.tpl"}
<!-- tiki.tpl /-->
<div id="tiki-main">

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
  <td>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        {include file="tiki-top_bar0.tpl"}
        <td id="page28" width="10" border="0" cellspacing="0" cellpadding="0"
height="100%"><img src="styles/kuroBK/page_0" width="10" height="1"
                 alt="" border="0" /></td>
        <td>

        {if $feature_top_bar eq 'y'}
          {include file="tiki-top_bar2.tpl"}
	  {include file="tiki-top_bar3.tpl"}
	  {include file="tiki-top_bar4.tpl"}
        {else}
	  {include file="tiki-top_bar5.tpl"}
	  {include file="tiki-top_bar6.tpl"}
        {/if} 
	{include file="tiki-show_page_header.tpl"}

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td WIDTH="100%" colspan="3" id="tiki-columns">
              <table align="left" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="3" id="leftcolumn">
                    <table border="0" cellspacing="0" cellpadding="0" height="100%">
                      <tr> 
                        <td id="above_menus"><img src="styles/kuroBK/page_0.gif" width="1" height="20" align="top" border="0" alt=""/></td>
                      </tr>
                      <tr> {if $feature_left_column eq 'y'} 
                        <td id="page22" height="100%">{section name=homeix loop=$left_modules} 
                          {$left_modules[homeix].data} {/section}</td>{/if}
                      </tr>
                      <tr>
                        <td id="below_menus" height="100%"><img src="styles/kuroBK/page_0" width="1" height="19" align="top" border="0"/></td>
                      </tr>
                    </table></td>
                  <td id="page24" width="100%" rowspan="3"> 
                    <div id="tiki-center"> {if $pagetop_msg ne ''} <span class="pagetop_msg">{$pagetop_msg}</span>{/if} 
                      <!-- content -->
                      {include file=$mid} 
                      <!-- end of content -->
                      {if $show_page_bar eq 'y'}
                        {include file="tiki-page_bar.tpl"}
                      {/if}
                    </div></td>
                    {if $feature_right_column eq 'y'}
                  <td id="rightcolumn" rowspan="3"> {section name=homeix loop=$right_modules} 
                    {$right_modules[homeix].data} {/section}</td>
                  {/if} </tr>
              </table> <!--right modus /-->
              </td></tr>
              {if $feature_bot_bar eq 'y'}
                {include file="tiki-bot_bar.tpl"} 
              {/if}           
            </table>
          </td>
          <td id="page29" width="9" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/kuroBK/page_0" width="9" height="1" alt="" border="0" /></td>
        </tr>
        {include file="tiki-bot_bar2.tpl"}
    </table>
    </td>
  </tr>
</table>
{include file="tiki-copyright.tpl"}
</div>
{include file="footer.tpl"}
<!-- / tiki.tpl /-->
