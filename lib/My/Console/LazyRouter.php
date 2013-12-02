<?php

namespace My\Console;

/**
 * Lazy application inicialization router
 *
 * For use Symfony Console
 *
 * @author	Patrik Votoček
 */
class LazyRouter extends Router
{

	/**
	 * @param \Nette\DI\Container
	 */
	public function __construct(\Nette\DI\Container $container, $serviceName = NULL)
	{
		if (!$serviceName) {
			$class = 'Symfony\Component\Console\Application';
			$services = $container->findByType($class);
                        if (empty($services)) {
				throw new \Nette\DI\MissingServiceException("Service of type $class not found.");
			} elseif (count($services) > 1) {
				throw new \Nette\DI\MissingServiceException("Multiple services of type $class found.");
			} else {
				$serviceName = $services[0];
			}
		}

		if (!$container->hasService($serviceName)) {
			throw new \Nette\DI\MissingServiceException("Service '$serviceName' not found.");
		}

		$this->callback = callback(function () use ($container, $serviceName) {
			$console = $container->getService($serviceName);
			$console->run();
		});
	}
}

