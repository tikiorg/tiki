    <div id="tiki-top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        {if $feature_sitelogo eq 'y'}
        <tr> 
          <td id="flash">
            <div id="sitelogo" {if $sitelogo_bgcolor ne ''} style="background-color: {$sitelogo_bgcolor};"{/if}><a href="./" title="{$sitelogo_title}"><img src="{$sitelogo_src}" alt="{$sitelogo_alt}" style="border: none" /></a>
              <div id="flashl">{include file="datetime.tpl"}</div>
              <div id="flashr">{include file="log-srch-msg.tpl"}</div>
            </div>
          </td>
        </tr>
        {/if}
        <tr>            
          <td width="100%" height="31" id="butbg" colspan="1"><div align="center">{phplayers id=42 type=horiz}</div></td>
        </tr>
        <tr>
          <td id="hr0" width="100%" colspan="1"><div><img src="styles/kuroBK/page_0.gif" width="728" height="3" alt="" /></div></td>
        </tr>
      </table>
    </div>
