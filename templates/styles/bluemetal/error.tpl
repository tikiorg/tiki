{include file="header.tpl"}

{* Index we display a wiki page here *} <br />

<div id="tiki-main">

{if $feature_top_bar eq 'y'}

<div id="tiki-top">

{include file="tiki-top_bar.tpl"}

</div>

{/if}

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#BBBFC5">

<tr>

    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr> 

          <td id="page12" width="77" height="27" ><img src="styles/bluemetal/page_0.gif" width="77" height="27" alt="" border="0" /></td>

          <td id="page11" height="27" width="100%">

<div align="center"></div></td>

          <td id="page15" width="69" height="27"> <div align="right"><img src="styles/bluemetal/page_0.gif" width="69" height="27" alt="" border="0" /></div></td>

        </tr>

      </table>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr>

          <td id="page28" width="10" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/bluemetal/page_0.gif" width="10" height="1"

		 alt="" border="0" /></td>

		  <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr> 
                  <td height="31" colspan="3" id="page37"> <div align="left"> 
                      <table width="100%" height="31" border="0" align="CENTER" cellpadding="0" cellspacing="0">
                        <tr> 
                          <td width="100%">&nbsp;</td>
                          <td width="170" height="31" background="styles/bluemetal/topbgsmall.gif"> 
                            <div align="center"><a href="http://TikiWiki.org" target="_blank"><img src="styles/bluemetal/tikibuttonmetal.gif" width="88" height="31" border="0" align="absbottom"></a></div></td>

                        </tr>
                      </table>
                    </div></td>
                </tr>
                <tr> 
                  <td id="page16" width="18" height="22" align="left" valign="BOTTOM"> 
                    <img src="styles/bluemetal/page_0.gif" width="18" height="22" alt="" /></td>
                  <td id="page17" width="100%"></td>
                  <td id="page18" width="18" height="22" align="right" valign="BOTTOM"> 
                    <div align="right"><img src="styles/bluemetal/page_0.gif" width="18" height="22" alt="" /></div></td>
                </tr>
              </table>

              <table align="LEFT" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="3" valign="TOP"> 
<table height="100%" border="0" cellpadding="0" cellspacing="0">
<tr> 

                        <td id="page7" width="170"> 
<table align="CENTER" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
                              <td align="CENTER">
<FORM class=forms action=tiki-searchindex.php method=get name=SearchBox>
                                  <div align="center">
                                    <INPUT 
      type=hidden value=pages name=where>
                                    <INPUT name=highlight value="  TYPE SEARCH HERE" size=23 onblur="if(this.value=='')this.value='  TYPE SEARCH HERE ';" onfocus="if(this.value=='zip code')this.value='';" OnClick="document.SearchBox.highlight.value='';"/>
                                    <input class=wikiaction type=submit value=GO! name=search>
                                  </div>
                                </FORM></td>
                            </tr>
                          </table></td>

                        <td id="page6" width="4" height="0"><img src="styles/bluemetal/page_0.gif" width="4" height="5" alt="" border="0" /></td>

                      </tr>

                      <tr> 

                        <td id="page8" width="170" valign="TOP"> </td>

                        <td id="page36" width="4" height="0"><img src="styles/bluemetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>

                      </tr>

                      <tr> {if $feature_left_column eq 'y'} 

                        <td id="page22" height="100%">{section name=homeix loop=$left_modules} 
                          {$left_modules[homeix].data} {/section}</td>
{/if}

                        <td width="4" id="page36"><img src="styles/bluemetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>

                      </tr>

                      <tr>

                        <td id="page8" width="170" height="100%"><img src="styles/bluemetal/page_0.gif" width="1" height="19" align="top" border="0"/></td>

                        <td id="page36"><img src="styles/bluemetal/page_0.gif" width="4" height="1" alt="" border="0" /></td>

                      </tr>

                    </table></td>

                  <td id="page24" width="100%" rowspan="3"> 
                    <div align="center" class="cbox">
                      <div align="center" class="cbox-title">{tr}Oops!{/tr}</div>
                      <div class="cbox-data"><p>{$msg}<br />
                            <br />
                            <a href="javascript:history.back()" class="linkmenu">{tr}Go 
                            back{/tr}</a><br />
                            <br />
                            <a href="{$tikiIndex}" class="linkmenu">{tr}Return 
                            to home page{/tr}</a> </p> </p>
                      </div></div></td>

					  {if $feature_right_column eq 'y'}

                  <td rowspan="3" id="rightcolumn"> 
<table align="CENTER" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
                        <td align="CENTER"> 
<div align="center">{if $user}<font size="2">Welcome</font> {$user}<br />
                            <A class=linkmodule 
      href="tiki-logout.php">{tr}[sign out]{/tr}</A>{else} </div>
<FORM action={$login_url} method=post name=LoginForm>
                            <div align="center">
                              <INPUT type=hidden 
      value={$challenge|escape} name=challenge>
                              <INPUT type=hidden 
      name=response>
                              <INPUT type="text" name="user" value="USER" size=9 onblur="if(this.value=='')this.value='USER';" onfocus="if(this.value=='USER')this.value='';" OnClick="document.LoginForm.user.value='';"/>
                              <INPUT type="text" name="pass" value="PASS" size=8 onblur="if(this.value=='')this.value='PASS';" onfocus="if(this.value=='PASS')this.value='';" OnClick="document.LoginForm.pass.value='';document.LoginForm.pass.type='password';"/>
                              <INPUT type=submit value="GO!" name=login>
                              <INPUT 
      type=hidden value=on name=rme>
                            </div>
                          </FORM>
                          {/if} </td>
                      </tr>
                    </table>
                    {section name=homeix loop=$right_modules} {$right_modules[homeix].data} 
                    {/section}</td>

                  {/if} </tr>

              </table>

          </td>

          <td id="page29" width="9" border="0" cellspacing="0" cellpadding="0" height="100%"><img src="styles/bluemetal/page_0.gif" width="9" height="1" alt="" border="0" /></td>

        </tr>

      </table>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr> 

            <td width="46" height="29" id="page31"><img src="styles/bluemetal/page_0.gif" width="46" height="29" alt="" border="0" /></td>

          <td WIDTH="100%" id="page30"></td>

          <td width="46" id="page35"> <div align="right"><a href="javascript:scroll(0,0);"><img src="styles/bluemetal/page_0.gif" width="46" height="29" alt="" border="0" /></a></div></td>

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
