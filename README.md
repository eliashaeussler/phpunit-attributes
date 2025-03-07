<div align="center">

# PHPUnit Attributes

[![Coverage](https://img.shields.io/coverallsCoverage/github/eliashaeussler/phpunit-attributes?logo=coveralls)](https://coveralls.io/github/eliashaeussler/phpunit-attributes)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/phpunit-attributes?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/phpunit-attributes/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/phpunit-attributes/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/phpunit-attributes/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/phpunit-attributes/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/phpunit-attributes/actions/workflows/tests.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/phpunit-attributes/php?logo=php)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)

</div>

A Composer library with additional attributes to enhance testing with PHPUnit.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/phpunit-attributes?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/phpunit-attributes?color=brightgreen)](https://packagist.org/packages/eliashaeussler/phpunit-attributes)

```bash
composer require --dev eliashaeussler/phpunit-attributes
```

## ⚡ Usage

The library ships with a ready-to-use PHPUnit extension. It must be registered
in your PHPUnit configuration file:

```diff
 <?xml version="1.0" encoding="UTF-8"?>
 <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
          bootstrap="vendor/autoload.php"
 >
+    <extensions>
+        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension" />
+    </extensions>
     <testsuites>
         <testsuite name="unit">
             <directory>tests</directory>
         </testsuite>
     </testsuites>
     <source>
         <include>
             <directory>src</directory>
         </include>
     </source>
 </phpunit>
```

Some attributes can be configured with custom extension parameters. These must
be added to the extension registration section like follows:

```diff
     <extensions>
-        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension" />
+        <bootstrap class="EliasHaeussler\PHPUnitAttributes\PHPUnitAttributesExtension">
+            <parameter name="fancyParameterName" value="fancyParameterValue" />
+        </bootstrap>
     </extensions>
```

## 🎢 Attributes

The following attributes are shipped with this library:

### PHP class

* [`#[ForbidsClass]`](docs/attributes/forbids-class.md)
* [`#[RequiresClass]`](docs/attributes/requires-class.md)

### PHP constant

* [`#[ForbidsConstant]`](docs/attributes/forbids-constant.md)
* [`#[RequiresConstant]`](docs/attributes/requires-constant.md)

### Composer package

* [`#[ForbidsPackage]`](docs/attributes/forbids-package.md)
* [`#[RequiresPackage]`](docs/attributes/requires-package.md)

### Environment

* [`#[ForbidsEnv]`](docs/attributes/forbids-env.md)
* [`#[RequiresEnv]`](docs/attributes/requires-env.md)

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
