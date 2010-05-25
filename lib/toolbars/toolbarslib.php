<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/smarty_tiki/block.self_link.php');

$toolbarPickerIndex = -1;

abstract class Toolbar
{
	protected $wysiwyg;
	protected $icon;
	protected $label;
	protected $type;
	
	private $requiredPrefs = array();

	public static function getTag( $tagName ) // {{{
	{
		if( $tag = Toolbar::getCustomTool( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarInline::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarBlock::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarLineBased::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarFckOnly::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarWikiplugin::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarPicker::fromName( $tagName ) )
			return $tag;
		elseif( $tag = ToolbarDialog::fromName( $tagName ) )
			return $tag;
		elseif( $tagName == 'fullscreen' )
			return new ToolbarFullscreen;
		elseif( $tagName == 'tikiimage' )
			return new ToolbarFileGallery;
		elseif( $tagName == 'help' )
			return new ToolbarHelptool;
		elseif( $tagName == 'switcheditor' )
			return new ToolbarSwitchEditor;
		elseif( $tagName == '-' )
			return new ToolbarSeparator;
		elseif( $tag = ToolbarSheet::fromName( $tagName ) )
			return $tag;
	} // }}}

	public static function getList( $include_custom = true ) // {{{
	{
		global $tikilib;
		$plugins = $tikilib->plugin_get_list();
		
		foreach( $plugins as & $name ) {
			$name = "wikiplugin_$name";
		}
		
		if ($include_custom) {
			$custom = Toolbar::getCustomList();
			$plugins = array_merge($plugins, $custom);
		}
		
		return array_unique (array_merge( array(
			'-',
			'bold',
			'italic',
			'underline',
			'strike',
			'sub',
			'sup',
			'tikilink',
			'link',
			'color',
			'bgcolor',
			'center',
			'table',
			'rule',
			'pagebreak',
			'blockquote',
			'h1',
			'h2',
			'h3',
			'toc',
			'list',
			'numlist',
			'specialchar',
			'smiley',
			'templates',
			'cut',
			'copy',
			'paste',
			'pastetext',
			'pasteword',
			'print',
			'spellcheck',
			'undo',
			'redo',
			'find',
			'replace',
			'selectall',
			'removeformat',
			'showblocks',
			'left',
			'right',
			'full',
			'indent',
			'outdent',
			'unlink',
			'style',
			'fontname',
			'fontsize',
			'source',
			'fullscreen',
			'help',
			'tikiimage',
			'switcheditor',
			'autosave',
			'nonparsed',
		
			'sheetsave',	// spreadsheet ones
			'addrow',
			'addrowmulti',
			'deleterow',
			'addcolumn',
			'deletecolumn',
			'addcolumnmulti',
			'sheetgetrange',
			'sheetfind',
			'sheetrefresh',
			'sheetclose',
		), $plugins ));
	} // }}}
	
	public static function getCustomList()
	{

		global $prefs;
		if( isset($prefs['toolbar_custom_list']) ) {
			$custom = @unserialize($prefs['toolbar_custom_list']);
			sort($custom);
		} else {
			$custom = array();
		}

		return $custom;
	}
	
	public static function getCustomTool($name) {
		global $prefs;
		if( isset($prefs["toolbar_tool_$name"]) ) {
			$data = unserialize($prefs["toolbar_tool_$name"]);
			$tag = Toolbar::fromData( $name, $data );
			return $tag;
		} else {
			return null;
		}
	
	}

	public static function isCustomTool($name) {
		global $prefs;
		return isset($prefs["toolbar_tool_$name"]);	
	}

	public static function saveTool($name, $label, $icon = 'pics/icons/shading.png', $token = '', $syntax = '', $type = 'Inline', $plugin = '') {
		global $prefs, $tikilib;
		
		$name = strtolower( preg_replace('/[\s,\/\|]+/', '_', $tikilib->take_away_accent( $name )) );
		$standard_names = Toolbar::getList(false);
		$custom_list = Toolbar::getCustomList();
		if (in_array($name, $standard_names)) {		// don't allow custom tools with the same name as standard ones
			$c = 1;
			while(in_array($name . '_' . $c, $custom_list)) {
				$c++;
			}
			$name = $name . '_' . $c;
		}

		$prefName = "toolbar_tool_$name";
		$data = array('name'=>$name, 'label'=>$label, 'icon'=>$icon, 'token'=>$token, 'syntax'=>$syntax, 'type'=>$type, 'plugin'=>$plugin);
		
		$tikilib->set_preference( $prefName, serialize( $data ) );
		
		if( !in_array( $name, $custom_list ) ) {
			$custom_list[] = $name;
			$tikilib->set_preference( 'toolbar_custom_list', serialize($custom_list) );
			$tikilib->set_lastUpdatePrefs();
		}
	}

	public static function deleteTool($name) {
		global $prefs, $tikilib;
		
		$name = strtolower( $name );

		$prefName = "toolbar_tool_$name";
		if( isset($prefs[$prefName]) ) {
			$tikilib->delete_preference( $prefName );
			
			$list = array();
			if( isset($prefs['toolbar_custom_list']) ) {
				$list = unserialize($prefs['toolbar_custom_list']);
			}
			if( in_array( $name, $list ) ) {
				$list = array_diff($list, array($name));
				$tikilib->set_preference( 'toolbar_custom_list', serialize($list) );
			}
	
		}
	}

	public static function deleteAllCustomTools() {
		global $tikilib;
		
		$tikilib->query('DELETE FROM `tiki_preferences` WHERE `name` LIKE \'toolbar_tool_%\'');
		$tikilib->query('DELETE FROM `tiki_preferences` WHERE `name` = \'toolbar_custom_list\'');
		
		//global $cachelib; require_once("lib/cache/cachelib.php");
		//$cachelib->invalidate('tiki_preferences_cache');
		$tikilib->set_lastUpdatePrefs();
	}
	

	public static function fromData( $tagName, $data ) { // {{{
		
		$tag = null;
		
		switch ($data['type']) {
			case 'Inline':
				$tag = new ToolbarInline();
				$tag->setSyntax( $data['syntax'] );
				break;
			case 'Block':
				$tag = new ToolbarBlock();
				$tag->setSyntax( $data['syntax'] );
				break;
			case 'LineBased':
				$tag = new ToolbarLineBased();
				$tag->setSyntax( $data['syntax'] );
				break;
			case 'Picker':
				$tag = new ToolbarPicker();
				break;
			case 'Separator':
				$tag = new ToolbarSeparator();
				break;
			case 'FckOnly':
				$tag = new ToolbarFckOnly();
				break;
			case 'Fullscreen':
				$tag = new ToolbarFullscreen();
				break;
			case 'TextareaResize':
				$tag = new ToolbarTextareaResize();
				break;
			case 'Helptool':
				$tag = new ToolbarHelptool();
				break;
			case 'FileGallery':
				$tag = new ToolbarFileGallery();
				break;
			case 'Wikiplugin':
				if (!isset($data['plugin'])) { $data['plugin'] = ''; }
				$tag = ToolbarWikiplugin::fromName('wikiplugin_' . $data['plugin']);
				if (empty($tag)) { $tag = new ToolbarWikiplugin(); }
				break;
			default:
				$tag = new ToolbarInline();
				break;
		}

		$tag->setLabel( $data['label'] )
			->setWysiwygToken( $data['token'] )
				->setIcon( !empty($data['icon']) ? $data['icon'] : 'pics/icons/shading.png' )
						->setType($data['type']);
		
		return $tag;
	}	// {{{

	abstract function getWikiHtml( $areaName );

	function isAccessible() // {{{
	{
		global $prefs;

		foreach( $this->requiredPrefs as $prefName )
			if( ! isset($prefs[$prefName]) || $prefs[$prefName] != 'y' )
				return false;

		return true;
	} // }}}

	protected function addRequiredPreference( $prefName ) // {{{
	{
		$this->requiredPrefs[] = $prefName;
	} // }}}

	protected function setIcon( $icon ) // {{{
	{
		$this->icon = $icon;

		return $this;
	} // }}}

	protected function setLabel( $label ) // {{{
	{
		$this->label = $label;

		return $this;
	} // }}}

	protected function setWysiwygToken( $token ) // {{{
	{
		$this->wysiwyg = $token;

		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		return $this;
	} // }}}

	protected function setType( $type ) // {{{
	{
		$this->type = $type;

		return $this;
	} // }}}

	function getIcon() // {{{
	{
		return $this->icon;
	} // }}}

	function getLabel() // {{{
	{
		return $this->label;
	} // }}}

	function getWysiwygToken() // {{{
	{
		return $this->wysiwyg;
	} // }}}
	
	function getSyntax() // {{{
	{
		return '';
	} // }}}
	
	function getType() // {{{
	{
		return $this->type;
	} // }}}
	
	function getIconHtml() // {{{
	{
		global $headerlib;
		return '<img src="' . htmlentities($headerlib->convert_cdn($this->icon), ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}
	
	function getSelfLink( $click, $title, $class ) { // {{{
		global $smarty;
		
		$params = array();
		$params['_onclick'] = $click . (substr($click, strlen($click)-1) != ';' ? ';' : '') . 'return false;';
		$params['_class'] = 'toolbar ' . (!empty($class) ? ' '.$class : '');
		$params['_ajax'] = 'n';
		$content = $title;
		$params['_icon'] = $this->icon;
			
		if (strpos($class, 'qt-plugin') !== false && $this->icon == 'pics/icons/plugin.png') {
			$params['_menu_text'] = 'y';
			$params['_menu_icon'] = 'y';
		}
		return smarty_block_self_link($params, $content, $smarty);
	} // }}}

}

class ToolbarSeparator extends Toolbar
{
	function __construct() // {{{
	{
		$this->setWysiwygToken('-')
			->setIcon('img/separator.gif')
				->setType('Separator');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return '|';
	} // }}}
}

class ToolbarFckOnly extends Toolbar
{
	function __construct( $token, $icon = '' ) // {{{
	{
		$fck_icon_path = 'lib/fckeditor_tiki/fckeditor-icons/';
		if (empty($icon)) {
			$img_path = 'lib/fckeditor_tiki/fckeditor-icons/' . $token . '.gif';
			if (is_file($img_path)) {
				$icon = $img_path;
			} else {
				$icon = 'pics/icons/shading.png';
			}
		}
		$this->setWysiwygToken( $token )
			->setIcon($icon)
				->setType('FckOnly');
	} // }}}
	
	public static function fromName( $name ) // {{{
	{
		switch( $name ) {
		case 'templates':
			return new self( 'Templates' );
		case 'cut':
			return new self( 'Cut' );
		case 'copy':
			return new self( 'Copy' );
		case 'paste':
			return new self( 'Paste' );
		case 'pastetext':
			return new self( 'PasteText' );
		case 'pasteword':
			return new self( 'PasteWord' );
		case 'print':
			return new self( 'Print' );
		case 'spellcheck':
			return new self( 'SpellCheck' );
		case 'undo':
			return new self( 'Undo' );
		case 'redo':
			return new self( 'Redo' );
		case 'selectall':
			return new self( 'SelectAll' );
		case 'removeformat':
			return new self( 'RemoveFormat' );
		case 'showblocks':
			return new self( 'ShowBlocks' );
		case 'left':
			return new self( 'JustifyLeft' );
		case 'right':
			return new self( 'JustifyRight' );
		case 'full':
			return new self( 'JustifyFull' );
		case 'indent':
			return new self( 'Indent' );
		case 'outdent':
			return new self( 'Outdent' );
		case 'unlink':
			return new self( 'Unlink' );
		case 'style':
			return new self( 'Style' );
		case 'fontname':
			return new self( 'FontName' );
		case 'fontsize':
			return new self( 'FontSize' );
		case 'source':
			return new self( 'Source' );
		case 'autosave':
			return new self( 'ajaxAutoSave', 'lib/fckeditor_tiki/plugins/ajaxAutoSave/images/ajaxAutoSaveDirty.gif' );
		case 'sub':
			return new self( 'Subscript' );
		case 'sup':
			return new self( 'Superscript' );
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return null;
	} // }}}
	
	function getLabel() // {{{
	{
		return $this->wysiwyg;
	} // }}}
}

class ToolbarInline extends Toolbar
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'bold':
			$label = tra('Bold');
			$icon = tra('pics/icons/text_bold.png');
			$wysiwyg = 'Bold';
			$syntax = '__text__';
			break;
		case 'italic':
			$label = tra('Italic');
			$icon = tra('pics/icons/text_italic.png');
			$wysiwyg = 'Italic';
			$syntax = "''text''";
			break;
		case 'underline':
			$label = tra('Underline');
			$icon = tra('pics/icons/text_underline.png');
			$wysiwyg = 'Underline';
			$syntax = "===text===";
			break;
		case 'strike':
			$label = tra('Strikethrough');
			$icon = tra('pics/icons/text_strikethrough.png');
			$wysiwyg = 'StrikeThrough';
			$syntax = '--text--';
			break;
		case 'nonparsed':
			$label = tra('Non-parsed (Wiki syntax does not apply)');
			$icon = tra('pics/icons/noparse.png');
			$wysiwyg = null;
			$syntax = '~np~text~/np~';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setSyntax( $syntax )
						->setType('Inline');
		
		return $tag;
	} // }}}

	function getSyntax() // {{{
	{
		return $this->syntax;
	} // }}}
	
	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		if ($this->syntax == '~np~text~/np~') {	// closing ~/np~ tag breaks toolbar when inside nested plugins
			return $this->getSelfLink('insertAt(\'' . $areaName . '\', \'~np~text~\'+\'/np~\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-inline');
		} else {
			return $this->getSelfLink('insertAt(\'' . $areaName . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-inline');
		}

	} // }}}
	
}

class ToolbarBlock extends ToolbarInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		global $prefs;
		switch( $tagName ) {
		case 'center':
			$label = tra('Align Center');
			$icon = tra('pics/icons/text_align_center.png');
			$wysiwyg = 'JustifyCenter';
			if ($prefs['feature_use_three_colon_centertag'] == 'y') {
				$syntax = ":::text:::";
			} else {
				$syntax = "::text::";
			}
			break;
		case 'rule':
			$label = tra('Horizontal Bar');
			$icon = tra('pics/icons/page.png');
			$wysiwyg = 'Rule';
			$syntax = '---';
			break;
		case 'pagebreak':
			$label = tra('Page Break');
			$icon = tra('lib/fckeditor_tiki/fckeditor-icons/Pagebreak.gif');
			$wysiwyg = 'PageBreak';
			$syntax = '...page...';
			break;
		case 'blockquote':
			$label = tra('Block Quote');
			$icon = tra('pics/icons/box.png');
			$wysiwyg = 'Blockquote';
			$syntax = '^text^';
			break;
		case 'h1':
		case 'h2':
		case 'h3':
			$label = tra('Heading') . ' ' . $tagName{1};
			$icon = 'pics/icons/text_heading_' . $tagName{1} . '.png';
			$wysiwyg = null;
			$syntax = str_repeat('!', $tagName{1}) . 'text';
			break;
		case 'toc':
			$label = tra('Table of contents');
			$icon = tra('pics/icons/book.png');
			$wysiwyg = 'TOC';
			$syntax = '{maketoc}';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setSyntax( $syntax )
						->setType('Block');
		
		return $tag;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return $this->getSelfLink('insertAt(\'' . $areaName . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\', true);',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-block');
	} // }}}
}

class ToolbarLineBased extends ToolbarInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'list':
			$label = tra('Unordered List');
			$icon = tra('pics/icons/text_list_bullets.png');
			$wysiwyg = 'UnorderedList';
			$syntax = '*text';
			break;
		case 'numlist':
			$label = tra('Ordered List');
			$icon = tra('pics/icons/text_list_numbers.png');
			$wysiwyg = 'OrderedList';
			$syntax = '#text';
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setSyntax( $syntax )
						->setType('LineBased');
		
