{if $user}
  {assign var=module_title value=$slvn_info.label}
  {tikimodule title="$module_title" name="since_last_visit"}
    <table>
      <tr height="20">
        <td align="center">
          <a class="linkmodule" href="tiki-calendar.php?todate={$slvn_info.lastLogin}" title="{tr}click to edit{/tr}">
            <b>{$slvn_info.lastLogin|tiki_short_date}</b>
          </a>
        </td>
      </tr>
    </table>
    {foreach key=pos item=slvn_item from=$slvn_info.items}
      {assign var=cname value=$slvn_item.cname}
      <div class="separator">
        <a class="separator" href="javascript:toggle('{$cname}');">
          {$slvn_item.count}&nbsp;{$slvn_item.label}
        </a>
      </div>
      <div id="{$cname}" {if $smarty.cookies.$cname ne 'o'}style="display:none;"{else}style="display:block;"{/if}>
        <table cellpadding="0" cellspacing="0">
          {section name=ix loop=$slvn_item.list}
            <tr class="module">
              <td width="10"/>
              <td width="20" align="right" class="module">{$smarty.section.ix.index_next})</td>
              <td>
                <a  class="linkmodule" 
                    href="{$slvn_item.list[ix].href|escape}"
                    title="{$slvn_item.list[ix].title|escape}">
                  {$slvn_item.list[ix].label|escape}
                </a>
              </td>
            </tr>
          {/section}
        </table>
      </div>
    {/foreach}
  {/tikimodule}
{/if}
      