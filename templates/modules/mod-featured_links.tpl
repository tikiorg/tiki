{* $Id$ *}

{if $prefs.feature_featuredLinks eq 'y'}
	{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Featured links{/tr}"}{/if}
	{tikimodule title=$tpl_module_title name="featured_links" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$featuredLinks}
	<li>
     {if $featuredLinks[ix].type eq 'f'}
	 	<a class="linkmodule" href="tiki-featured_link.php?type={$featuredLinks[ix].type}&amp;url={$featuredLinks[ix].url|escape:"url"}">{$featuredLinks[ix].title}</a>
     {else}
	 	<a class="linkmodule" {if $featuredLinks[ix].type eq 'n'}target='_blank'{/if} href="{$featuredLinks[ix].url}">{$featuredLinks[ix].title}</a>
	{/if}
	</li>
    {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
  {/tikimodule}
{/if}
