{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-num_submissions.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_submissions eq 'y'}
{tikimodule title="{tr}Waiting Submissions{/tr}" name="num_submissions"}
  {tr}We have{/tr} {$modNumSubmissions} {tr}submissions waiting to be examined{/tr}.
{/tikimodule}
{/if}