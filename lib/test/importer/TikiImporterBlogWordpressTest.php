<?php

require_once(dirname(__FILE__) . '/tikiimporter_testcase.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter_blog_wordpress.php');

/** 
 * @group importer
 */
class TikiImporter_Blog_Wordpress_Test extends TikiImporter_TestCase
{

    protected function setUp()
    {
		date_default_timezone_set('UTC');
        $this->obj = new TikiImporter_Blog_Wordpress;
    }

    public function testImport()
    {
        $parsedData = 'Some text';

        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('validateInput', 'extractBlogInfo', 'parseData', 'insertData', 'setupTiki', 'extractPermalinks'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('extractBlogInfo')->will($this->returnValue(array()));
        $obj->expects($this->once())->method('parseData')->will($this->returnValue($parsedData));
        $obj->expects($this->once())->method('insertData')->with($parsedData);
        $obj->expects($this->once())->method('setupTiki');
        $obj->expects($this->exactly(0))->method('extractPermalinks');

        $this->expectOutputString("Loading and validating the XML file\n\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>");
        $_FILES['importFile']['type'] = 'text/xml'; 
        $obj->import(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
        unset($_FILES['importFile']);

        $this->assertTrue($obj->dom instanceof DOMDocument);
        $this->assertTrue($obj->dom->hasChildNodes());
	}
	
	public function testImportShouldHandleAttachments()
    {
        $parsedData = 'Some text';

        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('validateInput', 'extractBlogInfo', 'parseData', 'insertData', 'downloadAttachments', 'setupTiki', 'extractPermalinks'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('extractBlogInfo')->will($this->returnValue(array()));
        $obj->expects($this->once())->method('parseData')->will($this->returnValue($parsedData));
        $obj->expects($this->once())->method('insertData')->with($parsedData);
        $obj->expects($this->once())->method('downloadAttachments');
        $obj->expects($this->once())->method('setupTiki');
        $obj->expects($this->once())->method('extractPermalinks');
        $_POST['importAttachments'] = 'on';
        $_POST['replaceInternalLinks'] = 'on';

        $obj->import(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
        
        unset($_POST['importAttachments']);
        unset($_POST['replaceInternalLinks']);
    }

    public function testParseData()
    {
        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractItems', 'extractTags', 'extractCategories'));
        $obj->expects($this->once())->method('extractItems')->will($this->returnValue(array('posts' => array(), 'pages' => array())));
		$this->expectOutputString("\nExtracting data from XML file:\n");
		$parsedData = $obj->parseData();
        $this->assertEquals(4, count($parsedData));
	}
	
	public function testExtractPermalinks()
	{
		$this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
        $this->obj->blogInfo['link'] = 'http://example.com';
        
        $expectedResult = array(
        	107 => array(
        		'oldLinks' => array(
        			'http://example.com/2007/03/11/materia-sobre-a-viagem-de-bicicleta-entre-as-chapadas/',
        			'/2007/03/11/materia-sobre-a-viagem-de-bicicleta-entre-as-chapadas/',
        			'http://example.com/?p=107',
        			'/?p=107',
        		),
        	),
        	36 => array(
        		'oldLinks' => array(
	        		'http://example.com/2008/01/20/circuito-grande-torres-del-paine/',
	        		'/2008/01/20/circuito-grande-torres-del-paine/',
	        		'http://example.com/?p=36',
	        		'/?p=36',
        		),
        	),
        	73 => array(
        		'oldLinks' => array(
	        		'http://example.com/2008/02/23/lo-mas-importante-son-los-veinte/',
	        		'/2008/02/23/lo-mas-importante-son-los-veinte/',
	        		'http://example.com/?p=73',
	        		'/?p=73',
        		),
        	),
        	10 => array(
        		'oldLinks' => array(
	        		'http://example.com/2009/05/04/como-impedir-que-o-editor-do-wordpress-tinymce-remova-quebras-de-linha/',
	        		'/2009/05/04/como-impedir-que-o-editor-do-wordpress-tinymce-remova-quebras-de-linha/',
	        		'http://example.com/?p=10',
	        		'/?p=10',
        		),
        	),
        );
        
        $this->assertEquals($expectedResult, $this->obj->extractPermalinks());
	}

	public function testIdentifyInternalLinks()
	{   
		$this->obj->permalinks = array(
        	107 => array(
        		'oldLinks' => array(
	        		'http://example.com/2007/03/11/materia-sobre-a-viagem-de-bicicleta-entre-as-chapadas/',
	        		'http://example.com/?p=107',
        		),
        	),
        	36 => array(
        		'oldLinks' => array(
	        		'http://example.com/2008/01/20/circuito-grande-torres-del-paine/',
	        		'http://example.com/?p=36',
        		),
        	),
        );
		
		$item['wp_id'] = 10;
        $item['content'] = 'Continuação do post sobre o uso de bicicletas na Europa. <a href="http://example.com/2007/03/11/materia-sobre-a-viagem-de-bicicleta-entre-as-chapadas/">Teste</a> E continua o texto por aqui.';
        $this->assertTrue($this->obj->identifyInternalLinks($item));
        
        $item['wp_id'] = 11;
        $item['content'] = 'Continuação do post sobre o uso de bicicletas na Europa. <a href="http://example.com/2007/03/11/outra-materia/">Teste</a> E continua o texto por aqui.';
        $this->assertFalse($this->obj->identifyInternalLinks($item));
	}
	
	public function testExtractItems()
	{
        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractInfo'));
        $obj->dom = new DOMDocument;
        $obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
        $obj->expects($this->exactly(4))->method('extractInfo')->will($this->returnValue(array()));
        
        $expectedResult = array(
        	'posts' => array(array(), array(), array()),
        	'pages' => array(array()),
        );
        
        $this->assertEquals($expectedResult, $obj->extractItems());
	}

	public function testExtractTags()
	{
		$expectedResult = array('alta montanha', 'barcelona', 'bicicleta', 'bicicletada', 'buenos aires', 'caminhada', 'canadá', 'carga',
			'cerro plata', 'chapada diamantina', 'chapada dos veadeiras', 'chile', 'cicloativismo', 'cicloturismo', 'cidade', 'cidades',
			'comida', 'conhecimento livre', 'creative commons', 'davi marski', 'debate', 'die-in', 'digikam', 'dmsc', 'dmsc2010',
			'el chaltén', 'eleições', 'escalada', 'europa', 'exiv2', 'filme', 'fotos', 'gelo', 'gettext', 'ghost bike', 'gsoc', 'hacklab',
			'januária', 'linux', 'livros', 'londres', 'mapas', 'mediawiki', 'mendoza', 'montanhismo', 'montreal', 'mudanças', 'null tag name', 'osorno',
			'parser', 'partidos políticos', 'patagônia', 'pear', 'php', 'phpbb', 'phpdocumentor', 'phpt', 'phpunit', 'plugin',
			'quinta livre', 'restaurantes vegetarianos', 'san pedro de atacama', 'santiago', 'são paulo', 'software livre', 'Text_Wiki',
			'tikifest', 'tikiwiki', 'tinymce', 'torres del paine', 'transporte', 'trekking', 'tv', 'ubuntu', 'unit tests', 'usp',
			'vegetarianismo', 'vídeo', 'vulcão', 'vulcão maipo', 'wiki', 'wordpress', 'youtube');

		$this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		
		$this->assertEquals($expectedResult, $this->obj->extractTags());
	}
	
	public function testExtractCategories()
	{
		$expectedResult = array(
			array('parent' => '', 'name' => 'bicicleta', 'description' => 'Qualquer descrição'),
			array('parent' => 'bicicleta', 'name' => 'cicloativismo', 'description' => ''),
			array('parent' => 'bicicleta', 'name' => 'cicloturismo', 'description' => ''),
			array('parent' => '', 'name' => 'hacklab', 'description' => ''),
			array('parent' => '', 'name' => 'montanhismo', 'description' => ''),
			array('parent' => '', 'name' => 'Sem categoria', 'description' => ''),
			array('parent' => '', 'name' => 'software livre', 'description' => ''),
			array('parent' => '', 'name' => 'Uncategorized', 'description' => ''),
			array('parent' => '', 'name' => 'vegetarianismo', 'description' => ''),
			array('parent' => '', 'name' => 'viagens', 'description' => ''),
			array('parent' => 'viagens', 'name' => 'argentina', 'description' => 'Another description'),
			array('parent' => 'viagens', 'name' => 'canadá', 'description' => ''),
			array('parent' => 'viagens', 'name' => 'chile', 'description' => ''),
			array('parent' => 'viagens', 'name' => 'europa', 'description' => ''),
		);

		$this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		
		$this->assertEquals($expectedResult, $this->obj->extractCategories());
	}
	
	public function testExtractInfoPost()
	{
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractComment', 'parseContent', 'identifyInternalLinks'));
		$obj->expects($this->exactly(3))->method('extractComment')->will($this->returnValue(true));
		$obj->expects($this->any())->method('parseContent')->will($this->returnValue('Test'));
		$obj->expects($this->once())->method('identifyInternalLinks')->will($this->returnValue(true));
		
		$obj->permalinks = array('not empty');
		
		$expectedResult = array(
			'categories' => array(
				0 => 'argentina',
				1 => 'montanhismo',
			),
			'tags' => array(
				0 => 'alta montanha',
				1 => 'cerro plata',
				2 => 'mendoza',
				3 => 'montanhismo',
			),
			'comments' => array(
				0 => true,
				1 => true,
				2 => true,
			),
			'name' => 'Lo más importante son los veinte',
			'author' => 'rodrigo',
			'content' => 'Test',
			'excerpt' => '',
			'wp_id' => 73,
			'created' => '1203784780',
			'type' => 'post',
			'hasInternalLinks' => true,
		);

		$obj->dom = new DOMDocument;
		$obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_post.xml');
		$data = $obj->extractInfo($obj->dom->getElementsByTagName('item')->item(0));

		$this->assertEquals($expectedResult, $data);
	}

	public function testExtractInfoPage()
	{
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractComments', 'parseContent', 'identifyInternalLinks'));
		$obj->expects($this->exactly(0))->method('extractComment')->will($this->returnValue(true));
		$obj->expects($this->any())->method('parseContent')->will($this->returnValue('Test'));
		$obj->expects($this->once())->method('identifyInternalLinks')->will($this->returnValue(true));
		
		$obj->permalinks = array('not empty');
		
		$expectedResult = array(
			'categories' => array(
				0 => 'cicloturismo',
				1 => 'viagens',
			),
			'tags' => array(
				0 => 'chapada diamantina',
				1 => 'cicloturismo',
				2 => 'januária',
				3 => 'tv',
				4 => 'youtube',
			),
			'comments' => array(),
			'name' => 'Matéria sobre a viagem de bicicleta entre as chapadas',
			'author' => 'rodrigo',
			'content' => 'Test',
			'excerpt' => '',
			'wp_id' => 107,
			'created' => 1173636811,
			'type' => 'page',
			'hasInternalLinks' => true,
			'revisions' => array(
				array(
					'data' => 'Test',
					'lastModif' => 1173636811,
					'comment' => '',
					'user' => 'rodrigo',
					'ip' => '',
					'is_html' => true,
				)
			),
		);

		$obj->dom = new DOMDocument;
		$obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_page.xml');
		$data = $obj->extractInfo($obj->dom->getElementsByTagName('item')->item(0));

		$this->assertEquals($expectedResult, $data);
	}

	public function testExtractCommentShouldReturnFalseForSpamOrTrashOrPingback()
	{
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_comment_spam.xml');
		
		// spam
		$this->assertFalse($this->obj->extractComment($this->obj->dom->getElementsByTagName('comment')->item(0)));
		
		// trash
		$this->assertFalse($this->obj->extractComment($this->obj->dom->getElementsByTagName('comment')->item(1)));
		
		// pingback
		$this->assertFalse($this->obj->extractComment($this->obj->dom->getElementsByTagName('comment')->item(2)));
	}
	
	public function testExtractCommentShouldReturnCommentArray()
	{
		$expectedResult = array(
			'author' => 'rodrigo',
			'author_email' => 'test@test.com',
			'author_url' => '',
			'author_ip' => '127.0.0.1',
			'created' => 1250059024,
			'data' => '<a href="#comment-33" rel="nofollow">@otavio </a> 
Olá Otavio, o Torres del Paine é um parte grande e bem movimentado. Se você for no verão vai encontrar gente sempre, principalmente no W. O circuito grande é um pouco menos movimentado mas ainda sim você encontra pessoas todos os dias. As trilhas estão minimamente sinalizadas. Lembro que levei comigo a carta topográfica do parque e uma bussóla mas não cheguei a utilizá-los.

Se você for fazer apenas caminhadas não terá problemas com os equipamentos que encontra no Brasil. Botas duplas só se estiver pensando em caminhar pelo Campo de Hielo Sur ou alguma outra coisa do tipo uma caminhada de vários dias por glaciares, escalar o Cerro Torre. É importante você ter uma camada impermeável (bota, calça e anorak). Eu fui com uma bota Trilogia e gostei bastante. A calça e o anorak (da Conquista e Trilhas e Rumos, respectivamente) seguraram o tranco. O problema deles é que não respiram direito, em pouco tempo de caminhada eu começo a fever dentro deles de calor, mas paciência. Equipamentos de goretex no Brasil são muito caros e não são necessários para alguma coisa como o Torres del Paine.

Sobre fazer sozinho ou não o W depende muito de você. Depende de quanta de experiência tem. Para uma pessoa que tenha um bom conhecimento de trilhas no Brasil não vejo necessidade alguma de guia, mas isso é uma escolha individual.

Estou a disposição para te ajudar com mais informações. Abraços, Rodrigo.',
			'approved' => 1,
			'type' => '',
		);
		
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_comment.xml');
		
		$comment = $this->obj->extractComment($this->obj->dom->getElementsByTagName('comment')->item(0));
		
		$this->assertEquals($expectedResult, $comment);
	}

	public function testExtractBlogCreatedDate()
	{
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		
		$this->assertEquals(1173636811, $this->obj->extractBlogCreatedDate());
	}
	
	public function testExtractBlogInfo()
	{
		$expectedResult = array(
			'title' => 'example.com',
			'link' => 'http://example.com',
			'desc' => 'Software livre, cicloativismo, montanhismo e quem sabe permacultura',
			'lastModif' => 1284989827,
			'created' => 1173636811,
		);

		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		$this->obj->extractBlogInfo();

		$this->assertEquals($expectedResult, $this->obj->blogInfo);
	}
	
	public function testExtractAttachmentsInfo()
	{
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_attachments.xml');
		
		$expectedResult = array(
			array(
				'name' => 'Parte da tela de administração do TinyMCE Advanced',
				'link' => 'http://example.com/files/tadv2.jpg',
				'created' => '1241461850',
				'author' => 'rodrigo',
				'fileName' => 'tadv2.jpg',
				'sizes' => array(
					'thumbnail' => array(
						'name' => 'tadv2-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => 'tadv2-300x171.jpg',
						'width' => 300,
						'height' => 171,
					),
				),
			),
			array(
				'name' => 'Hostelaria Las Torres',
				'link' => 'http://example.com/files/1881232-hostelaria-las-torres-0.jpg',
				'created' => '1242095082',
				'author' => 'rodrigo',
				'fileName' => '1881232-hostelaria-las-torres-0.jpg',
				'sizes' => array(
					'thumbnail' => array(
						'name' => '1881232-hostelaria-las-torres-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881232-hostelaria-las-torres-0-300x225.jpg',
						'width' => 300,
						'height' => 225,
					),
				),
			),
			array(
				'name' => 'Caminhando no gelo no Vale do Silêncio',
				'link' => 'http://example.com/files/1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg',
				'created' => '1242095085',
				'author' => 'rodrigo',
				'fileName' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg',
				'sizes' => array(
					'thumbnail' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-225x300.jpg',
						'width' => 225,
						'height' => 300,
					),
				),
			),
		);
		
		$attachments = $this->obj->extractAttachmentsInfo();
		
		$this->assertEquals($expectedResult, $attachments);
	}
	
	public function testDownloadAttachmentsShouldDisplayMessageIfNoAttachments()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$filegallib = $this->getMock('FileGalLib', array('insert_file',));
		$filegallib->expects($this->exactly(0))->method('insert_file')->will($this->returnValue(1));
		
		$this->obj->dom = new DOMDocument;
		$this->expectOutputString("\n\nNo attachments found to import!\n");
		$this->obj->downloadAttachments(); 
	}

	function testCreateFileGallery()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$filegallib = $this->getMock('FileGalLib', array('replace_file_gallery'));
		$filegallib->expects($this->once())->method('replace_file_gallery')->will($this->returnValue(3));
		
		$this->obj->blogInfo['title'] = 'Test';
		
		$this->assertEquals(3, $this->obj->createFileGallery());
	}
	
	public function testDownloadAttachment()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$filegallib = $this->getMock('FileGalLib', array('insert_file'));
		$filegallib->expects($this->exactly(3))->method('insert_file')->will($this->returnValue(1));
				
		$adapter = new Zend_Http_Client_Adapter_Test();
		
		$adapter->setResponse(
			"HTTP/1.1 200 OK"         . "\r\n" .
			"Content-type: image/jpg" . "\r\n" .
			"Content-length: 1034"	  . "\r\n" .
										"\r\n" .
    		'empty content'
		);
		
		$client = new Zend_Http_Client();
		$client->setAdapter($adapter);
		
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('getHttpClient', 'createFileGallery'));
		$obj->expects($this->once())->method('getHttpClient')->will($this->returnValue($client));
		$obj->expects($this->once())->method('createFileGallery')->will($this->returnValue(1));
        $obj->dom = new DOMDocument;
        $obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_attachments.xml');

