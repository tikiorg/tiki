<?php /* Smarty version 2.6.22, created on 2009-03-04 13:13:14
         compiled from tiki-admin-include-profiles.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'remarksbox', 'tiki-admin-include-profiles.tpl', 195, false),array('modifier', 'escape', 'tiki-admin-include-profiles.tpl', 227, false),)), $this); ?>

<script type="text/javascript">
var baseURI = '<?php echo $_SERVER['REQUEST_URI']; ?>
';
<?php echo '
function refreshCache( entry ) { // {{{
	var status = document.getElementById( \'profile-status-\' + entry );
	var datespan = document.getElementById( \'profile-date-\' + entry );
	var pending = \'img/icons2/status_pending.gif\';

	if( status.src == pending )
		return;
	
	status.src = pending;

	var req = getHttpRequest( \'POST\', baseURI + \'&refresh=\' + escape(entry), true );
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200) {
				var data = eval( "(" + req.responseText + ")" );
				status.src = \'img/icons2/status_\' + data.status + \'.gif\';
				datespan.innerHTML = data.lastupdate;
			} else
				alert("Error loading page\\n");
		}
	};
	req.send(\'\');
} // }}}

function showDetails( id, domain, profile ) { // {{{
	
	var nid = id + "-sub";
	var prev = document.getElementById( id );
	var obj = document.getElementById( nid );

	if( obj )
	{
		obj.id = null;
		obj.parentNode.removeChild( obj );
		return;
	}

	var req = getHttpRequest( \'POST\', baseURI + \'&getinfo&pd=\' + escape(domain) + \'&pp=\' + escape(profile), true );
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200) {
				var data = eval( "(" + req.responseText + ")" );

				var row = document.createElement( \'tr\' );
				var cell = document.createElement( \'td\' );
				var body = document.createElement( \'div\' );
				var ul = document.createElement( \'ul\' );

				row.appendChild( cell );
				cell.colSpan = 3;

				if( data.already )
				{
					var p = document.createElement( \'p\' );
					p.innerHTML = "A version of this profile is already installed.";
					p.style.fontWeight = \'bold\';
					cell.appendChild(p);

					var form = document.createElement( \'form\' );
					var p = document.createElement(\'p\');
					var submit = document.createElement(\'input\');
					var pd = document.createElement(\'input\');
					var pp = document.createElement(\'input\');
					form.method = \'post\';
					form.action = document.location.href;

					form.appendChild(p);
					submit.type = \'submit\';
					submit.name = \'forget\';
					submit.value = \'Forget Past Installation\';
					p.appendChild(submit);
					pd.type = \'hidden\';
					pd.name = \'pd\';
					pd.value = domain;
					p.appendChild(pd);
					pp.type = \'hidden\';
					pp.name = \'pp\';
					pp.value = profile;
					p.appendChild(pp);

					cell.appendChild(form);
				}
				else if( data.installable )
				{
					var form = document.createElement( \'form\' );
					var p = document.createElement(\'p\');
					var submit = document.createElement(\'input\');
					var pd = document.createElement(\'input\');
					var pp = document.createElement(\'input\');
					form.method = \'post\';
					form.action = document.location.href;

					var iTable = document.createElement(\'table\');
					iTable.className = \'normal\';

					var rowNum = 0;
					for( i in data.userInput ) {
						if( typeof(data.userInput[i]) != \'string\' )
							continue;

						var iRow = iTable.insertRow( rowNum++ );
						var iLabel = iRow.insertCell( 0 );
						var iField = iRow.insertCell( 1 );

						iRow.className = \'formcolor\';

						iLabel.appendChild( document.createTextNode( i ) );
						var iInput = document.createElement( \'input\' );
						iInput.type = \'text\';
						iInput.name = i;
						iInput.value = data.userInput[i];

						iField.appendChild( iInput );
					}

					if( rowNum > 0 )
						form.appendChild( iTable );

					form.appendChild(p);

					submit.type = \'submit\';
					submit.name = \'install\';
					submit.value = \'Install Now\';
					p.appendChild(submit);
					pd.type = \'hidden\';
					pd.name = \'pd\';
					pd.value = domain;
					p.appendChild(pd);
					pp.type = \'hidden\';
					pp.name = \'pp\';
					pp.value = profile;
					p.appendChild(pp);

					cell.appendChild(form);
				}
				else if( data.error )
				{
					var p = document.createElement(\'p\');
					p.style.fontWeight = \'bold\';
					p.innerHTML = "An error occured during the profile validation. This profile cannot be installed. Message: " + data.error;
					cell.appendChild(p);
				}
				else
				{
					var p = document.createElement(\'p\');
					p.style.fontWeight = \'bold\';
					p.innerHTML = "An error occured during the profile validation. This profile cannot be installed.";
					cell.appendChild(p);
				}

				if( data.dependencies.length > 1 )
				{
					for( k in data.dependencies )
					{
						if( typeof(data.dependencies[k]) != \'string\')
							continue;

						var li = document.createElement( \'li\' );
						var a = document.createElement( \'a\' );
						a.href = data.dependencies[k];
						a.innerHTML = data.dependencies[k];

						li.appendChild( a );
						ul.appendChild( li );
					}

					var p = document.createElement( \'p\' );
					p.innerHTML = \'These profiles will be installed:\';
					cell.appendChild( p );
					cell.appendChild( ul );
				}

				body.innerHTML = data.content;
				body.style.height = \'200px\';
				body.style.overflow = \'auto\';
				body.style.borderStyle = \'solid\';
				body.style.borderWidth = \'2px\';
				body.style.borderColor = \'black\';

				cell.appendChild( body );

				row.id = nid;
				prev.parentNode.insertBefore( row, prev.nextSibling );
			}
		}
	}
	req.send(\'\');
} // }}}
'; ?>

</script>
<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'tip','title' => 'Tip')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><a class="rbox-link" href="http://profiles.tikiwiki.org">TikiWiki Profiles</a><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<div class="cbox">
	<div class="cbox-title">Profile repository status</div>
	<div class="cbox-data">
		<table class="normal">
			<tr>
				<th>Profile repository</th>
				<th>Status</th>
				<th>Last update</th>
			</tr>
			<?php $_from = $this->_tpl_vars['sources']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['entry']):
?>
				<tr>
					<td><?php echo $this->_tpl_vars['entry']['short']; ?>
</td>
					<td><img id="profile-status-<?php echo $this->_tpl_vars['k']; ?>
" src="img/icons2/status_<?php echo $this->_tpl_vars['entry']['status']; ?>
.gif"/></td>
					<td><span id="profile-date-<?php echo $this->_tpl_vars['k']; ?>
"><?php echo $this->_tpl_vars['entry']['formatted']; ?>
</span> <a href="javascript:refreshCache(<?php echo $this->_tpl_vars['k']; ?>
)" class="icon"><img src="pics/icons/arrow_refresh.png" class="icon" alt="Refresh"/></a></td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
</div>

<div class="cbox">
	<div class="cbox-title"><a name="profile-results">Profile list</a></div>
	<div class="cbox-data">
		<form method="get" action="tiki-admin.php#profile-results">
			<table class="admin"><tr>
				<col width="30%"/>
				<col width="70%"/>

				<tr>
					<td class="form">Profile:</td>
					<td><input type="text" name="profile" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['profile'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"/></td>
				</tr>
				<tr>
					<td class="form">Category:</td>
					<td><input type="text" name="category" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['category'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"/></td>
				</tr>
			
				<tr>
					<td class="form">Repository:</td>
					<td>
						<select name="repository">
							<option value="">All</option>
							<?php $_from = $this->_tpl_vars['sources']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['source']):
?>
								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['source']['url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php if ($this->_tpl_vars['repository'] == $this->_tpl_vars['source']['url']): ?> selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['source']['short'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</option>
							<?php endforeach; endif; unset($_from); ?>
						</select>
						<input type="hidden" name="page" value="profiles"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="input_submit_container"><input type="submit" name="list" value="List" /></td>
				</tr>
			</table>
		</form>
		<table class="normal">
			<tr>
				<th>Profile</th>
				<th>Repository</th>
				<th>Category</th>
			</tr>
			<?php $_from = $this->_tpl_vars['result']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['profile']):
?>
				<tr id="profile-<?php echo $this->_tpl_vars['k']; ?>
">
					<td><a href="javascript:showDetails( 'profile-<?php echo $this->_tpl_vars['k']; ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['profile']['domain'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
', '<?php echo ((is_array($_tmp=$this->_tpl_vars['profile']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
' )"><?php echo ((is_array($_tmp=$this->_tpl_vars['profile']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></td>
					<td><?php echo $this->_tpl_vars['profile']['domain']; ?>
</td>
					<td><?php echo $this->_tpl_vars['profile']['category']; ?>
</td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
</div>

<div class="cbox">
	<div class="cbox-title">Configuration</div>
	<div class="cbox-data">
		<form action="tiki-admin.php?page=profiles" method="post">
			<table class="admin"><tr>
				<col width="30%"/>
				<col width="70%"/>
		
				<tr>
					<td class="form">
						Profile repositories:
						<div>
							<small>Profiles can be installed from multiple repositories. Enter one repository URL per line.</small>
						</div>
					</td>
					<td>
						<textarea name="profile_sources" rows="5"><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['profile_sources'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
					</td>
				</tr>

				<tr>
					<td class="form">
						Data Channels:
						<div>
							<small>Data channels create a named pipe to run profiles from user space. One channel per line. Each line is comma delimited and contain <strong>channel name, domain, profile, allowed groups</strong>. </small>
						</div>
						<div>
							<small><a href="http://profiles.tikiwiki.org/Data+Channels">More information</a></small>
						</div>
					</td>
					<td>
						<textarea name="profile_channels" rows="5"><?php echo ((is_array($_tmp=$this->_tpl_vars['prefs']['profile_channels'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea>
					</td>
				</tr>
				<tr>		
					<td colspan="2" class="input_submit_container"><input type="submit" name="config" value="Save" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">
<?php $_from = $this->_tpl_vars['oldSources']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k']):
?>
	refreshCache(<?php echo $this->_tpl_vars['k']; ?>
);
<?php endforeach; endif; unset($_from); ?>
</script>