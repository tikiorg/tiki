{* $Id$ *}

{if $user}
    {if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My galleries{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_image_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$modUserG}
	    <li><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></li>
	{/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
	{/tikimodule}
    {/if}
{/if}
