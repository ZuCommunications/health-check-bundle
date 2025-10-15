# Changelog

## [0.3.0](https://github.com/ZuCommunications/health-check-bundle/compare/v0.2.1...v0.3.0) (2025-10-15)


### Features

* add symfony 7 compatibility ([a89952b](https://github.com/ZuCommunications/health-check-bundle/commit/a89952b81d778c6ac624a2491ceffcca95ccd6bc))

## [0.3.0](https://github.com/ZuCommunications/health-check-bundle/compare/v0.2.1...v0.3.0) (2025-10-15)

### Added

* add official support for Symfony 7.3 while retaining compatibility with Symfony 6.4

### Changed

* update all Symfony runtime dependencies to `^6.4|^7.0` and refresh the lockfile against Symfony 7.3
* align dev tools (BrowserKit, CssSelector, Mailer, PHPUnit Bridge) with Symfony 7.3
* document repository contribution guidelines in `AGENTS.md`

### Upgrade Notes

* update consumer applications with `composer require zucommunications/health-check-bundle:^0.3` to pull the Symfony 7 compatible release
* ensure host projects register the bundle and route imports as before; no new configuration is required unless `framework.property_info` is already customized

## [0.2.1](https://github.com/ZuCommunications/health-check-bundle/compare/v0.2.0...v0.2.1) (2025-07-09)


### Bug Fixes

* container called non public mailer.mailer ([#15](https://github.com/ZuCommunications/health-check-bundle/issues/15)) ([69339a4](https://github.com/ZuCommunications/health-check-bundle/commit/69339a4d1830b4eb3472ba834d4224e35b5cc34f))


### Miscellaneous

* update readme ([#10](https://github.com/ZuCommunications/health-check-bundle/issues/10)) ([288ac83](https://github.com/ZuCommunications/health-check-bundle/commit/288ac8373a74f433565b18625f81f9e05e49567b))

## [0.2.0](https://github.com/ZuCommunications/health-check-bundle/compare/v0.1.0...v0.2.0) (2024-07-09)


### Features

* add http status code to check ([6652ba3](https://github.com/ZuCommunications/health-check-bundle/commit/6652ba39619c7e5e74e24dde0ec2d84bd82de5f2))


### Miscellaneous

* added proper ci checks ([365534d](https://github.com/ZuCommunications/health-check-bundle/commit/365534d7f6f667bc7e4dacddf5be8b84bd3b89f2))
* ci adjust ([2150217](https://github.com/ZuCommunications/health-check-bundle/commit/215021791d8b0d9bb98b047f7160fb36ff516238))
* comment out cs-fixer for now ([741aa57](https://github.com/ZuCommunications/health-check-bundle/commit/741aa57fa3f56e2e673d822a7a4c38408ecd728b))
* run cs fixer ([ffaf4e0](https://github.com/ZuCommunications/health-check-bundle/commit/ffaf4e0545a619afc087e8782fd7a3e11a89abc3))

## 1.0.0 (2024-07-03)


### Miscellaneous

* added ci and edited readme ([10aa1cf](https://github.com/ZuCommunications/health-check-bundle/commit/10aa1cf04062dfbb3fa7ce5105ec44b2bd273540))
* initial commit ([b593c9c](https://github.com/ZuCommunications/health-check-bundle/commit/b593c9ce9ac87252384b9fc3401928c55a85759f))
* **main:** release 1.0.0 ([130f05b](https://github.com/ZuCommunications/health-check-bundle/commit/130f05b9a443c9bc7c883e01f75ec964b83c588e))
* setup bundle ([edb8a43](https://github.com/ZuCommunications/health-check-bundle/commit/edb8a43fb4604fe4400ff7c5e2b4f7c63fc98871))
* setup release ([6f84447](https://github.com/ZuCommunications/health-check-bundle/commit/6f844479253e359dd9ba7a7fd1b7e14c56d89248))

## 1.0.0 (2024-07-02)


### Miscellaneous

* initial commit ([b593c9c](https://github.com/ZuCommunications/health-check-bundle/commit/b593c9ce9ac87252384b9fc3401928c55a85759f))
* setup bundle ([edb8a43](https://github.com/ZuCommunications/health-check-bundle/commit/edb8a43fb4604fe4400ff7c5e2b4f7c63fc98871))
* setup release ([6f84447](https://github.com/ZuCommunications/health-check-bundle/commit/6f844479253e359dd9ba7a7fd1b7e14c56d89248))