		return $tag;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return $this->getSelfLink('insertAt(\'' . $areaName . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\', true, true);',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-line');
	} // }}}
}


class ToolbarPicker extends Toolbar
{
	private $list;
	private $index;
	private $name;
	
	public static function fromName( $tagName ) // {{{
	{
		global $headerlib;
		$prefs = array();

		switch( $tagName ) {
		case 'specialchar':
			$wysiwyg = 'SpecialChar';
			$label = tra('Special Characters');
			$icon = tra('lib/fckeditor_tiki/fckeditor-icons/Specialchar.gif');
			// Line taken from DokuWiki
            $list = explode(' ','À à Á á Â â Ã ã Ä ä Ǎ ǎ Ă ă Å å Ā ā Ą ą Æ æ Ć ć Ç ç Č č Ĉ ĉ Ċ ċ Ð đ ð Ď ď È è É é Ê ê Ë ë Ě ě Ē ē Ė ė Ę ę Ģ ģ Ĝ ĝ Ğ ğ Ġ ġ Ĥ ĥ Ì ì Í í Î î Ï ï Ǐ ǐ Ī ī İ ı Į į Ĵ ĵ Ķ ķ Ĺ ĺ Ļ ļ Ľ ľ Ł ł Ŀ ŀ Ń ń Ñ ñ Ņ ņ Ň ň Ò ò Ó ó Ô ô Õ õ Ö ö Ǒ ǒ Ō ō Ő ő Œ œ Ø ø Ŕ ŕ Ŗ ŗ Ř ř Ś ś Ş ş Š š Ŝ ŝ Ţ ţ Ť ť Ù ù Ú ú Û û Ü ü Ǔ ǔ Ŭ ŭ Ū ū Ů ů ǖ ǘ ǚ ǜ Ų ų Ű ű Ŵ ŵ Ý ý Ÿ ÿ Ŷ ŷ Ź ź Ž ž Ż ż Þ þ ß Ħ ħ ¿ ¡ ¢ £ ¤ ¥ € ¦ § ª ¬ ¯ ° ± ÷ ‰ ¼ ½ ¾ ¹ ² ³ µ ¶ † ‡ · • º ∀ ∂ ∃ Ə ə ∅ ∇ ∈ ∉ ∋ ∏ ∑ ‾ − ∗ √ ∝ ∞ ∠ ∧ ∨ ∩ ∪ ∫ ∴ ∼ ≅ ≈ ≠ ≡ ≤ ≥ ⊂ ⊃ ⊄ ⊆ ⊇ ⊕ ⊗ ⊥ ⋅ ◊ ℘ ℑ ℜ ℵ ♠ ♣ ♥ ♦ 𝛼 𝛽 𝛤 𝛾 𝛥 𝛿 𝜀 𝜁 𝛨 𝜂 𝛩 𝜃 𝜄 𝜅 𝛬 𝜆 𝜇 𝜈 𝛯 𝜉 𝛱 𝜋 𝛳 𝜍 𝛴 𝜎 𝜏 𝜐 𝛷 𝜑 𝜒 𝛹 𝜓 𝛺 𝜔 𝛻 𝜕 ★ ☆ ☎ ☚ ☛ ☜ ☝ ☞ ☟ ☹ ☺ ✔ ✘ × „ “ ” ‚ ‘ ’ « » ‹ › — – … ← ↑ → ↓ ↔ ⇐ ⇑ ⇒ ⇓ ⇔ © ™ ® ′ ″');
			$list = array_combine( $list, $list );
			break;
		case 'smiley':
			$wysiwyg = 'Smiley';
			$label = tra('Smileys');
			$icon = tra('img/smiles/icon_smile.gif');
			$rawList = array( 'biggrin', 'confused', 'cool', 'cry', 'eek', 'evil', 'exclaim', 'frown', 'idea', 'lol', 'mad', 'mrgreen', 'neutral', 'question', 'razz', 'redface', 'rolleyes', 'sad', 'smile', 'surprised', 'twisted', 'wink', 'arrow', 'santa' );
			$prefs[] = 'feature_smileys';

			$list = array();
			global $headerlib;
			foreach( $rawList as $smiley ) {
				$tra = htmlentities( tra($smiley), ENT_QUOTES, 'UTF-8' );
				$list["(:$smiley:)"] = '<img src="' . $headerlib->convert_cdn('img/smiles/icon_' .$smiley . '.gif') . '" alt="' . $tra . '" title="' . $tra . '" border="0" width="15" height="15" />';
			}
			break;
		case 'color':
			$wysiwyg = 'TextColor';
			$label = tra('Foreground color');
			$icon = tra('pics/icons/palette.png');
			$rawList = array();
			
			$hex = array('0', '3', '6', '9', 'C', 'F');
			$count_hex = count($hex);

			for ($r = 0; $r < $count_hex; $r++){ // red
				for ($g = 0; $g < $count_hex; $g++){ // green
					for ($b = 0; $b < $count_hex; $b++){ // blue
						$color = $hex[$r] . $hex[$g] . $hex[$b];
						$rawList[] = $color;
					}
				}
			}
			$list = array();
			foreach( $rawList as $color) {
				$list["~~#$color:text~~"] = "<span style='background-color: #$color' title='#$color' />&nbsp;</span>";
			}
			$headerlib->add_css('.toolbars-picker span {display: block; width: 14px; height: 12px}');
			break;

		case 'bgcolor':
			$label = tra('Background Color');
			$icon = tra('pics/icons/palette_bg.png');
			$wysiwyg = 'BGColor';

			$hex = array('0', '3', '6', '9', 'C', 'F');
			$count_hex = count($hex);

			for ($r = 0; $r < $count_hex; $r++){ // red
				for ($g = 0; $g < $count_hex; $g++){ // green
					for ($b = 0; $b < $count_hex; $b++){ // blue
						$color = $hex[$r].$hex[$g].$hex[$b];
						$rawList[] = $color;
					}
				}
			}
			$list = array();
			foreach( $rawList as $color) {
				$list["~~black,#$color:text~~"] = "<span style='background-color: #$color' title='#$color' />&nbsp;</span>";
			}
			$headerlib->add_css('.toolbars-picker span {display: block; width: 14px; height: 12px}');
			break;

		default:
			return;
		}

		$tag = new self;
		$tag->setWysiwygToken( $wysiwyg )
			->setLabel( $label )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setList( $list )
						->setType('Picker')
							->setName($tagName);
		
		foreach( $prefs as $pref ) {
			$tag->addRequiredPreference( $pref );
		}

		global $toolbarPickerIndex;
		++$toolbarPickerIndex;
		$tag->index = $toolbarPickerIndex;
		ToolbarPicker::setupJs();

		return $tag;
	} // }}}

