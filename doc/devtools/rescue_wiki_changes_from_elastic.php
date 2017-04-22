<?php
/**
 * Try to get changes from an elastic search index and perform each wiki page change event
 *
 * $Id$
 */

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

define('TIKI_CONSOLE', 1);
if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}
require_once('tiki-setup.php');

/**
 * Add a singleton command "rescue" using the Symfony concole component just for this script
 *
 * Class ESRescueCommand
 * @package Tiki\Command
 */
class ESRescueCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('rescue')
			->setDescription("Rescues wiki changes from the activity stream in an elasticsearch index\nExample usage: php doc/devtools/rescue_wiki_changes_from_elastic.php rescue -u http://elastic.example.com:9200/ -i mytiki_main")
			->addOption(
				'elasticuri',
				'u',
				InputOption::VALUE_OPTIONAL,
				'Elastic search URI',
				'http://localhost:9200/'
			)
			->addOption(
				'indexname',
				'i',
				InputOption::VALUE_OPTIONAL,
				'Index Name',
				'tiki_main'
			)
			->addOption(
				'startdate',
				's',
				InputOption::VALUE_OPTIONAL,
				'Start Date',
				'2016-11-04'
			)
			->addOption(
				'enddate',
				'e',
				InputOption::VALUE_OPTIONAL,
				'End Date',
				'2017-04-05'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$elasticUri = $input->getOption('elasticuri');
		$indexName = $input->getOption('indexname');
		$startDate = $input->getOption('startdate');
		$endDate = $input->getOption('enddate');

		$results = $this->getWikiPageEditEvents($elasticUri, $indexName, $startDate, $endDate);

		$output->writeln(tr('<comment>Found %0 wiki edits</comment>', count($results)));

		// TODO the page edits
	}

	/**
	 *
	 * @param $elasticUri
	 * @param $indexName
	 * @param $startDate
	 * @param $endDate
	 * @return array|bool
	 * @internal param $unifiedsearchlib
	 * @internal param $this
	 */
	private function getWikiPageEditEvents($elasticUri, $indexName, $startDate, $endDate)
	{
		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');
		$connection = new \Search_Elastic_Connection($elasticUri);
		$esIndex = new \Search_Elastic_Index($connection, $indexName);

		$query = new \Search_Query;
		$unifiedsearchlib->initQueryBase($query);
		//$query = $unifiedsearchlib->buildQuery($filter, $query);	// annoying, can't use type in this as that converts it to object_type, meh

		$query->filterIdentifier('wiki page', 'type');
		$query->filterType('activity');
		if ($startDate && $endDate) {
			$query->filterRange($startDate, $endDate);
		}
		$query->setOrder('modification_date_nasc');

		$query->setRange(0, 1000);

		//  build query for es
		$builder = new \Search_Elastic_OrderBuilder;
		$orderPart = $builder->build($query->getSortOrder());

		$builder = new \Search_Elastic_QueryBuilder($esIndex);
		$queryPart = $builder->build($query->getExpr());

		$fullQuery = array_merge(
			$queryPart,
			$orderPart,
			[
				'from' => 0,
				'size' => 1000,
			]
		);


		$data = json_encode($fullQuery);


		$full = "{$elasticUri}{$indexName}/_search";
		//$full = "{$elasticUri}{$indexName}/_validate/query?explain=true";

		$client = \TikiLib::lib('tiki')->get_http_client($full);

		$client->setRawBody($data);
		$client->setMethod(\Zend\Http\Request::METHOD_GET);
		$client->setHeaders(['Content-Type: application/json']);
		try {
			$response = $client->send();
			$body = $response->getBody();
			$decoded = json_decode($body);

			//$resultSet = new Search_Elastic_ResultSet($entries, $hits->total, $resultStart, $resultCount);

			$hits = $decoded->hits;
			$results = [];

			foreach ($hits->hits as $hit) {
				$results[] = [
					'modification_date' => $hit->_source->modification_date,
					'page' => $hit->_source->object,
					'user' => $hit->_source->user,
					'version' => $hit->_source->version,
					'old_version' => $hit->_source->old_version,
					'data' => $hit->_source->data,
					'old_data' => $hit->_source->old_data,
				];
			}

			return $results;
		} catch (\Exception $e) {
			echo $e->getMessage();
			return null;
		}

	}

}

// create the application and new console

$console = new Application;
$console->add(new ESRescueCommand);
$console->setDefaultCommand('rescue');

// run the command
$console->run();

