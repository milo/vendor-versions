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
	private $path;

	/** @var string|null */
	private $panelTitle;

	/** @var string|null */
	private $iconColor;


	/**
	 * @param  string $composerJsonPath  path to composer.json's file or directory
	 */
	public function __construct($composerJsonPath = null)
	{
		$path = realpath($composerJsonPath ?: __DIR__ . '/../../../../composer.json');
		if ($path === false) {
			$this->error = "Path '$composerJsonPath' does not exist.";
		} elseif (is_dir($path)) {
			$this->path = $path . DIRECTORY_SEPARATOR . 'composer.json';
		} else {
			$this->path = $path;
		}
	}


	/**
	 * @param  string $color
	 * @return void
	 */
	public function setIconColor($color)
	{
		$this->iconColor = $color;
	}


	/**
	 * @param  string $title
	 * @return void
	 */
	public function setPanelTitle($title)
	{
		$this->panelTitle = $title;
	}


	/**
	 * @return string
	 */
	public function getTab()
	{
		ob_start();
		$iconColor = $this->escapeHtml($this->iconColor ?: '#478CCC');
		require __DIR__ . '/templates/Panel.tab.phtml';
		return ob_get_clean();
	}


	/**
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();

		if ($this->error === null) {
			$jsonFile = $this->path;
			$lockFile = substr_replace($jsonFile, 'lock', strrpos($jsonFile, '.') + 1);

			$required = array_filter($this->decode($jsonFile));
			$installed = array_filter($this->decode($lockFile));
			$required += ['require' => [], 'require-dev' => []];
			$installed += ['packages' => [], 'packages-dev' => []];
			$data = [
				'Packages' => self::format($installed['packages'], $required['require']),
				'Dev Packages' => self::format($installed['packages-dev'], $required['require-dev']),
			];
		}

		$panelTitle = $this->panelTitle ?: 'Vendor Versions';
		$error = $this->error;
		$esc = function ($str) {
			return $this->escapeHtml($str);
		};

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
				'installed' => $p['version'] . ($p['version'] === 'dev-master' && isset($p['source']['reference'])
					? ('#' . substr($p['source']['reference'], 0, 7))
					: ''
				),

				'required' => isset($required[$p['name']])
					? $required[$p['name']]
					: null,

				'url' => isset($p['source']['url'])
					? preg_replace('/\.git$/', '', $p['source']['url'])
					: null,
			];
		}

		ksort($data);
		return $data;
	}


	/**
	 * @param  string $file
	 * @return array|null
	 */
	private function decode($file)
	{
		if (!is_file($file)) {
			$this->error = "File '$file' does not exist.'";
			return null;
		}

		$json = @file_get_contents($file);
		if ($json === false) {
			$this->error = error_get_last()['message'];
			return null;
		}

		$decoded = @json_decode($json, true);
		if (!is_array($decoded)) {
			$this->error = error_get_last()['message'];
			return null;
		}

		return $decoded;
	}


	/**
	 * @param  string $str
	 * @return string
	 */
	private function escapeHtml($str)
	{
		return htmlSpecialChars((string) $str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}
}
