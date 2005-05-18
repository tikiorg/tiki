{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_actions.tpl,v 1.4 2005-05-18 11:03:29 mose Exp $ *}

{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` actions{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last actions{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_actions" flip=$module_params.flip decorations=$module_params.decorations}
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
