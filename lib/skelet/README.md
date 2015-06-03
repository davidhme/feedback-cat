## Skelet Framework

### Introduction

Skelet is a framework for creating WordPress plugins, it eases the creation of advanced option pages, shortcodes and WordPress editor buttons.

######Contents

* [Installation](#installation)
* [Usage](#usage)
* [File Structure](#file-structure)
* [Register Pages](#register-pages)
* [Register Tabs](#register-tabs)
* [Register Options](#register-options)
* [Register Shortcodes](#register-shortcodes)

###<a name="installation"></a>Installation

Let's assume that you want to use Skelet in your plugin called "My plugin" (and whose slug is most probably `my_plugin`)

* Drop the `/skelet` folder somewhere inside your plugin folder
* Include the plugin bootstrap file in your plugin, make sure you get the path right, here is an example, it assumes that the `/skelet` folder sits on the root of your plugin:

```PHP
    <?php
    // wp-content/my_plugin/my_plugin.php

    // Include Skelet
    include dirname( __FILE__ ) . '/skelet/skelet.php';

    /**
     * My plugin code
     */
```

###<a name="usage"></a>Usage
* You can access options you defined like this:

####All options values

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $all_my_options = paf();

    var_dump( $all_my_options );
```

####A single option value

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $my_option = paf( 'my_option_id' );

    var_dump( $my_option );
```
If an option has a default value, and that option has not yet been set, `paf( 'option_id' )` will return the default value for `option_id`.

####A single option definition

This comes in handy when you want to know the default value of an option that has already been set, for example.

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $my_option = paf_d( 'my_option_id' );
    $my_option_default = $my_option[ 'default' ];

    var_dump( $my_option_default );
```

###<a name="file-structure"></a>File Structure

> It's just pages, tabs, sections and options

Pages, tabs, sections and options definitions for a plugin can reside anywhere inside that plugin, the plugin doesn't force any file structure or naming rules. Yet Skelet comes with a nice function that allows you to imprort all your files containing definition at once, the function is `skelet_dir()`, it expects a folder path as the first parameter, example:

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php
    skelet_dir( dirname( __FILE__ ) . '/data' );
```

Calling `skelet_dir()` in the previous example imports (with an `include`) definitions from the following files:

* `.../data/pages.php`
* `.../data/tabs.php`
* `.../data/sections.php`
* `.../data/options.php`

The framework comes with a few examples demonstrating the different features, you can use them as a starting point, those options are located in the folder `/sample-data` inside the framework folder. If you want to enable the sample data, set `$skelet_use_sample_data` to `TRUE`, Like this:

```PHP
    // wp-content/my_plugin/my_plugin.php

    $skelet_use_sample_data = TRUE;
    include dirname( __FILE__ ) . '/skelet/skelet.php';
    
```

###<a name="register-pages"></a>Register Pages

Here is an example of defining a page:

```PHP
    <?php
    // wp-content/my_plugin/admin/data/pages.php

    // Make sure our temporary variable is empty
    $pages = array();
    
    $pages[ 'my_page_slug' ] = array(
        'title'         => __( 'Framework Demo Page' ),   
        'menu_title'    => __( 'Framework Demo' ),     
    );

    // Register pages
    paf_pages( $pages );
```

Here is an alternative, passing the array directly to `paf_pages()` and using brackets (`[...]`) instead of `array()`, beware, brackets were introduced in PHP 5.4:

```PHP
    <?php
    // wp-content/my_plugin/admin/data/pages.php

    // Register pages
    paf_pages( [ 'my_page_slug' => [
        'title' => __( 'Framework Demo Page' ),   
        'menu_title' => __( 'Framework Demo' ),     
    ] ] );
```

#####Pages Parameters

* `title` The page title

* `menu_title` The text for the page menu item

* `icon_url` The menu icon, ignored when using parent since subpages don't have icons in WordPress, this parameter accepts the same values you would use in WordPress own [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page).

* `position` The position in the menu order this menu should appear, as you would use in [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page).

* `parent` The slug name for the parent menu, as you would use in [add_submenu_page()](http://codex.wordpress.org/Function_Reference/add_submenu_page).

* `submit_button (default='Save Changes')` Text for the submit button.

* `reset_button` Text for the reset button, if ommitted, there will be no reset button.

* `success (default='Settings saved.')` Text for the success message.

###<a name="register-tabs"></a>Register Tabs

Registering tabs work in the same way:

```PHP
    <?php
    // wp-content/my_plugin/admin/data/tabs.php

    $tabs = array();
    
    $tabs[ 'my_tab_slug'] = array(
        'title'         => __( 'Tab one' ),
        'menu_title'    => __( 'Tab 1' ),
        'page'          => __( 'my_page_slug' ),
    );

    // Register tabs
    paf_tabs( $tabs );
```

####Tabs Parameters

Most page parameters work for tabs as well but don't forget to specify which page the tabs belong to with the `page` parameter.

* `page` The slug for the page the tab belongs to.

###<a name="register-options"></a>Register Options

Here is an example of defining a text field:

```PHP
    <?php
    // wp-content/my_plugin/admin/data/options.php

    $options = array();
    
    $options[ 'my_option_name' ] = array(
        'type' => 'text',
        'page' => 'page_a',
        'title' => __( 'Welcome to my text field' ),
    );

    // Register options
    paf_options( options );
```

####Options Parameters

* `page` The slug of the page the option belongs to.

* `tab` The slug of the tab the option belongs to.

* `type (default=text)` The option type

  * `text`
  * `textarea`
  * `checkbox`
  * `radio`
  * `select`
  * `media` produces an input field with upload functionality


* `title` The option title

* `subtitle` A small description under the option title

* `description` The text to show under the form field, setting it to `~` will instruct the framework to output the code that defines the current option. 

* `placeholder` The placeholder text

* `default` The default value, use arrays or comma separated values when working with `select`, `radio` or `checkbox`.

* `value` The value to show in the form for textual fields

* `colorpicker` If set to true for a text input field, it will become a color picker.

* `selected` The value to show in the form for selection based fields, use arrays or comma separated values.

* `multiple` Tells `select` fields to allow multiple choice

* `options` Associative array of value/text pair that make the available choices for `select`, `radio` or `checkbox`. 

  **Tip:** Accepts also `posts` and `terms`
  
  **Tip:** If the text matches an image URL, the image is shown instead of the URL.
  
* `args` The parameter to pass to WordPress `get_posts()` or `get_terms()` when necessary, i.e, when the `options` parameter of a selection based field was set to `posts` or `terms`.

* `taxonomies (defaut=category,post_tag,link_category,post_format)` The taxonomies to query when using `terms` as a value for `options` on a selection based form field.

  
* `separator (default=<br />)` The separator between `radio` and `checkbox` options

* `width (textarea only)` The width for a normal textarea, valid CSS values are expected (px, %, calc...), by default, text areas will span the width of the page.

* `height (textarea only)` The height for a normal textarea.

* `cols (textarea only)` The number of columns for a normal textarea.

* `rows (textarea only)` The number of rows for a normal textarea.

* `editor` If set to true for a textarea, it will use a WYSIWYG editor.

* `editor_height` An integer, the height in pixels of the WYSIWYG editor, see [this](http://wordpress.stackexchange.com/a/163260/17187) for more information about WYSIWYG height in WordPress.

* `textarea_rows (default=20)` An integer, the number of rows in the WYSIWYG editor, see [this](http://wordpress.stackexchange.com/a/163260/17187) for more information about WYSIWYG height in WordPress.

* `teeny` If set to true, the WYSIWYG editor will have less icons.

* `media_buttons (default=TRUE)` Weither to show the media upload button or not.

###<a name="register-shortcodes"></a>Register Shortcodes

You can define and register a shortcode like this:

```PHP
    <?php
    // wp-content/my_plugin/admin/data/shortcode.php

    $shortcodes = array();
    
    $shortcodes[ 'my_shortcode_a' ] = array(
        'text'  => __( 'My Shortcode A' ),
        'title' => __( 'Fancy description for shortcode A' ),
    );

    $shortcodes[ 'my_shortcode_b' ] = array(
        'icon'  => 'http://placehold.it/32x32/900/fff/',
        'title' => __( 'Fancy description for shortcode B' ),
    );

    // Register tabs
    paf_shortcodes( $shortcodes );
```

####Shortcode Parameters

* `image` Absolute or relative path to the image used for the button

* `text` Text can be used instead of an image

* `wrap (default=false)` When set to `false` the shortcode will replace the text currently selected in the text editor and when set to `true`, the selected content will be wrapped in the shortcode.

* `func` This is the function that will handle the shortcode, Skelet will use the function called: 
  * The parameter value, for example, if `'func' => 'my_func'`, the shortcode will be handled by the function `my_func()`
  * The shortcode tag with `_func` at the end, so if the shortcode tag is `[super_tag]`, the function will be `super_tag_func()`
  * The shortcode tag, so if the shortcode tag is `[super_tag]`, the function will be `super_tag()`
  * A default function provided by skelet that will simply print some information about the shortcode that has been used.


* `parameters` an array of fields, these fields definitions ressemble the ones for options. When parameters are found, clicking the shortcode button will open a modal window for building the shortcode with those parameters.

* `width (default=0.5)` The width percentage of the modal window relative the the page width.

* `height (default=0.5)` The height percentage of the modal window relative the the page height.
