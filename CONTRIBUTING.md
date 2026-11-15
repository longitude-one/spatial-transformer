# Contributing

Use PHP 8.4 or newer, Composer 2 and Node.js 22 or newer.

```bash
composer install
composer update-tools
npm install --ignore-scripts
composer test
composer quality
composer validate --strict
```

Source uses `LongitudeOne\SpatialTransformer\` under `lib/`; tests mirror it under
`tests/` with namespace `LongitudeOne\SpatialTransformer\Tests\`.
Use strict types, the shared copyright header and Conventional Commits.
Public documentation is written in English.

## Quality and coverage

`composer quality` runs PHP CS Fixer in check mode, PHPStan level 9, PHPMD,
PHP_CodeSniffer and Markdown lint. Use `composer cs-fix` to apply formatting.
The four PHP tools have isolated Composer environments under `quality/`.
Composer lockfiles and dependency directories remain local and ignored.

Enable PCOV or Xdebug before running `composer coverage`. Reports go to
`build/coverage/`. `composer coverage-ci` uses `phpunit.ci.xml` and generates only
Clover XML. `composer coverage-check` verifies a nonempty report; no numeric
threshold is imposed. `composer check` also validates and audits dependencies.

## Continuous integration

Pull requests targeting `main`, manual runs and the weekly schedule exercise PHP
8.4 and 8.5. Changed-path selection avoids irrelevant jobs. Markdown has a separate
job and the aggregate required check is named `CI`.

CI caches Composer and npm downloads. PHP 8.4 produces Clover coverage for Codecov
when `CODECOV_TOKEN` is configured. A lightweight workflow updates the Codecov
baseline on pushes to `main`. CI publishes no HTML coverage or coverage artifacts.

Before merge, run tests and quality checks and obtain an independent review.
The maintainer controls merges and releases. Configure the repository's Codecov
token and require the aggregate `CI` check in branch protection.

## Release tooling

Run from the repository root with
`commit-and-tag-version --config quality/commit-and-tag-version/.versionrc.cjs --dry-run`
to preview a release. Actual releases require the maintainer's authorization.
