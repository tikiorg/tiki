{include file="header.tpl"}

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
{/if}

<div class="admin">
<table class="normal">
<tr><th colspan="2">{tr}Operations{/tr}</th></tr>

</table>

</div>

{include file="footer.tpl"}
