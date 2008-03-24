{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_events.tpl,v 1.8 2007-10-14 17:51:01 mose Exp $ *}

{if $prefs.feature_calendar eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` modified events{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last modifed events{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_modif_events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastEvents}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;{$modLastEvents[ix].start|tiki_short_datetime}<br />
       <a class="linkmodule" href="tiki-calendar.php?todate={$modLastEvents[ix].start}" title="{$modLastEvents[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastEvents[ix].user ne ''}{$modLastEvents[ix].user}{else}{tr}Anonymous{/tr}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastEvents[ix].name|truncate:$maxlen:"...":true}
        {else}
         {$modLastEvents[ix].name}
        {/if}
       </a>
      </td>
     </tr>
    {/section}
   </table>
{/tikimodule}
{/if}
