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

        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('validateInput', 'parseData', 'insertData'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('parseData')->will($this->returnValue($parsedData));
        $obj->expects($this->once())->method('insertData')->with($parsedData);

        $this->expectOutputString("Loading and validating the XML file\n\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>");
        $obj->import(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');

        $this->assertTrue($obj->dom instanceof DOMDocument);
        $this->assertTrue($obj->dom->hasChildNodes());
	}
/*
    public function testImportWithoutInternalMocking()
    {
        global $tikilib;
        $tikilib = $this->getMock('TikiLib', array('create_page', 'update_page', 'page_exists', 'remove_all_versions'));
        $obj = $this->getMock('TikiImporter_Wiki_Mediawiki', array('saveAndDisplayLog'));
        $obj->expects($this->exactly(12))->method('saveAndDisplayLog');

        $expectedImportFeedback = array('totalPages' => 4, 'importedPages' => 4);
        
        $obj->import(dirname(__FILE__) . '/fixtures/mediawiki_sample.xml');

        $this->assertTrue($obj->dom instanceof DOMDocument);
        $this->assertTrue($obj->dom->hasChildNodes());
        $this->assertEquals($expectedImportFeedback, $_SESSION['tiki_importer_feedback']);
	}*/

/*	public function testImportShouldHandleAttachments()
    {
        $parsedData = 'Some text';

        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('validateInput', 'parseData', 'insertData', 'downloadAttachments'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('parseData')->will($this->returnValue($parsedData));
        $obj->expects($this->once())->method('insertData')->with($parsedData);
        $obj->expects($this->once())->method('downloadAttachments');
        $_POST['importAttachments'] = 'on';

        $obj->import(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
	}*/

    public function testParseData()
    {
        $obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractInfo', 'extractBlogInfo'));
        $obj->dom = new DOMDocument;
        $obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
        $obj->expects($this->exactly(5))->method('extractInfo')->will($this->returnValue(array()));
        $obj->expects($this->once())->method('extractBlogInfo')->will($this->returnValue(array()));
		$this->expectOutputString("\nStarting to parse data:\n");
		$parsedData = $obj->parseData();
        $this->assertEquals(5, count($parsedData));
	}

	public function testExtractInfoPost()
	{
		$obj = $this->getMock('TikiImporter_Blog_Wordpress', array('extractComment'));
		$obj->expects($this->exactly(3))->method('extractComment')->will($this->returnValue(true));
		
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
			'created' => '1203784780',
			'type' => 'post',
		);

		$obj->dom = new DOMDocument;
		$obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_post.xml');
		$data = $obj->extractInfo($obj->dom->getElementsByTagName('item')->item(0));

		$this->assertEquals($expectedResult, $data);
	}

	public function testExtractInfoPage()
	{
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
			'created' => 1173636811,
			'type' => 'page',
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

		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_page.xml');
		$data = $this->obj->extractInfo($this->obj->dom->getElementsByTagName('item')->item(0));

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
	
	public function testExtractBlogInfo()
	{
		$expectedResult = array(
			'title' => 'rodrigo.utopia.org.br',
			'desc' => 'Software livre, cicloativismo, montanhismo e quem sabe permacultura',
			'created' => '1284989827',
		);

		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_sample.xml');
		$this->obj->extractBlogInfo();

		$this->assertEquals($expectedResult, $this->obj->blogInfo);
	}

/*    public function testDownloadAttachment()
    {
        $this->obj->attachmentsDestDir = dirname(__FILE__) . '/fixtures/';

        $sourceAttachments = array('sourceTest.jpg', 'sourceTest2.jpg');
        $destAttachments = array('test.jpg', 'test2.jpg');
        $i = count($sourceAttachments) - 1;
        $cwd = getcwd();
        chdir(dirname(__FILE__));

        while ($i >= 0) {
            fopen($this->obj->attachmentsDestDir . $sourceAttachments[$i], 'w');
            $i--;
        }
 
        $this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/mediawiki_sample.xml');
        $this->obj->downloadAttachments();

        $this->expectOutputString("\n\nStarting to import attachments:\nFile test2.jpg successfully imported!\nFile test.jpg successfully imported!\n");

        $i = count($sourceAttachments) - 1;
        while ($i >= 0) {
            $filePath = $this->obj->attachmentsDestDir . $destAttachments[$i];
            $this->assertFileExists($filePath);
            unlink($filePath);
            unlink($this->obj->attachmentsDestDir . $sourceAttachments[$i]);
            $i--;
        }
        chdir($cwd);
    }

    public function testDownloadAttachmentShouldNotImportIfFileAlreadyExist()
    {
        $this->obj->attachmentsDestDir = dirname(__FILE__) . '/fixtures/';
        $this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/mediawiki_sample.xml');
        $attachments = array('test.jpg', 'test2.jpg');

        foreach ($attachments as $attachment) {
            $filePath = $this->obj->attachmentsDestDir . $attachment;
            fopen($filePath, 'w');
        }

        $this->obj->downloadAttachments();
        $this->expectOutputString("\n\nStarting to import attachments:\nNOT importing file test2.jpg as there is already a file with the same name in the destination directory (" . $this->obj->attachmentsDestDir . ")\nNOT importing file test.jpg as there is already a file with the same name in the destination directory (" . $this->obj->attachmentsDestDir . ")\n");
       
        foreach ($attachments as $attachment) {
            $filePath = $this->obj->attachmentsDestDir . $attachment;
            unlink($filePath);
        }
    }

    public function testDownloadAttachmentsShouldDisplayMessageIfNoAttachments()
    {
        $this->obj->dom = new DOMDocument;
        $this->expectOutputString("\n\nNo attachments found to import! Make sure you have created your XML file with the dumpDump.php script and with the option --uploads. This is the only way to import attachment.\n");
        $this->obj->downloadAttachments(); 
    }

    public function testDownloadAttachmentsShouldDisplayMessageIfUnableToDownloadFile()
    {
        $this->obj->attachmentsDestDir = dirname(__FILE__) . '/fixtures/';
        $this->obj->dom = new DOMDocument;
        $this->obj->dom->load(dirname(__FILE__) . '/fixtures/mediawiki_invalid_upload.xml');
        $this->obj->downloadAttachments();

        $this->expectOutputString("\n\nStarting to import attachments:\nUnable to download file Qlandkartegt-0.11.1.tar.gz. File not found.\nUnable to download file Passelivre.jpg. File not found.\n");
    }
 */
}
