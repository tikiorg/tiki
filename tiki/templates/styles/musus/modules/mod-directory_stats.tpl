{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-directory_stats.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_directory eq 'y'}
{tikimodule title="{tr}Directory Stats{/tr}" name="directory_stats"}

  {tr}Sites{/tr}: {$modDirStats.valid}<br />
  {tr}Sites to validate{/tr}: {$modDirStats.invalid}<br />
  {tr}Categories{/tr}: {$modDirStats.categs}<br />
  {tr}Searches{/tr}: {$modDirStats.searches}<br />
  {tr}Visited links{/tr}: {$modDirStats.visits}<br />

{/tikimodule}
{/if}
