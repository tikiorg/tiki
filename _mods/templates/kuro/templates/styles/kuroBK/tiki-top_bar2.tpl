    <div id="tiki-top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">

          <!--td height="32" colspan="3"id="page21"> 
            <div align="left"> 
                            
                      <table width="100%" height="32" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                                
                          <td width="100%" height="31" id="butbg"><div align="center">{*phplayers id=42 type=horiz*}</div></td>
                          <td width="100%" valign="bottom" align="center"><div align="center"></div></td>
                                
                          <td></td>
                              </tr>
                            </table>
                      </div></td-->

        <tr> 
          <td id="flash"><div id="flash"><div id="flashl">{include file="datetime.tpl"}</div>
          <div id="flashr">{include file="log-srch-msg.tpl"}</div></div></td>
        </tr>
      </table>
    </div>
<div id="page-titlebar">

<h1 class="pagetitle">{if $feature_wiki_description eq 'y'}{$description}{/if}
{if $lock}<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />{/if}
</h1>
</div>