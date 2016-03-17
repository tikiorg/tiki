<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Search\Federated;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('tiki.lib.federatedsearch')) {
			return;
		}

		$definition = $container->getDefinition('tiki.lib.federatedsearch');

		$taggedServices = $container->findTaggedServiceIds('tiki.federatedsearch.index');
		foreach ($taggedServices as $id => $tagAttributes) {
			foreach ($tagAttributes as $attributes) {
				$definition->addMethodCall('addIndex', [
					$attributes['index'],
					new Reference($id),
				]);
			}
		}
	}
}

