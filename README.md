# Vendor Versions
Vendor Versions is a bar panel for [Tracy](https://tracy.nette.org/) debugger. It loads `composer.json` and `composer.lock` files and shows you versions of all currently installed libraries.

![Tracy panel screenshot](https://github.com/milo/vendor-versions/raw/master/screenshot.png)


# Installation
Use [Composer](https://getcomposer.org) and require `milo/vendor-versions` package.

If you are using Nette DI container, register panel in `config.neon`:
```yaml
extensions:
	vendorVersions: Milo\VendorVersions\Bridges\Nette\DI\Extension(%debugMode%)

# Optionally set path do directory with composer.json and composer.lock files
vendorVersions:
	dir: 'some/path'
```

If you are not using Nette DI, register panel manually:
```php
Tracy\Debugger::getBar()->addPanel(
	new Milo\VendorVersions\Panel
);


# Optionally with a path to directory with composer.json and composer.lock files
Tracy\Debugger::getBar()->addPanel(
	new Milo\VendorVersions\Panel(__DIR__ . '/some/dir')
);
```
