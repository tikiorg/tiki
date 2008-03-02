{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_actions.tpl,v 1.6 2007/10/14 17:51:00 mose *}

{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` actions{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last actions{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_actions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastActions}
      <li>
	  <a class="linkmodule" href="tiki-index.php{if $modLastActions[ix].pageName ne ''}?page={$modLastActions[ix].pageName}{/if}" title="{$modLastActions[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastActions[ix].user ne ''}{$modLastActions[ix].user}{else}?{/if}{if (strlen($modLastActions[ix].action) > $maxlen) && ($maxlen > 0)}, {$modLastActions[ix].action}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastActions[ix].action|truncate:$maxlen:"...":true}
        {else}
         {$modLastActions[ix].action}
        {/if}
       </a>
      </li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