	function setName( $name ) // {{{
	{
		$this->name = $name;
		
		return $this;
	} // }}}

	function setList( $list ) // {{{
	{
		$this->list = $list;
		
		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}
	
	public function getSyntax( $areaName = '$areaName' ) {
		return 'displayPicker( this, \'' . $this->name . '\', \'' . $areaName . '\')';	// is enclosed in double quotes later
	}
	
	static private function setupJs() {
		
		static $pickerAdded = false;
		global $headerlib;

		if( ! $pickerAdded ) {
			$pickerAdded = true;
			$headerlib->add_js( <<<JS
window.pickerData = [];
var pickerDiv, displayPicker, displayDialog;

displayPicker = function( closeTo, list, areaname ) {
	if (pickerDiv) {
		\$jq('div.toolbars-picker').remove();	// simple toggle
		pickerDiv = false;
		return;
	}
	textarea = getElementById( areaname);
	// quick fix for Firefox 3.5 losing selection on changes to popup
	if (typeof textarea.selectionStart != 'undefined') {
		var tempSelectionStart = textarea.selectionStart;
		var tempSelectionEnd = textarea.selectionEnd;
	}		
	pickerDiv = document.createElement('div');
	document.body.appendChild( pickerDiv );

	var coord = \$jq(closeTo).offset();
	coord.bottom = coord.top + \$jq(closeTo).height();

	pickerDiv.className = 'toolbars-picker';
	pickerDiv.style.left = coord.left + 'px';
	pickerDiv.style.top = (coord.bottom + 8) + 'px';

	// quick fix for Firefox 3.5 losing selection on changes to popup
	if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
		textarea.selectionStart = tempSelectionStart;
	}
	if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
		textarea.selectionEnd = tempSelectionEnd;
	}  

	var prepareLink = function( link, ins, disp ) {
		if (!link) return;
		
		link.innerHTML = disp;
		link.href = 'javascript:void(0)';
		link.onclick = function() {
			insertAt( areaname, ins );
	
			textarea = getElementById( areaname);	
			// quick fix for Firefox 3.5 losing selection on changes to popup
			if (typeof textarea.selectionStart != 'undefined') {
				var tempSelectionStart = textarea.selectionStart;
				var tempSelectionEnd = textarea.selectionEnd;	
			}

			\$jq('div.toolbars-picker').remove();
			pickerDiv = false;

			// quick fix for Firefox 3.5 losing selection on changes to popup
        	if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
                textarea.selectionStart = tempSelectionStart;
     		}
			if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
            	textarea.selectionEnd = tempSelectionEnd;
       		}

			return false;
		}
	};

