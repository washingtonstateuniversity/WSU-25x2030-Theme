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

### Feedback thank you message

* The message displayed after a comment has been submitted needs to be added to the shortcode like so: `[comments_template]`_Message here_`[/comments_template]`.
