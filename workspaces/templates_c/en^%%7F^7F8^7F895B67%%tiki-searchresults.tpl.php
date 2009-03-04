<?php /* Smarty version 2.6.22, created on 2009-03-04 13:06:21
         compiled from tiki-searchresults.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'popup_init', 'tiki-searchresults.tpl', 2, false),array('function', 'button', 'tiki-searchresults.tpl', 25, false),array('block', 'title', 'tiki-searchresults.tpl', 4, false),array('block', 'add_help', 'tiki-searchresults.tpl', 70, false),array('block', 'pagination_links', 'tiki-searchresults.tpl', 178, false),array('modifier', 'escape', 'tiki-searchresults.tpl', 119, false),array('modifier', 'strip_tags', 'tiki-searchresults.tpl', 153, false),array('modifier', 'tiki_long_datetime', 'tiki-searchresults.tpl', 171, false),)), $this); ?>

<?php echo smarty_function_popup_init(array('src' => "lib/overlib.js"), $this);?>

<?php if (! ( $this->_tpl_vars['searchNoResults'] )): ?>
  <?php $this->_tag_stack[] = array('title', array('admpage' => 'search')); $_block_repeat=true;smarty_block_title($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Search results<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_title($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>


<?php ob_start(); ?>
		<ul><li>+ : A leading plus sign indicates that this word must be present in every object returned.</li>
		<li>- : A leading minus sign indicates that this word must not be present in any row returned.</li>
    	<li>By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.</li>
		<li>< > : These two operators are used to change a word's contribution to the relevance value that is assigned to a row.</li>
		<li>( ) : Parentheses are used to group words into subexpressions.</li>
		<li>~ : A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.</li>
		<li>* : An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.</li>
		<li>&quot; : The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed.</li></ul>
<?php $this->_smarty_vars['capture']['advanced_search_help'] = ob_get_contents(); ob_end_clean(); ?>


	<div class="nohighlight">
		<?php if (! ( $this->_tpl_vars['searchStyle'] == 'menu' )): ?> 
        	<?php if ($this->_tpl_vars['prefs']['feature_search_show_object_filter'] == 'y'): ?>
			<div class="navbar">
                Search in:<br />
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=pages",'_text' => 'Entire Site','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='pages'"), $this);?>

                <?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=calendars",'_text' => 'Calendars','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='calendars'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_wiki'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=wikis",'_text' => 'Wiki Pages','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='wikis'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_galleries'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=galleries",'_text' => 'Galleries','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='galleries'"), $this);?>

									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=images",'_text' => 'Images','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='images'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=files",'_text' => 'Files','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='files'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_forums'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=forums",'_text' => 'Forums','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='forums'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_faqs'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=faqs",'_text' => 'Faqs','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='faqs'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=blogs",'_text' => 'Blogs','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='blogs'"), $this);?>

									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=posts",'_text' => 'Blogs Post','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='posts'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_directory'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=directory",'_text' => 'Directory','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='directory'"), $this);?>

                <?php endif; ?>
                
                <?php if ($this->_tpl_vars['prefs']['feature_articles'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=articles",'_text' => 'Articles','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='articles'"), $this);?>

                <?php endif; ?>
                <?php if ($this->_tpl_vars['prefs']['feature_trackers'] == 'y'): ?>
									<?php echo smarty_function_button(array('_auto_args' => 'where,highlight,date','href' => "?where=trackers",'_text' => 'Trackers','_selected_class' => 'highlight','_selected' => "'".($this->_tpl_vars['where'])."'=='trackers'"), $this);?>

                <?php endif; ?>
			</div><!-- navbar -->
		<?php endif; ?>
      <?php endif; ?> 



<?php if ($this->_tpl_vars['prefs']['feature_search_show_search_box'] == 'y'): ?>
<form class="forms" method="get" action="tiki-searchresults.php">
    Find <input id="fuser" name="highlight" size="14" type="text" accesskey="s" value="<?php echo $this->_tpl_vars['words']; ?>
"/>
		<?php if (! ( $this->_tpl_vars['searchStyle'] == 'menu' )): ?> 
		<label for="boolean">Advanced search:<input type="checkbox" name="boolean"<?php if ($this->_tpl_vars['boolean'] == 'y'): ?> checked="checked"<?php endif; ?> /></label>
		<?php $this->_tag_stack[] = array('add_help', array('show' => 'y','title' => 'Advanced Search Help','id' => 'advanced_search_help')); $_block_repeat=true;smarty_block_add_help($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php echo $this->_smarty_vars['capture']['advanced_search_help']; ?>

		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_add_help($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<label for="date">Date Search:
		<select name="date" onchange="javascript:submit()">
		<?php unset($this->_sections['date']);
$this->_sections['date']['name'] = 'date';
$this->_sections['date']['start'] = (int)0;
$this->_sections['date']['loop'] = is_array($_loop=12) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['date']['step'] = ((int)1) == 0 ? 1 : (int)1;
$this->_sections['date']['show'] = true;
$this->_sections['date']['max'] = $this->_sections['date']['loop'];
if ($this->_sections['date']['start'] < 0)
    $this->_sections['date']['start'] = max($this->_sections['date']['step'] > 0 ? 0 : -1, $this->_sections['date']['loop'] + $this->_sections['date']['start']);
else
    $this->_sections['date']['start'] = min($this->_sections['date']['start'], $this->_sections['date']['step'] > 0 ? $this->_sections['date']['loop'] : $this->_sections['date']['loop']-1);
if ($this->_sections['date']['show']) {
    $this->_sections['date']['total'] = min(ceil(($this->_sections['date']['step'] > 0 ? $this->_sections['date']['loop'] - $this->_sections['date']['start'] : $this->_sections['date']['start']+1)/abs($this->_sections['date']['step'])), $this->_sections['date']['max']);
    if ($this->_sections['date']['total'] == 0)
        $this->_sections['date']['show'] = false;
} else
    $this->_sections['date']['total'] = 0;
if ($this->_sections['date']['show']):

            for ($this->_sections['date']['index'] = $this->_sections['date']['start'], $this->_sections['date']['iteration'] = 1;
                 $this->_sections['date']['iteration'] <= $this->_sections['date']['total'];
                 $this->_sections['date']['index'] += $this->_sections['date']['step'], $this->_sections['date']['iteration']++):
$this->_sections['date']['rownum'] = $this->_sections['date']['iteration'];
$this->_sections['date']['index_prev'] = $this->_sections['date']['index'] - $this->_sections['date']['step'];
$this->_sections['date']['index_next'] = $this->_sections['date']['index'] + $this->_sections['date']['step'];
$this->_sections['date']['first']      = ($this->_sections['date']['iteration'] == 1);
$this->_sections['date']['last']       = ($this->_sections['date']['iteration'] == $this->_sections['date']['total']);
?>	
		<option value="<?php echo $this->_sections['date']['index']; ?>
" <?php if ($this->_sections['date']['index'] == $this->_tpl_vars['date']): ?>selected="selected"<?php endif; ?>><?php if ($this->_sections['date']['index'] == 0): ?>All dates<?php else: ?><?php echo $this->_sections['date']['index']; ?>
 Month<?php endif; ?></option>
		<?php endfor; endif; ?>
		</select>
		<?php endif; ?>

<?php if ($this->_tpl_vars['prefs']['feature_search_show_object_filter'] == 'y'): ?>
<?php if (( $this->_tpl_vars['searchStyle'] == 'menu' )): ?>
<span class='searchMenu'>
    in
    <select name="where">
    <option value="pages">Entire Site</option>
    <?php if ($this->_tpl_vars['prefs']['feature_wiki'] == 'y'): ?>
       <option value="wikis">Wiki Pages</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_calendar'] == 'y'): ?>
       <option value="calendars">Calendar Items</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_galleries'] == 'y'): ?>
       <option value="galleries">Galleries</option>
       <option value="images">Images</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_file_galleries'] == 'y'): ?>
       <option value="files">Files</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_forums'] == 'y'): ?>
       <option value="forums">Forums</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_faqs'] == 'y'): ?>
       <option value="faqs">FAQs</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_blogs'] == 'y'): ?>
       <option value="blogs">Blogs</option>
       <option value="posts">Blog Posts</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_directory'] == 'y'): ?>
       <option value="directory">Directory</option>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['prefs']['feature_articles'] == 'y'): ?>
       <option value="articles">Articles</option>
    <?php endif; ?>
    </select>
   </span> 
<?php else: ?>
    <input type="hidden" name="where" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['where'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
	<?php if ($this->_tpl_vars['forumId']): ?><input type="hidden" name="forumId" value="<?php echo $this->_tpl_vars['forumId']; ?>
" /><?php endif; ?>
<?php endif; ?>
<?php endif; ?>
    <input type="submit" class="wikiaction" name="search" value="Go"/>
</form>
<?php endif; ?>

</div><!--nohighlight-->





<?php if ($this->_tpl_vars['searchStyle'] != 'menu' && ! $this->_tpl_vars['searchNoResults']): ?>
	<div class="highlight simplebox">
		 Found "<?php echo $this->_tpl_vars['words']; ?>
" in <?php if ($this->_tpl_vars['where3']): ?><?php echo $this->_tpl_vars['where2']; ?>
: <?php echo $this->_tpl_vars['where3']; ?>
<?php else: ?><?php echo $this->_tpl_vars['cant_results']; ?>
 <?php echo $this->_tpl_vars['where2']; ?>
<?php endif; ?>
	</div>
<?php endif; ?>

<?php if (! $this->_tpl_vars['searchNoResults']): ?>
	<div class="searchresults">
	<br /><br />
	<?php unset($this->_sections['search']);
$this->_sections['search']['name'] = 'search';
$this->_sections['search']['loop'] = is_array($_loop=$this->_tpl_vars['results']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['search']['show'] = true;
$this->_sections['search']['max'] = $this->_sections['search']['loop'];
$this->_sections['search']['step'] = 1;
$this->_sections['search']['start'] = $this->_sections['search']['step'] > 0 ? 0 : $this->_sections['search']['loop']-1;
if ($this->_sections['search']['show']) {
    $this->_sections['search']['total'] = $this->_sections['search']['loop'];
    if ($this->_sections['search']['total'] == 0)
        $this->_sections['search']['show'] = false;
} else
    $this->_sections['search']['total'] = 0;
if ($this->_sections['search']['show']):

            for ($this->_sections['search']['index'] = $this->_sections['search']['start'], $this->_sections['search']['iteration'] = 1;
                 $this->_sections['search']['iteration'] <= $this->_sections['search']['total'];
                 $this->_sections['search']['index'] += $this->_sections['search']['step'], $this->_sections['search']['iteration']++):
$this->_sections['search']['rownum'] = $this->_sections['search']['iteration'];
$this->_sections['search']['index_prev'] = $this->_sections['search']['index'] - $this->_sections['search']['step'];
$this->_sections['search']['index_next'] = $this->_sections['search']['index'] + $this->_sections['search']['step'];
$this->_sections['search']['first']      = ($this->_sections['search']['iteration'] == 1);
$this->_sections['search']['last']       = ($this->_sections['search']['iteration'] == $this->_sections['search']['total']);
?>
		<?php echo ''; ?><?php if ($this->_tpl_vars['prefs']['feature_search_show_object_type'] == 'y'): ?><?php echo ''; ?><?php if ($this->_tpl_vars['results'][$this->_sections['search']['index']]['type'] > ''): ?><?php echo '<b>'; ?><?php echo $this->_tpl_vars['results'][$this->_sections['search']['index']]['type']; ?><?php echo '::</b>'; ?><?php endif; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if (! empty ( $this->_tpl_vars['results'][$this->_sections['search']['index']]['parentName'] )): ?><?php echo '::<a href="'; ?><?php echo $this->_tpl_vars['results'][$this->_sections['search']['index']]['parentHref']; ?><?php echo '">'; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['results'][$this->_sections['search']['index']]['parentName'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?><?php echo '</a>&nbsp;-&gt;'; ?><?php endif; ?><?php echo ''; ?>

		<a href="<?php echo $this->_tpl_vars['results'][$this->_sections['search']['index']]['href']; ?>
&amp;highlight=<?php echo $this->_tpl_vars['words']; ?>
" class="wiki"><?php echo ((is_array($_tmp=$this->_tpl_vars['results'][$this->_sections['search']['index']]['pageName'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</a>
		<?php if ($this->_tpl_vars['prefs']['feature_search_show_visit_count'] == 'y'): ?>
			<b>(Hits: <?php echo $this->_tpl_vars['results'][$this->_sections['search']['index']]['hits']; ?>
)</b>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['prefs']['feature_search_show_pertinence'] == 'y'): ?>
    	<?php if ($this->_tpl_vars['prefs']['feature_search_fulltext'] == 'y'): ?>
				<?php if ($this->_tpl_vars['results'][$this->_sections['search']['index']]['relevance'] <= 0): ?>
					&nbsp;(Simple search)
        <?php else: ?>
					&nbsp;(Relevance: <?php echo $this->_tpl_vars['results'][$this->_sections['search']['index']]['relevance']; ?>
)
        <?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>    
		<br />
		<div class="searchdesc"><?php echo ((is_array($_tmp=$this->_tpl_vars['results'][$this->_sections['search']['index']]['data'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</div>

		<?php if ($this->_tpl_vars['prefs']['feature_search_show_last_modification'] == 'y'): ?>
			<div class="searchdate">Last modification date: <?php echo ((is_array($_tmp=$this->_tpl_vars['results'][$this->_sections['search']['index']]['lastModif'])) ? $this->_run_mod_handler('tiki_long_datetime', true, $_tmp) : smarty_modifier_tiki_long_datetime($_tmp)); ?>
</div>
		<?php endif; ?>
		<br/>
	<?php endfor; else: ?>
		No pages matched the search criteria
	<?php endif; ?>
</div>
<?php $this->_tag_stack[] = array('pagination_links', array('cant' => $this->_tpl_vars['cant'],'step' => $this->_tpl_vars['maxRecords'],'offset' => $this->_tpl_vars['offset'])); $_block_repeat=true;smarty_block_pagination_links($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_pagination_links($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>