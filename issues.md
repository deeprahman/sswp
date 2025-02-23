# List of issues found

##Blocking access to the users endpoint.

Note that this endpoint is used by parts of WordPress like the visual editor to load the list of authors inside posts. We recommend you to warn of the negative side effects this can have or to user another method to solve this.
# Block access to the users endpoint for any version of the API
RewriteRule ^wp-json/wp/v[0-9]+/users.*$ - [R=404,L]

## The URL(s) declared in your plugin seems to be invalid or does not work.

From your plugin:

Plugin URI: https://deeprahman.com/wp-securing-setup - wp-securing-setup.php - This URL replies us with a 404 HTTP code, meaning that it does not exists or it is not a public URL.

## You haven't added yourself to the "Contributors" list for this plugin.

In your readme file, the "Contributors" parameter is a case-sensitive, comma-separated list of all WordPress.org usernames that have contributed to the code.

Your username is not in this list, you need to add yourself if you want to appear listed as a contributor to this plugin.

If you don't want to appear that's fine, this is not mandatory, we're warning you just in case.

Analysis result:

# WARNING: None of the listed contributors "Deep Rahman, Shamim Akhter" is the WordPress.org username of the owner of the plugin "deepwebdev".

## Not permitted files

A plugin typically consists of files related to the plugin functionality (php, js, css, txt, md) and maybe some multimedia files (png, svg, jpg) and / or data files (json, xml).

We have detected files that are not among of the files normally found in a plugin, are they necessary? If not, then those won't be allowed.

Optionally, you can use the wp dist-archive command from WP-CLI in conjunction with a .distignore file. This prevents unwanted files from being included in the distribution archive.

Example(s) from your plugin:
01_15-18-49_wp-securing-setup/admin/.sswp-files-permissions-tools-page.php.swp


## No publicly documented resource for your compressed content

In reviewing your plugin, we cannot find a non-compiled version of your javascript and/or css related source code.

In order to comply with our guidelines of human-readable code, we require you to include the source code and / or a link to the non-compressed, developer libraries you’ve included in your plugin. If you include a link, this may be in your source code, however we require you to also have it in your readme.

https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#4-code-must-be-mostly-human-readable

We strongly feel that one of the strengths of open source is the ability to review, observe, and adapt code. By maintaining a public directory of freely available code, we encourage and welcome future developers to engage with WordPress and push it forward.

That said, with the advent of larger and larger plugins using more complex libraries, people are making good use of build tools (such as composer or npm) to generate their distributed production code. In order to balance the need to keep plugin sizes smaller while still encouraging open source development, we require plugins to make the source code to any compressed files available to the public in an easy to find location, by documenting it in the readme.

For example, if you’ve made a Gutenberg plugin and used npm and webpack to compress and minify it, you must either include the source code within the published plugin or provide access to a public maintained source that can be reviewed, studied, and yes, forked.

We strongly recommend you include directions on the use of any build tools to encourage future developers.

