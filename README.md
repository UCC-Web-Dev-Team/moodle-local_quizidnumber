# Quiz student ID number (`local_quizidnumber`)

A Moodle local plugin that overlays a student's ID number as a tiled, diagonal
watermark across quiz **attempt** and **review** pages.

The watermark makes screenshots and photographs of quiz questions traceable back
to the account that took the attempt, which discourages sharing question banks
outside the exam.

## What it does

- Adds a repeating, rotated watermark of the logged-in user's `idnumber` to
  `mod-quiz-attempt` and `mod-quiz-review` pages.
- Shows the watermark **only to students**. Any user with the
  `mod/quiz:viewreports` capability (teachers, managers, admins) is excluded, so
  a teacher reviewing an attempt never sees a watermark.
- Falls back to a "Not set" string when the user has no ID number on their
  profile.
- Is purely visual: `pointer-events: none` means every click and keypress passes
  straight through to the quiz.
- Stays visible when a page is printed or saved to PDF.

No settings, no database tables, no capabilities of its own.

## Requirements

| | |
|---|---|
| Moodle | 4.4 or later (`2024042200`) — requires the hook system. Includes 4.4, 4.5 and the 5.x series |
| PHP | As required by your Moodle version |
| Plugin type | `local` |
| Component | `local_quizidnumber` |

Moodle 4.4 introduced the hook API this plugin uses
(`\core\hook\output\before_standard_top_of_body_html_generation`). It will not
work on 4.3 or earlier.