	for( var i in window.pickerData[list] ) {
		var chr = window.pickerData[list][i];
		var link = document.createElement( 'a' );

		//pickerDiv.appendChild( document.createTextNode(' ') );
		prepareLink( link, i, chr );
		pickerDiv.appendChild( link );
	}
}

JS
, 0 );
		}
	}

	function getWikiHtml( $areaName ) // {{{
	{
		global $headerlib, $prefs;
		$headerlib->add_js( "window.pickerData['$this->name'] = " . json_encode($this->list) . ";" );
		if ($prefs['feature_jquery_ui'] != 'y') {
			$headerlib->add_jsfile('lib/jquery/jquery-ui/ui/jquery-ui.js');
			$headerlib->add_cssfile( 'lib/jquery/jquery-ui/themes/' . $prefs['feature_jquery_ui_theme'] . '/jquery-ui.css' );
		}
		
		return $this->getSelfLink($this->getSyntax($areaName),
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-picker');
	} // }}}
}

class ToolbarDialog extends Toolbar
{
	private $list;
	private $index;
	
	public static function fromName( $tagName ) // {{{
	{
		global $prefs;
		$tool_prefs = array();

		switch( $tagName ) {
		case 'tikilink':
			$label = tra('Wiki Link');
			$icon = tra('pics/icons/page_link.png');
			$wysiwyg = 'tikilink';
			$list = array('Wiki Link',
						'<label for="tbWLinkDesc">Show this text</label>',
						'<input type="text" id="tbWLinkDesc" class="ui-widget-content ui-corner-all" style="width: 100%" />',
						'<label for="tbWLinkURL">Link to this page</label>',
						'<input type="text" id="tbWLinkPage" class="ui-widget-content ui-corner-all" style="width: 100%" />',
						$prefs['wikiplugin_alink'] == 'y' ? '<label for="tbWLinkRel">Anchor:</label>' : '',
						$prefs['wikiplugin_alink'] == 'y' ? '<input type="text" id="tbWLinkAnchor" class="ui-widget-content ui-corner-all" style="width: 100%" />' : '',
						$prefs['feature_semantic'] == 'y' ? '<label for="tbWLinkRel">Semantic relation:</label>' : '',
						$prefs['feature_semantic'] == 'y' ? '<input type="text" id="tbWLinkRel" class="ui-widget-content ui-corner-all" style="width: 100%" />' : '',
						'{"open": function () {
$jq("#tbWLinkPage").tiki("autocomplete", "pagename");
var s = getTASelection($jq(getElementById(areaname))[0]);
var m = /\((.*)\(([^\|]*)\|?([^\|]*)\|?([^\|]*)\|?\)\)/g.exec(s);
if (m && m.length > 4) {
	if ($jq("#tbWLinkRel")) { $jq("#tbWLinkRel").val(m[1]); }
	$jq("#tbWLinkPage").val(m[2]);
	if (m[4]) {
		if ($jq("#tbWLinkAnchor")) { $jq("#tbWLinkAnchor").val(m[3]); }
		$jq("#tbWLinkDesc").val(m[4]);
	} else {
		$jq("#tbWLinkDesc").val(m[3]);
	}
} else {
	$jq("#tbWLinkDesc").val(s);
	if ($jq("#tbWLinkAnchor")) { $jq("#tbWLinkAnchor").val(""); }
}
						},
						"buttons": { "Cancel": function() { $jq(this).dialog("close"); },'.
						'"Insert": function() {
var s = "(";
if ($jq("#tbWLinkRel") && $jq("#tbWLinkRel").val()) { s += $jq("#tbWLinkRel").val(); }
s += "(" + $jq("#tbWLinkPage").val();
if ($jq("#tbWLinkAnchor") && $jq("#tbWLinkAnchor").val()) { s += "|" + ($jq("#tbWLinkAnchor").val().indexOf("#") != 0 ? "#" : "") + $jq("#tbWLinkAnchor").val(); }
if ($jq("#tbWLinkDesc").val()) { s += "|" + $jq("#tbWLinkDesc").val(); }
s += "))";
insertAt(areaname, s, false, false, true); 

textarea = getElementById( areaname);
// quick fix for Firefox 3.5 losing selection on changes to popup
if (typeof textarea.selectionStart != "undefined") {
	var tempSelectionStart = textarea.selectionStart;
	var tempSelectionEnd = textarea.selectionEnd;
}

$jq(this).dialog("close");

// quick fix for Firefox 3.5 losing selection on changes to popup
if (typeof textarea.selectionStart != "undefined" && textarea.selectionStart != tempSelectionStart) {
        textarea.selectionStart = tempSelectionStart;
}
if (typeof textarea.selectionEnd != "undefined" && textarea.selectionEnd != tempSelectionEnd) {
        textarea.selectionEnd = tempSelectionEnd;
}

}}}'
					);

			break;
		case 'link':
			$wysiwyg = 'Link';
			$label = tra('External Link');
			$icon = tra('pics/icons/world_link.png');
			$list = array('External Link',
						'<label for="tbLinkDesc">Show this text</label>',
						'<input type="text" id="tbLinkDesc" class="ui-widget-content ui-corner-all" style="width: 100%" />',
						'<label for="tbLinkURL">link to this URL</label>',
						'<input type="text" id="tbLinkURL" class="ui-widget-content ui-corner-all" style="width: 100%" />',
						'<label for="tbLinkRel">Relation:</label>',
						'<input type="text" id="tbLinkRel" class="ui-widget-content ui-corner-all" style="width: 100%" />',
						$prefs['cachepages'] == 'y' ? '<br /><label for="tbLinkNoCache" style="display:inline;">No cache:</label>' : '',
						$prefs['cachepages'] == 'y' ? '<input type="checkbox" id="tbLinkNoCache" class="ui-widget-content ui-corner-all" />' : '',
						'{"width": 300, "open": function () {
$jq("#tbWLinkPage").tiki("autocomplete", "pagename");
var s = getTASelection($jq(getElementById(areaname))[0]);
var m = /\[([^\|]*)\|?([^\|]*)\|?([^\|]*)\]/g.exec(s);
if (m && m.length > 3) {
	$jq("#tbLinkURL").val(m[1]);
	$jq("#tbLinkDesc").val(m[2]);
	if (m[3]) {
		if ($jq("#tbLinkNoCache") && m[3] == "nocache") {
			$jq("#tbLinkNoCache").attr("checked", "checked");
		} else {
			$jq("#tbLinkRel").val(m[3]);
		}			
	} else {
		$jq("#tbWLinkDesc").val(m[3]);
	}
} else {
	if (s.match(/(http|https|ftp)([^ ]+)/ig) == s) {	// v simple URL match
		$jq("#tbLinkURL").val(s);
	} else {
		$jq("#tbLinkDesc").val(s);
	}
}
if (!$jq("#tbLinkURL").val()) {
	$jq("#tbLinkURL").val("http://");
}
						},
						"buttons": { "Cancel": function() { $jq(this).dialog("close"); },'.
						'"Insert": function() {
var s = "[" + $jq("#tbLinkURL").val();
if ($jq("#tbLinkDesc").val()) { s += "|" + $jq("#tbLinkDesc").val(); }
if ($jq("#tbLinkRel").val()) { s += "|" + $jq("#tbLinkRel").val(); }
if ($jq("#tbLinkNoCache") && $jq("#tbLinkNoCache").attr("checked")) { s += "|nocache"; }
s += "]";
insertAt(areaname, s, false, false, true); 

textarea = getElementById( areaname);
// quick fix for Firefox 3.5 losing selection on changes to popup
if (typeof textarea.selectionStart != "undefined") {
	var tempSelectionStart = textarea.selectionStart;
	var tempSelectionEnd = textarea.selectionEnd;
}
$jq(this).dialog("close");

// quick fix for Firefox 3.5 losing selection on changes to popup
if (textarea.selectionStart != tempSelectionStart) {
        textarea.selectionStart = tempSelectionStart;
}
if (textarea.selectionEnd != tempSelectionEnd) {
        textarea.selectionEnd = tempSelectionEnd;
}

}}}'
					);
			break;

		case 'table':
			$icon = tra('pics/icons/table.png');
			$wysiwyg = 'Table';
			$label = tra('Table Builder');
			$list = array('Table Builder',
						'{"open": function () {
var s = getTASelection($jq(getElementById(areaname))[0]);
var m = /\|\|([\s\S]*?)\|\|/mg.exec(s);
var vals = [], rows=3, cols=3, c, r, i, j;
if (m) {
	m = m[1];
	m = m.split("\n");
	rows = 0;
	cols = 1;
	for(i = 0; i < m.length; i++) {
		var a2 = m[i].split("|");
		var a = [];
		for (j = 0; j < a2.length; j++) {	// links can have | chars in
			if (a2[j].indexOf("[") > -1 && a2[j].indexOf("[[") == -1 && a2[j].indexOf("]") == -1 ) {	// external link
				a[a.length] = a2[j];
				j++;
				var k = true;
				while ( j < a2.length && k ) {
					a[a.length-1] += "|" + a2[j];
					if (a2[j].indexOf("]") > -1) {	// closed
						k = false;
					} else {
						j++;
					}
				}
			} else if (a2[j].search(/\(\S*\(/) > -1 && a2[j].indexOf("))") == -1) {
				a[a.length] = a2[j];
				j++;
				var k = true;
				while ( j < a2.length && k ) {
					a[a.length-1] += "|" + a2[j];
					if (a2[j].indexOf("))") > -1) {	// closed
						k = false;
					} else {
						j++;
					}
				}
			} else {
				a[a.length] = a2[j];
			}
		}
		vals[vals.length] = a;
		if (a.length > cols) { cols = a.length; }
		if (a.length) { rows++; }
	}
}
for (r = 1; r <= rows; r++) {
	for (c = 1; c <= cols; c++) {
		var v = "";
		if (vals.length) {
			if (vals[r-1] && vals[r-1][c-1]) {
				v = vals[r-1][c-1];
			} else {
				v = "   ";
			}
		} else {
			v = "   ";	//row " + r + ",col " + c + "";
		}
		var el = $jq("<input type=\"text\" id=\"tbTableR" + r + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90/cols) + "%\" />");
		$jq(this).append(el);
	}
	if (r == 1) {
		el = $jq("<img src=\"pics/icons/add.png\" />");
		$jq(this).append(el);
		el.click(function () {
			var pr = $jq(this).parent();
			$jq(pr).attr("cols", $jq(pr).attr("cols")+1);
			for (r = 1; r <= $jq(pr).attr("rows"); r++) {
				v = "   ";	//"row " + r + ",col " + $jq(pr).attr("cols") + "";
				var el = $jq("<input type=\"text\" id=\"tbTableR" + r + "C" + $jq(pr).attr("cols") + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90/$jq(pr).attr("cols")) + "%\" />");
				$jq("#tbTableR" + r + "C" + ($jq(pr).attr("cols")-1)).after(el);
			}
			$jq(pr).find("input").width(90/$jq(pr).attr("cols") + "%");
		});
	}
	$jq(this).append($jq("<br />"));
}
el = $jq("<img src=\"pics/icons/add.png\" />");
$jq(this).append(el);
el.click(function () {
	var pr = $jq(this).parent();
	$jq(pr).attr("rows", $jq(pr).attr("rows")+1);
	for (c = 1; c <= $jq(pr).attr("cols"); c++) {
		v = "   ";	//"row " + $jq(pr).attr("rows") + ",col " + c + "";
		var el = $jq("<input type=\"text\" id=\"tbTableR" + $jq(pr).attr("rows") + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90/$jq(pr).attr("cols")) + "%\" />");
		$jq(this).before(el);
	}
	$jq(this).before("<br />");
$jq(pr).dialog("option", "height", ($jq(pr).attr("rows")+1) * 1.2 * $jq("#tbTableR1C1").height() + 130);
});

