{* $Id$ *}
<div id="siteloginbar_popup">
	<ul class="clearfix cssmenu_horiz">
		{if $prefs.allowRegister eq 'y' and !($user)}
			<li class="login_link tabmark" id="register_link"><a href="tiki-register.php" class="register_link" title="{tr}Click here to register{/tr}"><span>{tr}Register{/tr}</span></a>
			</li>
		{/if}
		{if $filegals_manager eq '' and $print_page ne 'y'}
			{if $prefs.feature_site_login eq 'y'}
				{if !empty($user)}
					<li class="login_link tabmark" id="logout_link"><a href="tiki-logout.php" class="login_link">{tr}Log out{/tr}</a>
						<ul>
							<li>
								<div class="tabcontent">{$user|userlink} | <a href="tiki-logout.php" title="{tr}Log out{/tr}">{tr}Log out{/tr}</a></div>
							</li>
						</ul>
				{else}
					<li class="login_link tabmark" id="login_link"><a href="tiki-login.php" class="login_link"><span>{tr}Log in{/tr}</span></a>
				{/if}
						<ul>
							<li>
								<div class="tabcontent">{include file='tiki-site_header_login.tpl'}</div>
							</li>
						</ul>
					</li>
			{/if}
		{/if}
	</ul>
</div>