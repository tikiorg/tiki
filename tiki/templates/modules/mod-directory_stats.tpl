{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_stats.tpl,v 1.7 2005-03-12 16:50:59 mose Exp $ *}

{if $feature_directory eq 'y'}
{tikimodule title="{tr}Directory Stats{/tr}" name="directory_stats" flip=$module_params.flip decorations=$module_params.decorations}

  {tr}Sites{/tr}: {$modDirStats.valid}<br />
  {tr}Sites to validate{/tr}: {$modDirStats.invalid}<br />
  {tr}Categories{/tr}: {$modDirStats.categs}<br />
  {tr}Searches{/tr}: {$modDirStats.searches}<br />
  {tr}Visited links{/tr}: {$modDirStats.visits}<br />

{/tikimodule}
{/if}