this.rows = rows; this.cols = cols;
$jq(this).dialog("option", "width", (cols+1) * 120 + 50);
$jq(this).dialog("option", "position", "center");
						},
						"width": 320, "buttons": { "Cancel": function() { $jq(this).dialog("close"); },'.
						'"Insert": function() {
var s = "||", rows, cols, c, r, rows2=1, cols2=1;
rows = this.rows ? this.rows : 3;
cols = this.cols ? this.cols : 3;
for (r = 1; r <= rows; r++) {
	for (c = 1; c <= cols; c++) {
		if ($jq("#tbTableR" + r + "C" + c).val()) {
			if (r > rows2) {
				rows2 = r;
			}
			if (c > cols2) {
				cols2 = c;
			}
		}
	}
}
for (r = 1; r <= rows2; r++) {
	for (c = 1; c <= cols2; c++) {
		s += $jq("#tbTableR" + r + "C" + c).val();
		if (c < cols2) { s += "|"; }
	}
	if (r < rows2) {  s += "\n"; }
}
s += "||";
insertAt(areaname, s, false, false, true);

// quick fix for Firefox 3.5 losing selection on changes to popup
textarea = getElementById( areaname);
if (typeof textarea.selectionStart != "undefined") {
	var tempSelectionStart = textarea.selectionStart;
	var tempSelectionEnd = textarea.selectionEnd;
}
$jq(this).dialog("close");

// quick fix for Firefox 3.5 losing selection on changes to popup
if (textarea.selectionStart != tempSelectionStart) {
        textarea.selectionStart = tempSelectionStart;
}
if (textarea.selectionEnd != tempSelectionEnd) {
        textarea.selectionEnd = tempSelectionEnd;
}

}}}'
					);
			break;

		case 'find':
			$icon = tra('pics/icons/find.png');
			$wysiwyg = 'Find';
			$label = tra('Find Text');
			$list = array('Find Text',
						'<label>Search:</label>',
						'<input type="text" id="tbFindSearch" class="ui-widget-content ui-corner-all" />',
						'<label for="tbLinkNoCache" style="display:inline;">Case Insensitivity:</label>',
						'<input type="checkbox" id="tbFindCase" checked="checked" class="ui-widget-content ui-corner-all" />',
						'{"open": function() {
	var s = getTASelection($jq(getElementById(areaname))[0]);
	$jq("#tbFindSearch").val(s);
						  },'.
						 '"buttons": { "Close": function() { $jq(this).dialog("close"); },'.
						  '"Find": function() {
	var s, opt, ta, str, re, p = 0, m;
	s = $jq("#tbFindSearch").removeClass("ui-state-error").val();
	opt = "";
	if ($jq("#tbFindCase").attr("checked")) {
		opt += "i";
	}
	ta = $jq(getElementById(areaname));
	str = ta.val();
	re = new RegExp(s,opt);
	p = getCaretPos(ta[0]);
	if (p && p < str.length) {
		m = re.exec(str.substring(p));
	} else {
		p = 0;
	}
	if (!m) {
		m = re.exec(str);
		p = 0;
	}
	if (m) {
		setSelectionRange(ta[0], m.index + p, m.index + s.length + p);
	} else {
		$jq("#tbFindSearch").addClass("ui-state-error");
	}
}}}'
					);

			break;

		case 'replace':
			$icon = tra('pics/icons/text_replace.png');
			$wysiwyg = 'Replace';
			$label = tra('Text Replace');
			$tool_prefs[] = 'feature_wiki_replace';
			
			$list = array('Text Replace',
						'<label>Search:</label>',
						'<input type="text" id="tbReplaceSearch" class="ui-widget-content ui-corner-all" />',
						'<label>Replace:</label>',
						'<input type="text" id="tbReplaceReplace" class="ui-widget-content ui-corner-all clearfix" />',
						'<label for="tbLinkNoCache" style="display:inline;">Case Insensitivity:</label>',
						'<input type="checkbox" id="tbReplaceCase" checked="checked" class="ui-widget-content ui-corner-all" />',
						'<br /><label for="tbLinkNoCache" style="display:inline;">Replace All:</label>',
						'<input type="checkbox" id="tbReplaceAll" checked="checked" class="ui-widget-content ui-corner-all" />',
						'{"open": function() {
	var s = getTASelection($jq(getElementById(areaname))[0]);
	$jq("#tbReplaceSearch").val(s);
						  },'.
						 '"buttons": { "Close": function() { $jq(this).dialog("close"); },'.
						'"Replace": function() {
	var s = $jq("#tbReplaceSearch").val();
	var r = $jq("#tbReplaceReplace").val();
	var opt = "";
	if ($jq("#tbReplaceAll").attr("checked")) {
		opt += "g";
	}
	if ($jq("#tbReplaceCase").attr("checked")) {
		opt += "i";
	}
	var str = $jq(getElementById(areaname)).val();
	var re = new RegExp(s,opt);
	$jq(getElementById(areaname)).val(str.replace(re,r));
}}}'
					);

			break;

		default:
			return;
		}

		$tag = new self;
		$tag->setWysiwygToken( $wysiwyg )
			->setLabel( $label )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setList( $list )
						->setType('Dialog');
		
		foreach( $tool_prefs as $pref ) {
			$tag->addRequiredPreference( $pref );
		}

		global $toolbarDialogIndex;
		++$toolbarDialogIndex;
		$tag->index = $toolbarDialogIndex;
		
		ToolbarDialog::setupJs();

		return $tag;
	} // }}}

	function setList( $list ) // {{{
	{
		$this->list = $list;
		
		return $this;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}
	
	public function getSyntax( $areaName = '$areaName' ) {
		return 'displayDialog( this, ' . $this->index . ', \'' . $areaName . '\')';
	}
	
	static private function setupJs() {
		
		static $dialogAdded = false;
		global $headerlib;

		if( ! $dialogAdded ) {
			$dialogAdded = true;
			$headerlib->add_js( <<<JS
window.dialogData = [];
var dialogDiv;

displayDialog = function( closeTo, list, areaname ) {
	var i, item, el, obj, tit = "";
	if (!dialogDiv) {
		dialogDiv = document.createElement('div');
		document.body.appendChild( dialogDiv );
	}
	\$jq(dialogDiv).empty();
	
	for( i = 0; i < window.dialogData[list].length; i++ ) {
		item = window.dialogData[list][i];
		if (item.indexOf("<") == 0) {	// form element
			el = \$jq(item);
			\$jq(dialogDiv).append( el );
		} else if (item.indexOf("{") == 0) {
			try {
				//obj = JSON.parse(item);	// safer, but need json2.js lib
				obj = eval("("+item+")");
			} catch (e) {
				alert(e.name + ' - ' + e.message);
			}
		} else {
			tit = item;
		}
	}
	
	// 2nd version fix for Firefox 3.5 losing selection on changes to popup
	saveTASelection(areaname);

	if (!obj) { obj = {}; }
	if (!obj.width) { obj.width = 210; }
	obj.bgiframe = true;
	obj.autoOpen - false;
	\$jq(dialogDiv).dialog('destroy').dialog(obj).dialog('option', 'title', tit).dialog('open');

	// 2nd version fix for Firefox 3.5 losing selection on changes to popup
	restoreTASelection(areaname);
	
	return false;
}

JS
, 0 );
		}
	}

	function getWikiHtml( $areaName ) // {{{
	{
		global $headerlib;
		$headerlib->add_js( "window.dialogData[$this->index] = " . json_encode($this->list) . ";", 1 + $this->index );
		
		return $this->getSelfLink($this->getSyntax($areaName),
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-picker');
	} // }}}
}

