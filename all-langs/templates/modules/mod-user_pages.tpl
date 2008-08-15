{* $Id$ *}

{if $user}
  {if $prefs.feature_wiki eq 'y'}
       
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My Pages{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
       {if $nonums != 'y'}<ol>{else}<ul>{/if}
       {section name=ix loop=$modUserPages}
       <li>
         <a class="linkmodule" href="tiki-index.php?page={$modUserPages[ix].pageName|escape:"url"}">
          {$modUserPages[ix].pageName}
         </a>
        </li>
       {/section}
	   {if $nonums != 'y'}</ol>{else}</ul>{/if}
       {/tikimodule}
       
  {/if} {* $prefs.feature_wiki eq 'y' *}
{/if}   {* $user *}
