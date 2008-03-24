{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_actions.tpl,v 1.6.2.1 2008-02-29 05:03:19 chibaguy Exp $ *}

{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` actions{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last actions{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_actions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastActions}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
       <a class="linkmodule" href="tiki-index.php{if $modLastActions[ix].pageName ne ''}?page={$modLastActions[ix].pageName}{/if}" title="{$modLastActions[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastActions[ix].user ne ''}{$modLastActions[ix].user}{else}?{/if}{if (strlen($modLastActions[ix].action) > $maxlen) && ($maxlen > 0)}, {$modLastActions[ix].action}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastActions[ix].action|truncate:$maxlen:"...":true}
        {else}
         {$modLastActions[ix].action}
        {/if}
       </a>
      </td>
     </tr>
    {/section}
   </table>
{/tikimodule}
