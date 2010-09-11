{title help="UserAssignedModules"}{tr}User assigned modules{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="navbar">
	{button href="tiki-user_assigned_modules.php?recreate=1" _text="{tr}Restore defaults{/tr}"}
</div>

<h2>{tr}User assigned modules{/tr}</h2>
<table >
	<tr>
		{if $prefs.feature_left_column ne 'n' || count($modules_l) > 0}
			<td >
				<b>{tr}Left column{/tr}</b>
				{if $prefs.feature_left_column eq 'n' and count($modules_l) > 0}<br /><span class="highlight">{tr}The column is disabled{/tr}</span>{/if}
			</td>
		{/if}
		{if $prefs.feature_right_column ne 'n' || count($modules_r) > 0}
			<td >
				<b>{tr}Right column{/tr}</b>
				{if $prefs.feature_right_column eq 'n' and count($modules_r) > 0}<br /><span class="highlight">{tr}The column is disabled{/tr}</span>{/if}
			</td>
		{/if}
	</tr>
	<tr>
		<!-- left column -->
		{if $prefs.feature_left_column ne 'n' || count($modules_l) > 0}
			<td>
				<table  class="normal">
					<tr>
						<th>{tr}#{/tr}</th>
						<th>{tr}Name{/tr}</th>
						<th>{tr}act{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false}
					{section name=ix loop=$modules_l}
						<tr class="{cycle}">
							<td>{$modules_l[ix].ord}</td>
							<td>{$modules_l[ix].name}</td>
							<td>
								<a class="link" href="tiki-user_assigned_modules.php?up={$modules_l[ix].moduleId}">{icon _id='resultset_up'}</a>
								<a class="link" href="tiki-user_assigned_modules.php?down={$modules_l[ix].moduleId}">{icon _id='resultset_down'}</a>
								{if $prefs.feature_right_column ne 'n'}
									<a class="link" href="tiki-user_assigned_modules.php?right={$modules_l[ix].moduleId}">
										{icon _id='resultset_next' alt="{tr}Right{/tr}" title="{tr}Move to Right Column{/tr}"}
									</a>
								{/if}
								{if $modules_l[ix].name ne 'application_menu' and $modules_l[ix].name ne 'login_box' and $modules_l[ix].type ne 'P'}
									<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_l[ix].moduleId}">{icon _id='cross' alt="{tr}Unassign{/tr}"}</a> 
								{/if}
							</td>
						</tr>
					{/section}
				</table>
			</td>
		{/if}
		<!-- right column -->
		{if $prefs.feature_right_column ne 'n' || count($modules_r) > 0}
			<td >
				<table  class="normal">
					<tr>
						<th>{tr}#{/tr}</th>
						<th>{tr}Name{/tr}</th>
						<th>{tr}act{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false}
					{section name=ix loop=$modules_r}
						<tr class="{cycle}">
							<td>{$modules_r[ix].ord}</td>
							<td>{$modules_r[ix].name}</td>
							<td>
								<a class="link" href="tiki-user_assigned_modules.php?up={$modules_r[ix].moduleId}">{icon _id='resultset_up'}</a>
								<a class="link" href="tiki-user_assigned_modules.php?down={$modules_r[ix].moduleId}">{icon _id='resultset_down'}</a>
								{if $prefs.feature_left_column ne 'n'}
									<a class="link" href="tiki-user_assigned_modules.php?left={$modules_r[ix].moduleId}">
										{icon _id='resultset_previous' alt="{tr}Left{/tr}" title="{tr}Move to Left Column{/tr}"}
									</a>
								{/if}
								{if $modules_r[ix].name ne 'application_menu' and $modules_r[ix].name ne 'login_box' and $modules_r[ix].type ne 'P'}
									<a class="link" href="tiki-user_assigned_modules.php?unassign={$modules_r[ix].moduleId}">{icon _id='cross' alt="{tr}Unassign{/tr}"}</a> 
								{/if}
							</td>
						</tr>
					{/section}
				</table>
			</td>
		{/if}
	</tr>
</table>

{if $canassign eq 'y'}
<br />
	<form action="tiki-user_assigned_modules.php" method="post">
		<h2>{tr}Assign module{/tr}</h2>
		<table class="formcolor">
			<tr>
				<td>{tr}Module{/tr}:</td>
				<td>
					<select name="module">
						{section name=ix loop=$assignables}
							<option value="{$assignables[ix].moduleId|escape}">{$assignables[ix].name}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Column{/tr}:</td>
				<td>
					<select name="position">
						{if $prefs.feature_left_column ne 'n'}<option value="l">{tr}Left{/tr}</option>{/if}
						{if $prefs.feature_right_column ne 'n'}<option value="r">{tr}Right{/tr}</option>{/if}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Order{/tr}:</td>
				<td>
					<select name="order">
						{section name=ix loop=$orders}
							<option value="{$orders[ix]|escape}">{$orders[ix]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="assign" value="{tr}Assign{/tr}" /></td>
			</tr>
		</table>
	</form>
{/if}
