{include file="header.tpl"}
{* Index we display a wiki page here *} <br />
<div id="tiki-main">
{if $feature_top_bar eq 'y'}
<div id="tiki-top">
{include file="tiki-top_bar.tpl"}
</div>
{/if} 
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#C0CBC1">
<tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td id="page12" width="77" height="27" ><img src="styles/greenmetal/page_0.gif" width="77" height="27" alt="" border="0" /></td>
          <td id="page11" height="27" width="100%">
<div align="center"></div></td>
          <td id="page15" width="69" height="27"> <div align="right"><img src="styles/greenmetal/page_0.gif" width="69" height="27" alt="" border="0" /></div></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td id="page28" width="10" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/greenmetal/page_0.gif" width="10" height="1"
		 alt="" border="0" /></td>
		  <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr> 
                  <td height="32" colspan="3"id="page21"> 
<div align="left"> 
                            
                      <table width="100%" height="32" border="0" align="CENTER" cellpadding="0" cellspacing="0">
<tr>
                                
                          <td width="80" height="31"><img src="img/tiki/tikibutton2.png" width="80" height="31" align="absbottom"></td>
                                <td width="100%" valign="BOTTOM" align="CENTER">
<div align="center"></div></td>
                                
                          <td></td>
                              </tr>
                            </table>
</div></td>
                </tr>
                <tr> 
                  <td width="18" height="22" align="left" id="page16"> <img src="styles/greenmetal/page_0.gif" width="18" height="22" alt="" /></td>
                  <td id="page17" width="100%"></td>
                  <td width="18" height="22" align="right" id="page18"> <div align="right"><img src="styles/greenmetal/page_0.gif" width="18" height="22" alt="" /></div></td>
                </tr>
              </table>
              <table align="LEFT" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="3"> 
<table border="0" cellspacing="0" cellpadding="0" height="100%">
                      <tr> 
                        <td id="page7"><img src="styles/greenmetal/page_0.gif" width="30" height="5"></td>
                        <td id="page6" width="4" height="0"><img src="styles/greenmetal/page_0.gif" width="4" height="5" alt="" border="0" /></td>
                      </tr>
                      <tr> 
                        <td id="page8"><img src="styles/greenmetal/green_nav_m.gif" width="23" height="11" align="ABSMIDDLE" border="0"><img src="styles/greenmetal/page_0.gif" width="1" height="19" align="top" border="0"/></td>
                        <td id="page36" width="4" height="0"><img src="styles/greenmetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                      </tr>
                      <tr> {if $feature_left_column eq 'y'} 
                        <td id="page22" height="100%">{section name=homeix loop=$left_modules} 
                          {$left_modules[homeix].data} {/section}</td>{/if}
                        <td width="4" id="page36"><img src="styles/greenmetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                      </tr>
                      <tr>
                        <td id="page8" height="100%"><img src="styles/greenmetal/page_0.gif" width="1" height="19" align="top" border="0"/></td>
                        <td id="page36"><img src="styles/greenmetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                      </tr>
                    </table></td>
                  <td id="page24" width="100%" rowspan="3"> 
                    <div id="tiki-center"> {if $pagetop_msg ne ''} <span class="pagetop_msg">{$pagetop_msg}</span> 
                      {/if} 
                      <!-- content -->
                      {include file=$mid} 
                      <!-- end of content -->
                    </div></td>
					  {if $feature_right_column eq 'y'}
                  <td id="rightcolumn" rowspan="3"> {section name=homeix loop=$right_modules} 
                    {$right_modules[homeix].data} {/section}</td>
                  {/if} </tr>
              </table>
          </td>
          <td id="page29" width="9" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/greenmetal/page_0.gif" width="9" height="1" alt="" border="0" /></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
            <td width="46" height="29" id="page31"><img src="styles/greenmetal/page_0.gif" width="46" height="29" alt="" border="0" /></td>
          <td WIDTH="100%" id="page30"></td>
          <td width="46" id="page35"> <div align="right"><a href="javascript:scroll(0,0);"><img src="styles/greenmetal/page_0.gif" width="46" height="29" alt="" border="0" /></a></div></td>
        </tr>
      </table> </td>
  </tr>
</table>
{if $feature_bot_bar eq 'y'} 
  <div id="tiki-bot">
{include file="tiki-bot_bar.tpl"}
</div>
{/if}
</div>
{include file="footer.tpl"}
