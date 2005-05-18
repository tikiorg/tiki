{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_visitors.tpl,v 1.4 2005-05-18 11:03:30 mose Exp $ *}

{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` visitors{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last visitors{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_visitors" flip=$module_params.flip decorations=$module_params.decorations}
   <table  border="0" cellpadding="0" cellspacing="0">
    {foreach from=$modLastVisitors key=key item=item}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$key+1})</td>
      {/if}
      <td class="module">&nbsp;
       <a class="linkmodule" href="tiki-user_information.php?view_user={$item.user|escape:"url"}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$item.user|truncate:$maxlen:"...":true}
        {else}
         {$item.user}
        {/if}
       </a>
	{tr}at{/tr} {$item.currentLogin|tiki_short_datetime}
      </td>
     </tr>
    {/foreach}
   </table>
{/tikimodule}
