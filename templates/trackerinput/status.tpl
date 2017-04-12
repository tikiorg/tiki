<select name="status" class="form-control">
	{foreach $status_types as $st => $stinfo}
		<option value="{$st}"
			{if $stinfo.name eq $status} selected="selected"{/if}
			class="tracker-{$stinfo.iconname}">
			{$stinfo.label|escape}
		</option>
	{/foreach}
</select>