        $this->expectOutputString("\n\nImporting attachments:\nAttachment tadv2.jpg successfully imported!\nAttachment 1881232-hostelaria-las-torres-0.jpg successfully imported!\nAttachment 1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg successfully imported!\n3 attachments imported and 0 errors.\n");
        
        $obj->downloadAttachments();
        
        $expectedResult = array(
        	array(
        		'fileId' => 1,
        		'oldUrl' => 'http://example.com/files/tadv2.jpg',
        		'sizes' => array(
					'thumbnail' => array(
						'name' => 'tadv2-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => 'tadv2-300x171.jpg',
						'width' => 300,
						'height' => 171,
					),
				),
        	),
        	array(
        		'fileId' => 1,
        		'oldUrl' => 'http://example.com/files/1881232-hostelaria-las-torres-0.jpg',
        		'sizes' => array(
					'thumbnail' => array(
						'name' => '1881232-hostelaria-las-torres-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881232-hostelaria-las-torres-0-300x225.jpg',
						'width' => 300,
						'height' => 225,
					),
				),
        	),
        	array(
        		'fileId' => 1,
        		'oldUrl' => 'http://example.com/files/1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg',
        		'sizes' => array(
					'thumbnail' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-225x300.jpg',
						'width' => 225,
						'height' => 300,
					),
				),
        	),
        );
        
        $this->assertEquals($expectedResult, $obj->newFiles);
	}
	
	public function testDownloadAttachmentShouldNotCallInsertFileWhenZendHttpClientFails()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$filegallib = $this->getMock('FileGalLib', array('insert_file'));
		$filegallib->expects($this->exactly(0))->method('insert_file');
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$adapter->setNextRequestWillFail(true);
		
		$client = new Zend_Http_Client();
		$client->setAdapter($adapter);
		
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('getHttpClient', 'createFileGallery'));
		$obj->expects($this->once())->method('createFileGallery')->will($this->returnValue(1));
		$obj->expects($this->once())->method('getHttpClient')->will($this->returnValue($client));
        $obj->dom = new DOMDocument;
        $obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_attachments.xml');
        
        $obj->downloadAttachments();
        
        $this->assertEquals(array(), $obj->newFiles);
	}
	
	public function testDownloadAttachmentShouldNotCallInsertFileWhen404()
	{
		global $filegallib; require_once('lib/filegals/filegallib.php');
		
		$filegallib = $this->getMock('FileGalLib', array('insert_file'));
		$filegallib->expects($this->exactly(0))->method('insert_file');		
		$adapter = new Zend_Http_Client_Adapter_Test();
		
		$adapter->setResponse(
			"HTTP/1.1 404 NOT FOUND"         . "\r\n" .
			"Content-type: image/jpg" . "\r\n" .
			"Content-length: 1034"	  . "\r\n" .
										"\r\n" .
    		'empty content'
		);
		
		$client = new Zend_Http_Client();
		$client->setAdapter($adapter);
		
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('getHttpClient', 'createFileGallery'));
		$obj->expects($this->once())->method('createFileGallery')->will($this->returnValue(1));
		$obj->expects($this->once())->method('getHttpClient')->will($this->returnValue($client));
        $obj->dom = new DOMDocument;
        $obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_attachments.xml');

        $this->expectOutputString("\n\nImporting attachments:\nUnable to download attachment tadv2.jpg. Error message was: 404 NOT FOUND\nUnable to download attachment 1881232-hostelaria-las-torres-0.jpg. Error message was: 404 NOT FOUND\nUnable to download attachment 1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg. Error message was: 404 NOT FOUND\n0 attachments imported and 3 errors.\n");
        
        $obj->downloadAttachments();
        
        $this->assertEquals(array(), $obj->newFiles);
	}
	
	public function testParseContentAttachmentsUrl()
	{

		$this->obj->newFiles = array(
			array(
				'fileId' => 2,
				'oldUrl' => 'http://example.com/files/1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0.jpg',
				'sizes' => array(
					'thumbnail' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881259-caminhando-no-gelo-no-vale-do-sil-ncio-0-225x300.jpg',
						'width' => 225,
						'height' => 300,
					),
				),
			),
			array(
				'fileId' => 1,
				'oldUrl' => 'http://example.com/files/1881232-hostelaria-las-torres-0.jpg',
				'sizes' => array(
					'thumbnail' => array(
						'name' => '1881232-hostelaria-las-torres-0-150x150.jpg',
						'width' => 150,
						'height' => 150,
					),
					'medium' => array(
						'name' => '1881232-hostelaria-las-torres-0-300x225.jpg',
						'width' => 300,
						'height' => 225,
					),
				),
			),
		);
		
		$content = file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content.txt');
		
		$expectedResult = file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_parsed.txt');
		
		$this->assertEquals($expectedResult, $this->obj->parseContentAttachmentsUrl($content));
	}
	
	public function testParseContentAttachmentsUrlShouldReturnSameContentIfNewFilesIsEmpty()
	{
		$content = '';
		$this->obj->newFiles = array();
		$this->assertEquals($content, $this->obj->parseContentAttachmentsUrl($content));
	}
	
	public function testValidateInput()
	{
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		$this->assertTrue($this->obj->validateInput());
	}
	
	public function testValidateInputShouldRaiseExceptionIfInvalidFile()
	{
		$this->setExpectedException('DOMException');
		
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_invalid.xml');
		$this->obj->validateInput();
	}
	
	public function testValidateInputShouldRaiseExceptionForMediawikiFile()
	{
		$this->setExpectedException('DOMException');
		
		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/mediawiki_sample.xml');
		$this->obj->validateInput();
	}
	
	public function testMatchWordpressShortcodes()
	{
		$content = "[my-shortcode] [my-shortcode/] [my-shortcode foo='bar' bar='foo'] [my-shortcode foo='bar'/]
			[my-shortcode2]content[/my-shortcode2] [my-shortcode2 foo='bar' bar='foo']content[/my-shortcode2] 
			[my-shortcode2 foo='bar' bar='foo']\n\ncontent\n\n[/my-shortcode2] [youtube width=\"625\" height=\"517\"]http://www.youtube.com/watch?v=4UCOWCfUkKU[/youtube]";
		
		$expectedResult = array(
			array('[youtube width="625" height="517"]http://www.youtube.com/watch?v=4UCOWCfUkKU[/youtube]', 'youtube', ' width="625" height="517"', 'http://www.youtube.com/watch?v=4UCOWCfUkKU'),
			array("[my-shortcode2 foo='bar' bar='foo']\n\ncontent\n\n[/my-shortcode2]", 'my-shortcode2', " foo='bar' bar='foo'", "\n\ncontent\n\n"),
			array("[my-shortcode2 foo='bar' bar='foo']content[/my-shortcode2]", 'my-shortcode2', " foo='bar' bar='foo'", 'content'),
			array('[my-shortcode2]content[/my-shortcode2]', 'my-shortcode2', '', 'content'),
			array("[my-shortcode foo='bar' bar='foo']", 'my-shortcode', " foo='bar' bar='foo'"),
			array("[my-shortcode foo='bar'/]", 'my-shortcode', " foo='bar'"),
			array('[my-shortcode/]', 'my-shortcode', ''),
			array('[my-shortcode]', 'my-shortcode', ''),
		);
		
		$this->assertEquals($expectedResult, $this->obj->matchWordpressShortcodes($content));
	}
	
	public function testParseWordpressShortcodes()
	{
		$content = file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_shortcodes.txt');
		$expectedResult = file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_shortcodes_parsed.txt');
		$this->assertEquals($expectedResult, $this->obj->parseWordpressShortcodes($content));
	}
}
