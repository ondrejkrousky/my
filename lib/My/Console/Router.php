<?php

namespace My\Console;

use Nette\Application\Request;

/**
 * Console router
 *
 * For use Symfony Console
 *
 * @author	Patrik Votoček
 */
class Router extends \Nette\Object implements \Nette\Application\IRouter
{
	/** @var \Nette\Callback */
	protected $callback;

	/**
	 * @param \Symfony\Component\Console\Application
	 */
	public function __construct(\Symfony\Component\Console\Application $console)
	{
		$this->callback = callback(function () use ($console) {
			$console->run();
		});
	}

	/**
	 * Maps command line arguments to a Request object
	 *
	 * @param  \Nette\Http\IRequest
	 * @return \Nette\Application\Request|NULL
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		if (PHP_SAPI !== 'cli') {
			return NULL;
		}

		return new Request('Nette:Micro', 'CLI', array('callback' => $this->callback));
	}

	/**
	 * This router is only unidirectional
	 *
	 * @param  \Nette\Application\Request
	 * @param  \Nette\Http\Url
	 * @return NULL
	 */
	public function constructUrl(Request $appRequest, \Nette\Http\Url $refUrl)
	{
		return NULL;
	}
}

