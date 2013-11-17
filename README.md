Totem
=====
[![Build Status](https://travis-ci.org/Taluu/Totem.png?branch=develop)](https://travis-ci.org/Taluu/Totem)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Taluu/Totem/badges/quality-score.png?s=b71f67e353a379e19b651697285ffed18d6f1554)](https://scrutinizer-ci.com/g/Taluu/Totem/)
[![Code Coverage](https://scrutinizer-ci.com/g/Taluu/Totem/badges/coverage.png?s=526dc791403caf731f6abb78d713291c68cf8446)](https://scrutinizer-ci.com/g/Taluu/Totem/)

```
       \\\\////
        |.)(.|
        | || |
        \(__)/   Changeset calculator between two state of a data
        |-..-|   Requires PHP 5.4 ; Compatible PHP 5.5
        |o\/o|
   .----\    /----.
  / / / |~~~~| \ \ \
 / / / /|::::|\ \ \ \
'-'-'-'-|::::|-'-'-'-'
       (((^^)))
        >>><<<   Snapshots currently natively supported :
        ||||||   - Object
        (o)(o)   - Array
        | /\ |
        (====)   Everything happens on the develop branch ; the master branch
        |_/\_|   is for releases or hotfixes only !
        (_/\_)
       _|_,__|
      (___\___)
```

Documentation
=============
For any pieces of document, please look for the docs/ directory. You may also 
check up [the compiled version](http://totem.readthedocs.org/en/latest/index.html)

Installation
============
You have multiple ways to install Totem. If you are unsure what to do, go with
[the archive release](#archive-release).

### Archive Release
1. Download the most recent release from the [release page](https://github.com/Taluu/Totem/releases)
2. Unpack the archive
3. Move the files somewhere in your project

### Development version
1. Install Git
2. `git clone git://github.com/Taluu/Totem.git`

### Via Composer
1. Install composer in your project: `curl -s http://getcomposer.org/installer | php`
2. Create a `composer.json` file in your project root:

    ```javascript

      {
        "require": {
          "taluu/totem": "~1.2.0"
        }
      }
    ```

3. Install via composer : `php composer.phar install`

Basic Usage
===========
```php
<?php

use Totem\Snapshot\ObjectSnapshot;

$object = (object) ['foo' => 'bar', 'baz' => 'qux'];
$snapshot = new ObjectSnapshot($object); // Totem\Snapshot\ObjectSnapshot

$object->foo = 'fubar';
$set = $snapshot->diff(new ObjectSnapshot($object)); // Totem\Set

var_dump($set->hasChanged('foo'),
         $set->getChange('foo')->getOld(),
         $set->getChange('foo')->getNew(),
         $set->hasChanged('bar'));

/* 
 * expected result :
 *
 * bool(true)
 * string(3) "bar"
 * string(5) "fubar"
 * bool(false)
 */
```

Running Tests
=============
```console
$ php composer.phar install
$ bin/phpunit
```

