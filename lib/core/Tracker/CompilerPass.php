<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		$definition = $container->getDefinition("tiki.lib.trk");

		$taggedServices = $container->findTaggedServiceIds('tiki.tracker.sectionformat');
		foreach ($taggedServices as $id => $tagAttributes) {
			foreach ($tagAttributes as $attributes) {
				if (! empty($attributes['mode'])) {
					$definition->addMethodCall('registerSectionFormat', [
						$attributes['layout'],
						$attributes['mode'],
						$attributes['template'],
						$attributes['label'],
					]);
				} else {
					$definition->addMethodCall('registerSectionFormat', [
						$attributes['layout'],
						'view',
						$attributes['template'],
						$attributes['label'],
					]);
					$definition->addMethodCall('registerSectionFormat', [
						$attributes['layout'],
						'edit',
						$attributes['template'],
						$attributes['label'],
					]);
				}
			}
		}
	}
}

