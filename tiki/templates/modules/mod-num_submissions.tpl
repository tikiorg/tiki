{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.7 2005-03-12 16:51:00 mose Exp $ *}

{if $feature_submissions eq 'y'}
{tikimodule title="{tr}Waiting Submissions{/tr}" name="num_submissions" flip=$module_params.flip decorations=$module_params.decorations}
  {tr}We have{/tr} {$modNumSubmissions} {tr}submissions waiting to be examined{/tr}.
{/tikimodule}
{/if}
