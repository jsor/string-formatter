StringFormatter
===============

[![Build Status](https://travis-ci.org/jsor/string-formatter.svg?branch=master)](https://travis-ci.org/jsor/string-formatter)
[![Coverage Status](https://coveralls.io/repos/jsor/string-formatter/badge.svg?branch=master&service=github)](https://coveralls.io/github/jsor/string-formatter?branch=master)

Installation
------------

Install the latest version with [Composer](https://getcomposer.org).

```bash
composer require jsor/string-formatter
```

Check the [Packagist page](https://packagist.org/packages/jsor/string-formatter) for
all available versions.

NameFormatter
=============

To use the NameFormatter, you need to install the 
[`jsor/locale-data`](https://packagist.org/packages/jsor/locale-data) package.

```bash
composer require jsor/locale-data
```

### NameFormatter::format

```php
public string NameFormatter::format(string $format[, array $values]);
```

Returns the appropriate representation of a person’s name and title as a string
produced according to the formatting string `$format` and the name parts defined
by `$values`.
 
The `$format` argument can contain any combination of characters and field
descriptors.

The following field descriptor are supported.

* `%f`
    Family names.
* `%F`
    Family names in uppercase.
* `%g`
    First given name.
* `%G`
    First given initial.
* `%l`
    First given name with latin letters. In some cultures, eg on Taiwan it is
    customary to also have a first name written with Latin letters, although the
    rest of the name is written in another script.
* `%o`
    Other shorter name, eg. "Bill".
* `%m`
    Additional given names.
* `%M`
    Initials for additional given names.
* `%p`
    Profession.
* `%s`
    Salutation, such as "Doctor"
* `%S`
    Abbreviated salutation, such as "Mr." or "Dr."
* `%d`
    Salutation list index for the list passed as third argument 
    (`$salutationList`).
    Supports the FDCC-sets conventions, with `1` for the `name_gen`, 
    `2` for `name_mr`, `3` for `name_mrs`, `4` for `name_miss`, `5` for `name_ms`.
* `%t`
    If the preceding field descriptor resulted in an empty string, then the
    empty string, else a space (or any other string defined in `$values`). 

The array argument `$values` can define a value for each field descriptor.
The keys can be either the descriptor character or a named key.

The following keys are supported.

* `family_name` or `family_names` or `f` (for `%f`)
* `family_name_in_uppercase` or `family_names_in_uppercase` or `F` (for `%F`)
* `given_name` or `given_names` or `g` (for `%g`)
* `given_initial` or `given_initials` or `G` (for `%G`)
* `given_name_with_latin_letters` or `given_names_with_latin_letters` or `l` (for `%l`)
* `other_shorter_name` or `other_shorter_names` or `o` (for `%o`)
* `additional_given_name` or `additional_given_names` or `m` (for `%m`)
* `initials_for_additional_given_name` or `initials_for_additional_given_names` or `M` (for `%M`)
* `profession` or `professions` or `p` (for `%p`)
* `salutation` or `salutations` or `s` (for `%s`)
* `abbreviated_salutation` or `abbreviated_salutations` or `S` (for `%S`)
* `salutation_list_index` or `salutations_list_index` or `d` (for `%d`)

#### Example

```php
use Jsor\NameFormatter;

$format = '%d%t%g%t%m%t%f';
$values = array(
    'given_name' => 'John',
    'family_name' => 'Doe',
    'salutation' => 'Mr.',
);

$formatter = new NameFormatter('en_US');
echo $formatter->format($format, $values);
```

The above example will output:

```
Mr. John Doe
```

### NameFormatter::formatDefault

```php
public string NameFormatter::formatDefault([array $values]);
```

Same as `NameFormatter::format`, but uses a default name format according to the
locale passed to the NameFormatter constructor.

#### Example

```php
use Jsor\NameFormatter;

$values = array(
    'given_name' => 'John',
    'family_name' => 'Doe',
    'salutation_list_index' => 2 // name_mr
);

$enUsFormatter = new NameFormatter('en_US');
echo $enUsFormatter->formatDefault($values)."\n";

$deDeFormatter = new NameFormatter('de_DE');
echo $deDeFormatter->formatDefault($values)."\n";

$zhTwFormatter = new NameFormatter('zh_TW');
echo $zhTwFormatter->formatDefault($values)."\n";
```

The above example will output:

```
Mr. John Doe
Herr John Doe
Doe John 先生
```

License
-------

Copyright (c) 2015 Jan Sorgalla. Released under the [MIT](LICENSE?raw=1) license.
