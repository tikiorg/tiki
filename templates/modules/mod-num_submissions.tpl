{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.6 2003-11-23 03:53:04 zaufi Exp $ *}

{if $feature_submissions eq 'y'}
{tikimodule title="{tr}Waiting Submissions{/tr}" name="num_submissions"}
  {tr}We have{/tr} {$modNumSubmissions} {tr}submissions waiting to be examined{/tr}.
{/tikimodule}
{/if}