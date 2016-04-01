{* $Id$ *}
{tabset name="admin_interwiki"}
	{tab name="{tr}Intertiki client{/tr}"}
		<h2>{tr}Intertiki client{/tr}</h2>
		<form action="tiki-admin.php?page=intertiki" method="post" name="intertiki" class="form-horizontal">
			<input type="hidden" name="ticket" value="{$ticket|escape}">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Feature{/tr}</label>
				<div class="col-sm-7">
		      		{preference name=feature_intertiki}
	      		</div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}Tiki Unique key{/tr}</label>
				<div class="col-sm-7">
				      <input type="text" name="tiki_key" value="{$prefs.tiki_key}" size="32" class="form-control"/>
			    </div>
		    </div>
		    <div class="form-group">
				<label class="col-sm-3 control-label">{tr}InterTiki Slave mode{/tr}
				{tr}<small>Warning: overrides manually registered local users</small>{/tr}</label>
				<div class="col-sm-7">
			        <select name="feature_intertiki_mymaster" onchange="check_server_visibility(this);" class="form-control">
						<option value="">{tr}No{/tr}</option>
						{foreach from=$prefs.interlist key=k item=i}
							<option value="{$k|escape}"{if $prefs.feature_intertiki_mymaster eq $k} selected="selected"{/if}>{$i.name} {tr}as master{/tr}</option>
						{/foreach}
					</select>
					{jq}
						{literal}
						function check_server_visibility(sel) {
							if (sel.selectedIndex == 0) {
								document.getElementById('admin-server-options').style.display = 'block';
								document.getElementById('admin-slavemode-options').style.display = 'none';
							} else {
								document.getElementById('admin-server-options').style.display = 'none';
								document.getElementById('admin-slavemode-options').style.display = 'block';
							}
						}
						{/literal}
					{/jq}
			    </div>
		    </div>
			<div id="admin-slavemode-options" style="display: {if $prefs.feature_intertiki_mymaster eq ''}none{else}block{/if}">
			 	<div class="form-group">
					<label class="col-sm-3 control-label" for="feature_intertiki_import_preferences">{tr}Import user preferences{/tr}</label>
					<div class="col-sm-7">
						<input type="checkbox" name="feature_intertiki_import_preferences" id="feature_intertiki_import_preferences" {if $prefs.feature_intertiki_import_preferences eq 'y'}checked="checked"{/if}/>
					</div>
					<label class="col-sm-3 control-label" for="feature_intertiki_import_groups">{tr}Import user groups{/tr}</label>
					<div class="col-sm-7">
						<input type="checkbox" name="feature_intertiki_import_groups" id="feature_intertiki_import_groups" {if $prefs.feature_intertiki_import_groups eq 'y'}checked="checked"{/if}/>
					</div>
					<label class="col-sm-3 control-label" for="feature_intertiki_imported_groups">{tr}Limit group import (comma-separated list of imported groups, leave empty to avoid limitation){/tr}</label>
					<div class="col-sm-7">
						<input type="text" name="feature_intertiki_imported_groups" id="feature_intertiki_imported_groups" value="{$prefs.feature_intertiki_imported_groups}" class="form-control" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="feature_intertiki_sharedcookie">{tr}Intertiki shared cookie for sliding auth under same domain{/tr}</label>
					<div class="col-sm-9">
					<input type="checkbox" id="feature_intertiki_sharedcookie" name="feature_intertiki_sharedcookie" {if $prefs.feature_intertiki_sharedcookie eq 'y'}checked="checked"{/if} />
					</div>
			</div>
		    <div class="form-group">
		    	<label class="col-sm-3 control-label">{tr}Add new server{/tr}</label>
	    			<div class="col-sm-7 margin-bottom-sm">
	    				<input type="text" name="new[name]" value="" class="form-control"
	    				placeholder="Server Name"/>
	    			</div>
	    			<div class="col-sm-7 col-sm-offset-3 margin-bottom-sm">
	    				<input type="text" name="new[port]" value="" class="form-control"
	    				placeholder="Server Port"/>
	    			</div>
	    			<div class="col-sm-7 col-sm-offset-3 margin-bottom-sm">
	    				<input type="text" name="new[path]" value="" class="form-control"
	    				placeholder="Server Path"/>
	    			</div>
	    			<div class="col-sm-7 col-sm-offset-3 margin-bottom-sm">
	    				<input type="text" name="new[groups]" value="" class="form-control"
	    				placeholder="Server Groups"/>
	    			</div>
	    	</div>
	    	<div class="form-group">
				<label class="col-sm-3 text-left control-label ">{tr}InterTiki Servers{/tr}</label>
				<div class="col-sm-9"></div>
		    </div>
    		{if $prefs.interlist}
    		</br>
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
		    					<td>{tr}Remove{/tr}</td>
		    				</tr>
		    			</thead>
		    			<tbody>
						{foreach key=k item=i from=$prefs.interlist}
							<tr>
								<td><input type="text" class="form-control" name="interlist[{$k}][name]" value="{$i.name}" /></td>
								<td><input type="text" class="form-control" name="interlist[{$k}][host]" value="{$i.host}" /></td>
								<td><input type="text" class="form-control" name="interlist[{$k}][port]" value="{$i.port}" /></td>
								<td><input type="text" class="form-control" name="interlist[{$k}][path]" value="{$i.path}" /></td>
								<td><input type="text" class="form-control" name="interlist[{$k}][groups]" value="{foreach item=g from=$i.groups name=f}{$g}{if !$smarty.foreach.f.last},{/if}{/foreach}" /></td>
								<td>
									<a href="tiki-admin.php?page=intertiki&amp;del={$k|escape:'url'}" class="tips" title=":{tr}Delete{/tr}">{icon name='delete'}</a>
									{tr}InterTiki Server{/tr} <b>{$k}</b>
								</td>
							</tr>
						{/foreach}
						<tbody>
					</table>
				</div>
			</div>
			{/if}
			</br>
			<div class="heading input_submit_container text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="intertikiclient" value="{tr}Save{/tr}"/>
			</div>
		</form>
	{/tab}
	{if $prefs.feature_intertiki_mymaster eq ''}
		{tab name="{tr}Intertiki server{/tr}"}
			<h2>{tr}Intertiki server{/tr}</h2>
			<form action="tiki-admin.php?page=intertiki" method="post" name="intertiki" class="form-horizontal">
				<input type="hidden" name="ticket" value="{$ticket|escape}">
				<div class="form-group">
					<label class="col-sm-3 control-label">{tr}Intertiki shared cookie for sliding auth under same domain{/tr}</label>
					<div class="col-sm-7">
					      <input type="checkbox" name="feature_intertiki_sharedcookie" {if $prefs.feature_intertiki_sharedcookie eq 'y'}checked="checked"{/if}/>
				    </div>
			    </div>
			    <div class="form-group">
					<label class="col-sm-3 control-label">{tr}Intertiki Server enabled{/tr}</label>
					<div class="col-sm-7">
					      <input type="checkbox" name="feature_intertiki_server" {if $prefs.feature_intertiki_server eq 'y'}checked="checked"{/if}/>
				    </div>
			    </div>
			    <div class="form-group">
					<label class="col-sm-3 control-label">{tr}Access Log file{/tr}</label>
					<div class="col-sm-7">
					      <input type="text" name="intertiki_logfile" value="{$prefs.intertiki_logfile}" size="42" class="form-control" />
				    </div>
			    </div>
			    <div class="form-group">
					<label class="col-sm-3 control-label">{tr}Errors Log file{/tr}</label>
					<div class="col-sm-7">
					      <input type="text" name="intertiki_errfile" value="{$prefs.intertiki_errfile}" size="42" class="form-control" />
				    </div>
			    </div>
			    <div class="form-group">
					<label class="col-sm-3 text-left control-label ">{tr}Known Hosts{/tr}</label>
					<div class="col-sm-9"></div>
			    </div>
			    <div class="form-group">
			    	<div class="col-sm-12">
			    		<table class="table">
			    			<thead>
								<tr>
									<td>&nbsp;</td>
									<td class="text-center"><label for="known_hosts_name">{tr}Name{/tr}</label></td>
									<td class="text-center"><label for="known_hosts_key">{tr}Key{/tr}</label></td>
									<td class="text-center"><label for="known_hosts_ip">{tr}IP{/tr}</label></td>
									<td class="text-center"><label for="known_hosts_contact">{tr}Contact{/tr}</label></td>
									<td class="text-center"><label for="known_hosts_can_register">{tr}Can register{/tr}</label></td>
								</tr>
							</thead>
							<tbody>
							{if $prefs.known_hosts}
								{foreach key=k item=i from=$prefs.known_hosts}
									<tr>
										<td>
											<a href="tiki-admin.php?page=intertiki&amp;delk={$k|escape:'url'}" class="tips" title=":{tr}Delete{/tr}">{icon name='delete'}</a>
										</td>
										<td>
											<input type="text" class="form-control" id="known_hosts_name" name="known_hosts[{$k}][name]" value="{$i.name}" />
										</td>
										<td>
											<input type="text" class="form-control" id="known_hosts_key" name="known_hosts[{$k}][key]" value="{$i.key}"  />
										</td>
										<td>
											<input type="text" class="form-control" id="known_hosts_ip" name="known_hosts[{$k}][ip]" value="{$i.ip}"  />
										</td>
										<td>
											<input type="text" class="form-control" id="known_hosts_contact"  name="known_hosts[{$k}][contact]" value="{$i.contact}"  />
										</td>
										<td>
											<input type="checkbox" class="form-control" id="known_hosts_can_register" name="known_hosts[{$k}][allowusersregister]" {if $i.allowusersregister eq 'y'}checked="checked"{/if} />
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
								<td class="text-center"><label class="sr-only" for="new_host_can_register">{tr}Can register{/tr}</label><input type="checkbox" id="new_host_can_register" name="newhost[allowusersregister]"/></td>
							</tr>
							</tbody>
						</table>
			    	</div>
				</div>
				<div class="heading input_submit_container text-center">
					<input type="submit" class="btn btn-primary btn-sm" name="intertikiserver" value="{tr}Save{/tr}"/>
				</div>
				</br>
			</form>
		{/tab}
	{/if}
{/tabset}

