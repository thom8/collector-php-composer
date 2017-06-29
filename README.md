# collector-php-composer

[![dependencies.io](https://img.shields.io/badge/dependencies.io-collector-3DA4E9.svg)](https://www.dependencies.io/docs/collectors/)
[![Docker](https://img.shields.io/badge/dockerhub-collector--php--composer-22B8EB.svg)](https://hub.docker.com/r/dependencies/collector-php-composer/)
[![GitHub release](https://img.shields.io/github/release/dependencies-io/collector-php-composer.svg)](https://github.com/dependencies-io/collector-php-composer/releases)
[![Build Status](https://travis-ci.org/dependencies-io/collector-php-composer.svg?branch=master)](https://travis-ci.org/dependencies-io/collector-php-composer)
[![license](https://img.shields.io/github/license/dependencies-io/collector-php-composer.svg)](https://github.com/dependencies-io/collector-php-composer/blob/master/LICENSE)

A [dependencies.io](https://www.dependencies.io)
[collector](https://www.dependencies.io/docs/collectors/)
that uses `composer install` to collect php dependencies.

## Usage

### .dependencies.yml

```yaml
collectors:
- type: php-composer
  path: /  # path with composer.json (and composer.lock)
  actors:
  - ...
```

### Works well with

- [repo-issue actor](https://www.dependencies.io/docs/actors/repo-issue/) ([GitHub repo](https://github.com/dependencies-io/actor-repo-issue/))


## Resources

- https://getcomposer.org/

## Support

Any questions or issues with this specific collector should be discussed in [GitHub
issues](https://github.com/dependencies-io/collector-php-composer/issues). If there is
private information which needs to be shared then you can instead use the
[dependencies.io support](https://app.dependencies.io/support).
