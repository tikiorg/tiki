<!-- START of {$smarty.template} --><center><div class="tabrow"><div class="tabrowRight"></div><div class="tabrowLeft"></div><div class="viewmode"><div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbuttonoff"><a href="{$myurl}?viewmode=day&todate={$now}" title="{tr}Today{/tr}">{tr}Today{/tr}</a></div><div class="calbuttonRight"></div></div>
<div id="prev">
{if $viewmode eq "day"}
<a href="{$myurl}?todate={$daybefore}" title="&laquo; {tr}Day{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "week"}
<a href="{$myurl}?todate={$weekbefore}" title="&laquo; {tr}Week{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "month"}
<a href="{$myurl}?todate={$monthbefore}" title="&laquo; {tr}Month{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "quarter"}
<a href="{$myurl}?todate={$quarterbefore}" title="&laquo; {tr}Quarter{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "semester"}
<a href="{$myurl}?todate={$semesterbefore}" title="&laquo; {tr}Semester{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{elseif $viewmode eq "year"}
<a href="{$myurl}?todate={$yearbefore}" title="&laquo; {tr}Year{/tr}" class="prev">{icon _id="resultset_previous" alt="{tr}Previous{/tr}"}</a>
{/if}
</div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'day'}on{else}off{/if}"><a href="{$myurl}?viewmode=day" title="{tr}Day{/tr}">{tr}Day{/tr}</a></div><div class="calbuttonRight"></div></div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'week'}on{else}off{/if}"><a href="{$myurl}?viewmode=week" title="{tr}Week{/tr}">{tr}Week{/tr}</a></div><div class="calbuttonRight"></div></div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'month'}on{else}off{/if}"><a href="{$myurl}?viewmode=month" title="{tr}Month{/tr}">{tr}Month{/tr}</a></div><div class="calbuttonRight"></div></div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'quarter'}on{else}off{/if}"><a href="{$myurl}?viewmode=quarter" title="{tr}Quarter{/tr}">{tr}Quarter{/tr}</a></div><div class="calbuttonRight"></div></div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'semester'}on{else}off{/if}"><a href="{$myurl}?viewmode=semester" title="{tr}Semester{/tr}">{tr}Semester{/tr}</a></div><div class="calbuttonRight"></div></div>
<div class="calbuttonBox"><div class="calbuttonLeft"></div><div class="calbutton{if $viewmode eq 'year'}on{else}off{/if}"><a href="{$myurl}?viewmode=year" title="{tr}Year{/tr}">{tr}Year{/tr}</a></div><div class="calbuttonRight"></div></div>
<div id="next">
{if $viewmode eq "day"}
<a href="{$myurl}?todate={$dayafter}" title="{tr}Day{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "week"}
<a href="{$myurl}?todate={$weekafter}" title="{tr}Week{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "month"}
<a href="{$myurl}?todate={$monthafter}" title="{tr}Month{/tr}&raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "quarter"}
<a href="{$myurl}?todate={$quarterafter}" title="{tr}Quarter{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "semester"}
<a href="{$myurl}?todate={$semesterafter}" title="{tr}Semester{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{elseif $viewmode eq "year"}
<a href="{$myurl}?todate={$yearafter}" title="{tr}Year{/tr} &raquo;" class="next">{icon _id="resultset_next" alt="{tr}Next{/tr}"}</a>
{/if}
</div></div></div></center><!-- END of {$smarty.template} -->