class ToolbarFullscreen extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Full Screen Edit') )
			->setIcon( 'pics/icons/application_get.png' )
			->setWysiwygToken( 'FitWindow' )
				->setType('Fullscreen');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		
		return $this->getSelfLink('toggleFullScreen(\''.$areaName.'\');return false;',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-fullscreen');
		
		
//		if( isset($_REQUEST['zoom']) )
//			$name = 'preview';
//		return '<input type="image" name="'.$name.'" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" class="toolbar qt-fullscreen" '.
//				'title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" value="wiki_edit" onclick="needToConfirm=false;" src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '"/>';
	} // }}}
}

class ToolbarHelptool extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Wiki Help') )
			->setIcon( 'pics/icons/help.png' )
				->setType('Helptool');
	} // }}}
	
	function getWikiHtml( $areaName ) // {{{
	{

		global $wikilib, $smarty, $plugins;
		if (!isset($plugins)) {
			include_once ('lib/wiki/wikilib.php');
			$plugins = $wikilib->list_plugins(true);
		}
		$smarty->assign_by_ref('plugins', $plugins);
		return $smarty->fetch("tiki-edit_help.tpl") . $smarty->fetch("tiki-edit_help_plugins.tpl");
		
	} // }}}

/* Useless
	function isAccessible() // {{{
	{
		return parent::isAccessible();
	} // }}}
*/
}

