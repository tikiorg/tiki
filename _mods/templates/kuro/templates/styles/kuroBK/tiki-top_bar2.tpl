  {if $feature_sitelogo eq 'y'}
    <div id="tiki-top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td id="flash" valign="middle">
            <div id="sitelogo" {if $sitelogo_bgcolor ne ''} style="background-color: {$sitelogo_bgcolor};"{/if}><a href="./" title="{$sitelogo_title}"><img src="{$sitelogo_src}" alt="{$sitelogo_alt}" valign="middle"/></a>
{*
              <div id="flashl">{include file="datetime.tpl"}</div>
              <div id="flashr">{include file="log-srch-msg.tpl"}</div>
*}
            </div>
          </td>
        </tr>
      </table>
    </div>
  {/if}
  <div id="page-titlebar">
    <h1 class="pagetitle">{if $feature_wiki_description eq 'y'}{$description}{/if}
      {if $lock}<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />{/if}
    </h1>
  </div>
