# Vendor Versions
Vendor Versions is a bar panel for [Tracy](https://tracy.nette.org/) debugger. It loads `composer.json` and `composer.lock` files and shows you versions of all currently installed libraries.

![Tracy panel screenshot](https://github.com/milo/vendor-versions/raw/master/screenshot.png)


# Installation
Use [Composer](https://getcomposer.org) and require `milo/vendor-versions` package.


## With the Nette DI Container
Register panel in `config.neon`:
```yaml
extensions:
	vendorVersions: Milo\VendorVersions\Bridges\Nette\DI\Extension(%debugMode%)

# Optionally set path to composer.json file
vendorVersions:
	path: 'path/to/composer.json'

# When you use panel multiple times, you may wish to adjust some visual properties
vendorVersions:
	panelTitle: 'For deployment'
	iconColor: 'green'
```


## Manual panel registration
```php
Tracy\Debugger::getBar()->addPanel(
	new Milo\VendorVersions\Panel
);


# Optionally set path to composer.json file
Tracy\Debugger::getBar()->addPanel(
	new Milo\VendorVersions\Panel(__DIR__ . '/some/dir')
);

# When you use panel multiple times, you may wish to adjust some visual properties
Tracy\Debugger::getBar()->addPanel(
	$panel = new Milo\VendorVersions\Panel(__DIR__ . '/some/dir')
);
$panel->setPanelTitle('For deployment');
$panel->setIconColor('green');
```
