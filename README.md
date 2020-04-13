
[![Build Status](https://travis-ci.org/intenseprogramming/feature-flag-bundle.svg?branch=master)](https://travis-ci.org/intenseprogramming/feature-flag-bundle)
[![Packagist](https://img.shields.io/packagist/dt/intprog/feature-flag-bundle.svg?style=popout)](https://packagist.org/packages/intprog/feature-flag-bundle)
[![codecov](https://codecov.io/gh/intenseprogramming/feature-flag-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/intenseprogramming/feature-flag-bundle)

# IntProg Feature Flag Bundle

IntProg Feature Flag Bundle is an eZ Platform bundle to handle feature-control giving you more control over when a
feature goes live.

Features can be checked for `enabled` and `disabled` state to save on precious exclamation marks (and headaches
overlooking them)!

## Installation

Add the bundle to your `config/bundles.php`

```php
<?php

return [
    // ...
    IntProg\FeatureFlagBundle\IntProgFeatureFlagBundle::class => ['all' => true],
    // ...
];
```

Add the routing configuration at `config/routes/int_prog_feature_flag.yaml` (or anywhere else it gets included)

```yaml
_intprogFeatureFlags:
    resource: '@IntProgFeatureFlagBundle/Resources/config/routing.yml'
```

## Configuration

Refer to the [configuration documentation](doc/CONFIGURATION.md).

## Usage

Refer to the [usage documentation](doc/USAGE.md).

## Changelog

Refer to the [changelog documentation](doc/CHANGELOG.md) or the 
[github release overview](https://github.com/intenseprogramming/feature-flag-bundle/releases).
