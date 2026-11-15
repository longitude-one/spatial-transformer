# LongitudeOne Spatial Transformer

[![Release](https://img.shields.io/github/v/release/longitude-one/spatial-transformer?include_prereleases&sort=semver&label=release)](https://github.com/longitude-one/spatial-transformer/releases)
[![PHP](https://img.shields.io/packagist/php-v/longitude-one/spatial-transformer?label=php)](https://packagist.org/packages/longitude-one/spatial-transformer)
[![License](https://img.shields.io/packagist/l/longitude-one/spatial-transformer)](https://github.com/longitude-one/spatial-transformer/blob/main/LICENSE)
[![CI](https://github.com/longitude-one/spatial-transformer/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/spatial-transformer/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/longitude-one/spatial-transformer/branch/main/graph/badge.svg)](https://codecov.io/gh/longitude-one/spatial-transformer/tree/main)
[![Monthly downloads](https://img.shields.io/packagist/dm/longitude-one/spatial-transformer)](https://packagist.org/packages/longitude-one/spatial-transformer)
[![Documentation](https://img.shields.io/badge/docs-v1-blue)](https://www.longitude-one.fr/spatial-transformer/v1/index.html)

Transform spatial objects through an immutable, constructor-injected Strategy API.
Requires PHP 8.4 or newer and `longitude-one/spatial-types`.

## Installation

```bash
composer require longitude-one/spatial-transformer
```

## Usage

```php
use LongitudeOne\SpatialTransformer\Transformer;

// Supply your own implementation of TransformStrategyInterface.
$strategy = new SomeTransformStrategy(/* configuration */);
$transformer = new Transformer($strategy);
$transformed = $transformer->transform($spatial);
```

This package provides the orchestration API; concrete strategies are application-defined.
See [transformation strategies](docs/strategies.md) and
[contributing](https://github.com/longitude-one/spatial-transformer/blob/main/CONTRIBUTING.md).

## License

Released under the [MIT license](LICENSE).
