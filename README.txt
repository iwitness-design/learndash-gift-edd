=== Gift LearnDash Courses ===
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

#Gift learndash Courses#

== Description ==

#Gift learndash courses using EDD platform#

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `learndash-gift-edd.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `learndash-gift-edd.zip`
2. Extract the `learndash-gift-edd` directory to your computer
3. Upload the `learndash-gift-edd` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

= Setting up plugin licensing =

1. Navigate to Settings >> Learndash Gift Courses.
2. Place the license key in the field labeled "License Key".
3. Select the "Activate License" button, and a valid license key will show an active status.
4. To deactivate the license and enter a new license, select the "Deactivate License" button.

= Setting up plugin update functionality through EDD =

1. Set the LGE_STORE_URL constant in `learndash-gift-edd/learndash-gift-edd.php` to the hosting site URL.
2. Set the LGE_ITEM_ID constant in `learndash-gift-edd/learndash-gift-edd.php` to the Product Id of the plugin on the hosting site.

== Changelog ==

= 1.1.1 =
Do not add purchaser to ConvertKit when purchasing as gift.

= 1.1.0 =
Added data sanitization for "email subject" and "buy as a gift" button text in the admin settings.
Added first and last name required validations in the EDD checkout page.

= 1.0 =
Initial plugin.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or "installation."  Arbitrary sections will be shown below the built-in sections outlined above.
