  {if $feature_sitelogo eq 'y'}
    <div id="tiki-top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr id="flash"> 
          <td id="site-leftcolumn" valign="middle">
            <span id="sitemod-left">
              {include file="datetime.tpl"}
              {include file="modules/mod-messages_unread_messages.tpl" module_params=$module_nodecorations}
            </span>
          </td><td id="site-centrecolumn" valign="middle">
            <div id="sitelogo" {if $sitelogo_bgcolor ne ''} style="background-color: {$sitelogo_bgcolor};"{/if}>
             <span><a href="./" title="{$sitelogo_title}"><img src="{$sitelogo_src}" alt="{$sitelogo_alt}" valign="middle"/></a></span>
            </div>
          </td><td id="site-rightcolumn">
            <span id="sitemod-right">
              {include file="modules/mod-login_box.tpl" module_params=$module_nodecorations}
            </span>
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
