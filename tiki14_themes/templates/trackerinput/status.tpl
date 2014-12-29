<select name="status" class="form-control">
	{foreach $status_types as $st => $stinfo}
		<option value="{$st}"
			{if $stinfo.name eq $status} selected="selected"{/if}
			style="background: url('{$stinfo.image|escape}') no-repeat;padding-left:17px;">
			{$stinfo.label|escape}
		</option>
	{/foreach}
</select>
