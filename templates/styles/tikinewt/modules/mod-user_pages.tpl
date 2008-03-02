{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-user_pages.tpl,v 1.15 2007/10/14 17:51:02 mose *}

{if $user}
  {if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My Pages{/tr}"}{/if}
	{tikimodule title=$tpl_module_title name="user_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modUserPages}
		<li>
		<a class="linkmodule" href="tiki-index.php?page={$modUserPages[ix].pageName|escape:"url"}">
          {$modUserPages[ix].pageName}</a>
		</li>
       {/section}
	   {if $nonums != 'y'}</ol>{else}</ul>{/if}
       {/tikimodule}
  {/if}
{/if}
