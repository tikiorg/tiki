{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-mid">
	<div class="panel panel-default">
		<div class="panel-heading">
			{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle"} {tr}Error{/tr}
		</div>
		<div class="panel-body">
			{$msg}
		</div>
	</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
