# Post Finder

Creates a rich input field that allows a user to curate and rank content items (posts or other custom post types)

## Installation

Clone the plugin to your `wp-content/plugins/` directory

## Usage

In your theme, you can call `pf_render( $name, $value, $options )` where you want to display a Post Finder field.

`$name` : Name you want to use on the input field

`$value` : Currently selected value(s). Should be a comma-separated string of post_ids

`$options` (optional) : Array of options that will be used to build the input

**Current options**
* `show_numbers` - Whether to show a positional number next to each item. Makes it easy to see which position each item has. Default true.
* `limit` - Limit how many items can be selected. Default 10.
* `include_script` - Whether to include the init script for the input. Default true. If false, you'll have to include this yourself in order for it to work.
```
jQuery( document ).ready( function( $ ) {
	$( '.post-finder' ).postFinder();
} );
```