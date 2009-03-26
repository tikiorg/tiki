{title help="User+Contacts+Prefs"}{tr}User Contacts Preferences{/tr}{/title}

{include file=tiki-mytiki_bar.tpl}
<div class="navbar">
	{button href="tiki-contacts.php" _text="{tr}Contacts{/tr}"}
</div>

<table class="admin" style="clear:both;">
	<tr>
		<td>
			{if $prefs.feature_tabs eq 'y'}
				{cycle values="1,2" name=tabs print=false advance=false}
				<div class="tabs">
					<span id="tab{cycle name=tabs advance=false}" class="tabmark">
						<a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Options{/tr}</a>
					</span>
					<span id="tab{cycle name=tabs advance=false}" class="tabmark">
						<a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Manage Fields{/tr}</a>
					</span>
				</div>
			{/if}

			{cycle name=content values="1,2" print=false advance=false}
			<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

				<div class="cbox">
					<div class="cbox-title">{tr}Options{/tr}</div>
					<div class="cbox-data">
						<form method='post' action='tiki-user_contacts_prefs.php'>
							<table class="admin">
								<tr>
									<td class="form">{tr}Default view{/tr}:</td>
									<td class="form">
										<input type='radio' name='user_contacts_default_view' value='list' {if $user_contacts_default_view eq 'list'}checked="checked"{/if}/>
										{tr}List View{/tr}
										<input type='radio' name='user_contacts_default_view' value='group' {if $user_contacts_default_view neq 'list'}checked="checked"{/if}/>
										{tr}Group View{/tr}
									</td>
								</tr>
								<tr>
									<td colspan="2" class="button">
										<input type='submit' name='prefs' value='{tr}Change preferences{/tr}' />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>

			<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

				<div class="cbox">
					<div class="cbox-title">{tr}Manage Fields{/tr}</div>
					<div class="cbox-data">
						<form method='post' action='tiki-user_contacts_prefs.php'>
							<table class="admin">
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
													<tr>
														<td class="{cycle advance=false}" width="2%">
															{if not $smarty.foreach.e.first}
																<a href="?ext_up={$ext.fieldId}" title="{tr}Up{/tr}">{icon _id='resultset_up'}</a>
															{/if}
														</td>
														<td class="{cycle advance=false}" width="2%">
															{if not $smarty.foreach.e.last}
																<a href="?ext_down={$ext.fieldId}" title="{tr}Down{/tr}">{icon _id='resultset_down'}</a>
															{/if}
														</td>
														<td class="{cycle advance=false}">{tr}{$ext.fieldname|escape}{/tr}</td>
														<td class="{cycle advance=true}">
															{if $ext.show eq 'y'}
																<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" title="{tr}Hide{/tr}">{icon _id='no_eye' alt='{tr}Hide{/tr}'}</a>
															{else}
																<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" title="{tr}Show{/tr}">{icon _id='eye' alt='{tr}Show{/tr}'}</a>
															{/if}
															<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
														</td>
													</tr>
												{/foreach}
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="button">
										{tr}Add{/tr}: <input type='text' name='ext_add' /> <input type='submit' name='add_fields' value='{tr}Add{/tr}' />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
