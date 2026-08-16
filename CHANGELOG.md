# Changelog

All Notable changes to `stampable` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

### Added
- A test suite of 44 tests: the trait's own behaviour, and feature tests running real Eloquent models on an in-memory
  sqlite database through Orchestra Testbench.
- GitHub Actions workflows running the test suite (PHP 8.4 and 8.5 against Laravel 12 and 13, lowest and highest
  dependencies), the coding style check, the static analysis and the code coverage on every pull request and on every
  push to `master`.
- A `Dockerfile` (with pcov, for coverage) and a `Makefile` to run everything inside a container.
- A `phpcs.xml.dist` ruleset, a `phpstan.neon.dist` (level 7 with larastan, no baseline) and a `rector.php`.
- The `analyse`, `rector`, `test-coverage` and `ci` composer scripts.
- `hasStamp()` and `getStampField()` on the trait and the contract.
- `Shetabit\Stampable\Exceptions\StampNotFoundException`.

### Changed
- **Breaking:** PHP 8.4 is now the minimum required version (was PHP 7.1), and Laravel 12 and 13 are the supported
  versions (were Laravel 5.1+).
- **Breaking:** every method of `Contracts\Stampable` and of the `HasStamps` trait declares its parameter and return
  types. An implementation of the contract has to declare compatible types.
- **Breaking:** a stamp the model does not declare now throws a `StampNotFoundException` instead of failing quietly.
  `isStampedBy()` and `isUnstampedBy()` used to answer `false` for a stamp that does not exist, which reads as "this
  record is not published" for what is really a typo. `markAsStamped()`, `markAsUnstamped()`, `scopeStamped()` and
  `scopeUnstamped()` used to raise an "undefined array key" warning and then write to — or query — a column named
  `null`.
- `illuminate/database` is required explicitly; the trait has always used Eloquent's query builder.

### Removed
- **Breaking:** the private `getFreshTimestamp()` of the trait, which shadowed nothing but duplicated Eloquent's own
  `freshTimestamp()` and fell back to a `date()` string outside of Laravel. The model's own timestamp is used now, so
  a stamp is written in the same format as `created_at`.
- The Travis CI configuration (`.travis.yml`), replaced by GitHub Actions.
- The StyleCI configuration (`.styleci.yml`), replaced by the PHP_CodeSniffer workflow.
- The StyleCI, Code Climate and Scrutinizer badges of the readme, replaced by the workflow badges and a coverage badge.
- The `branch-alias` of `composer.json`.

### Fixed
- **The negated dynamic scope queried a column called `null`.** `__call()` recognised `Post::unpublished()` and then
  forwarded the *method name* to the scope instead of the stamp name — `scopeUnstamped($query, 'unpublished')` — so
  the scope looked `unpublished` up in the stamp list, found nothing, and ran `whereNull(null)`. It only ever worked
  for the positive form, and only because there the method name happens to equal the stamp name.
- **The dynamic scopes ignored the case of the stamp.** `getStampScope()` compared a lower-cased method name against
  the stamp names as they were declared, so a stamp named `isVerified` had no working scope at all.
- **A dynamic scope no longer depends on the order the stamps are declared in.** `getStampScope()` looped over every
  stamp without stopping at the one it matched, so a later stamp could overwrite an earlier match.
- **A stamp is no longer confused with the negation of another.** `getStampBehavior()` looped over the stamps in the
  outer loop and broke out of the inner one only, so a later stamp overwrote the match of an earlier one and the
  answer depended on the order the stamps were declared in. The prefixes are now tried in an order that lets a stamp
  whose own name starts with `un` win over the negation of the stamp behind it.
- `__call()` no longer carries a branch for `increment` and `decrement` that could not be reached — Eloquent declares
  both, so `__call()` never sees them — and that would have called itself until the stack ran out if it ever had
  been. They are left to Eloquent, and a test covers that they still work.
- **A model that declares no stamps behaves like a plain model.** `getStamps()` read `$this->stamps` behind an
  `!empty()` on a property that does not have to exist.

## Date - 2019-01-09

### Fixed
- Nothing

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
