{* $Id$ *}

{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` visitors{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Visitors{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_visitors" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
    <ol style="list-style-position:inside; margin:0; padding:0;{if ($nonums eq 'y') or ($showavatars eq 'y')} list-style-type:none;{else} list-style-type:decimal;{/if}">
{if !$user}
	<li>
{if $showavatars eq 'y'}
         <table class="admin"><tr class="odd"><td width="50">
	 <img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No avatar.{/tr}" border="0"/>
         </td><td>
{/if}
	 {if $prefs.allowRegister eq 'y'}<a class="linkmodule" href="tiki-register.php" title="{tr}Register{/tr}">{/if}{tr}You{/tr}{if $prefs.allowRegister eq 'y'}</a>{/if}<div align="right">{$currentLogin|tiki_short_datetime}</div>
{if $showavatars eq 'y'}
         </td></tr></table>
{/if}

        </li>
{/if}
    {cycle values="even,odd" print=false}
    {foreach from=$modLastVisitors key=key item=item}
        <li>

{if $showavatars eq 'y'}
         <table class="admin"><tr class="{cycle advance=true}"><td width="50">{$item.user|avatarize|default:'<img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No avatar.{/tr}" />'}</td><td>
{/if}

         <a class="linkmodule" href="tiki-user_information.php?view_user={$item.user|escape:"url"}">
{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
          {$item.user|userlink:'link':'not_set':'':$maxlen}
{else}
          {$item.user|userlink}
{/if}
         </a><div style="text-align:right;">{$item.currentLogin|tiki_short_datetime}</div>
{if $showavatars eq 'y'}
         </td></tr></table>
{/if}
      </li>
    {/foreach}
    </ol>
{/tikimodule}
