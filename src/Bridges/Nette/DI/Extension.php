<?php

namespace Milo\VendorVersions\Bridges\Nette\DI;

use Nette;

/**
 * Nette DI extension.
 *
 * @licence  MIT
 * @link     https://github.com/milo/vendor-versions
 */
class Extension extends Nette\DI\CompilerExtension
{
	private $defaults = [
		'dir' => NULL,
	];

	/** @var bool */
	private $debugMode;


	/**
	 * @param  bool $debugMode
	 */
	public function __construct($debugMode)
	{
		$this->debugMode = $debugMode;
	}


	public function loadConfiguration()
	{
		if ($this->debugMode) {
			$config = $this->getConfig() + $this->defaults;

			$container = $this->getContainerBuilder();
			$container->getDefinition('tracy.bar')
				->addSetup('addPanel', [
					new Nette\DI\Statement('Milo\VendorVersions\Panel', [$config['dir'].'/composer.json'])
				]);
		}
	}

}
