<?php
/**
 * Try to get changes from an elastic search index and perform each wiki page change event
 *
 * $Id$
 */

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
	private $elasticUri;
	private $indexName;

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
			)
			->addOption(
				'confirm',
				'c',
				InputOption::VALUE_NONE,
				'Confirm (add -c or --confirm to perform the actual changes)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->elasticUri = $input->getOption('elasticuri');
		$this->elasticUri = strrpos($this->elasticUri, '/') !== (strlen($this->elasticUri) - 1) ? $this->elasticUri . '/' : $this->elasticUri;
		$this->indexName = $input->getOption('indexname');

		$startDate = $input->getOption('startdate');
		$endDate = $input->getOption('enddate');

		$results = $this->getWikiPageEditEvents($startDate, $endDate);

		$output->writeln(tr('<comment>Found %0 wiki edits</comment>', count($results)));

		$confirm = $input->getOption('confirm');
		if (! $confirm) {
			$output->writeln(tr('<info>Dry run mode: add --confirm to run for real</info>', count($results)));
		}

		$this->makeThePageUpdates($results, $output, $confirm);
	}

	/**
	 * @param array $edits
	 * @param OutputInterface $output
	 */
	private function makeThePageUpdates(array $edits, OutputInterface $output, $confirm = false)
	{
		$tikilib = \TikiLib::lib('tiki');
		$histlib = \TikiLib::lib('hist');
		$now = date('c');

		$progress = new ProgressBar($output, count($edits));
		if ($confirm) {
			$progress->start();
		}
		$transaction = $tikilib->begin();

		foreach ($edits as $edit) {

			$page = $edit['page'];
			if (! $tikilib->page_exists($page)) {
				$info = $this->getWikiPage($page);
				if ($confirm) {
					$tikilib->create_page(
						$page,
						0,
						$edit['data'],
						strtotime($info['creation_date']),
						'Page created by rescue script ' . $now,
						$edit['user'],
						'0.0.0.0',
						$info['description'],
						$info['language'],
						null,
						null,
						null,
						'',
						0,
						strtotime($info['creation_date'])
					);
				} else {
					$output->writeln(tr('<comment>Page: "%0" (version %1) CREATED</comment>', $page, $edit['version']));
				}

				if ($edit['version'] > 1 && $confirm) {	// missing history
					$tiki_history = $tikilib->table('tiki_history');
					$old_version = ! empty($edit['old_version']) ? $edit['old_version'] : $edit['version'] - 1;

					$tiki_history->update([
						'version' => $old_version,
					], [
						'pageName' => $page,
					]);

					$tiki_history->insert([
						'pageName' => $page,
						'version' => $edit['version'],
						'version_minor' => 0,
						'lastModif' => strtotime($edit['modification_date']),
						'user' => $edit['user'],
						'ip' => '0.0.0.0',
						'comment' => 'Version created by rescue script ' . $now,
						'data' => $edit['old_data'],
						'description' => $info['description'],
						'is_html' => 0,	// a guess
					]);
				}
			} else {
				$info = $tikilib->get_page_info($page);

				if (! $histlib->get_version($page, $edit['version'])) {
					if ($confirm) {
						$tikilib->update_page(
							$page,
							$edit['data'],
							'Edit restored by rescue script ' . $now,
							$edit['user'],
							'0.0.0.0',
							null,
							0,
							'',
							null,
							null,
							strtotime($edit['modification_date'])
						);
					} else {
						$output->writeln(tr('<info>Page: "%0" (version %1) UPDATED</info>', $page, $edit['version']));
					}
				} else {
					// version alrteady exists, do what?
				}
			}

			if ($confirm) {
				$progress->advance();
			}
		}
		if ($confirm) {
			$progress->finish();
		}

		$output->writeln('');
		$output->writeln(tr('Committing transaction'));
		$transaction->commit();

		$output->writeln('');
		$output->writeln(tr('Done'));
	}

	/**
	 * Get all activity stream events for wiki pages
	 *
	 * @param $startDate
	 * @param $endDate
	 * @return array|bool
	 * @internal param $unifiedsearchlib
	 * @internal param $this
	 */
	private function getWikiPageEditEvents($startDate, $endDate)
	{
		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');
		$connection = new \Search_Elastic_Connection($this->elasticUri);
		$esIndex = new \Search_Elastic_Index($connection, $this->indexName);

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


		$full = "{$this->elasticUri}{$this->indexName}/_search";
		//$full = "{$elasticUri}{$indexName}/_validate/query?explain=true";

		$client = \TikiLib::lib('tiki')->get_http_client($full);

		$client->setRawBody($data);
		$client->setMethod(\Zend\Http\Request::METHOD_GET);
		$client->setHeaders(['Content-Type: application/json']);
		try {
			$response = $client->send();
			$body = $response->getBody();
			$decoded = json_decode($body);

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

	/**
	 * Gets a single wiki page definitioopn from the index (for missing pages)
	 *
	 * @param string $page page name
	 * @return array|bool
	 * @internal param $unifiedsearchlib
	 * @internal param $this
	 */
	private function getWikiPage($page)
	{
		$unifiedsearchlib = \TikiLib::lib('unifiedsearch');
		$connection = new \Search_Elastic_Connection($this->elasticUri);
		$esIndex = new \Search_Elastic_Index($connection, $this->indexName);

		$query = new \Search_Query;
		$unifiedsearchlib->initQueryBase($query);
		//$query = $unifiedsearchlib->buildQuery($filter, $query);	// annoying, can't use type in this as that converts it to object_type, meh

		$query->filterIdentifier($page, 'object_id');
		$query->filterType('wiki page');

		//  build query for es
		$builder = new \Search_Elastic_QueryBuilder($esIndex);
		$queryPart = $builder->build($query->getExpr());

		$data = json_encode($queryPart);


		$full = "{$this->elasticUri}{$this->indexName}/_search";
		//$full = "{$elasticUri}{$indexName}/_validate/query?explain=true";

		$client = \TikiLib::lib('tiki')->get_http_client($full);

		$client->setRawBody($data);
		$client->setMethod(\Zend\Http\Request::METHOD_GET);
		$client->setHeaders(['Content-Type: application/json']);

		$response = $client->send();
		$body = $response->getBody();
		$decoded = json_decode($body, true);

		if ($decoded && ! empty($decoded['hits']['hits'][0]['_source'])) {
			return $decoded['hits']['hits'][0]['_source'];
		} else {
			return false;
		}
	}

}

// create the application and new console

$console = new Application;
$console->add(new ESRescueCommand);
$console->setDefaultCommand('rescue');

// run the command
$console->run();

