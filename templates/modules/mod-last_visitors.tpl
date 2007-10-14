{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_visitors.tpl,v 1.11 2007-10-14 17:51:01 mose Exp $ *}

{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` visitors{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Visitors{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_visitors" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
    <ol style="list-style-position:inside; margin:0; padding:0;{if $nonums eq 'y'} list-style-type:none;{else} list-style-type:decimal;{/if}">
    {foreach from=$modLastVisitors key=key item=item}
        <li><a class="linkmodule" href="tiki-user_information.php?view_user={$item.user|escape:"url"}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$item.user|userlink:'link':'not_set':'':$maxlen}
        {else}
         {$item.user|userlink}
        {/if}
       </a><div style="text-align:right;">{$item.currentLogin|tiki_short_datetime}</div>
      </li>
    {/foreach}
    </ol>
{/tikimodule}