From your plugin:
build/index.js:1  ...(()=>{var t={687:(t,e,i)=>{var n,s,o;!function(){"use strict";s=[i(428),i(883)],void 0===(o="function"==typeof(n=function(t){return t.ui.formResetMixin={_formResetHandler:function(){var e=t(this);setT... 


## Do not use HEREDOC or NOWDOC syntax in your plugins

While both are totally valid, and in many ways desirable features of PHP that allow you to output content, it comes with a cost that is too high for most plugins.

The primary issue is that most (if not all) codesniffers won't detect lack of escaping in code when you use HEREDOC or NOWDOC. While there are ways around this they have the end result of dashing all that readability to the rubbish pile and leaving you with a jumbled mess that won't properly be scanned.

We feel the risk here is much higher than the benefits, which is why we don't permit their use.

Example(s) from your plugin:
includes/class-sswp-apache-directives-validator.php:438 // $invalidFilesMatchBlock = <<<EOD
includes/class-sswp-server-directives-apache.php:135 $rules         = <<<EOD
includes/class-sswp-server-directives-apache.php:173 $rules              = <<<EOD
includes/class-sswp-apache-directives-validator.php:455 // $validFilesMatchBlock = <<<EOD
includes/class-sswp-server-directives-apache.php:151 $rules         = <<<EOD
includes/class-sswp-apache-directives-validator.php:422 // $filesBlock = <<<EOD
includes/class-sswp-apache-directives-validator.php:472 // $singleDirectives= <<<EOD
includes/class-sswp-server-directives-apache.php:180 $rules = <<<EOD

... out of a total of 9 incidences.

## Including Libraries Already In Core

Your plugin has included a copy of a library that WordPress already includes.

WordPress includes a number of useful libraries, such as jQuery, Atom Lib, SimplePie, PHPMailer, PHPass, and more. For security and stability reasons, plugins may not include those libraries in their own code, but instead must use the versions of those libraries packaged with WordPress.

You can see the list of JS Libraries here:

https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-and-js-libraries-included-and-registered-by-wordpress

While we do not YET have a decent public facing page to list all these libraries, we do have a list here:

https://meta.trac.wordpress.org/browser/sites/trunk/api.wordpress.org/public_html/core/credits/wp-59.php#L739

It’s fine to locally include add-ons not in core, but please ONLY add those additional files. For example, you do not need the entire jquery UI library for one file. If your code doesn't work with the built-in versions of jQuery, it's most likely a noConflict issue.

Example(s) from your plugin:
includes/enqueue-scripts/sswp-enqueue-admin-scripts.php:63 wp_enqueue_style( 'jquery-ui-theme', 'https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css', array(), '1.13.3' );

## Calling core loading files directly

Calling core files like wp-config.php, wp-blog-header.php, wp-load.php directly via an include is not permitted.

These calls are prone to failure as not all WordPress installs have the exact same file structure. In addition it opens your plugin to security issues, as WordPress can be easily tricked into running code in an unauthenticated manner.

Your code should always exist in functions and be called by action hooks. This is true even if you need code to exist outside of WordPress. Code should only be accessible to people who are logged in and authorized, if it needs that kind of access. Your plugin's pages should be called via the dashboard like all the other settings panels, and in that way, they'll always have access to WordPress functions.

https://developer.wordpress.org/plugins/hooks/

There are some exceptions to the rule in certain situations and for certain core files. In that case, we expect you to use require_once to load them and to use a function from that file immediately after loading it.

If you are trying to "expose" an endpoint to be accessed directly by an external service, you have some options.
You can expose a 'page' use query_vars and/or rewrite rules to create a virtual page which calls a function. A practical example.
You can create an AJAX endpoint.
You can create a REST API endpoint.

Example(s) from your plugin:
includes/class-sswp-server-directives-apache.php:5 require_once ABSPATH . 'wp-admin/includes/misc.php';


## Determine files and directories locations correctly

WordPress provides several functions for easily determining where a given file or directory lives.

We detected that the way your plugin references some files, directories and/or URLs may not work with all WordPress setups. This happens because there are hardcoded references or you are using the WordPress internal constants.

Let's improve it, please check out the following documentation:

https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/

It contains all the functions available to determine locations correctly.

Most common cases in plugins can be solved using the following functions:
For where your plugin is located: plugin_dir_path() , plugin_dir_url() , plugins_url()
For the uploads directory: wp_upload_dir() (Note: If you need to write files, please do so in a folder in the uploads directory, not in your plugin directories).

Example(s) from your plugin:
includes/class-sswp-server-directives-apache.php:194 $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';
includes/class-sswp-server-directives-apache.php:172 $htaccess_path      = WP_CONTENT_DIR . '/uploads/.htaccess';

... out of a total of 10 incidences.

ℹ️ If your plugin creates files, those should be saved in the uploads directory.

In order to determine that directory location you can make use of the wp_upload_dir() function.

Also, it's a good practice that you store those files under a folder with the same name as your plugin slug "secure-setup"

Example:
$path = wp_upload_dir()['basedir'] . '/secure-setup';


Example(s) from your plugin:
includes/class-sswp-server-directives-apache.php:172 $htaccess_path      = WP_CONTENT_DIR . '/uploads/.htaccess';
includes/class-sswp-server-directives-apache.php:194 $htaccess_path = WP_CONTENT_DIR . '/uploads/.htaccess';


## Saving data in the plugin folder and/or asking users to edit/write to plugin.

We cannot accept a plugin that forces (or tells) users to edit the plugin files in order to function, or saves data in the plugin folder.

Plugin folders are deleted when upgraded, so using them to store any data is problematic. Also bear in mind, any data saved in a plugin folder is accessible by the public. This means anyone can read it and use it without the site-owner’s permission.

It is preferable that you save your information to the database, via the Settings API, especially if it’s privileged data.

If that’s not possible, because you’re uploading media files, you should use the media uploader.

If you can’t do either of those, you must save the data outside the plugins folder. We recommend using the uploads directory, creating a folder there with the slug of your plugin as name, as that will make your plugin compatible with multisite and other one-off configurations.

Please refer to the following links:

https://developer.wordpress.org/plugins/settings/
https://developer.wordpress.org/reference/functions/media_handle_upload/
https://developer.wordpress.org/reference/functions/wp_handle_upload/
https://developer.wordpress.org/reference/functions/wp_upload_dir/

Example(s) from your plugin:
sswp-logger.php:23 file_put_contents($log_file, $formatted_log, FILE_APPEND);
# ↳ Detected: plugin_dir_path


## Sanitization for register_setting()

Fields registered through register_setting() should be sanitized properly.

Fortunately, in this case, it is very easy to indicate which function should be used to sanitize the input. ✨

The third argument of this function accepts an array() in which you can add your sanitizing function in the sanitize_callback key.

Just like this:
register_setting(
    'pluginprefix_group',
    'pluginprefix_option_name',
    array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    )
);

Make sure you use a proper sanitization function (WordPress has plenty of them!) and add other options as needed.

Please, check out the register_setting() documentation for more information and code examples.

Example(s) from your plugin:
includes/settings/sswp-default-settings.php:14 register_setting('sswp_options_group', SSWP_SETTINGS);


## Internationalization: Don't use variables or defines as text, context or text domain parameters.

In order to make a string translatable in your plugin you are using a set of special functions. These functions collectively are known as "gettext".

There is a dedicated team in the WordPress community to translate and help other translating strings of WordPress core, plugins and themes to other languages.

To make them be able to translate this plugin, please do not use variables or function calls for the text, context or text domain parameters of any gettext function, all of them NEED to be strings. Note that the translation parser reads the code without executing it, so it won't be able to read anything that is not a string within these functions.

For example, if your gettext function looks like this...
esc_html__( $greetings , 'secure-setup' );
...the translator won't be able to see anything to be translated as $greetings is not a string, it is not something that can be translated.
You need to give them the string to be translated, so they can see it in the translation system and can translate it, the correct would be as follows...
esc_html__( 'Hello, how are you?' , 'secure-setup' );

This also applies to the translation domain, this is a bad call:
esc_html__( 'Hello, how are you?' , $plugin_slug );
The fix here would be like this
esc_html__( 'Hello, how are you?' , 'secure-setup' );
Also note that the translation domain needs to be the same as your plugin slug.

What if we want to include a dynamic value inside the translation? Easy, you need to add a placeholder which will be part of the string and change it after the gettext function does its magic, you can use printf to do so, like this:
printf(

      /* translators: %s: First name of the user */
      esc_html__( 'Hello %s, how are you?', 'secure-setup' ),
      esc_html( $user_firstname )
);

You can read https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#text-domains for more information.

Example(s) from your plugin:
includes/sswp-htaccess-form.php:51 __('Your custom error message here', Sswp_Securing_Setup::DOMAIN);
admin/templates/protection-form.htm.php:49 esc_html__('Save Settings', Sswp_Securing_Setup::DOMAIN);
includes/class-sswp-file-permission-manager.php:424 __('Path is not ownerd by WordPress', Sswp_Securing_Setup::DOMAIN);
admin/templates/protection-form.htm.php:43 esc_attr__('Redirect requests to the users REST endpoint to 404 HTTP error', Sswp_Securing_Setup::DOMAIN);
admin/templates/protection-form.htm.php:17 esc_attr__('Protect the WordPress log at default location', Sswp_Securing_Setup::DOMAIN);
includes/sswp-file-permission.php:53 __('Successfully reverted permission', Sswp_Securing_Setup::DOMAIN);
admin/templates/protection-form.htm.php:23 esc_attr__('Select which file-types should have access to uploads directory', Sswp_Securing_Setup::DOMAIN);
admin/sswp-files-permissions-tools-page.php:12 __('Files Permission', Sswp_Securing_Setup::DOMAIN);

... out of a total of 24 incidences.

## Internationalization: Text domain does not match plugin slug.

In order to make a string translatable in your plugin you are using a set of special functions. These functions collectively are known as "gettext".

These functions have a parameter called "text domain", which is a unique identifier for retrieving translated strings.

This "text domain" must be the same as your plugin slug so that the plugin can be translated by the community using the tools provided by the directory. As for example, if this plugin slug is "secure-setup" the Internationalization functions should look like:
esc_html__('Hello', 'secure-setup');

From your plugin, you have set your text domain as follows:
# This plugin is using the domain "wp-securing-setup" for 1 element(s).

However, the current plugin slug is this:
secure-setup


## Generic function/class/define/namespace/option names

All plugins must have unique function names, namespaces, defines, class and option names. This prevents your plugin from conflicting with other plugins or themes. We need you to update your plugin to use more unique and distinct names.

A good way to do this is with a prefix. For example, if your plugin is called "Easy Custom Post Types" then you could use names like these:
function ecpt_save_post()
class ECPT_Admin{}
namespace ECPT;
update_option( 'ecpt_settings', $settings );
define( 'ECPT_LICENSE', true );
global $ecpt_options;

Don't try to use two (2) or three (3) letter prefixes anymore. We host nearly 100-thousand plugins on WordPress.org alone. There are tens of thousands more outside our servers. Believe us, you’re going to run into conflicts.

You also need to avoid the use of __ (double underscores), wp_ , or _ (single underscore) as a prefix. Those are reserved for WordPress itself. You can use them inside your classes, but not as stand-alone function.

Please remember, if you're using _n() or __() for translation, that's fine. We're only talking about functions you've created for your plugin, not the core functions from WordPress. In fact, those core features are why you need to not use those prefixes in your own plugin! You don't want to break WordPress for your users.

Related to this, using if (!function_exists('NAME')) { around all your functions and classes sounds like a great idea until you realize the fatal flaw. If something else has a function with the same name and their code loads first, your plugin will break. Using if-exists should be reserved for shared libraries only.

Remember: Good prefix names are unique and distinct to your plugin. This will help you and the next person in debugging, as well as prevent conflicts.

Analysis result:
# This plugin is using the prefix "sswp" for 26 element(s).

# Cannot use "get" as a prefix.
includes/sswp-file-permission.php:2 function sswp_get_file_permissions
# Cannot use "do" as a prefix.
includes/sswp-file-permission.php:8 function do_recommended_permission
# Cannot use "handle" as a prefix.
includes/sswp-htaccess-form.php:32 function handle_htaccess_post_req
includes/sswp-htaccess-form.php:77 function handle_htaccess_get_req
includes/sswp-xml-rpc.php:5 function handle_xml_rpc_method
# Cannot use "wp" as a prefix.
sswp-logger.php:30 function sswp_logger
sswp-misc.php:3 function sswp_convert_to_octal_pers_from_string
includes/settings/sswp-default-settings.php:14 register_setting('sswp_options_group', SSWP_SETTINGS);
includes/class-sswp-server-directives-factory.php:4 class sswp_Server_Directives_Factory
includes/class-sswp-file-regex-pattern-creator.php:3 class sswp_File_Regex_Pattern_Creator
includes/sswp-htaccess-form.php:80 function sswp_save_htaccess_option
includes/traits/class-sswp-ownership-permission-trait.php:2 trait sswp_Ownership_Permission_Trait
includes/class-sswp-file-permission-manager.php:13 class sswp_File_Permission_Manager
includes/class-sswp-server-directives.php:7 class sswp_Server_Directives
includes/class-wp-securing-setup.php:3 class Sswp_Securing_Setup
includes/class-sswp-apache-directives-validator.php:3 class sswp_Apache_Directives_Validator
includes/class-sswp-server-directives-apache.php:7 class sswp_Server_Directives_Apache
includes/REST/file-permission.php:25 function sswp_file_permissions_permission_check
includes/REST/file-permission.php:35 function sswp_file_permissions_callback
includes/REST/htaccess-protect.php:25 function sswp_htaccess_protect_permission_check
includes/REST/htaccess-protect.php:29 function sswp_htaccess_protect_callback
wp-securing-setup.php:44 function sswp_activate
wp-securing-setup.php:60 function sswp_deactivate
admin/sswp-files-permissions-tools-page.php:7 function sswp_files_permission_page
# Cannot use "enqueue" as a prefix.
includes/enqueue-scripts/sswp-enqueue-admin-scripts.php:52 function enqueue_jquery_scripts

# Looks like there are elements not using common prefixes.
sswp-logger.php:8 function write_log
includes/sswp-file-permission.php:24 function revert_to_original
includes/class-sswp-server-directives-factory.php:22 $is_nginx;
includes/sswp-htaccess-form.php:133 $htaccess_from_settings;
includes/sswp-htaccess-form.php:14 $GLOBALS['allowed_functions'];
includes/sswp-htaccess-form.php:36 $GLOBALS['htaccess_settings'];
includes/sswp-htaccess-form.php:67 function from_data_with_message
includes/sswp-htaccess-form.php:90 function protect_debug_log
includes/sswp-htaccess-form.php:99 function protect_update_directory
includes/sswp-htaccess-form.php:117 function protect_rest_endpoint
includes/sswp-htaccess-form.php:132 function allowed_files
includes/class-sswp-server-directives.php:17 $is_litespeed;
includes/REST/htaccess-protect.php:30 $allowed_methods;
wp-securing-setup.php:46 $is_nginx;
admin/sswp-files-permissions-tools-page.php:27 function file_permission_page_html


## Allowing Direct File Access to plugin files

Direct file access is when someone directly queries your file. This can be done by simply entering the complete path to the file in the URL bar of the browser but can also be done by doing a POST request directly to the file. For files that only contain a PHP class the risk of something funky happening when directly accessed is pretty small. For files that contain procedural code, functions and function calls, the chance of security risks is a lot bigger.

You can avoid this by putting this code at the top of all PHP files that could potentially execute code if accessed directly :
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

Example(s) from your plugin:
includes/class-sswp-file-permission-manager.php:3 
includes/settings/sswp-file-permission-settings.php:49 
includes/enqueue-scripts/sswp-enqueue-admin-scripts.php:3 
includes/settings/sswp-default-settings.php:3 
admin/templates/page.htm.php:7 
includes/class-sswp-server-directives-factory.php:3 
includes/REST/file-permission.php:3 
includes/REST/htaccess-protect.php:3 

... out of a total of 12 incidences.

👉 Your next steps
Please:
Read this email.
Please work to understand the issues shared, check the included examples, check the documentation, research the issue on internet, and gain a better understanding of what's happening and how you can fix it. We want you to thoroughly understand these issues so that you can take them into account when maintaining your plugin in the future.
Note that there may be false positives - we are humans and make mistakes, we apologize if there is anything we have gotten wrong.
If you have doubts you can ask us for clarification, when asking us please be clear, concise, direct and include an example.
You can make use of tools like PHPCS or Plugin Check to further help you with finding all the issues.
Fix your plugin.
Test your plugin on a clean WordPress installation. You can try Playground.
Go to "Add your plugin" and upload an updated version of this plugin. You can update the code there whenever you need to, along the review process, and we will check the latest version.
Reply to this email telling us that you have updated it, and let us know if there is anything we need to know or have in mind.
Please do not list the changes made as we will review the whole plugin again, just share anything you want to clarify.

