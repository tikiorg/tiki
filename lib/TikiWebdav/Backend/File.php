<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_Backends_File extends ezcWebdavSimpleBackend implements ezcWebdavLockBackend
{
	private $requestMimeType;

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
			//'supportedlock',
			//'lockdiscovery',
			);

	protected $resourceStorage = null;
	protected $propertyStorage = null;

	public function getRoot() {
		return $this->root;
	}

	public function __construct()
	{
		global $prefs;

		// avoid not having a deadlock when trying to acquire WebDav lock
		print_debug("Lock Directory: ".$prefs['fgal_use_dir']."\n", FILE_APPEND );
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

	public function lock( $waitTime, $timeout )
	{
		// Check and raise lockLevel counter
		print_debug("LOCK Level: ".$this->lockLevel." \n");
		if ( $this->lockLevel > 0 )
		{
			// Lock already acquired
			++$this->lockLevel;
			return;
		}

		$lockStart = microtime( true );

		// Timeout is in microseconds...
		$timeout /= 1000000;
		$lockFileName = $this->options->lockFileName;

		// fopen in mode 'x' will only open the file, if it does not exist yet.
		// Even this is is expected it will throw a warning, if the file
		// exists, which we need to silence using the @
		if ( !empty($this->options->lockFileName) ) {
			if ( file_exists($lockFileName) ) {
				while ( file_exists($lockFileName) )
				{
					// This is untestable.
					if ( microtime( true ) - $lockStart > $timeout )
					{
						// Release timed out lock
						unlink( $lockFileName );
						$lockStart = microtime( true );
					}
					else
					{
						usleep( $waitTime );
					}
				}
			}
			@file_put_contents($lockFileName, microtime(),FILE_APPEND );
		}

		// Add first lock
		++$this->lockLevel;
		print_debug("LOCK END \n");
	}

	public function unlock()
	{
		print_debug("unLOCK Level: ".$this->lockLevel." \n");
		if ( --$this->lockLevel === 0 )
		{
			// Remove the lock file
			$lockFileName = $this->options->lockFileName;
			if ( !empty($this->options->lockFileName) ) {
				unlink( $lockFileName );
			}
			print_debug("Remove LOCK: ".$this->options->lockFileName." \n");
		}
	}

	public function __get( $name )
	{
		switch ( $name )
		{
			case 'options':
				return $this->$name;

			default:
				throw new ezcBasePropertyNotFoundException( $name );
		}
	}

	public function __set( $name, $value )
	{
		switch ( $name )
		{
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
		if ( empty($this->options->lockFileName) )
		{
			return true;
		}

		try
		{
			$this->lock( $this->options->waitForLock, 2000000 );
		}
		catch ( ezcWebdavLockTimeoutException $e )
		{
			print_debug("LOCK: failed\n");
			return false;
		}
		print_debug("LOCK: Acquired\n");
		return true;
	}

	protected function freeLock()
	{
		print_debug("FreeLOCK: ".$this->options->lockFileName."\n");
		if ( empty($this->options->lockFileName) )
		{
			return true;
		}

		$this->unlock();
	}

	protected function createCollection( $path )
	{
		global $user, $tikilib;
		global $filegallib; require_once('lib/filegals/filegallib.php');

		if ( empty( $path ) )
			return false;

		if ( substr( $path, -1, 1 ) === '/' ) $path = substr( $path, 0, -1 );

		if ( ( $objectId = $filegallib->get_objectid_from_virtual_path( dirname( $path ) ) ) === false || $objectId['type'] != 'filegal' )
			return false;

		// Get parent filegal info as a base
		$filegalInfo = $filegallib->get_file_gallery_info($objectId['id']);

		$filegalInfo['galleryId'] = -1;
		$filegalInfo['parentId'] = $objectId['id'];
		$filegalInfo['name'] = basename( $path );
		$filegalInfo['description'] = '';
		$filegalInfo['user'] = $user;

		return (bool) $filegallib->replace_file_gallery($filegalInfo);
	}

	protected function createResource( $path, $content = null )
	{
		return true;
	}
	protected function _createResource( $path, $content = null )
	{
		global $user, $tikilib, $prefs;
		global $filegallib; require_once('lib/filegals/filegallib.php');

		print_debug("createResource: $path\n");
		if ( empty($path)
				|| substr($path, -1, 1) == '/'
				|| ( $objectId = $filegallib->get_objectid_from_virtual_path( dirname( $path ) ) ) === false
				|| $objectId['type'] != 'filegal'
			 ) {
				print_debug("createResource:  failed\n");	
				return false;
		}

		$name = basename( $path );
		if ( empty($content) ) $content = '';

		include_once('lib/mime/mimelib.php');
		if ( $prefs['fgal_use_db'] === 'n' ) {
			$fhash = md5( $name );
			do
			{
				$fhash = md5( uniqid( $fhash ) );
			}
			while ( file_exists( $this->root . '/' . $fhash ) );

			if ( @file_put_contents( $this->root . '/' . $fhash, $content ) === false ) {
				print_debug("createResource: ". $this->root . '/' . $fhash ." failed\n");	
				return false;
			}
			$mime = tiki_get_mime($name, 'application/octet-stream', $this->root . '/' . $fhash);
		} else {
			$fhash = '';
			$mime = tiki_get_mime_from_content($content,'application/octet-stream', $name);
		}

		$fileId = $filegallib->insert_file(
				$objectId['id'],
				$name,
				'',
				$name,
				$content,
				@strlen( $content ),
				$mime,
				$user,
				$fhash,
				'',
				$user
				);
		print_debug("createResource: end fileID=$fileId\n");	
		return $fileId != 0;
	}

	protected function setResourceContents( $path, $content )
	{
		global $user, $tikilib, $prefs;
		global $filegallib; require_once('lib/filegals/filegallib.php');

		if ( empty($path) || substr($path, -1, 1) == '/' ) {
			print_debug("\nsetResourceContents failed empty path or directory\n");
			return false;
		}
		if ( ( $objectId = $filegallib->get_objectid_from_virtual_path( $path ) ) === false ) {
			print_debug("\nCreateResouce new $path\n");
			return $this->_createResource($path, $content ) ;
		}

		if ($objectId['type'] != 'file') {
			print_debug("\nsetResourceContents failed : destination is not a file\n");
			return false;
		}

		include_once('lib/mime/mimelib.php');
		$name = basename( $path );
		if ( empty($content) ) $content = '';

		if ( $prefs['fgal_use_db'] === 'n' ) {
			$fhash = md5( $name );
			do
			{
				$fhash = md5( uniqid( $fhash ) );
			}
			while ( file_exists( $this->root . '/' . $fhash ) );

			$mime = tiki_get_mime($name,  'application/octet-stream', $this->root . '/' . $fhash);
		} else {
			$fhash = '';
			$mime = tiki_get_mime_from_content($content,'application/octet-stream', $name);
		}

		print_debug("setResourceContents : $path/$fhash \n");

		$fileInfo = $filegallib->get_file_info($objectId['id'], false, false);
		$filegalInfo = $filegallib->get_file_gallery_info($fileInfo['galleryId']);

		if ( $prefs['fgal_use_db'] === 'n' && @file_put_contents( $this->root . '/' . $fhash, $content ) === false ) {
			return false;
		}

		$fileId = $filegallib->replace_file(
				$objectId['id'],
				$fileInfo['name'],
				$fileInfo['description'],
				$fileInfo['filename'],
				$content,
				@strlen( $content ),
				$mime,
				$user,
				$fhash,
				'',
				$filegalInfo,
				true
				);
		print_debug("setResourceContents: fileId = $fileId end \n");	
		return $fileId;
	}

	protected function getResourceContents( $path )
	{
		global $tikilib, $prefs;
		global $filegallib; require_once('lib/filegals/filegallib.php');

		$result = false;
		$objectId = $filegallib->get_objectid_from_virtual_path( $path );

		if ( $objectId !== false && $objectId['type'] == 'file' )
		{
			if ($prefs['feature_file_galleries_save_draft'] == 'y') {
				$fileInfo = $filegallib->get_file_draft($objectId['id']);
			} else {
				$fileInfo = $filegallib->get_file($objectId['id']);
			}
			if ( empty($fileInfo['path']) ) {
				return $fileInfo['data'];
			} else {
				$result = $this->root . '/' . $fileInfo['path'];

				if ( ! file_exists($result) )
					return false;
			}
			return file_get_contents( $result );
		}
	}

	///TODO
	protected function getPropertyStorage( $path )
	{
		//print_debug("getPropertyStorage method \n");
		$storagePath = $this->options->propertyStoragePath.'/properties-'.md5($path);
		// If no properties has been stored yet, just return an empty property
		// storage.
		if ( !is_file( $storagePath ) )
		{
			return new ezcWebdavBasicPropertyStorage();
		}

		// Create handler structure to read properties
		$handler = new ezcWebdavPropertyHandler(
				$xml = new ezcWebdavXmlTool()
				);
		$storage = new ezcWebdavBasicPropertyStorage();

		// Read document
		try
		{
			$doc = $xml->createDom( file_get_contents( $storagePath ) );
		}
		catch ( ezcWebdavInvalidXmlException $e )
		{
			throw new ezcWebdavFileBackendBrokenStorageException(
					"Could not open XML as DOMDocument: '{$storage}'."
					);
		}

		// Get property node from document
		$properties = $doc->getElementsByTagname( 'properties' )->item( 0 )->childNodes;

		// Extract and return properties
		$handler->extractProperties(
				$properties,
				$storage
				);


		print_debug("getPropertyStorage method end " . print_r($properties,true) ."\n");
		return $storage;
	}

	protected function storeProperties( $path, ezcWebdavBasicPropertyStorage $storage )
	{
		$storagePath = $this->options->propertyStoragePath.'/properties-'.md5($path);
		print_debug("storeProperties method $storagePath\n");

		// Create handler structure to read properties
		$handler = new ezcWebdavPropertyHandler(
				$xml = new ezcWebdavXmlTool()
				);

		// Create new dom document with property storage for one namespace
		$doc = new DOMDocument( '1.0' );

		$properties = $doc->createElement( 'properties' );
		$doc->appendChild( $properties );

		$handler->serializeProperties(
				$storage,
				$properties
				);

		print_debug("storeProperties method end\n");

		return $doc->save( $storagePath );
	}

	///TODO
	public function setProperty( $path, ezcWebdavProperty $property )
	{
		print_debug("setProperty method PATH=$path PROPERTY:".$property->name."\n");
		// Check if property is a self handled live property and return an
		// error in this case.
		if ( ( $property->namespace === 'DAV:' ) &&
				in_array( $property->name, $this->handledLiveProperties, true ) &&
				( $property->name !== 'getcontenttype' ) &&
				( $property->name !== 'lockdiscovery' ) )
		{
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
		print_debug("removeProperty method\n");
		// Live properties may not be removed.
		if ( $property instanceof ezcWebdavLiveProperty )
		{
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
		print_debug("resetProperties method\n");
		$this->storeProperties( $path, $storage );
		return true;
	}

	public function getProperty( $path, $propertyName, $namespace = 'DAV:' )
	{
		global $tikilib, $prefs;
		global $filegallib; include_once('lib/filegals/filegallib.php');

		print_debug("GetProperty($path, $propertyName, $namespace)\n");
		if ( ( $objectId = $filegallib->get_objectid_from_virtual_path($path) ) === false ) {
			return false;
		}

		$isCollection = ( $objectId['type'] == 'filegal' );

		if ( isset($this->resourceStorage[$objectId['id']]) ) {
			// Use cached values
			$tikiInfo = $this->resourceStorage[$objectId['id']];
		} else {
			if ( $isCollection ) {
				$tikiInfo = $filegallib->get_file_gallery_info($objectId['id']);
			} else {
				if ($prefs['feature_file_galleries_save_draft'] == 'y') {
					$tikiInfo = $filegallib->get_file_info($objectId['id'], true, true, true);
				} else {
					$tikiInfo = $filegallib->get_file_info($objectId['id']);
				}
			}

			$this->resourceStorage[$objectId['id']] = $tikiInfo;
		}

		$storage = $this->getPropertyStorage( $path );

		$properties = $storage->getAllProperties();
		// Handle dead propreties
		if ( $namespace !== 'DAV:' )
		{
			return $properties[$namespace][$propertyName];
		}

		// Handle live properties
		switch ( $propertyName )
		{
			case 'getcontentlength':
				$property = new ezcWebdavGetContentLengthProperty(
						$isCollection ?
						ezcWebdavGetContentLengthProperty::COLLECTION :
						$tikiInfo['filesize']
						);
				return $property;

			case 'getlastmodified':
				$property = new ezcWebdavGetLastModifiedProperty( new ezcWebdavDateTime(
							'@' . (int)$tikiInfo['lastModif']
							) );
				print_debug("-> " . $tikiInfo['lastModif'] ."\n");
				return $property;

			case 'creationdate':
				$property = new ezcWebdavCreationDateProperty( new ezcWebdavDateTime(
							'@' . (int)$tikiInfo['created']
							) );
				print_debug("-> " . $tikiInfo['created'] ."\n");
				return $property;

			case 'displayname':
				$property = new ezcWebdavDisplayNameProperty(
						$isCollection ? $tikiInfo['name'] : $tikiInfo['filename']
						);
				print_debug("-> " . ($isCollection ? $tikiInfo['name'] : $tikiInfo['filename']) ."\n");
				return $property;

			case 'getcontenttype':
				$property = new ezcWebdavGetContentTypeProperty(
						$isCollection ?
						'httpd/unix-directory' :
						( empty($tikiInfo['filetype']) ? 'application/octet-stream' : $tikiInfo['filetype'] )
						);
				print_debug("-> " . ( $isCollection ?  'httpd/unix-directory' : ( empty($tikiInfo['filetype']) ? 'application/octet-stream' : $tikiInfo['filetype'] ) ) ."\n" . print_r($property,true) . "\n");
				return $property;

			case 'getetag':
				$md5 = ( $isCollection || empty($tikiInfo['hash']) ) ? md5($path) : $tikiInfo['hash'];
				$property = new ezcWebdavGetEtagProperty(
						'"' . $md5 . '-' . crc32($md5) . '"'
						);
				print_debug("-> " . '"' . $md5 . '-' . crc32($md5) . '"' ."\n");
				return $property;

			case 'resourcetype':
				$property = new ezcWebdavResourceTypeProperty(
						$isCollection ?
						ezcWebdavResourceTypeProperty::TYPE_COLLECTION :
						ezcWebdavResourceTypeProperty::TYPE_RESOURCE
						);
				print_debug("-> " . ( $isCollection ? 'TYPE_COLLECTION' : 'TYPE_RESOURCE' ) ."\n");
				return $property;

			case 'supportedlock':
				if ( !isset($properties[$namespace][$propertyName]) ) {
					$property = new ezcWebdavLockDiscoveryProperty();
				} else {
					$property = $properties[$namespace][$propertyName];
				}
				print_debug("-> " . print_r($property, true) . "\n");
				return $property;

			case 'lockdiscovery':
				if ( !isset($properties[$namespace][$propertyName]) ) {
					$property = new ezcWebdavLockDiscoveryProperty();
				} else {
					$property = $properties[$namespace][$propertyName];
				}
				print_debug("-> " . print_r($property, true) . "\n");
				return $property;

			default:
				// Handle all other live properties like dead properties
				$properties = $storage->getAllProperties();
				return $properties[$namespace][$propertyName];
		}
	}

	private function getContentLength( $path )
	{
		$contentlength = $this->getProperty( $path, 'getcontentlength' );
		print_debug("getContentLength $path". $getcontentlength->contentlength ."\n");
		return $getcontentlength->contentlength;
	}

	protected function getETag( $path )
	{
		if ( $etag = $this->getProperty( $path, 'getetag' ) ) {
			return $etag->etag;
		} else {
			return md5($path);
		}
	}

	public function getAllProperties( $path )
	{
		$storage = $this->getPropertyStorage( $path );

		// Add all live properties to stored properties
		foreach ( $this->handledLiveProperties as $property )
		{
				$storage->attach( $this->getProperty( $path, $property ) );
				$this->storeProperties( $path, $storage );
		}

		return $storage;
	}

	protected function performCopy( $fromPath, $toPath, $depth = ezcWebdavRequest::DEPTH_INFINITY )
	{
		global $prefs, $filegallib, $tikilib, $user;

		$infos = array( 'source' => array(), 'dest' => array() );
		$infos['dest']['name'] = basename( $toPath );
		$source = $fromPath;
		$dest = $toPath;

		foreach ( array( 'source', 'dest' ) as $k )
		{
			// Get source and dest infos
			if ( ( $infos[$k] = $filegallib->get_objectid_from_virtual_path( $$k ) ) !== false )
			{
				switch ( $infos[$k]['type'] )
				{
					case 'filegal':
						$infos[$k]['infos'] = $filegallib->get_file_gallery_info( $infos[$k]['id'] );
						$infos[$k]['parentId'] = $infos[$k]['infos']['parentId'];
						$infos[$k]['name'] = $infos[$k]['infos']['name'];
						break;

					case 'file':
						$infos[$k]['infos'] = $filegallib->get_file( $infos[$k]['id'] );
						$infos[$k]['parentId'] = $infos[$k]['infos']['galleryId'];
						$infos[$k]['name'] = $infos[$k]['infos']['filename'];
						break;
				}
			}
			// If dest doesn't exist, it usually means that the file / filegal has to be renamed
			elseif ( $k == 'dest' )
			{
				if ( ( $objectId = $filegallib->get_objectid_from_virtual_path( dirname( $$k ) ) ) !== false
						&& $objectId['type'] == 'filegal'
					 )
				{
					$infos[$k] = array(
							'id' => $infos['source']['id'],
							'type' => $infos['source']['type'],
							'infos' => $infos['source']['infos'],
							'parentId' => $objectId['id'],
							'name' => basename( $$k )
							);

					switch ( $infos[$k]['type'] )
					{
						case 'filegal':
							$infos[$k]['infos']['name'] = $infos[$k]['name'];
							$infos[$k]['infos']['parentId'] = $infos[$k]['parentId'];
							break;

						case 'file':
							$infos[$k]['infos']['name'] = $infos[$k]['name'];
							$infos[$k]['infos']['filename'] = $infos[$k]['name'];
							$infos[$k]['infos']['galleryId'] = $infos[$k]['parentId'];
							break;
					}

					$doRename = true;
				}
			}
			// If source doesn't exist, we stop here
			else
			{
				return false;
			}
		}

		$doMove = $infos['source']['parentId'] != $infos['dest']['parentId'];

		switch ( $infos['source']['type'] )
		{
			case 'filegal':
				// Duplicate
				$newId = $filegallib->duplicate_file_gallery( $infos['source']['id'], $infos['dest']['name'] );

				if ( ((bool) $newId ) !== true )
				{
					return false;
				}

				if ( $doMove )
				{
					// Move in an other gallery
					return (bool) $filegallib->move_file_gallery(
							$newId,
							$infos['dest']['parentId']
							);
				}

				return true;

			case 'file':
				if ( $prefs['fgal_use_db'] === 'n' ) {
					$newPath = md5( $infos['dest']['name'] );
					do
					{
						$newPath = md5( uniqid( $newPath ) );
					}
					while ( file_exists( $this->root . '/' . $newPath ) );

					if ( @copy( $this->root . '/' . $infos['source']['infos']['path'] , $this->root . '/' . $newPath ) === false )
					{
						return false;
					}
				} else {
					$newPath = '';
				}

				$newId = $filegallib->insert_file(
						$infos['source']['parentId'],
						$infos['dest']['name'],
						$infos['source']['infos']['description'],
						$infos['dest']['name'],
						$infos['source']['infos']['data'],
						$infos['source']['infos']['filesize'],
						$infos['source']['infos']['filetype'],
						$user,
						$newPath,
						'',
						$infos['source']['infos']['author'],
						$infos['source']['infos']['created'],
						$infos['source']['infos']['lockedby']
						);

				if ( ((bool) $newId ) !== true )
				{
					return false;
				}

				if ( $doMove )
				{
					return (bool) $filegallib->set_file_gallery(
							$newId,
							$infos['dest']['parentId']
							);
				}

				return true;
		}

		return false;
	}

	protected function performDelete( $path )
	{
		global $filegallib; include_once('lib/filegals/filegallib.php');

		if ( ( $objectId = $filegallib->get_objectid_from_virtual_path($path) ) === false )
			return false;

		switch ( $objectId['type'] )
		{
			case 'file': return (bool) $filegallib->remove_file( $filegallib->get_file($objectId['id']) );
			case 'filegal': return (bool) $filegallib->remove_file_gallery($objectId['id']);
		}

		return false;
	}

	protected function nodeExists( $path )
	{
		global $filegallib; include_once('lib/filegals/filegallib.php');
		return $filegallib->get_objectid_from_virtual_path($path) !== false;
	}

	protected function isCollection( $path )
	{
		global $filegallib; include_once('lib/filegals/filegallib.php');
		return ( $objectId = $filegallib->get_objectid_from_virtual_path($path) ) !== false && $objectId['type'] == 'filegal';
	}

	protected function getCollectionMembers( $path )
	{
		global $tikilib;
		global $filegallib; include_once('lib/filegals/filegallib.php');

		$contents = array();
		$errors = array();


		$galleryId = ( $objectId = $filegallib->get_objectid_from_virtual_path($path) ) !== false ? $objectId['id'] : false;

		print_debug("-> getCollectionMembers\ngalleryId:$galleryId\n");
		if ( $galleryId !== false ) {
			if ( $gal_info = $filegallib->get_file_gallery((int)$galleryId )) {
				$tikilib->get_perm_object($galleryId, 'file gallery', $gal_info);
			}

			$files = $filegallib->get_files( 0
					, -1
					, 'name_desc'
					, ''
					, (int)$galleryId
					, true
					, true
					, false
					, true
					, false
					, false
					, false
					, false
					, ''
					, true
					, false
					, ($gal_info['show_backlinks']!='n')
					, ''
					, ''
					);


			foreach ( $files['data'] as $fileInfo ) {
				if ( $fileInfo['isgal'] == '1' ) {
					// Add collection without any children
					$contents[] = new ezcWebdavCollection( $path . $fileInfo['name'] . '/' );
				} else {
					// Add files without content
					//$contents[] = new ezcWebdavResource( $path . $fileInfo['name'] . ( $fileInfo['nbArchives'] > 0 ? "?".$fileInfo['nbArchives'] : '') );
					$contents[] = new ezcWebdavResource( $path . $fileInfo['filename'] );
				}
			}
		}

		print_debug("getCollectionMembers ".print_r($contents,true). "\n");
		return $contents;
	}

	public function get( ezcWebdavGetRequest $request )
	{
		print_debug("-- HTTP method: GET --\n");
		$return = parent::get( $request );

		return $return;
	}

	public function head( ezcWebdavHeadRequest $request )
	{
		print_debug("-- HTTP method: HEAD --\n");
		$return = parent::head( $request );

		return $return;
	}

	public function propFind( ezcWebdavPropFindRequest $request )
	{
		print_debug("-- HTTP method: PROPFIND --\n");
		$return = parent::propFind( $request );

		return $return;
	}

	public function propPatch( ezcWebdavPropPatchRequest $request )
	{
		print_debug("-- HTTP method: PROPPATCH --\n");
		$this->acquireLock();
		$return = parent::propPatch( $request );
		$this->freeLock();

		return $return;
	}

	public function put( ezcWebdavPutRequest $request )
	{
		print_debug("-- HTTP method: PUT --\n");
		$this->acquireLock();
		$return = parent::put( $request );
		$this->freeLock();

		return $return;
	}

	public function delete( ezcWebdavDeleteRequest $request )
	{
		print_debug("-- HTTP method: DELETE --".print_r($request,true)."\n");
		$this->acquireLock();
		$return = parent::delete( $request );
		$this->freeLock();

		return $return;
	}

	public function copy( ezcWebdavCopyRequest $request )
	{
		print_debug("-- HTTP method: COPY --\n");
		$this->acquireLock();
		$return = parent::copy( $request );
		$this->freeLock();

		return $return;
	}

	public function move( ezcWebdavMoveRequest $request )
	{
		global $tikilib, $prefs;
		global $filegallib; include_once('lib/filegals/filegallib.php');

		print_debug("-- HTTP method: MOVE --\n");

		$this->acquireLock();

		// Indicates wheather a destiantion resource has been replaced or not.
		// The success response code depends on this.
		$replaced = false;

		// Extract paths from request
		$source = $request->requestUri;
		$dest = $request->getHeader( 'Destination' );

		// Check authorization
		// Need to do this before checking of node existence is checked, to
		// avoid leaking information 

		if ( !ezcWebdavServer::getInstance()->isAuthorized( $dest, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
		{
			$this->freeLock();
			return $this->createUnauthorizedResponse(
					$dest,
					$request->getHeader( 'Authorization' )
					);
		}

		// Check if resource is available
		if ( !$this->nodeExists( $source ) )
		{
			$this->freeLock();
			return new ezcWebdavErrorResponse(
					ezcWebdavResponse::STATUS_404,
					$source
					);
		}

		// If source and destination are equal, the request should always fail.
		if ( $source === $dest )
		{
			$this->freeLock();
			return new ezcWebdavErrorResponse(
					ezcWebdavResponse::STATUS_403,
					$source
					);
		}

		// Check if destination resource exists and throw error, when
		// overwrite header is F
		if ( ( $request->getHeader( 'Overwrite' ) === 'F' ) &&
				$this->nodeExists( $dest ) )
		{
			$this->freeLock();
			return new ezcWebdavErrorResponse(
					ezcWebdavResponse::STATUS_412,
					$dest
					);
		}

		// Check if the destination parent directory already exists, otherwise
		// bail out.
		if ( !$this->nodeExists( $destDir = dirname( $dest ) ) )
		{
			$this->freeLock();
			return new ezcWebdavErrorResponse(
					ezcWebdavResponse::STATUS_409,
					$dest
					);
		}

		// Verify If-[None-]Match headers on the $dest if it exists
		if ( $this->nodeExists( $dest ) &&
				( $res = $this->checkIfMatchHeaders( $request, $dest ) ) !== null
			 )
		{
			$this->freeLock();
			return $res;
		}
		// Verify If-[None-]Match headers on the on $dests parent dir, if it
		// does not exist
		elseif ( ( $res = $this->checkIfMatchHeaders( $request, $destDir ) ) !== null )
		{
			$this->freeLock();
			return $res;
		}

		// The destination resource should be deleted if it exists and the
		// overwrite headers is T
		if ( ( $request->getHeader( 'Overwrite' ) === 'T' ) &&
				$this->nodeExists( $dest ) )
		{
			// Check sub-sequent authorization on destination
			$authState = $this->recursiveAuthCheck(
					$request,
					$dest,
					ezcWebdavAuthorizer::ACCESS_WRITE,
					true
					);
			if ( count( $authState['errors'] ) !== 0 )
			{
				// Permission denied on deleting destination
				$this->freeLock();
				return $authState['errors'][0];
			}

			$replaced = true;

			if ( count( $delteErrors = $this->performDelete( $dest ) ) > 0 )
			{
				$this->freeLock();
				return new ezcWebdavMultistatusResponse( $delteErrors );
			}
		}

		// All checks are passed, we can actually move now.

		$infos = array();
		$doRename = false;
		$doMove = false;

		foreach ( array( 'source', 'dest' ) as $k )
		{
			// Get source and dest infos
			if ( ( $infos[$k] = $filegallib->get_objectid_from_virtual_path( $$k ) ) !== false )
			{
				switch ( $infos[$k]['type'] )
				{
					case 'filegal':
						$infos[$k]['infos'] = $filegallib->get_file_gallery_info( $infos[$k]['id'] );
						$infos[$k]['parentId'] = $infos[$k]['infos']['parentId'];
						$infos[$k]['name'] = $infos[$k]['infos']['name'];
						break;

					case 'file':
						///TODO: Throw an error if dest is a file, but source is a filegal

						$infos[$k]['infos'] = $filegallib->get_file( $infos[$k]['id'] );
						$infos[$k]['parentId'] = $infos[$k]['infos']['galleryId'];
						$infos[$k]['name'] = $infos[$k]['infos']['filename'];
						break;
				}
			}
			// If dest doesn't exist, it usually means that the file / filegal has to be renamed
			elseif ( $k == 'dest' )
			{
				///TODO: Throw an error if dest is a new filegal, but source is a file

				if ( ( $objectId = $filegallib->get_objectid_from_virtual_path( dirname( $$k ) ) ) !== false
						&& $objectId['type'] == 'filegal'
					 )
				{
					$infos[$k] = array(
							'id' => $infos['source']['id'],
							'type' => $infos['source']['type'],
							'infos' => $infos['source']['infos'],
							'parentId' => $objectId['id'],
							'name' => basename( $$k )
							);

					switch ( $infos[$k]['type'] )
					{
						case 'filegal':
							$infos[$k]['infos']['name'] = $infos[$k]['name'];
							$infos[$k]['infos']['parentId'] = $infos[$k]['parentId'];
							break;

						case 'file':
							$infos[$k]['infos']['name'] = $infos[$k]['name'];
							$infos[$k]['infos']['filename'] = $infos[$k]['name'];
							$infos[$k]['infos']['galleryId'] = $infos[$k]['parentId'];
							break;
					}

					$doRename = true;
				}
			}
			// If source doesn't exist, we stop here
			else
			{
				break;
			}
		}

		$doMove = $infos['source']['parentId'] != $infos['dest']['parentId'];
		$noErrors = true;

		switch ( $infos['source']['type'] )
		{
			case 'filegal':

				if ( $doRename )
				{
					$noErrors = (bool) $filegallib->replace_file_gallery(
							$infos['dest']['infos']
							);
				}
				// Move is not needed if the rename occured, since filegal renaming function handle the move already
				elseif ( $doMove )
				{
					$noErrors = (bool) $filegallib->move_file_gallery(
							$infos['source']['id'],
							$infos['dest']['parentId']
							);
				}

				break;

			case 'file':

				if ( $doRename )
				{
					if ( $prefs['fgal_use_db'] === 'n' ) {
						$newPath = md5( $infos['dest']['name'] );
						do
						{
							$newPath = md5( uniqid( $newPath ) );
						}
						while ( file_exists( $this->root . '/' . $newPath ) );

						if ( ( @rename( $this->root . '/' . $infos['source']['infos']['path'] , $this->root . '/' . $newPath ) === false )
								|| ( @file_put_contents( $this->root . '/' . $infos['source']['infos']['path'], '' ) === false )
							 )
						{
							$this->freeLock();
							return false;
						}
					} else {
						$newPath = '';
					}

					$noErrors = (bool) $filegallib->replace_file(
							$infos['source']['id'],
							$infos['dest']['name'],
							$infos['source']['infos']['description'],
							$infos['dest']['name'],
							$infos['source']['infos']['data'],
							$infos['source']['infos']['filesize'],
							$infos['source']['infos']['filetype'],
							$user,
							$newPath,
							'',
							$filegallib->get_file_gallery_info( $infos['source']['parentId'] ),
							false,
							$infos['source']['infos']['author'],
							$infos['source']['infos']['created'],
							$infos['source']['infos']['lockedby']
							);
				}

				if ( $doMove && $noErrors )
				{
					$noErrors = (bool) $filegallib->set_file_gallery(
							$infos['source']['id'],
							$infos['dest']['parentId']
							);
				}

				break;
		}
		$this->freeLock();

		// Send proper response on success
		if ( $noErrors )
		{
			$return = new ezcWebdavMoveResponse(
					$replaced
					);
		}
		else
		{
			$return = new ezcWebdavErrorResponse(
					ezcWebdavResponse::STATUS_500
					);
		}


		print_debug("-- HTTP method: MOVE end --\n");
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
}
