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
		'dir' => null,
		'panelTitle' => null,
		'iconColor' => null,
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

			$builder = $this->getContainerBuilder();
			$builder->addDefinition($this->prefix('panel'))
				->setFactory('Milo\VendorVersions\Panel', [$config['dir']])
				->setAutowired(false)
				->addSetup('setPanelTitle', [$config['panelTitle']])
				->addSetup('setIconColor', [$config['iconColor']]);

			$builder->getDefinition('tracy.bar')
				->addSetup('addPanel', ['@' . $this->prefix('panel')]);
		}
	}
}
