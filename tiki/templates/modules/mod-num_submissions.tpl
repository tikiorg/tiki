{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-num_submissions.tpl,v 1.11 2007-10-04 22:17:47 nyloth Exp $ *}

{if $prefs.feature_submissions eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Waiting Submissions{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="num_submissions" flip=$module_params.flip decorations=$module_params.decorations}
  {tr}We have{/tr} {$modNumSubmissions} <a class="linkmodule" href="tiki-list_submissions.php">{tr}submissions waiting to be examined{/tr}</a>.
{/tikimodule}
{/if}
