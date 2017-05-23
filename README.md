[![Build Status](https://travis-ci.org/incompass/AirbrakeBundle.svg?branch=master)](https://travis-ci.org/incompass/AirbrakeBundle)

# Airbrake Bundle

This bundle integrates Symfony with Airbrake.

## Installation

### Composer
```
composer require incompass/airbrake-bundle
```

### Symfony

Add the bundle to your AppKernel.

```
public function registerBundles()
   {
       $bundles = array(
           ...
           new Incompass\AirbrakeBundle\AirbrakeBundle(),
           ...
       );
   }
```

Add configuration values:

```
airbrake:
    project_id: %project_id%
    project_key: %project_key%
    ignored_exceptions: %ignored_exceptions%
    host: %airbrake_host%
```

## Contributors

Joe Mizzi (casechek/incompass)

## Attributions

This bundle was influenced by the following other Airbrake bundles:

https://github.com/aminin/airbrake-bundle
https://github.com/incompass/AirbrakeBundle
