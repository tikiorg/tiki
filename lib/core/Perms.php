<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


/**
 * Facade class of the permission subsystem. Once configured, the ::get()
 * static method can be used to obtain accessors for specific objects.
 * The accessor will contain all the rules applicable to the object.
 *
 * Sample usage:
 *   $perms = Perms::get( array(
 *       'type' => 'wiki page',
 *       'object' => 'HomePage',
 *   ) );
 *
 *   if ( $perms->view_calendar ) {
 *      // ...
 *   }
 *
 * Global permissions may be obtained using Perms::get() without a context.
 * 
 * Please note that the Perms will not be correct for checking of access for
 * objects that depend on their parent, for example, even if a trackeritem has
 * no object or category perms on itself, the tracker's perms should be considered
 * in the checking. However, the Perms object with 'type' => 'trackeritem' will 
 * only get the perms of the object/it's categories itself and not take into
 * account the parent tracker. To do so, use the new Perms::getCombined instead.
 *
 * The facade also provides a convenient way to filter lists based on
 * permissions. Using the method will also used the underlying::bulk()
 * method to charge permissions for multiple objects at once and reduce
 * the amount of queries required.
 *
 * Sample usage:
 *   $pages = $tikilib->listpages();
 *
 *   $filtered = Perms::filter(
 *       array( 'type' => 'wiki page' ), 
 *       'object',
 *       $pages,
 *       array( 'object' => 'pageName' ),
 *       'view' );
 *
 * The sample above would return the data without elements not visible,
 * assuming tiki_p_view is required, in the same format as provided.
 * In a standard configuration, this filter would use a maximum of
 * 4 queries to the database, less if some elements were previously
 * loaded.
 *
 * The permission facade handles local caching of the decision rules,
 * meaning that calling the facade for the same object twice will not
 * cause multiple queries to the database. Rather, the same object will
 * be provided. Moreover, if two objects use the same rules, like two
 * objects with the same set of categories, the rules will be shared
 * between the two accessors.
 *
 * Configuration of the facade is required only once. Configuration
 * includes indicating which rules apply, which are the active groups
 * for the current user and a prefix for backwards compatibility. The 
 * rules are provided as a list of ResolverFactory objects. Each of
 * these objects will fetch the permissions applicable for the given
 * context. The first factory providing a Resolver for the context
 * will be the applicable set of rules. For example, when configured
 * with ObjectFactory, CategoryFactory and GlobalFactory, the facade
 * would first search for object permissions, if none are found, it
 * would fall back to categories and finally to globals. Global
 * guarentees a basic set of rules.
 *
 * The context is provided as an array for extensibility. Currently,
 * type and object are the only two known keys.
 *
 * Resolvers are group agnostic, meaning the same resolver will be
 * provided no matter which groups have been configured. This allows
 * for more caching possible. As a general rule, the permission sub-
 * system fetches all the information it may require and counts on
 * caching to have the extra cost diminished over multiple requests.
 *
 * The accessors are simply a binding between the groups and the
 * resolver that provides a convenient access to the permissions.
 * The introduction paragraphs mentionned accessors were build for
 * specific objects and shared when multiple requests were made. This
 * is in fact incorrect. A new accessor is built every time, however
 * those are very thin and they share a common resolver. These separate
 * instances allow to reconfigure the accessors depending on the
 * environment in which they are used. For example, the accessors are
 * configured with the global groups by default. However, they can be
 * replaced to evaluate the permissions for a different user
 * by creating a new Perms_Context object before accessing the perms,
 * e.g.
 * 		$permissionContext = new Perms_Context($aUserName);
 *
 * Each ResolverFactory will generate a hash from the context which
 * represents a unique key to the matching resolver it would provide.
 * The hash provided by the global factory is a constant key, the one
 * provided by the object factory is straightforward and the one
 * provided for categories is a list of all categories applicable to
 * the object. These hashes are used to shortcut the amount of
 * database queries executed by reusing as much data as possible.
 */
class Perms
{
	private static $instance;

	private $prefix = '';
	private $groups = array();
	private $factories = array();
	private $checkSequence = null;

	private $hashes = array();

	/** 
	 * Provides a new accessor configured with the global settings and
	 * a resolver appropriate to the context requested.
	 */
	public static function get( $context = array() )
	{
		if (! is_array($context)) {
			$args = func_get_args();
			$context = array(
				'type' => $args[0],
				'object' => $args[1],
			);
		}

		if (self::$instance) {
			return self::$instance->getAccessor($context);
		} else {
			$accessor = new Perms_Accessor;
			$accessor->setContext($context);

			return $accessor;
		}
	}

	public static function getCombined( $context = array() ) {

		if (! is_array($context)) {
			$args = func_get_args();
			$context = array( 
				'type' => $args[0],
				'object' => $args[1],
			);
		}

		if ($context['type'] == 'trackeritem') {
			$perms = Perms::get('trackeritem', $context['object']);
			$resolver = $perms->getResolver();

			if (method_exists($resolver, 'from') && $resolver->from() != '') {
				// Item permissions are valid if they are assigned directly to the object or category, otherwise
				// tracker permissions are better than global ones.
				return Perms::get($context); 
                        } else {
				$context['type'] = 'tracker';
				$context['object'] = TikiLib::lib('trk')->get_tracker_for_item($context['object']);
				return Perms::get($context);
			}
		}

		return Perms::get($context);
	}

	public function getAccessor(array $context = array())
	{
		$accessor = new Perms_Accessor;
		$accessor->setContext($context);

		$accessor->setPrefix($this->prefix);
		$accessor->setGroups($this->groups);

		if ($this->checkSequence) {
			$accessor->setCheckSequence($this->checkSequence);
		}

		if ($resolver = $this->getResolver($context)) {
			$accessor->setResolver($resolver);
		}

		return $accessor;
	}

	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Sets the global Perms instance to use when obtaining accessors.
	 */
	public static function set(self $perms)
	{
		self::$instance = $perms;
	}

