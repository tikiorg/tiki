{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_stats.tpl,v 1.11 2007-10-14 17:51:00 mose Exp $ *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Directory Stats{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="directory_stats" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}

  {tr}Sites{/tr}: {$modDirStats.valid}<br />
  {tr}Sites to validate{/tr}: {$modDirStats.invalid}<br />
  {tr}Categories{/tr}: {$modDirStats.categs}<br />
  {tr}Searches{/tr}: {$modDirStats.searches}<br />
  {tr}Visited links{/tr}: {$modDirStats.visits}<br />

{/tikimodule}
{/if}
