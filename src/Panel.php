<?php

namespace Milo\VendorVersions;

use Tracy;

/**
 * Bar panel for Tracy (https://tracy.nette.org/) shows versions of libraries parsed from composer.lock.
 *
 * @licence  MIT
 * @link     https://github.com/milo/vendor-versions
 */
class Panel implements Tracy\IBarPanel
{
	/** @var string */
	private $error;

	/** @var string */
	private $dir;


	/**
	 * @param  string $composerLockDir  path to composer.lock's directory
	 */
	public function __construct($composerLockDir = NULL)
	{
		$composerLockDir = $composerLockDir ?: __DIR__ . '/../../../../';
		if (!is_dir($dir = @realpath($composerLockDir))) {
			$this->error = "Path '$composerLockDir' is not a directory.";
		} elseif (!is_file($dir . DIRECTORY_SEPARATOR . 'composer.lock')) {
			$this->error = "Directory '$dir' does not contain the composer.lock file.";
		} else {
			$this->dir = $dir;
		}
	}


	/**
	 * @return string
	 */
	public function getTab()
	{
		ob_start();
		require __DIR__ . '/templates/Panel.tab.phtml';
		return ob_get_clean();
	}


	/**
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();

		$jsonFile = $this->dir . DIRECTORY_SEPARATOR . 'composer.json';
		$lockFile = $this->dir . DIRECTORY_SEPARATOR . 'composer.lock';

		$required = $this->decode($jsonFile);
		$installed = $this->decode($lockFile);

		if ($this->error === NULL) {
			$required = array_filter($required) + ['require' => [], 'require-dev' => []];
			$installed = array_filter($installed) + ['packages' => [], 'packages-dev' => []];
			$data = [
				'Packages' => self::format($installed['packages'], $required['require']),
				'Dev Packages' => self::format($installed['packages-dev'], $required['require-dev']),
			];
		}

		$error = $this->error;

		require __DIR__ . '/templates/Panel.panel.phtml';
		return ob_get_clean();
	}


	/**
	 * @param  array $packages
	 * @param  array $required
	 * @return array
	 */
	private static function format(array $packages, array $required)
	{
		$data = [];
		foreach ($packages as $p) {
			$data[$p['name']] = (object) [
				'installed' => $p['version'] . ($p['version'] === 'dev-master'
					? (' #' . substr($p['source']['reference'], 0, 7))
					: ''
				),

				'required' => isset($required[$p['name']])
					? $required[$p['name']]
					: NULL,

				'url' => isset($p['source']['url'])
					? preg_replace('/\.git$/', '', $p['source']['url'])
					: NULL,
			];
		}

		ksort($data);
		return $data;
	}


	/**
	 * @param  string $file
	 * @return array|NULL
	 */
	private function decode($file)
	{
		$json = @file_get_contents($file);
		if ($json === FALSE) {
			$this->error = error_get_last()['message'];
			return NULL;
		}

		$decoded = @json_decode($json, TRUE);
		if (!is_array($decoded)) {
			$this->error = error_get_last()['message'];
			return NULL;
		}

		return $decoded;
	}

}
