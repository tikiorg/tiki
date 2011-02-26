<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_Backends_Wiki extends ezcWebdavSimpleBackend
{
	protected $options;
	protected $root;
	protected $lockLevel = 0;

	protected $handledLiveProperties = array(
			'getcontentlength',
			'getlastmodified',
			'creationdate',
			'displayname',
			'getetag',
			'getcontenttype',
			'resourcetype',
			);

	protected $propertyStorage = null;

	public function getRoot()
	{
		return $this->root;
	}

	public function __construct()
	{
		global $prefs;

		// avoid not having a deadlock when trying to acquire WebDav lock
		print_debug("Lock Directory: ".$prefs['fgal_use_dir']."\n" . "\n" );
		if ( !empty($prefs['fgal_use_dir']) && file_exists($prefs['fgal_use_dir'] ) ) {
			$this->root = realpath( $prefs['fgal_use_dir'] );
		} else {
			$this->root = realpath( 'temp/' );
		}
		$this->options = new ezcWebdavFileBackendOptions( array (
					'lockFileName' => $this->root.'/.webdav_lock',
					'waitForLock' => 200000,
					'propertyStoragePath' => $this->root,
					'noLock' => false )
				);
		$this->propertyStorage = new ezcWebdavBasicPropertyStorage();
	}

	public function __get( $name )
	{
                print_debug("get " .$name . "\n");

		switch ( $name ) {
			case 'options':
				return $this->$name;

			default:
				throw new ezcBasePropertyNotFoundException( $name );
		}
	}

	public function __set( $name, $value )
	{
                print_debug("set " .$name . " " . $value . "\n");

		switch ( $name ) {
			case 'options':
				if ( ! $value instanceof ezcWebdavMemoryBackendOptions ) ///FIXME
				{
					throw new ezcBaseValueException( $name, $value, 'ezcWebdavMemoryBackendOptions' ); ///FIXME
				}

				$this->$name = $value;
				break;

			default:
				throw new ezcBasePropertyNotFoundException( $name );
		}
	}

	protected function acquireLock( $readOnly = false )
	{
		return true;
	}

	protected function freeLock()
	{
		return true;
	}

	protected function getMimeType( $path, $filename = '' )
	{
                print_debug("getMimeType " . $path . " " . $filename . "\n");
		return 'application/octet-stream';
	}

	protected function createCollection( $path )
	{
		global $user, $tikilib;

                print_debug("createCollection " . $path . "\n");

		return false;
	}

	protected function createResource( $path, $content = null )
	{
		global $user, $tikilib, $prefs;

                print_debug("createResource " . $path . "\n");

		if ( $this->isCollection($path) ) {
                        return null;
                }

                return $tikilib->create_page($this->get_page_name_from_virtual_path($path), 0, $content, $tikilib->now, "Created from WebDAV", $user,  $tikilib->get_ip_address());
	}

	protected function setResourceContents( $path, $content )
	{
		global $user, $tikilib, $prefs;

                print_debug("setResourceContents " . $path . "\n");

		if ( $this->isCollection($path) ) {
                        return null;
                }

                return $tikilib->update_page($this->get_page_name_from_virtual_path($path), $content, "Edited from WebDAV", $user, $tikilib->get_ip_address());
	}

	protected function getResourceContents( $path )
	{
		global $tikilib, $prefs;

                print_debug("getResourceContents " .$path . "\n");

		if ( $this->isCollection($path) ) {
                        return null;
                }

                $info = $tikilib->get_page_info($this->get_page_name_from_virtual_path($path));

                return $info['data'];
	}

	protected function getPropertyStorage( $path )
	{
                print_debug("getPropertyStorage " .$path . "\n");

		if ( @file_exists($storagePath = $this->options->propertyStoragePath.'/properties-'.md5($path)) ) {
			$xml = ezcWebdavServer::getInstance()->xmlTool->createDom( @file_get_contents($storagePath) );
		} else {
			$xml = ezcWebdavServer::getInstance()->xmlTool->createDom();
		}
		$handler = new ezcWebdavPropertyHandler(
				new ezcWebdavXmlTool()
				);
		try {
			$handler->extractProperties($xml->getElementsByTagNameNS('DAV:','*'),$this->propertyStorage);
		}
		catch ( Exception $e ) {
		}

		return $this->propertyStorage;
	}

	protected function storeProperties( $path, ezcWebdavBasicPropertyStorage $storage )
	{
                print_debug("storeProperties " .$path . "\n");

                $storagePath = $this->options->propertyStoragePath.'/properties-'.md5($path);

		// Create handler structure to read properties
		$handler = new ezcWebdavPropertyHandler(
				$xml = new ezcWebdavXmlTool()
				);

		// Create new dom document with property storage for one namespace
		$doc = new DOMDocument( '1.0' );

		$properties = $doc->createElement( 'properties' );
		$doc->appendChild( $properties );

		// Store and store properties
		foreach ($this->handledLiveProperties as $propName) {
			$storage->detach($propName);
		}
		$handler->serializeProperties(
				$storage,
				$properties
				);

		print_debug("storeProperties method end\n");

		return $doc->save( $storagePath );
	}

	public function setProperty( $path, ezcWebdavProperty $property )
	{
                print_debug("setProperty " .$path . "\n");

                if ( ( $property->namespace === 'DAV:' ) &&
				in_array( $property->name, $this->handledLiveProperties, true ) &&
				( $property->name !== 'getcontenttype' ) &&
			( $property->name !== 'lockdiscovery' ) ) {
			return false;
		}

		// Get namespace property storage
		$storage = $this->getPropertyStorage( $path );

		// Attach property to store
		$storage->attach( $property );

		// Store document back
		$this->storeProperties( $path, $storage );

		return true;
	}

	public function removeProperty( $path, ezcWebdavProperty $property )
	{
                print_debug("removeProperty " .$path . "\n");

		if ( $property instanceof ezcWebdavLiveProperty ) {
			return false;
		}

		// Get namespace property storage
		$storage = $this->getPropertyStorage( $path );

		// Attach property to store
		$storage->detach( $property->name, $property->namespace );

		// Store document back
		$this->storeProperties( $path, $storage );

		return true;
	}

	public function resetProperties( $path, ezcWebdavPropertyStorage $storage )
	{
                print_debug("resetProperties " .$path . "\n");

		$this->storeProperties( $path, $storage );
		return true;
	}

	public function getProperty( $path, $propertyName, $namespace = 'DAV:' )
	{
		global $tikilib, $prefs;

                print_debug("getProperty " .$path . " " . $propertyName . " " . $namespace . "\n");

		if ( !$this->nodeExists($path) ) {
                        return false;
                }

                $isCollection = $this->isCollection($path);
                $tikiInfo = null;

		if ( !$isCollection ) {
                        $tikiInfo = $tikilib->get_page_info($this->get_page_name_from_virtual_path($path));
                }

                $storage = $this->getPropertyStorage( $path );

		$properties = $storage->getAllProperties();
		// Handle dead propreties
		if ( $namespace !== 'DAV:' ) {
			return $properties[$namespace][$propertyName];
		}

                // Handle live properties
		switch ( $propertyName ) {
			case 'getcontentlength':
				$property = new ezcWebdavGetContentLengthProperty(
						$isCollection ?
						ezcWebdavGetContentLengthProperty::COLLECTION :
						$tikiInfo['page_size']
						);
				return $property;

			case 'getlastmodified':
				$property = new ezcWebdavGetLastModifiedProperty( new ezcWebdavDateTime(
							'@' . (int)$tikiInfo['lastModif']
							) );
				return $property;

			case 'creationdate':
				$property = new ezcWebdavCreationDateProperty( new ezcWebdavDateTime(
							'@' . (int)$tikiInfo['created']
							) );
				return $property;

			case 'displayname':
				$property = new ezcWebdavDisplayNameProperty(
						$tikiInfo['pageName']
						);
				return $property;

			case 'getcontenttype':
				$property = new ezcWebdavGetContentTypeProperty(
						$isCollection ?
						'httpd/unix-directory' :
                                                'application/octet-stream'
						);
				return $property;

			case 'getetag':
				$md5 = md5($path);
				$property = new ezcWebdavGetEtagProperty(
						'"' . $md5 . '-' . crc32($md5) . '"'
						);
				return $property;

			case 'resourcetype':
				$property = new ezcWebdavResourceTypeProperty(
						$isCollection ?
						ezcWebdavResourceTypeProperty::TYPE_COLLECTION :
						ezcWebdavResourceTypeProperty::TYPE_RESOURCE
						);

				return $property;

			case 'supportedlock':
				if ( !isset($properties[$namespace][$propertyName]) ) {
					$property = new ezcWebdavLockDiscoveryProperty();
				} else {
					$property = $properties[$namespace][$propertyName];
				}

				return $property;

			case 'lockdiscovery':
				if ( !isset($properties[$namespace][$propertyName]) ) {
					$property = new ezcWebdavLockDiscoveryProperty();
				} else {
					$property = $properties[$namespace][$propertyName];
				}

				return $property;

			default:
				// Handle all other live properties like dead properties
				$properties = $storage->getAllProperties();
				return $properties[$namespace][$propertyName];
		}
	}

	private function getContentLength( $path )
	{
                print_debug("getContentLength " .$path . "\n");

		return null;
	}

	protected function getETag( $path )
	{
                print_debug("getETag " .$path . "\n");

		if ( $etag = $this->getProperty( $path, 'getetag' ) ) {
			return $this->getProperty( $path, 'getetag' )->etag;
		} else {
			return md5($path);
		}
	}

	public function getAllProperties( $path )
	{
                print_debug("getAllProperties " .$path . "\n");

                $storage = $this->getPropertyStorage( $path );

		// Add all live properties to stored properties
		foreach ( $this->handledLiveProperties as $property ) {
			$storage->attach( $this->getProperty( $path, $property ) );
		}

		return $storage;
	}

	protected function performCopy( $fromPath, $toPath, $depth = ezcWebdavRequest::DEPTH_INFINITY )
	{
		global $tikilib, $wikilib;
		include_once ('lib/wiki/wikilib.php');

		print_debug("performCopy " . $fromPath . " -> ". $toPath . "\n");

		$page = $this->get_page_name_from_virtual_path($fromPath);
		$info = $tikilib->get_page_info($page);

		if ( !$info ) {
			return array(ezcWebdavResponse::STATUS_404);
		}

		$perms = $tikilib->get_perm_object($page, 'wiki page', $info);

                if ( $perms['tiki_p_edit'] == 'y' && !$this->isCollection($fromPath) && !$this->nodeExists($toPath) ) {
                        if ($wikilib->wiki_duplicate_page($page, $this->get_page_name_from_virtual_path($toPath))) {
				return array();
			} else {
				return array(ezcWebdavResponse::STATUS_500);
			}
                }

		return array(ezcWebdavResponse::STATUS_409);
	}

	protected function performDelete( $path )
	{
                global $tikilib;

		print_debug("performDelete " . $path . "\n");

		$page = $this->get_page_name_from_virtual_path($path);
		$info = $tikilib->get_page_info($page);

		if ( !$info ) {
			return array(ezcWebdavResponse::STATUS_404);
		}

		$perms = $tikilib->get_perm_object($page, 'wiki page', $info);

                if ( $perms['tiki_p_remove'] == 'y' && $perms['tiki_p_edit'] == 'y' && !$this->isCollection($path) ) {
                        if ($tikilib->remove_all_versions($this->get_page_name_from_virtual_path($path), "Remove from WebDav")) {
				return array();
			} else {
				return array(ezcWebdavResponse::STATUS_500);
			}
                }

		return array(ezcWebdavResponse::STATUS_409);
	}

	protected function nodeExists( $path )
	{
                global $tikilib;

                print_debug("nodeExists " .$path . "\n");

		if ( empty($path) || $path[0] != '/' ) {
                        return false;
                }

                if ( $path == '/' ) {
			return true;
		}

                return $tikilib->page_exists($this->get_page_name_from_virtual_path($path));
	}

	protected function isCollection( $path )
	{
                print_debug("isCollection " .$path . " -> " . ( $path === '/' ) . "\n");

                if ( $path === '/' ) {
			return true;
		}

		return false;
	}

	protected function getCollectionMembers( $path )
	{
		global $tikilib, $user;

                print_debug("getCollectionMembers " .$path . "\n");

                $contents = array();
		$errors = array();

		if ( $path !== '/' ) {
                        return $contents;
                }

		$groups = $tikilib->get_user_groups( $user );
		$perms = Perms::getInstance();
		$perms->setGroups( $groups );
                $pages = $tikilib->list_pages();

		foreach ($pages['data'] as $page) {
                        $contents[] = new ezcWebdavResource( $path . $page['pageName'] );
                }

		return $contents;
	}

	public function get( ezcWebdavGetRequest $request )
	{
		print_debug("-- HTTP method: GET --\n");
		$return = parent::get( $request );

                print_debug("get -> ". $return . "\n");

		return $return;
	}

	public function head( ezcWebdavHeadRequest $request )
	{
		print_debug("-- HTTP method: HEAD --\n");
		$return = parent::head( $request );

                print_debug("head \n");

		return $return;
	}

	public function propFind( ezcWebdavPropFindRequest $request )
	{
		print_debug("-- HTTP method: PROPFIND --\n");
		$return = parent::propFind( $request );

                print_debug("propFind \n");

		return $return;
	}

	public function propPatch( ezcWebdavPropPatchRequest $request )
	{
		print_debug("-- HTTP method: PROPPATCH --\n");
		$this->acquireLock();
		$return = parent::propPatch( $request );
		$this->freeLock();

                print_debug("propPatch \n");

		return $return;
	}

	public function put( ezcWebdavPutRequest $request )
	{
		print_debug("-- HTTP method: PUT --\n");
		$this->acquireLock();
		$return = parent::put( $request );
		$this->freeLock();

                print_debug("put \n");

		return $return;
	}

	public function delete( ezcWebdavDeleteRequest $request )
	{
		print_debug("-- HTTP method: DELETE --".print_r($request,true)."\n");
		$this->acquireLock();
		$return = parent::delete( $request );
		$this->freeLock();

                print_debug("delete \n");

		return $return;
	}

	public function copy( ezcWebdavCopyRequest $request )
	{
		print_debug("-- HTTP method: COPY --\n");
		$this->acquireLock();
		$return = parent::copy( $request );
		$this->freeLock();

                print_debug("copy \n");

		return $return;
	}

	public function makeCollection( ezcWebdavMakeCollectionRequest $request )
	{
		print_debug("-- HTTP method: MAKECOL --\n");
		$this->acquireLock();
		$return = parent::makeCollection( $request );
		$this->freeLock();

		return $return;
	}

        private function get_page_name_from_virtual_path( $path )
        {
                return substr($path, 1);
        }
}
