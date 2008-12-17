<?php
require_once 'TikiPageControls.php';

class TikiPageControls_Blog extends TikiPageControls
{
	private $info;
	private $blogId;

	function __construct( $blog_data ) // {{{
	{
		parent::__construct( 'blog', $blog_data['blogId'], $blog_data['title'] );

		$this->blogId = $blog_data['blogId'];
		$this->info = $blog_data;
	} // }}}

	function build() // {{{
	{
		global $bloglib;
		require_once 'lib/blogs/bloglib.php';
		$bloglib->load_blog_permissions( $blog_data, $this->getUser(), $this->hasPerm('tiki_p_admin') );

		$this->setHeading( $this->info['title'], $this->link( 'blog', $this->blogId ) );

		$this->addActionMenu();
	} // }}}

	function addActionMenu() // {{{
	{
		$actionMenu = $this->addMenu( 'actions', tra('Actions') );

		if( ( $this->getUser() && $this->info['user'] == $this->getUser() )
			|| $this->hasPerm( 'tiki_p_blog_admin' ) ) {

			if( $this->hasPerm( 'tiki_p_admin' ) 
				|| $this->info['individual'] != 'y' 
				|| $this->info['individual_tiki_p_blog_create_blog'] == 'y' ) {

				$link = $this->link( 'url', 'tiki-edit_blog.php', array(
					'blogId' => $this->blogId,
				) );
				$actionMenu->addItem( tra('Edit'), $link, 'edit' )
					->setIcon( 'pics/icons/page_edit.png' )
					->setSelected( $this->isMode( 'edit' ) );
			}
		}

		if( $this->hasPerm( 'tiki_p_blog_post' ) ) {
			if( $this->hasPerm( 'tiki_p_admin' )
				|| $this->info['individual'] != 'y'
				|| $this->info['individual_tiki_p_blog_post'] == 'y' ) {
				
				$link = $this->link( 'url', 'tiki-blog_post.php', array(
					'blogId' => $this->blogId,
				) );
				$actionMenu->addItem( tra('Post'), $link, 'post' )
					->setIcon( 'pics/icons/pencil_add.png' );
			}
		}

		if( $this->hasAnyOfPerm( 'tiki_p_admin', 'tiki_p_assign_perm_blog' ) ) {
			$link = $this->link( 'url', 'tiki-objectpermissions', array(
				'objectType' => 'blog',
				'permType' => 'blogs',
				'objectId' => $this->blogId,
				'objectName' => $this->info['title'],
			) );
			$actionMenu->addItem( tra('Permissions'), $link, 'permissions' )
				->setSelected( $this->isMode( 'permissions' ) )
				->setIcon( ( $this->info['individual'] == 'y' ) ? 'pics/icons/key_active.png' : 'pics/icons/key.png' );
		}

		if( ( $this->getUser() && $this->info['user'] == $this->getUser() )
			|| $this->hasPerm( 'tiki_p_blog_admin' ) ) {

			if( $this->hasPerm( 'tiki_p_admin' )
				|| $this->info['individual'] != 'y'
				|| $this->info['individual_tiki_p_blog_create_blog'] == 'y' ) {

				$link = $this->link( 'bloglist', null, array(
					'remove' => $this->blogId,
				) );
				$actionMenu->addItem( tra('Remove'), $link, 'remove' )
					->setSelected( $this->isMode( 'remove' ) )
					->setIcon( 'pics/icons/cross.png' );
			}
		}

		$this->removeMenu( $actionMenu, 0 );
	} // }}}
}

?>
