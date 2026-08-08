# Changelog

All notable changes to `local_quizidnumber` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.3] — 2026-08-08

Documentation-only release. No code change — upgrading is optional and only
matters if you want the corrected README in your installed copy.

### Fixed

- README documented the watermark opacity as `0.08`; it has been `0.12` since
  the value was nudged up, so anyone following the customising notes was
  starting from the wrong number.

### Added

- README now documents `LICENSE`, the CI workflow, and a Development section
  listing the Moodle, PHP and database combinations covered by CI.

### Notes

- Plugin version: `2026080802`.

## [1.0.2] — 2026-08-08

Tidy-up release. No change to what the plugin does or how it behaves on a site.

### Added

- `pix/icon.svg` and `pix/logo.svg` — plugin artwork, previously untracked and
  therefore missing from the 1.0.1 package.

### Removed

- Unused `studentid` language string. It was defined but never referenced —
  the watermark renders the ID number on its own, with no label prefix. Sites
  carrying a local override or translation for this key can drop it; Moodle
  ignores orphaned overrides, so nothing breaks if they don't.

### Notes

- Plugin version: `2026080801`.

## [1.0.1] — 2026-08-08

Housekeeping release addressing the Moodle plugin review findings. No change to
what the plugin does or how it behaves on a site.

### Added

- `LICENSE` file carrying the full GNU GPL v3 text at the plugin root, so the
  licensing terms ship with the package.
- GitHub Actions workflow (`.github/workflows/ci.yml`) running the
  [moodle-plugin-ci](https://github.com/moodlehq/moodle-plugin-ci) checks
  against Moodle 4.4, 4.5, 5.0, 5.1 and 5.2 on PostgreSQL and MariaDB.

### Changed

- Repository renamed to `moodle-local_quizidnumber` to follow the recommended
  `moodle-{plugintype}_{pluginname}` pattern. The plugin directory inside
  Moodle is unchanged and must still be `local/quizidnumber`.

### Fixed

- Coding-style violations reported by the Moodle Code Checker on the first CI
  run: a blank line after the opening brace of `hook_callbacks` and `provider`
  (`PSR12.Classes.OpeningBraceSpace`), and the English language strings not
  being in alphabetical order (`moodle.Files.LangFilesOrdering`).

### Notes

- Plugin version: `2026080800`.

## [1.0.0] — 2026-07-26

First stable release.

### Added

- Tiled, diagonal watermark of the student's `idnumber` on `mod-quiz-attempt`
  and `mod-quiz-review` pages, injected via
  `\core\hook\output\before_standard_top_of_body_html_generation`.
- Capability check that hides the watermark from anyone holding
  `mod/quiz:viewreports`, so teachers, managers and admins reviewing an attempt
  are never watermarked.
- "Not set" fallback string for users with no ID number on their profile.
- Print stylesheet rule keeping the watermark visible on printed and
  PDF-exported copies of an attempt or review.
- `aria-hidden="true"` on the overlay and `pointer-events: none` in CSS, so the
  watermark is invisible to screen readers and never intercepts input.
- Null privacy provider declaring that the plugin stores no personal data.
- English language pack.

### Notes

- Requires Moodle 4.4 (`2024042200`) or later for the hook API.
- Plugin version: `2026063000`.

[1.0.3]: https://github.com/UCC-Web-Dev-Team/moodle-local_quizidnumber/releases/tag/v1.0.3
[1.0.2]: https://github.com/UCC-Web-Dev-Team/moodle-local_quizidnumber/releases/tag/v1.0.2
[1.0.1]: https://github.com/UCC-Web-Dev-Team/moodle-local_quizidnumber/releases/tag/v1.0.1
[1.0.0]: https://github.com/UCC-Web-Dev-Team/moodle-local_quizidnumber/releases/tag/v1.0.0
