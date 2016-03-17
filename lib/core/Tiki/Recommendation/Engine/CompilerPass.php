<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation\Engine;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		$taggedServices = $container->findTaggedServiceIds('tiki.recommendation.engine');
		foreach ($taggedServices as $id => $tagAttributes) {
			foreach ($tagAttributes as $attributes) {
				$definition = $container->getDefinition("tiki.recommendation.{$attributes['set']}.set");

				$definition->addMethodCall('registerWeighted', [
					$attributes['engine'],
					$attributes['weight'],
					new Reference($id),
				]);
			}
		}
	}
}

