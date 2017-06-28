{* $Id$ *}
<form action="tiki-admin.php?page=intertiki" method="post" name="intertiki" class="form-horizontal">
	{include file='access/include_ticket.tpl'}
	{include file='admin/include_apply_top.tpl'}
	{tabset name="admin_interwiki"}
		{tab name="{tr}Intertiki Client{/tr}"}
			<em>{tr}Set up this Tiki site as the Intertiki client{/tr}</em><br><br>
			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_intertiki}
			</fieldset>
			<fieldset>
				<legend>{tr}Client server settings{/tr}</legend>
				{preference name=tiki_key}
				{preference name=feature_intertiki_sharedcookie}
			</fieldset>
			<fieldset>
				<legend>{tr}Currently linked master server{/tr}</legend>
				{preference name=feature_intertiki_mymaster mode=notempty}
				<div class="adminoptionboxchild feature_intertiki_mymaster_childcontainer">
					{preference name=feature_intertiki_import_preferences}
					{preference name=feature_intertiki_import_groups}
					{preference name=feature_intertiki_imported_groups}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Add an available master server{/tr}</legend>
				{foreach $serverFields as $field}
					<div class="form-group">
						<label class="col-sm-4 control-label">{tr}Server {$field}{/tr}</label>
						<div class="col-sm-8">
							<input type="text" name="new[{$field}]" value="" class="form-control">
						</div>
					</div>
				{/foreach}
			</fieldset>
			{if $prefs.interlist}
				<fieldset>
					<legend>{tr}Available master Tiki servers{/tr}</legend>
					<div class="form-group">
						<div class="col-sm-12">
							<table class="table">
								<thead>
								<tr>
									<td>{tr}Name{/tr}</td>
									<td>{tr}Host{/tr}</td>
									<td>{tr}Port{/tr}</td>
									<td>{tr}Path{/tr}</td>
									<td>{tr}Group{/tr}</td>
									<td></td>
								</tr>
								</thead>
								<tbody>
								{foreach key=k item=i from=$prefs.interlist}
									<tr>
										<td><input type="text" class="form-control" name="interlist[{$k}][name]" value="{$i.name}"></td>
										<td><input type="text" class="form-control" name="interlist[{$k}][host]" value="{$i.host}"></td>
										<td><input type="text" class="form-control" name="interlist[{$k}][port]" value="{$i.port}"></td>
										<td><input type="text" class="form-control" name="interlist[{$k}][path]" value="{$i.path}"></td>
										<td><input type="text" class="form-control" name="interlist[{$k}][groups]" value="{foreach item=g from=$i.groups name=f}{$g}{if !$smarty.foreach.f.last},{/if}{/foreach}"></td>
										<td>
											<button type="submit" name="del" value="{$k}" class="btn btn-link tips timeout" title="{tr}Delete master server{/tr}:{$k}">{icon name='delete'}</button>
										</td>
									</tr>
								{/foreach}
								<tbody>
							</table>
						</div>
					</div>
				</fieldset>
			{/if}
		{/tab}
		{if $prefs.feature_intertiki_mymaster eq ''}
			{tab name="{tr}Intertiki Master Server{/tr}"}
				<em>{tr}Set up this Tiki site as the InterTiki master server{/tr}</em><br><br>
				<fieldset>
					<legend>{tr}Activate the feature{/tr}</legend>
					{preference name=feature_intertiki_server}
				</fieldset>
				<fieldset>
					<legend>{tr}Master server settings{/tr}</legend>
					{preference name=intertiki_logfile}
				</fieldset>
				<fieldset>
					<legend>{tr}Allowed client servers{/tr}</legend>
					<div class="form-group">
						<div class="col-sm-12">
							<table class="table">
								<thead>
								<tr>
									<td>&nbsp;</td>
									<td><label for="known_hosts_name">{tr}Name{/tr}</label></td>
									<td><label for="known_hosts_key">{tr}Key{/tr}</label></td>
									<td><label for="known_hosts_ip">{tr}IP{/tr}</label></td>
									<td><label for="known_hosts_contact">{tr}Contact{/tr}</label></td>
									<td><label for="known_hosts_can_register">{tr}Can register{/tr}</label></td>
								</tr>
								</thead>
								<tbody>
								{if $prefs.known_hosts}
									{foreach key=k item=i from=$prefs.known_hosts}
										<tr>
											<td>
												<button type="submit" name="delk" class="btn btn-link" value="{$k|escape}" class="tips timeout" title=":{tr}Delete{/tr}">{icon name='delete'}</button>
											</td>
											<td>
												<input type="text" class="form-control" id="known_hosts_name" name="known_hosts[{$k}][name]" value="{$i.name}">
											</td>
											<td>
												<input type="text" class="form-control tips" id="known_hosts_key" name="known_hosts[{$k}][key]" value="{$i.key}"
													readonly="readonly" title="|{tr}To change the host key you need to remove and add it as a new one{/tr}">
											</td>
											<td>
												<input type="text" class="form-control" id="known_hosts_ip" name="known_hosts[{$k}][ip]" value="{$i.ip}">
											</td>
											<td>
												<input type="text" class="form-control" id="known_hosts_contact" name="known_hosts[{$k}][contact]" value="{$i.contact}">
											</td>
											<td>
												<input type="checkbox" class="form-control" id="known_hosts_can_register" name="known_hosts[{$k}][allowusersregister]" {if isset($i.allowusersregister) && $i.allowusersregister eq 'y'}checked="checked"{/if} />
											</td>
										</tr>
									{/foreach}
								{/if}
								<tr>
									<td>{tr}New:{/tr}</td>
									<td><label class="sr-only" for="new_host_name">{tr}New{/tr}</label><input type="text" class="form-control" id="new_host_name" name="newhost[name]" value=""/></td>
									<td><label class="sr-only" for="new_host_key">{tr}Key{/tr}</label><input type="text" class="form-control" id="new_host_key" name="newhost[key]" value=""/></td>
									<td><label class="sr-only" for="new_host_ip">{tr}IP{/tr}</label><input type="text" class="form-control" id="new_host_ip" name="newhost[ip]" value=""/></td>
									<td><label class="sr-only" for="new_host_contact">{tr}Contact{/tr}</label><input type="text" class="form-control" id="new_host_contact" name="newhost[contact]" value=""/></td>
									<td><label class="sr-only" for="new_host_can_register">{tr}Can register{/tr}</label><input type="checkbox" id="new_host_can_register" name="newhost[allowusersregister]"/></td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</fieldset>
			{/tab}
		{/if}
	{/tabset}
	{include file='admin/include_apply_bottom.tpl'}
</form>

