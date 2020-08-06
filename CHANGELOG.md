# Changelog

All notable changes to this project will be documented in this file, per [the Keep a Changelog standard](http://keepachangelog.com/), and will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - TBD

## [0.4.0] - 2020-08-06
### Added
- Added `CustomEvent` and associated IE11 polyfill to listen for changes outside of the plugin (props [@jaredrethman](https://github.com/jaredrethman) via [#32](https://github.com/10up/post-finder/pull/32))
- Documentation updates (props [@jeffpaul](https://github.com/jeffpaul) via [#33](https://github.com/10up/post-finder/pull/33))

### Changed
- Moved saved connections above the search field (props [@gthayer](https://github.com/gthayer) via [#30](https://github.com/10up/post-finder/pull/30))
- Only loading the plugin if on WordPress context (props [@nicholasio](https://github.com/nicholasio) via [#29](https://github.com/10up/post-finder/pull/29))
- Update `grunt-sass` package version to v2.1 (props [@jaredrethman](https://github.com/jaredrethman) via [#32](https://github.com/10up/post-finder/pull/32))
- Versioning to follow SemVer (props [@daveross](https://github.com/daveross) via [#27](https://github.com/10up/post-finder/pull/27))

## [0.3.0] - 2017-07-05
### Added
- Pagination to search results (props [@dkotter](https://github.com/dkotter) via [#21](https://github.com/10up/post-finder/pull/21))
- Allow the option to turn off the display of the Recent Posts dropdown (props [@dkotter](https://github.com/dkotter) via [#22](https://github.com/10up/post-finder/pull/22))
- Use Grunt for compiling (props [@dkotter](https://github.com/dkotter) via [#18](https://github.com/10up/post-finder/pull/18))
- Internationalization support (props [@dkotter](https://github.com/dkotter) via [#19](https://github.com/10up/post-finder/pull/19))

### Changed
- Localization updates (props [@eugene-manuilov](https://github.com/eugene-manuilov) via [#24](https://github.com/10up/post-finder/pull/24))
- Adjust styling to better match WordPress (props [@dkotter](https://github.com/dkotter) via [#17](https://github.com/10up/post-finder/pull/17))
- Code formatting and cosmetic fixes (props [@dkotter](https://github.com/dkotter), [@eugene-manuilov](https://github.com/eugene-manuilov) via [#20](https://github.com/10up/post-finder/pull/20), [#25](https://github.com/10up/post-finder/pull/25))
- `composer.json` properties updates (props [@eugene-manuilov](https://github.com/eugene-manuilov) via [#23](https://github.com/10up/post-finder/pull/23))

## [0.2.0] - 2017-07-05
### Added
- Ability to add posts when none exist yet
- Proper namespacing, serialization, sanitization
- Support for limits, query args, multiple post types, template filtering
- Select works on change or click
- `composer.json` file

### Changed
- Major updates to CSS and styles
- Overlay markup, JS improvements, issue with JS and CSS paths

### Fixed
- Undefined variable reference, form submission denial

## [0.1.0] - 2013-08-07
- Initial release of Post Finder plugin.

[Unreleased]: https://github.com/10up/post-finder/compare/trunk...develop
[0.4.0]: https://github.com/10up/post-finder/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/10up/post-finder/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/10up/post-finder/compare/cac1515...0.2.0
[0.1.0]: https://github.com/10up/post-finder/tree/cac1515fd654ac1bbb2c5528bf8f967417f1d473
