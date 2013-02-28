{* $Id$ *}

{if $user}
{tikimodule error=$module_params.error title=$tpl_module_title name="since_last_visit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{tr}Since your last visit on{/tr}<br>
<div class=date>{$nvi_info.lastVisit|tiki_short_datetime|replace:"[":""|replace:"]":""}</div>
{$nvi_info.images} {tr}New Images{/tr}<br>
{$nvi_info.pages} {tr}Wiki Pages Changed{/tr}<br>
{$nvi_info.files} {tr}New Files{/tr}<br>
{$nvi_info.comments} {tr}New Comments{/tr}<br>
{$nvi_info.trackers} {tr}New Tracker Items{/tr}<br>
{$nvi_info.calendar} {tr}New Calendar Events{/tr}<br>
{$nvi_info.users} {tr}New Users{/tr}<br>
{/tikimodule}
{/if}
