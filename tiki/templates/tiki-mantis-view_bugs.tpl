<a class="pagetitle" href="tiki-mantis-view_bugs.php">{tr}Mantis View Bugs{/tr}</a>
<br />
<br />
<form method="post" action="tiki-mantis-view_bugs.php?f=3">
<input type="hidden" name="type" value="1" />
<input type="hidden" name="sort" value="{$t_sort}" />
<input type="hidden" name="dir" value="{$t_dir}" />
<input type="hidden" name="page_number" value="{$f_page_number}" />
<input type="hidden" name="per_page" value="{$t_filter}" />
<table class="normal">

{*Filter Form Header Row*}
<tr>
	<td class="heading">{tr}reporter{/tr}</td>
	<td class="heading">{tr}assigned_to{/tr}</td>
	<td class="heading">{tr}category{/tr}</td>
	<td class="heading">{tr}severity{/tr}</td>
	<td class="heading">{tr}status{/tr}</td>
	<td class="heading">{tr}show{/tr}</td>
	<td class="heading">{tr}changed{/tr}</td>
	<td class="heading">{tr}hide_status{/tr}</td>
</tr>

{*Filter Form Fields*}
<tr>
	{*Reporter*}
	<td>
		<select name="reporter_id">
			<option value="any">{tr}any{/tr}</option>
			<option value="any"></option>
			{section name=x loop=$t_filter_reporter_id}
			<option value="{$t_filter_reporter_id[x]|escape}" {if $t_filter_reporter_id[x].value eq $p_filter_reporter_id}selected="selected"{/if}>{$t_filter_reporter_id[x]}</option>
			{/section}
		</select>
	</td>

	{*Handler*}
	<td>
		<select name="handler_id">
			<option value="" {if $fields[ix].value eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
			<option value="any">{tr}any{/tr}</option>
			<option value="none" <?php check_selected( $t_filter['handler_id'], 'none' ); ?>>{tr}none{/tr}</option>
{section name=ux loop=$users}
<option value="{$users[ux]|escape}">{$users[ux]}</option>
{/section}
			<option value="any"></option>
			<?php print_assign_to_option_list( $t_filter['handler_id'] ) ?>
		</select>
	</td>

	{*Category*}
	<td>
		<select name="show_category">
			<option value="any">{tr}any{/tr}</option>
			<option value="any"></option>
			<?php # This shows orphaned categories as well as selectable categories ?>
			<?php print_category_complete_option_list( $t_filter['show_category'] ) ?>
		</select>
	</td>

	{*Severity*}
	<td>
		<select name="show_severity">
			<option value="any">{tr}any{/tr}</option>
			<option value="any"></option>
			<?php print_enum_string_option_list( 'severity', $t_filter['show_severity'] ) ?>
		</select>
	</td>

	{*Status*}
	<td>
		<select name="show_status">
			<option value="any"><?php echo lang_get( 'any' ) ?></option>
			<option value="any"></option>
			<?php print_enum_string_option_list( 'status', $t_filter['show_status'] ) ?>
		</select>
	</td>

	<?php # -- Number of bugs per page -- ?>
	<td>
		<input type="text" name="per_page" size="3" maxlength="7" value="<?php echo $t_filter['per_page'] ?>" />
	</td>

	<?php # -- Highlight changed bugs -- ?>
	<td>
		<input type="text" name="highlight_changed" size="3" maxlength="7" value="<?php echo $t_filter['highlight_changed'] ?>" />
	</td>

	<?php # -- Hide closed bugs -- ?>
	<td>
		<input type="checkbox" name="hide_resolved" <?php check_checked( $t_filter['hide_resolved'], 'on' ); ?> />&nbsp;<?php echo lang_get( 'filter_resolved' ); ?>
		<input type="checkbox" name="hide_closed" <?php check_checked( $t_filter['hide_closed'], 'on' ); ?> />&nbsp;<?php echo lang_get( 'filter_closed' ); ?>
	</td>
</tr>


</table>