class ToolbarFileGallery extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Choose or upload images') )
			->setIcon( tra('pics/icons/pictures.png') )
				->setWysiwygToken( 'tikiimage' )
					->setType('FileGallery')
						->addRequiredPreference('feature_filegals_manager');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		global $smarty;
		
		require_once $smarty->_get_plugin_filepath('function','filegal_manager_url');
		return $this->getSelfLink('openFgalsWindow(\''.htmlentities(smarty_function_filegal_manager_url(array('area_name'=>$areaName), $smarty)).'\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-filegal');
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible() && ! isset($_REQUEST['zoom']);
	} // }}}
}

class ToolbarSwitchEditor extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Switch Editor (wiki or WYSIWYG)') )
			->setIcon( tra('pics/icons/pencil_go.png') )
				->setWysiwygToken( 'tikiswitch' )
					->setType('SwitchEditor')
						->addRequiredPreference('feature_wysiwyg');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		global $smarty;
		
		return $this->getSelfLink('switchEditor(\'wysiwyg\', $jq(this).parents(\'form\')[0]);',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-switcheditor');
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible() && ! isset($_REQUEST['zoom']) && ! isset($_REQUEST['hdr']);	// no switch editor if zoom of section edit
	} // }}}
	
/*	function getLabel() // {{{
	{
		return $this->label;
	} // }}}
*/
	
}

class ToolbarWikiplugin extends Toolbar
{
	private $pluginName;

	public static function fromName( $name ) // {{{
	{
		global $tikilib;
		if( substr( $name, 0, 11 ) == 'wikiplugin_'  ) {
			$name = substr( $name, 11 );
			if( $info = $tikilib->plugin_info( $name ) ) {
				if (isset($info['icon']) and $info['icon'] != '') {
					$icon = $info['icon'];
				} else {
					$icon = 'pics/icons/plugin.png';
				}

				$tag = new self;
				$tag->setLabel( str_ireplace('wikiplugin_', '', $info['name'] ))
					->setIcon( $icon )
					->setWysiwygToken( self::getToken( $name ) )
					->setPluginName( $name )
					->setType('Wikiplugin');

				return $tag;
			}
		}
	} // }}}

	function setPluginName( $name ) // {{{
	{
		$this->pluginName = $name;

		return $this;
	} // }}}

	function getPluginName() // {{{
	{
		return $this->pluginName;
	} // }}}

	function isAccessible() // {{{
	{
		global $tikilib;
		$dummy_output = '';
		return parent::isAccessible() && $tikilib->plugin_enabled( $this->pluginName, $dummy_output );
	} // }}}

	private static function getToken( $name ) // {{{
	{
		switch($name) {
		case 'flash': return 'Flash';
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return $this->getSelfLink('popup_plugin_form(\'' . $areaName . '\',\'' . $this->pluginName . '\')',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-plugin');
	} // }}}
}

class ToolbarSheet extends Toolbar
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
			case 'sheetsave':
				$label = tra('Save Sheet');
				$icon = tra('pics/icons/disk.png');
				$syntax = '$jq.sheet.saveSheet();';
				break;
			case 'addrow':
				$label = tra('Add Row');
				$icon = tra('pics/icons/sheet_row_add.png');
				$syntax = 'jS.controlFactory.addRow(null, null, ":last");';	// add row after end to workaround bug in jquery.sheet.js 1.0.2
				break;														// TODO fix properly for 5.1
			case 'addrowmulti':
				$label = tra('Add Multi-Rows');
				$icon = tra('pics/icons/sheet_row_add_multi.png');
				$syntax = 'jS.controlFactory.addRowMulti();';
				break;
			case 'deleterow':
				$label = tra('Delete Row');
				$icon = tra('pics/icons/sheet_row_delete.png');
				$syntax = 'jS.deleteRow();';
				break;
			case 'addcolumn':
				$label = tra('Add Column');
				$icon = tra('pics/icons/sheet_col_add.png');
				$syntax = 'jS.controlFactory.addColumn(true);';	// add col after current or at end if none selected
				break;
			case 'deletecolumn':
				$label = tra('Delete Column');
				$icon = tra('pics/icons/sheet_col_delete.png');
				$syntax = 'jS.deleteColumn();';
				break;
			case 'addcolumnmulti':
				$label = tra('Add Multi-Columns');
				$icon = tra('pics/icons/sheet_col_add_multi.png');
				$syntax = 'jS.controlFactory.addColumnMulti();';
				break;
			case 'sheetgetrange':
				$label = tra('Get Cell Range');
				$icon = tra('pics/icons/sheet_get_range.png');
				$syntax = 'jS.appendToFormula(jS.getTdRange());';
				break;
			case 'sheetfind':
				$label = tra('Find');
				$icon = tra('pics/icons/find.png');
				$syntax = 'jS.cellFind();';
				break;
			case 'sheetrefresh':
				$label = tra('Refresh Calculations');
				$icon = tra('pics/icons/arrow_refresh.png');
				$syntax = 'jS.calc(jS.obj.tableBody());';
				break;
			case 'sheetclose':
				$label = tra('Finish Editing');
				$icon = tra('pics/icons/close.png');
				$syntax = '$jq("#edit_button").click();';	// temporary workaround TODO properly
				break;
				
			default:
				return;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
				->setSyntax( $syntax )
					->setType('Sheet');
		
		return $tag;
	} // }}}

