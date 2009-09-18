<?php

// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/smarty_tiki/block.self_link.php');

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
		elseif( $tagName == 'fullscreen' )
			return new ToolbarFullscreen;
		elseif( $tagName == 'enlarge' )
			return new ToolbarTextareaResize( 'enlarge' );
		elseif( $tagName == 'reduce' )
			return new ToolbarTextareaResize( 'reduce' );
		elseif( $tagName == 'tikiimage' )
			return new ToolbarFileGallery;
		elseif( $tagName == 'help' )
			return new ToolbarHelptool;
		elseif( $tagName == '-' )
			return new ToolbarSeparator;
	} // }}}

	public static function getList() // {{{
	{
		global $tikilib;
		$plugins = $tikilib->plugin_get_list();
		
		$custom = Toolbar::getCustomList();
		
		foreach( $plugins as & $name )
			$name = "wikiplugin_$name";
		
		$plugins = array_merge($plugins, $custom);

		return array_unique (array_merge( array(
			'-',
			'bold',
			'italic',
			'strike',
			'sub',
			'sup',
			'tikilink',
			'link',
			'anchor',
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
			'image',
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
			'underline',
			'unlink',
			'style',
			'fontname',
			'fontsize',
			'source',
			'fullscreen',
			'enlarge',
			'reduce',
			'help',
			'tikiimage',
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
		
		$name = strtolower( preg_replace('/[\s,\/\|]+/', '_', $name) );

		$prefName = "toolbar_tool_$name";
		$data = array('name'=>$name, 'label'=>$label, 'icon'=>$icon, 'token'=>$token, 'syntax'=>$syntax, 'type'=>$type, 'plugin'=>$plugin);
		
		$tikilib->set_preference( $prefName, serialize( $data ) );
		
		$list = array();
		if( isset($prefs['toolbar_custom_list']) ) {
			$list = unserialize($prefs['toolbar_custom_list']);
		}
		if( !in_array( $name, $list ) ) {
			$list[] = $name;
			$tikilib->set_preference( 'toolbar_custom_list', serialize($list) );
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
		return '<img src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->getLabel(), ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}
	
	function getSelfLink( $click, $title, $class ) { // {{{
		global $smarty;
		
		$params = array();
		$params['_onclick'] = $click . (substr($click, strlen($click)-1) != ';' ? ';' : '') . 'return false;';
		$params['_class'] = 'toolbar ' . (!empty($class) ? ' '.$class : '');
		$params['_ajax'] = 'n';
		$content = $title;
		$params['_icon'] = $this->icon;
			
		if (strpos($class, 'qt-plugin') !== false && !empty($title)) {
			$params['_menu_text'] = 'y';
			$params['_menu_icon'] = 'y';
		} else {
		}
		return smarty_block_self_link($params, $content, $smarty);
	} // }}}

}

class ToolbarSeparator extends Toolbar
{
	function __construct() // {{{
	{
		$this->setWysiwygToken('-')
			->setIcon('pics/icons/tree_vertline.png')
				->setType('Separator');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return '|';
	} // }}}
}

class ToolbarFckOnly extends Toolbar
{ 
	private function __construct( $token, $icon = 'pics/icons/shading.png' ) // {{{
	{
		$this->setWysiwygToken( $token )
			->setIcon($icon)
				->setType('FckOnly');
	} // }}}
	
	public static function fromName( $name ) // {{{
	{
		switch( $name ) {
		case 'templates':
			return new self( 'Templates', 'pics/icons/page_white_stack.png' );
		case 'cut':
			return new self( 'Cut', 'pics/icons/cut.png' );
		case 'copy':
			return new self( 'Copy', 'pics/icons/page_copy.png' );
		case 'paste':
			return new self( 'Paste', 'pics/icons/page_paste.png' );
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
		case 'find':
			return new self( 'Find' );
		case 'replace':
			return new self( 'Replace' );
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
		case 'underline':
			return new self( 'Underline' );
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
			return new self( 'ajaxAutoSave' );
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return null;
	} // }}}
	
	function getLabel( $areaName ) // {{{
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
		case 'strike':
			$label = tra('Strikethrough');
			$icon = tra('pics/icons/text_strikethrough.png');
			$wysiwyg = 'StrikeThrough';
			$syntax = '--text--';
			break;
		case 'sub':
			$label = tra('Subscript');
			$icon = tra('pics/icons/text_subscript.png');
			$wysiwyg = 'Subscript';
			$syntax = '{SUB()}text{SUB}';
			break;
		case 'sup':
			$label = tra('Superscript');
			$icon = tra('pics/icons/text_superscript.png');
			$wysiwyg = 'Superscript';
			$syntax = '{SUP()}text{SUP}';
			break;
		case 'tikilink':
			$label = tra('Wiki Link');
			$icon = tra('pics/icons/page_link.png');
			$wysiwyg = 'tikilink';
			$syntax = '((text))';
			break;
		case 'link':
			$label = tra('Link');
			$icon = tra('pics/icons/world_link.png');
			$wysiwyg = 'Link';
			$syntax = '[http://example.com|text]';
			break;
		case 'anchor':
			$label = tra('Anchor');
			$icon = tra('pics/icons/anchor.png');
			$wysiwyg = 'Anchor';
			$syntax = '{ANAME()}text{ANAME}';
			break;
		case 'color':
			$label = tra('Text Color');
			$icon = tra('pics/icons/palette.png');
			$wysiwyg = 'TextColor';
			$syntax = '~~red:text~~';
			break;
		case 'bgcolor':
			$label = tra('Background Color');
			$icon = tra('pics/icons/palette.png');
			$wysiwyg = 'BGColor';
			$syntax = '~~white,black:text~~';
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

	function getSyntax( $syntax ) // {{{
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
		return $this->getSelfLink('insertAt(\'' . $areaName . '\', \'' . addslashes(htmlentities($this->syntax, ENT_COMPAT, 'UTF-8')) . '\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-inline');

	} // }}}
	
}

class ToolbarBlock extends ToolbarInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'center':
			$label = tra('Align Center');
			$icon = tra('pics/icons/text_align_center.png');
			$wysiwyg = 'JustifyCenter';
			$syntax = "::text::";
			break;
		case 'table':
			$label = tra('Table');
			$icon = tra('pics/icons/table.png');
			$wysiwyg = 'Table';
			$syntax = '||r1c1|r1c2\nr2c1|r2c2||';
			break;
		case 'rule':
			$label = tra('Horizontal Bar');
			$icon = tra('pics/icons/page.png');
			$wysiwyg = 'Rule';
			$syntax = '---';
			break;
		case 'pagebreak':
			$label = tra('Page Break');
			$icon = tra('pics/icons/page.png');
			$wysiwyg = 'PageBreak';
			$syntax = '---';
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
		case 'image':
			$label = tra('Image');
			$icon = tra('pics/icons/picture.png');
			$wysiwyg = 'tikiimage';
			$syntax = '{img src= width= height= link= }';
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

static $toolbarPickerIndex = -1;

class ToolbarPicker extends Toolbar
{
	private $list;
	
	public static function fromName( $tagName ) // {{{
	{
		$prefs = array();

		switch( $tagName ) {
		case 'specialchar':
			$wysiwyg = 'SpecialChar';
			$label = tra('Special Characters');
			$icon = tra('pics/icons/world_edit.png');
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
			foreach( $rawList as $smiley ) {
				$tra = htmlentities( tra($smiley), ENT_QUOTES, 'UTF-8' );
				$list["(:$smiley:)"] = '<img src="img/smiles/icon_' .$smiley . '.gif" alt="' . $tra . '" title="' . $tra . '" border="0" width="15" height="15" />';
			}
			break;
		default:
			return;
		}

		$tag = new self;
		$tag->setWysiwygToken( $wysiwyg )
			->setLabel( $label )
				->setIcon( !empty($icon) ? $icon : 'pics/icons/shading.png' )
					->setList( $list )
						->setType('Picker');
		
		foreach( $prefs as $pref ) {
			$tag->addRequiredPreference( $pref );
		}

		global $toolbarPickerIndex;
		++$toolbarPickerIndex;
		ToolbarPicker::setupJs();

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
		global $toolbarPickerIndex;
		return 'displayPicker( this, ' . $toolbarPickerIndex . ', \'' . $areaName . '\')';
	}
	
	static private function setupJs() {
		
		static $pickerAdded = false;
		global $headerlib;

		if( ! $pickerAdded ) {
			$headerlib->add_js( <<<JS
var pickerData = [];
var pickerDiv;

function displayPicker( closeTo, list, areaname ) {
	if (pickerDiv) {
		\$jq('div.toolbars-picker').remove();	// simple toggle
		pickerDiv = false;
		return;
	}
	pickerDiv = document.createElement('div');
	document.body.appendChild( pickerDiv );

	var coord;
	if (typeof closeTo.getCoordinates == 'function') {	// moo
		coord = closeTo.getCoordinates();
	} else if (\$jq) {									// jq
		coord = \$jq(closeTo).offset();
		coord.bottom = coord.top + \$jq(closeTo).height();
	}
	pickerDiv.className = 'toolbars-picker';
	pickerDiv.style.left = coord.left + 'px';
	pickerDiv.style.top = (coord.bottom + 8) + 'px';

	var prepareLink = function( link, ins, disp ) {
		link.innerHTML = disp;
		link.href = 'javascript:void(0)';
		link.onclick = function() {
			insertAt( areaname, ins );
			\$jq('div.toolbars-picker').remove();
			pickerDiv = false;
		}
	};

	for( var i in pickerData[list] ) {
		var char = pickerData[list][i];
		var link = document.createElement( 'a' );

		pickerDiv.appendChild( link );
		pickerDiv.appendChild( document.createTextNode(' ') );
		prepareLink( link, i, char );
	}
}

JS
, 0 );
		}
	}

	function getWikiHtml( $areaName ) // {{{
	{
		global $headerlib;
		$headerlib->add_js( "pickerData.push( " . json_encode($this->list) . " );", 1 );
		
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
		$name = 'zoom';
		if( isset($_REQUEST['zoom']) )
			$name = 'preview';
		return '<input type="image" name="'.$name.'" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" class="toolbar qt-fullscreen" '.
				'title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" value="wiki_edit" onclick="needToConfirm=false;" title="" class="icon" src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '"/>';
	} // }}}
}

class ToolbarTextareaResize extends Toolbar
{
	private $diff;

	function __construct( $type ) // {{{
	{
		switch( $type ) {
		case 'reduce':
			$this->setLabel( tra('Reduce area height') )
				->setIcon( tra('pics/icons/arrow_in.png') )
					->setType('TextareaResize');
			$this->diff = '-10';
			break;

		case 'enlarge':
			$this->setLabel( tra('Enlarge area height') )
				->setIcon( tra('pics/icons/arrow_out.png') )
					->setType('TextareaResize');
			$this->diff = '+10';
			break;

		default:
			throw new Exception('Unknown resize icon type type');
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		return $this->getSelfLink('textareasize(\'' . $areaName . '\', ' . $this->diff . ', 0)',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-resize');
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible() && ! isset($_REQUEST['zoom']);
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
		return $smarty->fetch("tiki-edit_help.tpl");
		
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible();
	} // }}}
}

class ToolbarFileGallery extends Toolbar
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Choose or upload images') )
			->setIcon( tra('pics/icons/pictures.png') )
				->setWysiwygToken( 'tikiimage' )
					->setType('FileGallery');
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		global $smarty;
		
		require_once $smarty->_get_plugin_filepath('function','filegal_manager_url');
		return $this->getSelfLink('openFgalsWindow(\''.smarty_function_filegal_manager_url(array('area_name'=>$areaName), $smarty).'\');',
							htmlentities($this->label, ENT_QUOTES, 'UTF-8'), 'qt-filegal');
	} // }}}

	function isAccessible() // {{{
	{
		return parent::isAccessible() && ! isset($_REQUEST['zoom']);
	} // }}}
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
		return parent::isAccessible() && $tikilib->plugin_enabled( $this->pluginName );
	} // }}}

/*	probably not need now?
	private static function getIcon( $name ) // {{{
	{
		// This property could be added to the plugin definition
		switch($name) {
		default:
			return 'pics/icons/plugin.png';
		}
	} // }}} */

	private static function getToken( $name ) // {{{
	{
		switch($name) {
		case 'flash': return 'Flash';
		}
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		if ($this->icon != 'pics/icons/plugin.png') {
			$label = '';
		} else {
			$label = htmlentities($this->label, ENT_QUOTES, 'UTF-8');
		}
		return $this->getSelfLink('popup_plugin_form(\'' . $areaName . '\',\'' . $this->pluginName . '\')',
							$label, 'qt-plugin');
	} // }}}
}

class ToolbarsList
{
	private $lines = array();

	private function __construct() {}
	
	public static function fromPreference( $section ) // {{{
	{
		global $tikilib;

		$global = $tikilib->get_preference( 'toolbar_global' . (strpos($section, '_comments') !== false ? '_comments' : ''));
		$local = $tikilib->get_preference( 'toolbar_'.$section, $global );

		return self::fromPreferenceString( $local );
	} // }}}

	public static function fromPreferenceString( $string ) // {{{
	{
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
		array_push($this->lines[0][sizeof($this->lines)-1], Toolbar::getTag( $name ));
		return true;
	}

	public	function insertTag ( $name, $unique = false ) {
		if ( $unique && $this->contains($name) ) {
			return false;
		}
		array_unshift($this->lines[0][0], Toolbar::getTag( $name ));	
		return true;
	}

	private function addLine( array $tags, array $rtags = array() ) // {{{
	{
		$elements = array();
		$j = count($rtags) > 1 ? 2 : 1;
		
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

			foreach( $line as $group ) {
				foreach( $group as $tag ) {

					if( $token = $tag->getWysiwygToken() )
						$lineOut[] = $token;
				}

				$lineOut[] = '-';
			}

			$lineOut = array_slice( $lineOut, 0, -1 );

			if( count($lineOut) )
				$lines[] = array($lineOut);
		}

		return $lines;
	} // }}}

	function getWikiHtml( $areaName ) // {{{
	{
		global $tiki_p_admin, $tiki_p_admin_toolbars, $smarty, $section;
		$html = '';

		$c = 0;
		foreach( $this->lines as $line ) {
			$lineHtml = '';
			$right = '';
			if (count($line) == 1) {
				$line[1] = array();
			}
			
			// $line[0] is left part, $line[1] right floated section
			for ($bitx = 0; $bitx < count($line); $bitx++ ) {
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
						$lineHtml .= "<div class='helptool-admin'>$lineBit $right</div>";
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

		return $right . $html;
	} // }}}
	
	function contains($name) { // {{{
		foreach( $this->lines as $line ) {
			foreach( $line as $group ) {
				foreach( $group as $tag ) {
					if ($tag->getLabel() == $name) {
						return true;
					}
				}
			}
		}
		return false;
	} // }}}
}

