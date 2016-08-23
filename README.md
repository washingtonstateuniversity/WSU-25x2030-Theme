# WSU 25x2030 Theme

[![Build Status](https://travis-ci.org/washingtonstateuniversity/WSU-25x2030-Theme.svg?branch=master)](https://travis-ci.org/washingtonstateuniversity/WSU-25x2030-Theme)

## Implementation notes

### Top section parallax effect

* The "WSU Spine Section IDs" plugin should be enabled.
* The top section should be a _Halves_ section, with no _Section Classes_ value, `panel` as the _Section Wrapper_ value, and `top` as the _Section Id_.
* The content goes in the first column, which should have `padded` set in the _Column Classes_ field.
* The second column should have `image` in the _Column Classes_ field, and the desired image set as the _Background Image_.

### Wipe effect (via doormat.js)

* Add `panel` and one of the `{color}-back` classes to the _Section Wrapper_ field for the sections that should have the wipe effect.
  * This should include the Top section, though it doesn't necessarily need a `{color}-back` class.
  * Individual story sections should _not_ have any value in the _Section Wrapper_ field.

### Story sections

* Stories should be added as _Quarters_ sections, with `story` added to the  _Section Classes_ field.
* The story text should be in the first or second column, which should have `excerpt` added to the _Column Classes_ field.
