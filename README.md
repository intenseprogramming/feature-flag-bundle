
[![Packagist](https://img.shields.io/packagist/dt/intprog/feature-flag-bundle.svg?style=popout)](https://packagist.org/packages/intprog/feature-flag-bundle)

# IntProg Feature Flag Bundle

:warning: This bundle is in early development.

:warning: Readme/Documentation will be finalized as the first stable release gets into the gears.

:heavy_exclamation_mark: This bundle targets ez platform v3 (yet to be released) only. :heavy_exclamation_mark:

IntProg Feature Flag Bundle is an eZ Platform bundle to handle feature-control giving you more control over when a
feature goes live.

Features can be checked for `enabled` and `disabled` state to save on precious exclamation marks (and headaches overlooking them)!

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
_intprogMigration:
    resource: '@IntProgFeatureFlagBundle/Resources/config/routing.yml'
```

## Configuration

To add a feature you can add new `yaml`-file in `config/packages/` with the following content

```yaml
int_prog_feature_flag:
    features:
        foo_feature:
            name: Example feature
            description: An example feature.
            default: true
        bar_feature:
            name:
                id: feature.with.translation.name
                context: feature_translation_context
            description:
                id: feature.with.translation.description
                context: feature_translation_context
            default: false
```

The name and description of features can be translated using `id` and `context`. The translation will be handled by the
`@translator`-service of symfony.

## Basic usage

### php

The `@intprog.repository.service.feature_flag`-service is used for internal handling but is publicly available.

Using the `@intprog.feature_flag.repository`-service is the recommended way. This "Wrapper" has some extra logic like
and is taking care of response-tagging and scope-resolution falling back to the next scope in succession until a defined
state is found or the configured default-value is used.

```php
/** @var IntProg\FeatureFlagBundle\API\FeatureFlagRepository $featureFlagRepository */
$featureFlagRepository->isEnabled('foo_feature');
$featureFlagRepository->isDisabled('bar_feature');
```

### twig

Functions to check a feature in `twig` are available.

```twig
{% if is_feature_enabled('foo_feature') %}
    feature is enabled
{% endif %}

{% if is_feature_disabled('bar_feature') %}
    feature is disabled
{% endif %}
```

### exposing to javascript etc.

Features can be exposed to a javascript-friendly context using either json, javascript or data-attributes.

Note: All of those functions export the result unescaped.

#### json

You can expose the features in a simple json format.

```twig
{{ expose_features_json() }}
```

This will result in an json-string containing an object with identifiers as keys and one boolean
attribute "`enabled`".

```json
{"feature_foo": {"enabled": true}, "feature_bar": {"enabled": false}}
```

#### data-attributes

Will output data-attributes to use on dom-elements.

```twig
<body {{ expose_features_data_attributes() }}>
```

This will result in the following html-snippet:

```html
<body data-feature-foo="true" data-feature-bar="false">
```

Note that the `_` will be replaced by `-`. Javascript-side access will be `body.dataset.featureFoo`.

#### javascript

Exposing to javascript will use the json-function to assign the value to a window-attribute.

```twig
Exposure in "window.ipFeatures":
{{ expose_features_javascript() }}

Or with adjusted key in the window object (will write the states to "window.features"):
{{ expose_features_javascript('features') }}
```

The javascript can be adjusted by overriding `@ezdesign/feature_flag/expose/javascript.html.twig`.

## Road to release

### completed

- Full configuration-scope support (global, siteaccess, siteaccess-group, default)
- Feature Management SPI
- Persistence Cache Handling
- HTTP-Cache support
- Dispatching events from SPI-Interactions
- Basic administration interface
- Policy setup (new policy type including a custom access-limitation)
- Enabling exposure to javascript
    - setting features as exposed/internal
    - handler to expose features in `window`-context, as `json`, as `html-data-tags`

### pending

- Administration interface cleanup
- Temporary activation of features via Cookie for Testing-Purposes

### Post release roadmap

- timed control for feature state changes
- feature dependencies (determining feature state including required features)
