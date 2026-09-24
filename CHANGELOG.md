# Changelog

All notable changes to `b13/backendpreviews` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] — 2026-09-24

### Removed

- **Support for TYPO3 v10.4, v11.5, and v12.4 is dropped**, together with PHP
  below 8.2. The extension now requires TYPO3 v13.4 or v14 and PHP 8.2. If you
  are on an older TYPO3, stay on the 1.5 releases—they keep working, they just
  do not get new features.
- **The `tt_content_drawItem` hook is gone.** `PageLayoutViewDrawItemHookInterface`
  no longer exists in the TYPO3 versions this release supports, so
  `B13\Backendpreviews\Hooks\BackendPreviewRenderer` and its registration were
  removed. Previews are rendered through the `previewRenderer` registered on
  `tt_content` plus the `PageContentPreviewRenderingEvent` listener, which is
  how it already worked on v12 and above. Only relevant if your own code
  referenced that class.
- **`ext_tables.php` is gone.** It only registered the pre-v12 backend skin;
  the stylesheet is registered unconditionally in `ext_localconf.php` now.
- **The Fontawesome icon registration is gone.** `exclamation-triangle` and
  `exclamation-circle` were registered but never used—the shipped warning
  partials use the core icons `actions-exclamation-*-alt`.

### Changed

- **Shipped partials sanitize their HTML output.** `Text`, `Listgroup`, and
  `Media/ImageTile` now use `f:sanitize.html()` instead of `f:format.raw()`, so
  markup passed in as `content` (or kept via `keepTags`) goes through TYPO3's
  `default` HTML sanitizer preset. Markup outside its allow list is removed:
  custom elements, form elements, `iframe`, `script`, and `on*` attributes.
  Unknown tags are shown escaped. Text cropped in the middle of a list no longer
  leaves unclosed tags behind. If your previews rely on such markup, override
  the partial or configure a sanitizer preset (see README).
- **Both render paths use `ViewFactoryInterface`.** `StandaloneView`, which is
  removed in TYPO3 v14, is no longer referenced anywhere. Preview templates are
  unaffected; the same templates, layouts, and partials keep rendering.
- **Metadata corrected:** the `authors` block in `composer.json` and the
  `author` in `ext_emconf.php` now name b13 GmbH, with the extension's creator
  credited in the README instead. The extension category changed from `fe` to
  `be`, which is what it actually is.

### Documentation

- **README brought in line with the code.** It described a hook that no longer
  exists, still listed TYPO3 v10.4 and PHP 7.4 as supported, pointed at
  `settings.yaml` instead of `config.yaml` for the site set dependency, and used
  a `previewRenderer` example (`textpic`) that has not applied for several major
  versions—`fluid_styled_content` registers none at all on v13 and v14.
- **A new "What Your Template Gets" section** spells out that v14 assigns a
  single `{record}` object while v13 assigns the row's fields individually, and
  which variables this extension adds on each version. Templates that read
  element fields directly need both spellings to work on v13 and v14; see
  [Breaking-92434](https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-92434-UseRecordAPIInPageModulePreviewRendering.html)
  for the core migration path.
- **`SECURITY.md` added** with the reporting route and a note on what a preview
  template can expose—in particular that `b13:getDatabaseRecord` reads whatever
  table it is pointed at without applying backend user permissions.
- **`CHANGELOG.md` added**, which is this file.

## [1.5.3] — 2026-08-26

### Added

- **Non-image media files get a fallback icon** in the preview instead of a
  broken thumbnail: video, audio, and everything else render the matching
  mimetype icon.

### Documentation

- **README completed** with a requirements section, the site set as an
  alternative to importing the PageTsConfig, and documentation for the three
  shipped ViewHelpers (`b13:renderBodytext`, `b13:getDatabaseRecord`,
  `b13:explodeList`).

## [1.5.2] — 2026-04-24

### Added

- **Contextual edit URL on TYPO3 v14.** Previews link through
  `record_edit_contextual`, so clicking one opens the element in the page
  module's own editing context instead of navigating away.

### Fixed

- Compatibility with TYPO3 14.3 and a v14 deprecation.

## [1.5.1] — 2026-02-26

### Fixed

- **The icon registration no longer runs in `ext_localconf.php`.** Using
  `IconRegistry` that early broke on some installations.

## [1.5.0] — 2026-02-13

### Added

- **TYPO3 v14 and Fluid 5 support**, alongside the existing versions.

### Changed

- **List group items are separated more clearly** in the preview markup.

### Fixed

- Several fixes to the shipped partials, the `Link` partial on v14, and
  dependency injection for `DatabaseRowService`.

## [1.4.3] and earlier

Released between 2021 and 2024, covering the TYPO3 v10 to v13 era. See the
[README](README.md) for what the extension does and how it is configured.