**Moodle 5.x.** The hook is unchanged in the 5.x series, and `version.php` sets
no `$plugin->supported` upper bound, so there is no artificial version cap —
the plugin is developed against Moodle 5.2. The only 5.x difference that affects
you is the install path: sites using the newer layout keep their code under
`public/`, so the plugin goes in `public/local/quizidnumber`. See
[Installation](#installation).

## Installation

The plugin must end up in your Moodle installation's `local/` directory, in a
folder named `quizidnumber` — the directory name matters, Moodle derives the
component name from it.

**Where `local/` lives depends on your Moodle version:**

| Layout | Plugin path |
|---|---|
| Moodle 4.4–4.5, and 5.x sites upgraded in place | `{moodleroot}/local/quizidnumber` |
| Moodle 5.0+ installed with the newer layout (a `public/` directory at the Moodle root) | `{moodleroot}/public/local/quizidnumber` |

Check which you have: if `ls` at your Moodle root shows a `public/` directory
containing `config.php` and `index.php`, use the `public/local` path.

The commands below use a `LOCALDIR` variable. Run **one** of these first, in the
same shell, before running anything else in this section:

```bash
LOCALDIR=local          # Moodle 4.4–4.5, or 5.x upgraded in place
# or
LOCALDIR=public/local   # Moodle 5.0+ with the public/ layout
```

### Option 1 — Git submodule (recommended)

Use this if your Moodle root is itself a Git repository and you want the plugin
version pinned and updatable alongside it.

From your Moodle root:

```bash
git submodule add git@github.com:UCC-Web-Dev-Team/quizidnumber.git $LOCALDIR/quizidnumber
git submodule update --init --recursive
git commit -m "Add local_quizidnumber as a submodule"
```

Over HTTPS instead of SSH:

```bash
git submodule add https://github.com/UCC-Web-Dev-Team/quizidnumber.git $LOCALDIR/quizidnumber
```

**Pin to a release tag.** A fresh submodule tracks whatever `main` pointed at
when it was added. To pin it to a released version:

```bash
git -C $LOCALDIR/quizidnumber fetch --tags
git -C $LOCALDIR/quizidnumber checkout v1.0.0
git add $LOCALDIR/quizidnumber
git commit -m "Pin local_quizidnumber to v1.0.0"
```

**Cloning a Moodle root that already has the submodule:**

```bash
git clone --recurse-submodules <your-moodle-repo>
# or, in an existing clone:
git submodule update --init --recursive
```

**Updating to a newer release:**

```bash
git -C $LOCALDIR/quizidnumber fetch --tags
git -C $LOCALDIR/quizidnumber checkout v1.1.0
git add $LOCALDIR/quizidnumber
git commit -m "Update local_quizidnumber to v1.1.0"
```

Then visit **Site administration → Notifications** to run the upgrade.

**Removing the submodule:**

```bash
git submodule deinit -f $LOCALDIR/quizidnumber
git rm -f $LOCALDIR/quizidnumber
rm -rf .git/modules/$LOCALDIR/quizidnumber
```

### Option 2 — Plain Git clone

Use this if your Moodle root is not under version control.

```bash
cd /path/to/moodle/$LOCALDIR      # e.g. .../moodle/local or .../moodle/public/local
git clone git@github.com:UCC-Web-Dev-Team/quizidnumber.git quizidnumber
cd quizidnumber
git checkout v1.0.0
```

### Option 3 — ZIP upload

1. Download `local_quizidnumber.zip` from the
   [Releases page](https://github.com/UCC-Web-Dev-Team/quizidnumber/releases).
2. Go to **Site administration → Plugins → Install plugins**.
3. Upload the ZIP, choose plugin type **Local plugin (local)**, and install.

Or unpack it manually:

```bash
unzip local_quizidnumber.zip -d /path/to/moodle/$LOCALDIR/
```

The ZIP contains a top-level `quizidnumber/` folder, so it unpacks to the
correct path.

### Finishing any install

1. Log in as an administrator.
2. Go to **Site administration → Notifications**.
3. Confirm the upgrade for **Quiz student ID number**.
4. Purge caches (**Site administration → Development → Purge caches**) if the
   watermark does not appear immediately — the CSS is cached.

## Setup after install

The watermark shows each user's `idnumber` field. If your users don't have one:

- Set it per user under **Site administration → Users → Browse list of users →
  *user* → Edit profile → Optional → ID number**.
- Or populate it in bulk via **Site administration → Users → Upload users** with
  an `idnumber` column.
- If your accounts come from LDAP, SAML or an external database, map the source
  attribute to `idnumber` in the auth plugin's data-mapping settings.

Users without an ID number see the "Not set" placeholder instead.

## How it works

| File | Role |
|---|---|
| `version.php` | Component name, version, Moodle requirement, release string |
| `db/hooks.php` | Registers the callback against the page-render hook |
| `classes/hook_callbacks.php` | Builds the watermark markup and injects it |
| `classes/privacy/provider.php` | Declares the plugin as null-provider for GDPR |
| `lang/en/local_quizidnumber.php` | English strings |
| `styles.css` | Positions, rotates and tints the watermark |

On every page load Moodle fires
`before_standard_top_of_body_html_generation`. The callback checks the page type
and the user's capabilities, then emits a fixed-position `div` containing 240
repeated spans of the ID number. CSS rotates the container −30° and oversizes it
to 200% so no corner is left empty; surplus tiles are clipped.

The overlay is marked `aria-hidden="true"` so screen readers ignore it.

### Customising the appearance

Edit `styles.css` and purge caches afterwards.

- **Opacity / colour** — `.local-quizidnumber-wm-item { color: rgba(128, 128, 128, 0.08); }`.
  Raise the alpha to make it more visible, lower it to make it subtler.
- **Text size** — `font-size` on the same rule.
- **Density** — the `gap` on `.local-quizidnumber-watermark`, and
  `WATERMARK_TILES` in `classes/hook_callbacks.php`.
- **Angle** — `transform: rotate(-30deg)` on the container.
- **Print appearance** — the `@media print` block.

If you customise a Git-submodule install, keep the change on a branch or fork so
it survives updates.

## Privacy

The plugin stores no personal data. It reads the `idnumber` already held on the
user's own profile and renders it back to that same user, so there is nothing to
export or delete. `classes/privacy/provider.php` declares this to the Moodle
privacy API.

Note that the watermark makes an ID number visible on screen, which matters if
students take quizzes in a shared or invigilated space where others can see the
display.

## Limitations

- Only `mod-quiz-attempt` and `mod-quiz-review` page types are covered.
- The watermark is client-side CSS and can be removed with browser dev tools by
  a determined user. It is a deterrent against casual sharing, not a hard
  control.
- Very long ID numbers may look crowded at the default tile spacing.

## Troubleshooting

**Watermark doesn't appear.** Purge caches. Confirm you're testing as a user
*without* `mod/quiz:viewreports` in that course — teachers and admins are
excluded by design.

**It says "Not set".** That account has no `idnumber` on its profile. See
[Setup after install](#setup-after-install).

**Nothing happens on Moodle 4.3 or earlier.** The hook API isn't available.
Upgrade to 4.4+.

**Layout looks broken.** Some custom themes set their own stacking contexts.
Adjust the `z-index` on `.local-quizidnumber-watermark`.

## Uninstalling

1. **Site administration → Plugins → Plugins overview**, find **Quiz student ID
   number**, choose **Uninstall**.
2. Remove the directory — see the submodule removal commands above, or just
   `rm -rf $LOCALDIR/quizidnumber` for a plain clone (`local/quizidnumber` on
   4.x, `public/local/quizidnumber` on the Moodle 5.0+ layout).
3. Purge caches.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GNU GPL v3 or later, matching Moodle itself.
See <http://www.gnu.org/copyleft/gpl.html>.

Copyright © 2026 Clemence Ayekple.
