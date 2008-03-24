{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-since_last_visit_new.tpl,v 1.12.2.2 2008-03-12 20:05:46 pkdille Exp $ *}

{if $user}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}`$slvn_info.label`{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="since_last_visit_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
    <table>
      <tr>
        <td align="center">
	{if $prefs.feature_calendar eq 'y'}
          <a class="linkmodule" href="tiki-calendar.php?todate={$slvn_info.lastLogin}" title="{tr}click to edit{/tr}">
	{/if}
            <b>{$slvn_info.lastLogin|tiki_short_date}</b>
	{if $prefs.feature_calendar eq 'y'}
          </a>
	{/if}
        </td>
      </tr>
    </table>
    {if $slvn_info.cant == 0}
         <div class="separator">
            {tr}Nothing has changed{/tr}
         </div>
    {else}
         {foreach key=pos item=slvn_item from=$slvn_info.items}
            {if $slvn_item.count > 0 }
              {assign var=cname value=$slvn_item.cname}
              <div class="separator">
                <a class="separator" href="javascript:toggle('{$cname}');">
                  {$slvn_item.label}:&nbsp;{$slvn_item.count}
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
                         {if $slvn_item.list[ix].label == ''}-{else}{$slvn_item.list[ix].label|escape}{/if}
                       </a>
                     </td>
                   </tr>
                 {/section}
               </table>
             </div>
           {/if}
         {/foreach}
    {/if}
  {/tikimodule}
{/if}
      