	function getSyntax() // {{{
	{
		return $this->syntax;
	} // }}}
	
	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return $this->getSelfLink(addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')),
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-sheet');

	} // }}}
	
}



class ToolbarsList
{
	private $lines = array();

	private function __construct() {}
	
	public static function fromPreference( $section, $tags_to_hide = array() ) // {{{
	{
		global $tikilib;

		$global = $tikilib->get_preference( 'toolbar_global' . (strpos($section, '_comments') !== false ? '_comments' : ''));
		$local = $tikilib->get_preference( 'toolbar_'.$section, $global );

		foreach($tags_to_hide as $name) {
			$local = str_replace($name, '', $local);
		}
		$local = str_replace(array(',,', '|,', ',|', ',/', '/,'), array(',', '|', '|', '/', '/'), $local);

		return self::fromPreferenceString( $local );
	} // }}}

	public static function fromPreferenceString( $string ) // {{{
	{
		global $toolbarPickerIndex;
		$toolbarPickerIndex = -1;
		$list = new self;

		$string = preg_replace( '/\s+/', '', $string );

		foreach( explode( '/', $string ) as $line ) {
			$bits = explode('|', $line);
			if (count($bits) > 1) {
				$list->addLine( explode( ',', $bits[0] ), explode( ',', $bits[1] ) );
			} else {
				$list->addLine( explode( ',', $bits[0] ) );
			}
		}

		return $list;
	} // }}}	

	public	function addTag ( $name, $unique = false ) {
		if ( $unique && $this->contains($name) ) {
			return false;
		}
		$this->lines[count($this->lines)-1][0][0][] = Toolbar::getTag( $name );
		return true;
	}

	public	function insertTag ( $name, $unique = false ) {
		if ( $unique && $this->contains($name) ) {
			return false;
		}
		array_unshift($this->lines[0][0][0], Toolbar::getTag( $name ));	
		return true;
	}

	private function addLine( array $tags, array $rtags = array() ) // {{{
	{
		$elements = array();
		$j = count($rtags) > 0 ? 2 : 1;
		
		for ($i = 0; $i <  $j; $i++) {
			$group = array();
			$elements[$i] = array();
			
			if ($i == 0) {
				$thetags = $tags;
			} else {
				$thetags = $rtags;
			}
			foreach( $thetags as $tagName ) {
				if( $tagName == '-' ) {
					if( count($group) ) {
						$elements[$i][] = $group;
						$group = array();
					}
				} else {
					if( ( $tag = Toolbar::getTag( $tagName ) ) 
						&& $tag->isAccessible() ) {
	
						$group[] = $tag;
					}
				}
			}
	
			if( count($group) ) {
				$elements[$i][] = $group;
			}
		}
		if( count( $elements ) )
			$this->lines[] = $elements;
	} // }}}

	function getWysiwygArray() // {{{
	{
		$lines = array();
		foreach( $this->lines as $line ) {
			$lineOut = array();

			foreach( $line as $bit ) {
				foreach( $bit as $group) {
					foreach( $group as $tag ) {
	
						if( $token = $tag->getWysiwygToken() )
							$lineOut[] = $token;
					}
	
					$lineOut[] = '-';
				}
			}

			$lineOut = array_slice( $lineOut, 0, -1 );

			if( count($lineOut) )
				$lines[] = array($lineOut);
		}

		return $lines;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		global $tiki_p_admin, $tiki_p_admin_toolbars, $smarty, $section, $prefs, $headerlib;
		$html = '';

		// $jq.selection() is in jquery.autocomplete.min.js
		
		if ($prefs['feature_jquery_autocomplete'] != 'y') {
			$headerlib->add_jsfile('lib/jquery/jquery-autocomplete/jquery.autocomplete.min.js');
		}

		$c = 0;
		foreach( $this->lines as $line ) {
			$lineHtml = '';
			$right = '';
			if (count($line) == 1) {
				$line[1] = array();
			}
			
			// $line[0] is left part, $line[1] right floated section
			for ($bitx = 0, $bitxcount_line = count($line); $bitx < $bitxcount_line; $bitx++ ) {
				$lineBit = '';
				
				if ($c == 0 && $bitx == 1 && ($tiki_p_admin == 'y' or $tiki_p_admin_toolbars == 'y')) {
					$params = array('_script' => 'tiki-admin_toolbars.php', '_onclick' => 'needToConfirm = true;', '_class' => 'toolbar', '_icon' => 'wrench', '_ajax' => 'n');
					if (isset($section)) { $params['section'] = $section; }
					$content = tra('Admin Toolbars');
					$right .= smarty_block_self_link($params, $content, $smarty);
				}
			
				foreach( $line[$bitx] as $group ) {
					$groupHtml = '';
					foreach( $group as $tag ) {
						$groupHtml .= $tag->getWikiHtml( $areaName );
					}
					
					if( !empty($groupHtml) ) {
						$param = empty($lineBit) ? '' : ' class="toolbar-list"';
						$lineBit .= "<span$param>$groupHtml</span>";
					}
					if ($bitx == 1) {
						if (!empty($right)) {
							$right = '<span class="toolbar-list">' . $right . '</span>';
						}
						$lineHtml = "<div class='helptool-admin'>$lineBit $right</div>" . $lineHtml;
					} else {
						$lineHtml = $lineBit;
					}
				}
				// adding admin icon if no right part - messy - TODO better
				if ($c == 0 && empty($lineBit) && !empty($right)) {
					$lineHtml .= "<div class='helptool-admin'>$right</div>";
				} 
			}
			if( !empty($lineHtml) ) {
				$html .= "<div>$lineHtml</div>";
			}
			$c++;
		}

		return $html;
	} // }}}
	
	function contains($name) { // {{{
		foreach( $this->lines as $line ) {
			foreach( $line as $group ) {
				foreach( $group as $tags ) {
					foreach($tags as $tag) {
						if ($tag->getLabel() == $name) {
							return true;
						}
					}
				}
			}
		}
		return false;
	} // }}}
}

