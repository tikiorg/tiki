<?php

include_once 'lib/wikiLingo_tiki/WikiMetadataLookup.php';

class WikiEvents {

	public $title;
	public $version;
	public $body;

	public function __construct($title, $version, $body)
	{
        global $host_tiki, $dbs_tiki, $user_tiki, $pass_tiki;

        FLP\Data::$dbConnectionString = 'mysql:host=' . $host_tiki . ';dbname=' . $dbs_tiki;
        FLP\Data::$dbUsername = $user_tiki;
        FLP\Data::$dbPassword = $pass_tiki;

		$this->title = $title;
		$this->version = $version;
		$this->body = $body;
	}

	public function listen() {
		//listener start
		if (isset($_POST['protocol'])) {
			echo FLP\Service\Receiver::receive();
			exit;
		}
		//listener end
	}

	public function direct() {
		//redirect start
		if (isset($_GET['phrase'])) {
			$phrase = (!empty($_GET['phrase']) ? $_GET['phrase'] : '');
		}

		//start session if that has not already been done
		if (!isset($_SESSION)) {
			session_start();
		}

		//recover from redirect if it happened
		if (!empty($_SESSION['phrase'])) {
			$phrase = $_SESSION['phrase'];
			unset($_SESSION['phrase']);

		}

		//check if versions are same, if they are not, redirect to where they do
		else if (!empty($phrase)) {
			$revision = FLP\Data::getRevision($phrase);
			//check if this version (latest) is
			if ($this->version != $revision->version) {
				//prep for redirect
				$_SESSION['phrase'] = $phrase;
				header('Location: ' . TikiLib::tikiUrl() . 'tiki-pagehistory.php?page=' . $revision->title . '&preview=' . $revision->version . '&nohistory');
				exit();
			}
		}
	}

	public function getParsed()
	{
		global $smarty;
		return $smarty->getTemplateVars('parsed')
			?: $smarty->getTemplateVars('previewd')
				?: '';
	}

	public function load() {
		global $smarty, $headerlib;
		//standard page
		$parsed = $smarty->getTemplateVars('parsed');
		if (!empty($parsed)) {
			$ui = new FLP\UI($parsed);
			$pairs = FLP\Data::GetPairsByTitleAndApplyToUI($this->title, $ui);
            $pairsJson = json_encode($pairs);
            $counts = json_encode(FLP\PairAssembler::$counts);
            $headerlib->add_js(<<<JS
var counts = $counts,
    flpData = $pairsJson,
    phrases = $('span.phrases'),
    phrasesLookupTable = {},
    show = function(table) {
        $('body')
            .append(table);
    };

for(var x = 0; x < flpData.length; x++){
    if(!phrasesLookupTable[flpData[x].pastText.sanitized]){
        phrasesLookupTable[flpData[x].pastText.sanitized] = [];
    }
    phrasesLookupTable[flpData[x].pastText.sanitized].push(flpData[x]);
}


for(var i = 0; i < flpData.length; i++) {
    var futureLink = new flp.Link({
        beginning: phrases.filter('span.phraseBeginning' + i),
        middle: phrases.filter('span.phrase' + i),
        end: phrases.filter('span.phraseEnd' + i),
        count: counts[flpData[i].pastText.sanitized],
        pairs: phrasesLookupTable[flpData[i].pastText.sanitized]
    });
}
JS
);
			$parsed = $ui->render();
			$smarty->assign('parsed', $parsed);
		} else {
			//history
			$previewd = $smarty->getTemplateVars('previewd');
			if (!empty($previewd)) {
				$ui = new FLP\UI($previewd);
				FLP\Data::GetPairsByTitleAndApplyToUI($this->title, $ui);
				$previewd = $ui->render();
				$smarty->assign('previewd', $previewd);
			}
		}
	}

    public function save() {
        $metadataLookup = new WikiMetadataLookup($this->title);
        $metadata = $metadataLookup->getPartial();

        FLP\Data::createArticle($this->title, $this->body, $metadata, $this->version);
    }
}