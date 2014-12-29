<?php

include_once 'lib/wikiLingo_tiki/WikiMetadataLookup.php';

/**
 * Class WikiEvents
 * Used for Tiki's "Wiki" feature
 */
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
		$received = FLP\Service\Receiver::receive();
        if ($received != null) {
            echo $received;
            exit;
        }
		//listener end
	}

	public function direct() {
        $headerlib = TikiLib::lib('header');
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
            $phraseSanitized = Phraser\Phraser::superSanitize($phrase);
            $headerlib->add_jq_onready(<<<JS
var phraseSanitized = '$phraseSanitized';
setTimeout(function() {
    if (!flp.selectAndScrollToFutureLink(phraseSanitized)) {
        flp.selectAndScrollToPastLink(phraseSanitized);
    }
}, 50);
JS
);


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
			} else {
                $phraseSanitized = Phraser\Phraser::superSanitize($phrase);
                $headerlib->add_jq_onready(<<<JS
var phraseSanitized = '$phraseSanitized';
setTimeout(function() {
    if (!flp.selectAndScrollToFutureLink(phraseSanitized)) {
        flp.selectAndScrollToPastLink(phraseSanitized);
    }
}, 50);
JS
                );
            }
		}
	}

	public function getParsed()
	{
		$smarty = TikiLib::lib('smarty');
		return $smarty->getTemplateVars('parsed')
			?: $smarty->getTemplateVars('previewd')
				?: '';
	}

	public function load() {
		$smarty = TikiLib::lib('smarty');
		$headerlib = TikiLib::lib('header');
		//standard page
		$parsed = $smarty->getTemplateVars('parsed');
		if (!empty($parsed)) {
			$ui = new FLP\UI($parsed);
            $ui->setContextAsPast();
			$pairs = FLP\Data::GetPairsByTitleAndApplyToUI($this->title, $ui);
            $pairsJson = json_encode($pairs);
            $length = count($pairs);
            $counts = json_encode(FLP\PairAssembler::$counts);
            $headerlib->add_js(<<<JS
(function(){
    var counts = $counts,
        length = $length,
        flpData = $pairsJson,
        phrases = $('span.phrases'),
        phrasesLookupTable = {};

    for(var x = 0; x < length; x++){
        if(!phrasesLookupTable[flpData[x].pastText.sanitized]){
            phrasesLookupTable[flpData[x].pastText.sanitized] = [];
        }
        phrasesLookupTable[flpData[x].pastText.sanitized].push(flpData[x]);
    }


    for(var i = 0; i < length; i++) {
        var futureLink = new flp.Link({
            beginning: phrases.filter('span.futurelink-beginning' + i),
            middle: phrases.filter('span.futurelink' + i),
            end: phrases.filter('span.futurelink-end' + i),
            to: 'future',
            count: counts[flpData[i].pastText.sanitized],
            pairs: phrasesLookupTable[flpData[i].pastText.sanitized]
        });
        flp.addFutureLink(futureLink);
    }
})();
JS
);
			$parsed = $ui->render();
			$smarty->assign('parsed', $parsed);
		} else {
			//history
			$previewd = $smarty->getTemplateVars('previewd');
			if (!empty($previewd)) {
				$ui = new FLP\UI($previewd);
                $ui->setContextAsPast();
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
