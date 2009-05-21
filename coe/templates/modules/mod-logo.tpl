{* $Id$ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Tiki Logo{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $prefs.feature_sitelogo eq 'y'}
   <div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>
      {if $prefs.sitelogo_src}<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
      {/if}
   </div>
   <div id="sitetitles">
      <div id="sitetitle">
         <a href="index.php">{$prefs.sitetitle}</a>
      </div>
      <div id="sitesubtitle">{$prefs.sitesubtitle}
      </div>
   </div>
{/if}
{/tikimodule}