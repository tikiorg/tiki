{include file="header.tpl"}

{* Index we display a wiki page here *} <br />

<div id="tiki-main">

{if $feature_top_bar eq 'y'}

<div id="tiki-top">

{include file="tiki-top_bar.tpl"}

</div>

{/if}

  <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#BBBFC5">

<tr>

    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr> 

          <td id="page12" width="77" height="27" ><img src="styles/classicblue/page_0.gif" width="77" height="27" alt="" border="0" /></td>

          <td id="page11" height="27" width="100%">

<div align="center"></div></td>

          <td id="page15" width="69" height="27"> <div align="right"><img src="styles/classicblue/page_0.gif" width="69" height="27" alt="" border="0" /></div></td>

        </tr>

      </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">

          <tr>

            <td id="page28" width="10" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/classicblue/page_0.gif" width="10" height="1"

		 alt="" border="0" /></td>

		    <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td rowspan="3" valign="TOP"> 
<table height="100%" border="0" cellpadding="0" cellspacing="0">


                      <tr> {if $feature_left_column eq 'y'} 

                        <td id="page22" height="100%">{section name=homeix loop=$left_modules} 
                          {$left_modules[homeix].data} {/section}</td>
                        <td width="4" id="page36"><img src="styles/bluemetalapr/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                      <tr>

                        <td id="page8" width="170" height="100%"><img src="styles/classicblue/page_0.gif" width="1" height="19" align="top" border="0"/></td>
                        <td id="page36"><img src="styles/classicblue/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                      </tr>

{/if}



                    </table></td>
                  <td id="page24" width="100%" rowspan="3"> 
                    <div align="center" id="tiki-center"> 
                      <div align="center" class="cbox-title">{tr}Oops!{/tr}</div>
                      <div align="center" class="cbox-data">
                        <p align="center">&nbsp;</p>
                        <p align="center">{$msg}<br />
                          <br />
                          <a href="javascript:history.back()" class="linkmenu">{tr}Go 
                          back{/tr}</a><br />
                          <br />
                          <a href="{$tikiIndex}" class="linkmenu">{tr}Return to 
                          home page{/tr}</a> </p>
                      </div>
                    </div></td>
                  {if $feature_right_column eq 'y'}

                  <td rowspan="3" valign="top"> 

                    <table height="100%" border="0" cellpadding="0" cellspacing="0">


                      <tr> {if $feature_right_column eq 'y'} 
                        <td width="4" id="page38"><img src="styles/classicblue/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                        <td id="page22" height="100%">{section name=homeix loop=$right_modules} 
                          {$right_modules[homeix].data} {/section}</td>
                      <tr>

                        <td id="page38"><img src="styles/classicblue/page_0.gif" width="4" height="1" alt="" border="0" /></td>
                        <td id="page8" width="170" height="100%"><img src="styles/classicblue/page_0.gif" width="1" height="19" align="top" border="0"/></td>
                      </tr>
                      						  
						  
                        {/if}


                    </table></td>
                  {/if} </tr>

              </table>


            <td id="page29" width="9" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/classicblue/page_0.gif" width="9" height="1" alt="" border="0" /></td>

        </tr>

      </table>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr> 

            <td width="46" height="29" id="page31"><img src="styles/classicblue/page_0.gif" width="46" height="29" alt="" border="0" /></td>

          <td WIDTH="100%" id="page30"></td>

          <td width="46" id="page35"> <div align="right"><a href="javascript:scroll(0,0);"><img src="styles/classicblue/page_0.gif" width="46" height="29" alt="" border="0" /></a></div></td>

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
