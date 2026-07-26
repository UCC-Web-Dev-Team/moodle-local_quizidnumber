# Contributing to `local_quizidnumber`

Thanks for taking the time to help. This is a small plugin, so the process is
deliberately light.

## Reporting issues

Open an issue at
<https://github.com/UCC-Web-Dev-Team/quizidnumber/issues> and include:

- Moodle version and PHP version
- Plugin version (from `version.php`) or release tag
- Theme in use, if the problem is visual
- What you expected, what happened, and steps to reproduce
- A screenshot, for layout or rendering problems

## Development setup

Clone into a working Moodle install so you can test against a real site:

```bash
cd /path/to/moodle/local
git clone git@github.com:UCC-Web-Dev-Team/quizidnumber.git quizidnumber
```

Enable developer mode while working:

- **Site administration → Development → Debugging** → set debug messages to
  *DEVELOPER* and tick *Display debug messages*.
- **Site administration → Development → Purge caches** after any CSS change.
  Moodle caches stylesheets aggressively; if your change doesn't show, this is
  almost always why.

## Coding standards

Follow the
[Moodle coding style](https://moodledev.io/general/development/policies/codingstyle):

- 4-space indent, no tabs; LF line endings; no trailing whitespace.
- Every PHP file starts with the GPL header and a `defined('MOODLE_INTERNAL') || die();`
  guard where applicable.
- Full PHPDoc blocks on files, classes and methods, including `@package`,
  `@copyright` and `@license`.
- All user-facing text goes through `get_string()` with an entry in
  `lang/en/local_quizidnumber.php`. No hardcoded strings.
- Escape any value rendered into HTML with `s()` or `format_string()`, and build
  markup with `html_writer` rather than string concatenation.
- CSS classes are prefixed `local-quizidnumber-`.

Check your work with the
[Moodle Code Checker](https://moodle.org/plugins/local_codechecker) plugin, or:

```bash
php -l <changed-file>.php
```

## Making a change

1. Branch from `main`: `git checkout -b short-descriptive-name`.
2. Keep commits focused and write imperative subject lines
   ("Reduce watermark opacity", not "reduced opacity").
3. Bump `$plugin->version` in `version.php` for anything that changes behaviour.
   The format is `YYYYMMDDXX` and it must increase, or Moodle won't run the
   upgrade.
4. Add an entry under an *Unreleased* heading in `CHANGELOG.md`.
5. Update `README.md` if you change install steps, requirements or behaviour.
6. Open a pull request against `main` describing what changed and how you
   tested it.

## Testing checklist

There is no automated test suite yet, so verify manually before opening a PR:

- [ ] Watermark appears for a student on a quiz **attempt** page.
- [ ] Watermark appears for a student on a quiz **review** page.
- [ ] Watermark is **absent** for a teacher reviewing that same attempt.
- [ ] Watermark is absent on non-quiz pages (dashboard, course view).
- [ ] A user with no `idnumber` sees the "Not set" fallback.
- [ ] The quiz is fully usable — clicking, typing and navigating are unaffected.
- [ ] Print preview still shows the watermark.
- [ ] Rendering holds up in Boost and in your site's custom theme, at both
      desktop and mobile widths.

## Releasing

Maintainers only.

1. Bump `$plugin->version` and `$plugin->release` in `version.php`.
2. Move the *Unreleased* changelog entries under the new version heading with a
   date.
3. Commit, then tag: `git tag -a vX.Y.Z -m "vX.Y.Z"`.
4. `git push origin main --follow-tags`.
5. Build the package — the ZIP must contain a top-level `quizidnumber/` folder:

   ```bash
   cd /path/to/moodle/local
   zip -r local_quizidnumber.zip quizidnumber \
     -x 'quizidnumber/.git/*' 'quizidnumber/.git' '*/.DS_Store'
   ```

6. Create a GitHub release on the tag and attach the ZIP.

## License

Contributions are accepted under GNU GPL v3 or later, matching Moodle.
