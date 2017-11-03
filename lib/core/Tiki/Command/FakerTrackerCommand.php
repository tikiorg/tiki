<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory as FakerFactory;
use TikiLib;
use Tracker_Definition;

/**
 * Enabled the usage of Faker as a way to load random data to trackers
 */
class FakerTrackerCommand extends Command
{
	/**
	 * Configures the current command.
	 */
	protected function configure()
	{
		$this
			->setName('faker:tracker')
			->setDescription('Generate tracker fake data')
			->addArgument(
				'tracker',
				InputArgument::REQUIRED,
				'Tracker id'
			)
			->addOption(
				'field',
				'f',
				InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
				'Override default faker for field. Format: field,faker[,faker_options]. Example: 1,text,30'
			)
			->addOption(
				'items',
				'i',
				InputOption::VALUE_OPTIONAL,
				'Number of items to generate',
				100
			)
			->addOption(
				'random-status',
				'r',
				InputOption::VALUE_NONE,
				'Generate random item status'
			);
	}

	/**
	 * Executes the current command.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return null|int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{

		if (! class_exists('\Faker\Factory')) {
			$output->writeln('<error>' . tra('Please install Faker package') . '</error>');
			return;
		}

		$trackerId = $input->getArgument('tracker');
		$numberItems = $input->getOption('items');
		$randomizeStatus = empty($input->getOption('random-status')) ? false : true;
		$fieldOverrideDefinition = $input->getOption('field');

		if (! is_numeric($numberItems)) {
			$output->writeln('<error>' . tra('The value of items is not a number') . '</error>');
			return;
		}

		$trackerDefinition = Tracker_Definition::get($trackerId);
		if (! $trackerDefinition) {
			$output->writeln('<error>' . tr('Tracker not found') . '</error>');
			return;
		}

		$fieldFakerOverride = [];
		foreach ($fieldOverrideDefinition as $fieldDefinition) {
			$arguments = array_map('trim', explode(',', $fieldDefinition));
			$fieldReference = array_shift($arguments);
			$action = array_shift($arguments);

			if (is_null($fieldReference) || is_null($action)) {
				$output->writeln('<error>' . tr('Invalid field definition: %0', $fieldDefinition) . '</error>');
				return;
			}

			if (empty($arguments)) {
				$fieldFakerOverride[$fieldReference] = $action;
			} else {
				$fieldFakerOverride[$fieldReference] = [$action, $arguments];
			}
		}

		$trackerFields = $trackerDefinition->getFields();
		$fakerFieldTypes = $this->mapTrackerItems();

		$fieldFakerMap = [];
		foreach ($trackerFields as $field) {
			if (isset($fieldFakerOverride[$field['fieldId']])) { // override by fieldId
				$fakerForField = $fieldFakerOverride[$field['fieldId']];
			} elseif (isset($fieldFakerOverride[$field['permName']])) { // override by permName
				$fakerForField = $fieldFakerOverride[$field['permName']];
			} elseif (isset($fakerFieldTypes[$field['type']])) { // default for field type
				$fakerForField = $fakerFieldTypes[$field['type']];
			} else { // if not defined, empty
				$fakerForField = '';
			}

			$fieldFakerMap[] = [
				'fieldId' => $field['fieldId'],
				'faker' => $fakerForField,
			];
		}

		/** @var \TrackerLib $trackerLib */
		$trackerLib = TikiLib::lib('trk');

		$faker = FakerFactory::create();

		for ($i = 0; $i < $numberItems; $i++) {
			$fieldData = [];

			foreach ($fieldFakerMap as $fieldFaker) {
				$value = '';
				if (is_array($fieldFaker['faker'])) {
					$fakerAction = $fieldFaker['faker'][0];
					$fakerArguments = $fieldFaker['faker'][1];
					if (! is_array($fakerArguments)) {
						$fakerArguments = [$fakerArguments];
					}
					$value = call_user_func_array([$faker, $fakerAction], $fakerArguments);
				} elseif (! empty($fieldFaker['faker'])) {
					$fakerAction = $fieldFaker['faker'];
					$value = $faker->$fakerAction;
				}
				$fieldData[] = [
					'fieldId' => $fieldFaker['fieldId'],
					'value' => $value,
				];
			}

			$status = ($randomizeStatus) ? array_rand(\TikiLib::lib('trk')->status_types()) : '';
			$trackerLib->replace_item($trackerId, 0, ['data' => $fieldData], $status);
		}
	}

	/**
	 * Return the current map of tracker field types to faker formatters
	 *
	 * @return array
	 */
	protected function mapTrackerItems()
	{
		$map = [
			//'e' => '', // Category - lookup on the valid values for category
			//'c' => '', // Checkbox - lookup on the valid values for the checkbox
			'y' => 'country', // Country Selector (improvement if uses the list from Tiki directly)
			'b' => ['numberBetween', [0, 10000]], // Currency Field
			'f' => 'unixTime', // Date and Time
			'j' => 'unixTime', // Date and Time
			//'d' => '', // Drop Down - lookup on the valid values for the drop down
			//'D' => '', // Drop Down with Other field - lookup on the valid values for the drop down
			//'R' => '', // Radio Buttons - lookup for valid values for Buttons
			//'M' => '', // Multiselect - lookup on the valid values for the select
			//'w' => '', // Dynamic Items List - lookup on the valid values from other tracker (and sync between different fields)
			'm' => 'email', // Email
			//'FG' => '', // Files - lookup on valid files from gallery
			'h' => '', // Header - empty
			//'icon' => '', // Icon - lookup on valid files from gallery
			//'r' => '', // Item Link - lookup on valid items
			//'l' => '', // Items List - lookup on valid items
			'LANG' => 'languageCode', // Language
			//'G' => '', // Location - needs geo coordinates in the format <lat>,<long>,<zoom>
			'math' => '', // Mathematical Calculation - empty
			'n' => ['numberBetween', [0, 10000]], // Numeric Field
			//'k' => '', // Page Selector - lookup for valid page names
			'S' => '', // Static Text - empty
			'a' => 'text', // Text Area
			't' => ['text', [30]], // Text Field
			'q' => '', // AutoIncrement - empty
			'L' => 'url', // Url
			//'u' => '', // User Selector - lookup for valid users
			//'g' => '', // Group Selector - lookup for valid groups
			'wiki' => 'text', //Wiki Page
			//'x' => '', // Action - Not supported
			//'articles' => '', // Articles - lookup for valid articles
			//'C' => '', // Computed Field - not supported - backward compatibility
			//'A' => '', // Attachment - deprecated in favor of files field
			'F' => ['words', [3, true]], // Tags
			//'GF' => '', // Geographic Feature
			//'i' => '', // Image - deprecated in favor of the files field
			//'N' => '', // In Group
			'I' => 'localIpv4', // IP Selector
			//'kaltura' => '', // Kaltura video
			'p' => '', // Ldap lookup - empty
			//'STARS' => '', // Rating - lookup for valid values
			//'*' => '', // Stars (deprecated)
			//'s' => '', // Stars (system - deprecated)
			//'REL' => '', // Relations
			//'STO' => '', // show.tiki.org - Not supported
			'usergroups' => '', // Display list of user groups - empty
			//'p' => '', // User Preference - Not Supported
			//'U' => '', // User Subscription
			'W' => '', // Webservice - empty
		];

		return $map;
	}
}
