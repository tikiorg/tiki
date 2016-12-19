{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quickadmin" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $prefs.javascript_enabled != 'y'}
	{$js = 'n'}
{else}
	{$js = 'y'}
{/if}
	{if $tiki_p_admin == "y"}
		<div id="quickadmin" class="btn-group">
			<div class="btn-group">
				{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
					{icon name="history"}
				</a>
				<ul class="dropdown-menu" role="menu">
					<li class="dropdown-title">
						{tr}Recent Preferences{/tr}
					</li>
					<li class="divider"></li>
					{foreach $recent_prefs as $p}
						<li>
							<a href="tiki-admin.php?lm_criteria={$p|escape}&amp;exact">{$p|stringfix}</a>
						</li>
						{foreachelse}
						<li>{tr}None{/tr}</li>
					{/foreach}
				</ul>
				{if $js == 'n'}</li></ul>{/if}
			</div>
			<div class="btn-group">
				{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
					{icon name='menu-extra'}
				</a>
				<ul class="dropdown-menu">
					<li class="dropdown-title">
						{tr}Quick Administration{/tr}
					</li>
					<li class="divider"></li>
					<li>
						<a href="tiki-wizard_admin.php?stepNr=0&amp;url=index.php">
							{icon name="wizard"} {tr}Wizards{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-admin.php">
							{icon name="cog"} {tr}Control panels{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-admin.php?page=look">
							{icon name="image"} {tr}Themes{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-adminusers.php">
							{icon name="user"} {tr}Users{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-admingroups.php">
							{icon name="group"} {tr}Groups{/tr}
						</a>
					</li>
					<li>
						{permission_link mode=text}
					</li>
					<li>
						<a href="tiki-admin_menus.php">
							{icon name="menu"} {tr}Menus{/tr}
						</a>
					</li>
						{if $prefs.lang_use_db eq "y"}
							<li>
								{if isset($smarty.session.interactive_translation_mode) && $smarty.session.interactive_translation_mode eq "on"}
									<a href="tiki-interactive_trans.php?interactive_translation_mode=off">
										{icon name="translate"} {tr}Turn off interactive translation{/tr}
									</a>
								{else}
									<a href="tiki-interactive_trans.php?interactive_translation_mode=on">
										{icon name="translate"} {tr}Turn on interactive translation{/tr}
									</a>
								{/if}
							</li>
						{/if}
					{if $prefs.feature_comments_moderation eq "y"}
						<li>
							<a href="tiki-list_comments.php">
								{icon name="comments"} {tr}Comment moderation{/tr}
							</a>
						</li>
					{/if}
					<li>
						<a href="tiki-admin_system.php?do=all">
							{icon name="trash"} {tr}Clear all caches{/tr}
						</a>
					</li>
					<li>
						<a href="{bootstrap_modal controller=search action=rebuild}">
							{icon name="index"} {tr}Rebuild search index{/tr}
						</a>
					</li>
					<li>
						<a  href="tiki-plugins.php">
							{icon name="plugin"} {tr}Plugin approval{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-syslog.php">
							{icon name="log"} {tr}Logs{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-admin_modules.php">
							{icon name="module"} {tr}Modules{/tr}
						</a>
					</li>
					{if $prefs.feature_debug_console}
						<li>
							<a href="{query _type='relative' show_smarty_debug=1}">
								{icon name="bug"} {tr}Smarty debug window{/tr}
							</a>
						</li>
					{/if}
					{if $prefs.feature_jcapture eq "y"}
						<li>
							<a href="#" onclick="openJCaptureDialog('none', '{$page|default:null}', event);return false;">
								{icon name="screencapture"} {tr}Screen capture{/tr}
							</a>
						</li>
					{/if}
				</ul>
				{if $js == 'n'}</li></ul>{/if}
			</div>
		</div>
	{/if}
{/tikimodule}
