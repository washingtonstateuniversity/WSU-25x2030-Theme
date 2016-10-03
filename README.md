# WSU 25x2030 Theme

[![Build Status](https://travis-ci.org/washingtonstateuniversity/WSU-25x2030-Theme.svg?branch=master)](https://travis-ci.org/washingtonstateuniversity/WSU-25x2030-Theme)

## Implementation notes

* The "WSU Spine Section IDs" plugin should be enabled.

### Home Page

The home page consists of two sections—the main hero image and the building strengths letter.

* The top section should be a _Halves_ section, with no _Section Classes_ value, `panel` as the _Section Wrapper_ value, and `top` as the _Section Id_.
* The content goes in the first column, which should have `padded` set in the _Column Classes_ field.
* The paragraph containing "Washington State University" should have a class of `wsu-top`.
* The introduction paragraph should have a class of `top-message`.
* The second column should have `image` and `hero-image` in the _Column Classes_ field.
* The letter should be wrapped in a _Section Wrapper_ with an ID of `letter`.

### (Removed) Wipe effect (via doormat.js)

* Add `panel` and one of the `{color}-back` classes to the _Section Wrapper_ field for the sections that should have the wipe effect.
  * This should include the Top section, though it doesn't necessarily need a `{color}-back` class.
  * Individual story sections should _not_ have any value in the _Section Wrapper_ field.

### Story sections

* Stories should be added as _Quarters_ sections, with `story` added to the  _Section Classes_ field.
* The story text should be in the first or second column, which should have `excerpt` added to the _Column Classes_ field.
* Stories sections should **not** have anything in the _Section Wrapper_ field.
* The *velocity* of each column in a story can be controlled with the `speed-slower-one`, `speed-slower-two`, `speed-faster-one`, and `speed-faster-two` classes.

### Feedback thank you message

* The message displayed after a comment has been submitted needs to be added to the shortcode like so: `[comments_template]`_Message here_`[/comments_template]`.

### Footer

* The penultimate and last (footer) sections of the page should **not** have anything in the _Section Wrapper_ field.
* The last section of the page (footer) should have a section ID of `footer`.
