{title help="User+Contacts+Prefs"}{tr}User Contacts Preferences{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
<div class="navbar">
	{button href="tiki-contacts.php" _text="{tr}Contacts{/tr}"}
</div>

{tabset name="contact_prefs"}
	{tab name="{tr}Options{/tr}"}
		<div class="cbox">
			<div class="cbox-title">{tr}Options{/tr}</div>
			<div class="cbox-data">
				<form method='post' action='tiki-user_contacts_prefs.php'>
					<table class="formcolor">
						<tr>
							<td>{tr}Default View{/tr}:</td>
							<td>
								<input type='radio' name='user_contacts_default_view' value='list' {if $user_contacts_default_view eq 'list'}checked="checked"{/if}/>
								{tr}List View{/tr}
								<input type='radio' name='user_contacts_default_view' value='group' {if $user_contacts_default_view neq 'list'}checked="checked"{/if}/>
								{tr}Group View{/tr}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type='submit' name='prefs' value="{tr}Change preferences{/tr}" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	{/tab}

	{tab name="{tr}Manage Fields{/tr}"}
		<div class="cbox">
			<div class="cbox-title">{tr}Manage Fields{/tr}</div>
			<div class="cbox-data">
				<form method='post' action='tiki-user_contacts_prefs.php'>
					<table class="normal">
						<tr>
							<td>
								<table class="normal">
									<tr>
										<th colspan="2">{tr}Order{/tr}</th>
										<th>{tr}Field{/tr}</th>
										<th>{tr}Action{/tr}</th>
									</tr>
									{cycle values="odd,even" print=false}
									{foreach from=$exts item=ext key=k name=e}
										<tr class="{cycle}">
											<td width="2%">
												{if not $smarty.foreach.e.first}
													<a href="?ext_up={$ext.fieldId}" title="{tr}Up{/tr}">{icon _id='resultset_up'}</a>
												{/if}
											</td>
											<td width="2%">
												{if not $smarty.foreach.e.last}
													<a href="?ext_down={$ext.fieldId}" title="{tr}Down{/tr}">{icon _id='resultset_down'}</a>
												{/if}
											</td>
											<td>{tr}{$ext.fieldname|escape}{/tr}</td>
											<td>
												{if $ext.flagsPublic eq 'y'}
													<a href="?ext_private={$ext.fieldId}" style="margin-left:20px;" title="{tr}Private{/tr}">{icon _id='user' alt="{tr}Private{/tr}"}</a>
												{else}
													<a href="?ext_public={$ext.fieldId}" style="margin-left:20px;" title="{tr}Public{/tr}">{icon _id='group' alt="{tr}Public{/tr}"}</a>
												{/if}
												{if $ext.show eq 'y'}
													<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" title="{tr}Hide{/tr}">{icon _id='no_eye' alt="{tr}Hide{/tr}"}</a>
												{else}
													<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" title="{tr}Show{/tr}">{icon _id='eye' alt="{tr}Show{/tr}"}</a>
												{/if}
												<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
											</td>
										</tr>
									{/foreach}
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								{tr}Add{/tr}: <input type='text' name='ext_add' /> <input type='submit' name='add_fields' value="{tr}Add{/tr}" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	{/tab}
{/tabset}
