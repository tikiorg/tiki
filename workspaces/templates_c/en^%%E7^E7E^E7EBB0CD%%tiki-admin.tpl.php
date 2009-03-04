<?php /* Smarty version 2.6.22, created on 2009-03-04 13:56:35
         compiled from tiki-admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'popup_init', 'tiki-admin.tpl', 2, false),array('function', 'breadcrumbs', 'tiki-admin.tpl', 6, false),array('function', 'cycle', 'tiki-admin.tpl', 31, false),array('function', 'icon', 'tiki-admin.tpl', 38, false),array('block', 'tr', 'tiki-admin.tpl', 26, false),array('block', 'remarksbox', 'tiki-admin.tpl', 30, false),array('modifier', 'stringfix', 'tiki-admin.tpl', 44, false),)), $this); ?>
sec
<?php echo smarty_function_popup_init(array('src' => "lib/overlib.js"), $this);?>

<div id="pageheader">

<?php if ($this->_tpl_vars['prefs']['feature_breadcrumbs'] == 'y'): ?>
    <?php echo smarty_function_breadcrumbs(array('type' => 'trail','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>

    <?php echo smarty_function_breadcrumbs(array('type' => 'pagetitle','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>

<?php endif; ?>

    <h1 class="center pagetitle"><?php echo smarty_function_breadcrumbs(array('type' => 'pagetitle','loc' => 'page','crumbs' => $this->_tpl_vars['crumbs']), $this);?>
</h1>

<?php echo smarty_function_breadcrumbs(array('type' => 'desc','loc' => 'page','crumbs' => $this->_tpl_vars['trail']), $this);?>


</div>

<?php if (in_array ( $this->_tpl_vars['adminpage'] , array ( 'features' , 'general' , 'login' , 'wiki' , 'gal' , 'fgal' , 'cms' , 'polls' , 'search' , 'blogs' , 'forums' , 'faqs' , 'trackers' , 'webmail' , 'rss' , 'directory' , 'userfiles' , 'maps' , 'metatags' , 'wikiatt' , 'score' , 'community' , 'messages' , 'calendar' , 'intertiki' , 'freetags' , 'gmap' , 'i18n' , 'wysiwyg' , 'copyright' , 'category' , 'module' , 'look' , 'textarea' , 'multimedia' , 'ads' , 'profiles' , 'semantic' , 'plugins' , 'webservices' , 'sefurl' , 'workspaces' ) )): ?>
  <?php $this->assign('include', $_GET['page']); ?>
<?php else: ?>
  <?php $this->assign('include', "list-sections"); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['include'] != "list-sections"): ?>
  <div class="simplebox adminanchors" ><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-admin-include-anchors.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
<?php endif; ?>

<?php if ($this->_tpl_vars['feature_version_checks'] == 'y' && $this->_tpl_vars['prefs']['tiki_needs_upgrade'] == 'y'): ?>
<div class="simplebox highlight"><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>A new version of Tikiwiki, <b><?php echo $this->_tpl_vars['tiki_release']; ?>
</b>, is available. You are currently running <b><?php echo $this->_tpl_vars['tiki_version']; ?>
</b>. Please visit <a href="http://tikiwiki.org/Download">http://tikiwiki.org/Download</a>.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
<?php endif; ?>

<?php if ($this->_tpl_vars['tikifeedback']): ?>
	<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'note','title' => 'Note')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php echo smarty_function_cycle(array('values' => "odd,even",'print' => false), $this);?>

		The following list of changes has been applied:
		<ul>
		<?php unset($this->_sections['n']);
$this->_sections['n']['name'] = 'n';
$this->_sections['n']['loop'] = is_array($_loop=$this->_tpl_vars['tikifeedback']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['n']['show'] = true;
$this->_sections['n']['max'] = $this->_sections['n']['loop'];
$this->_sections['n']['step'] = 1;
$this->_sections['n']['start'] = $this->_sections['n']['step'] > 0 ? 0 : $this->_sections['n']['loop']-1;
if ($this->_sections['n']['show']) {
    $this->_sections['n']['total'] = $this->_sections['n']['loop'];
    if ($this->_sections['n']['total'] == 0)
        $this->_sections['n']['show'] = false;
} else
    $this->_sections['n']['total'] = 0;
if ($this->_sections['n']['show']):

            for ($this->_sections['n']['index'] = $this->_sections['n']['start'], $this->_sections['n']['iteration'] = 1;
                 $this->_sections['n']['iteration'] <= $this->_sections['n']['total'];
                 $this->_sections['n']['index'] += $this->_sections['n']['step'], $this->_sections['n']['iteration']++):
$this->_sections['n']['rownum'] = $this->_sections['n']['iteration'];
$this->_sections['n']['index_prev'] = $this->_sections['n']['index'] - $this->_sections['n']['step'];
$this->_sections['n']['index_next'] = $this->_sections['n']['index'] + $this->_sections['n']['step'];
$this->_sections['n']['first']      = ($this->_sections['n']['iteration'] == 1);
$this->_sections['n']['last']       = ($this->_sections['n']['iteration'] == $this->_sections['n']['total']);
?>
			<li class="<?php echo smarty_function_cycle(array(), $this);?>
">
				<p>
			<?php if ($this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['st'] == 0): ?>
				<?php echo smarty_function_icon(array('_id' => 'delete','alt' => 'disabled','style' => "vertical-align: middle"), $this);?>

			<?php elseif ($this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['st'] == 1): ?>
				<?php echo smarty_function_icon(array('_id' => 'accept','alt' => 'enabled','style' => "vertical-align: middle"), $this);?>

			<?php else: ?>
				<?php echo smarty_function_icon(array('_id' => 'accept','alt' => 'changed','style' => "vertical-align: middle"), $this);?>

			<?php endif; ?>
					preference <strong><?php $this->_tag_stack[] = array('tr', array()); $_block_repeat=true;smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo ((is_array($_tmp=$this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['mes'])) ? $this->_run_mod_handler('stringfix', true, $_tmp) : smarty_modifier_stringfix($_tmp)); ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_tr($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></strong><br />
					(<em>preference name:</em> <?php echo $this->_tpl_vars['tikifeedback'][$this->_sections['n']['index']]['name']; ?>
)
				</p>
			</li>
		<?php endfor; endif; ?>
		</ul>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "tiki-admin-include-".($this->_tpl_vars['include']).".tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br style="clear:both" />
<?php $this->_tag_stack[] = array('remarksbox', array('type' => 'tip','title' => 'Crosslinks to other features and settings')); $_block_repeat=true;smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>

	Other sections:<br />
	<?php if ($this->_tpl_vars['prefs']['feature_sheet'] == 'y'): ?> <a href="tiki-sheets.php">Spreadsheet</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_newsletters'] == 'y'): ?> <a href="tiki-admin_newsletters.php">Newsletters</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_surveys'] == 'y'): ?> <a href="tiki-admin_surveys.php">Surveys</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_quizzes'] == 'y'): ?> <a href="tiki-edit_quiz.php">Quizzes</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_integrator'] == 'y'): ?> <a href="tiki-admin_integrator.php">Integrator</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_html_pages'] == 'y'): ?> <a href="tiki-admin_html_pages.php">HTML pages</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_shoutbox'] == 'y'): ?> 
		<a href="tiki-shoutbox.php">Shoutbox</a>
		<a href="tiki-admin_shoutbox_words.php">Shoutbox Words</a> 
	<?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_live_support'] == 'y'): ?> <a href="tiki-live_support_admin.php">Live Support</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_charts'] == 'y'): ?> <a href="tiki-admin_charts.php">Charts</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['feature_eph'] == 'y'): ?> <a href="tiki-eph_admin.php">Ephemerides</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_workflow'] == 'y'): ?> <a href="tiki-g-admin_processes.php">Workflow</a> <?php endif; ?>
	
	<?php if ($this->_tpl_vars['prefs']['feature_games'] == 'y'): ?> <a href="tiki-list_games.php">Games</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_contact'] == 'y'): ?> <a href="tiki-contact.php">Contact us</a> <?php endif; ?>
	<hr />

	Administration features:<br />
	<a href="tiki-adminusers.php">Users</a> 
	<a href="tiki-admingroups.php">Groups</a> 
	<a href="tiki-admin_security.php">Security</a> 
	<a href="tiki-admin_system.php">System</a> 
	<a href="tiki-syslog.php">SysLogs</a> 
	<a href="tiki-phpinfo.php">phpinfo</a> 
	<a href="tiki-mods.php">Mods</a>
	<?php if ($this->_tpl_vars['prefs']['feature_banning'] == 'y'): ?><a href="tiki-admin_banning.php">Banning</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['lang_use_db'] == 'y'): ?><a href="tiki-edit_languages.php">Edit Languages</a> <?php endif; ?>
	<hr />

	Transversal features (which apply to more than one section):<br />
	<a href="tiki-admin_notifications.php">Mail Notifications</a> 
	<hr />

	Navigation features:<br />
	<a href="tiki-admin_menus.php">Menus</a> 
	<a href="tiki-admin_modules.php">Modules</a>
	<?php if ($this->_tpl_vars['prefs']['feature_categories'] == 'y'): ?> <a href="tiki-admin_categories.php">Categories</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_featuredLinks'] == 'y'): ?><a href="tiki-admin_links.php">Links</a><?php endif; ?>
	<hr />

	Look &amp; feel (themes):<br />
	<?php if ($this->_tpl_vars['prefs']['feature_theme_control'] == 'y'): ?> <a href="tiki-theme_control.php">Theme Control</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_edit_templates'] == 'y'): ?> <a href="tiki-edit_templates.php">Edit Templates</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_editcss'] == 'y'): ?> <a href="tiki-edit_css.php">Edit CSS</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_mobile'] == 'y'): ?> <a href="tiki-mobile.php">Mobile</a> <?php endif; ?>
	<hr />

	Text area features (features you can use in all text areas, like wiki pages, blogs, articles, forums, etc):<br />
	<a href="tiki-admin_cookies.php">Cookies</a> 
	<?php if ($this->_tpl_vars['prefs']['feature_hotwords'] == 'y'): ?> <a href="tiki-admin_hotwords.php">Hotwords</a> <?php endif; ?>
	<a href="tiki-list_cache.php">Cache</a> 
	<a href="tiki-admin_quicktags.php">QuickTags</a> 
	<a href="tiki-admin_content_templates.php">Content Templates</a> 
	<a href="tiki-admin_dsn.php">DSN</a> 
	<?php if ($this->_tpl_vars['prefs']['feature_drawings'] == 'y'): ?><a href="tiki-admin_drawings.php">Drawings</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_dynamic_content'] == 'y'): ?><a href="tiki-list_contents.php">Dynamic Content</a> <?php endif; ?>
	<a href="tiki-admin_external_wikis.php">External Wikis</a> 
	<?php if ($this->_tpl_vars['prefs']['feature_mailin'] == 'y'): ?><a href="tiki-admin_mailin.php">Mail-in</a> <?php endif; ?>
	<hr />

	Stats &amp; banners:<br />
	<?php if ($this->_tpl_vars['prefs']['feature_stats'] == 'y'): ?> <a href="tiki-stats.php">Stats</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_referer_stats'] == 'y'): ?> <a href="tiki-referer_stats.php">Referer Stats</a> <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_search'] == 'y' && $this->_tpl_vars['prefs']['feature_search_stats'] == 'y'): ?> <a href="tiki-search_stats.php">Search Stats</a>  <?php endif; ?>
	<?php if ($this->_tpl_vars['prefs']['feature_banners'] == 'y'): ?> <a href="tiki-list_banners.php">Banners</a> <?php endif; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_remarksbox($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>