	/**
	 * Loads the data for multiple contexts at the same time. This method
	 * can be used to reduce the amount of queries performed to request
	 * multiple accessors. The method simply forwards the bulk call to
	 * each of the ResolverFactory object in sequence, which is then
	 * responsible to handle the call in an efficient manner and return
	 * the list of objects which are left to be handled. Only the remaining
	 * objects are sent to the subsequent factories.
	 *
	 * @param $baseContext array The part of the context common to all
	 *                           objects.
	 * @param $bulkKey string The key added for each of the objects in bulk
	 *                        loading.
	 * @param $data array A simple list of values to be loaded (such as a 
	 *                    list of page names) or a list of records. When
	 *                    a list of records is provided, the $dataKey
	 *                    parameter is required.
	 * @param $dataKey mixed The key to fetch from each record when a dataset
	 *                       is used.
	 */
	public static function bulk(array $baseContext, $bulkKey, array $data, $dataKey = null)
	{
		$remaining = array();

		foreach ($data as $entry) {
			if ($dataKey) {
				$value = $entry[$dataKey];
			} else {
				$value = $entry;
			}

			$remaining[] = $value;
		}

		if (count($remaining)) {
			self::$instance->loadBulk($baseContext, $bulkKey, $remaining);
		}
	}

	/**
	 * Filters a dataset based on a permission. The method will perform bulk
	 * loading of the permissions on all objects in the dataset and then
	 * filter the dataset with a single permission.
	 *
	 * @param $baseContext array The part of the context common to all
	 *                           objects.
	 * @param $bulkKey string The key added for each of the objects in bulk
	 *                        loading.
	 * @param $data array A list of records.
	 * @param $contextMap mixed The key to fetch from each record as the object.
	 * @param $permission string The permission name to validate on each record.
	 * @return array What remains of the dataset after filtering.
	 */
	public static function filter(array $baseContext, $bulkKey, array $data, array $contextMap, $permission)
	{
		self::bulk($baseContext, $bulkKey, $data, $contextMap[$bulkKey]);

		$valid = array();

		foreach ($data as $entry) {
			if (self::hasPerm($baseContext, $contextMap, $entry, $permission)) {
				$valid[] = $entry;
			}
		}

		return $valid;
	}

	public static function simpleFilter($type, $key, $permission, array $data)
	{
		return self::filter(
			array('type' => $type),
			'object',
			$data,
			array('object' => $key),
			$permission
		);
	}

	private static function hasPerm($baseContext, $contextMap, $entry, $permission)
	{
		$context = $baseContext;
		foreach ($contextMap as $to => $from) {
			$context[$to] = $entry[$from];
		}

		$accessor = self::get($context);
		if (is_array($permission)) {
			foreach ($permission as $perm) {
				if ($accessor->$perm) {
					return true;
				}
			}
		} else {
			return $accessor->$permission;
		}
	}

	public static function mixedFilter(array $baseContext, $discriminator, $bulkKey, $data, $contextMapMap, $permissionMap)
	{
		//echo '<pre>BASECONTEXT'; print_r($baseContext); echo 'DISCRIMATOR';print_r($discriminator); echo 'BULKEY';print_r($bulkKey); echo 'DATA';print_r($data); echo 'CONTEXTMAPMAP';print_r($contextMapMap); echo 'PERMISSIONMAP';print_r($permissionMap); echo '</pre>';

		$perType = array();

		foreach ($data as $row) {
			$type = $row[$discriminator];
			if (! isset($perType[$type])) {
				$perType[$type] = array();
			}

			$key = $contextMapMap[$type][$bulkKey];
			$perType[$type][] = $row[$key];
		}

		foreach ($perType as $type => $values) {
			$context = $baseContext;
			$context[ $contextMapMap[$type][$discriminator] ] = $type;

			self::$instance->loadBulk($context, $bulkKey, $values);
		}

		$valid = array();

		foreach ($data as $entry) {
			$type = $entry[$discriminator];

			if (self::hasPerm($baseContext, $contextMapMap[$type], $entry, $permissionMap[$type])) {
				$valid[] = $entry;
			}
		}

		return $valid;
	}

	function setGroups(array $groups)
	{
		$this->groups = $groups;
	}

	function getGroups()
	{
		return $this->groups;
	}

	function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	function setResolverFactories(array $factories)
	{
		$this->factories = $factories;
	}

	function setCheckSequence(array $sequence)
	{
		$this->checkSequence = $sequence;
	}

	private function getResolver(array $context)
	{
		$toSet = array();
		$resolver = null;

		foreach ($this->factories as $factory) {
			$hash = $factory->getHash($context);

			if (isset($this->hashes[$hash])) {
				$resolver = $this->hashes[$hash];
				break;
			} else {
				$toSet[] = $hash;
			}

			if ($resolver = $factory->getResolver($context)) {
				break;
			}
		}

		if (! $resolver) {
			$resolver = false;
		}

		// Limit the amount of hashes preserved to reduce memory consumption
		if (count($this->hashes) > 128) {
			$this->hashes = array();
		}

		foreach ($toSet as $hash) {
			$this->hashes[$hash] = $resolver;
		}

		return $resolver;
	}

	private function loadBulk($baseContext, $bulkKey, $data)
	{
		foreach ($this->factories as $factory) {
			$data = $factory->bulk($baseContext, $bulkKey, $data);
		}
	}

	public function clear()
	{
		$this->hashes = array();
		foreach ($this->factories as $factory) {
			if (method_exists($factory, 'clear')) {
				$factory->clear();
			}
		}
	}
}

