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
			'name' => 'Lo más importante son los veinte',
			'type' => 'post',
			'author' => 'rodrigo',
			'content' => 'Test',
			'excerpt' => '',
			'created' => '1203795580',
		);

		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_post.xml');
		$data = $this->obj->extractInfo($this->obj->dom->getElementsByTagName('item')->item(0));

		$this->assertEquals(8, count($data));
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
			'name' => 'Matéria sobre a viagem de bicicleta entre as chapadas',
			'type' => 'page',
			'author' => 'rodrigo',
			'revisions' => array('Test'),
			'excerpt' => '',
			'created' => '1173647611',
		);

		$this->obj->dom = new DOMDocument;
		$this->obj->dom->load(dirname(__FILE__) . '/fixtures/wordpress_page.xml');
		$data = $this->obj->extractInfo($this->obj->dom->getElementsByTagName('item')->item(0));

		$this->assertEquals(8, count($data));
		$this->assertEquals($expectedResult, $data);
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
