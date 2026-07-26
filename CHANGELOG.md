# Changelog

All notable changes to `local_quizidnumber` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[1.0.0]: https://github.com/UCC-Web-Dev-Team/quizidnumber/releases/tag/v1.0.0
