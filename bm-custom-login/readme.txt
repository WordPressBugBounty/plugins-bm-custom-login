=== WP Custom Login ===
Contributors: teydeastudio, bartoszgadomski, BinaryMoon
Tags: custom login, login page, login customizer, branding, login logo
Requires at least: 6.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 3.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Plugin URI: https://wpcustomlogin.com/?utm_source=WP+Custom+Login

Customize the WordPress login screen with your own colors, logo, backgrounds, and form styles.

== Description ==

**WP Custom Login lets you fully customize the WordPress login page to match your brand identity.**

Whether you run a single site or a multisite network, this plugin gives you control over every visual element of the login screen -- from the logo and background to form fields, buttons, links, and more.

**Key benefits:**

* Adjust colors, fonts, spacing, and layout of every login page element without writing CSS.
* Add your logo, social media links, and custom footer to create a branded login experience.
* Support for multilingual sites with per-language text customization for labels, buttons, and notices.
* Works with WordPress multisite networks for consistent branding across all sites.

WP Custom Login is a good fit for freelancers, agencies, and organizations that want a professional, branded login page. It includes a live preview in the admin settings, so you can see your changes before they go live.

Learn more at [wpcustomlogin.com](https://wpcustomlogin.com/?utm_source=WP+Custom+Login).

== Features ==

= Free Features =

* **Body background color** -- Set a custom background color for the login page.
* **Custom logo** -- Replace the WordPress logo with your site icon or a custom image, and configure its link URL and alignment.
* **Form container styling** -- Customize the form background color, border radius, padding, box shadow, and alignment.
* **Label styling** -- Adjust font size, weight, letter case, spacing, text color, and toggle label visibility.
* **Input field styling** -- Set background colors, borders, padding, font, shadow, and placeholder text for normal, hover, and focus states.
* **Checkbox styling** -- Apply custom colors to the login form checkboxes and checkmark icon.
* **Primary button styling** -- Customize colors, font, size, width, alignment, shadow, and per-language button labels.
* **Secondary button styling** -- Configure colors, font, border, and per-language labels for secondary buttons.
* **Notice and error styling** -- Set colors for error, notice, and success messages, and add a custom persistent notice with per-language text.
* **Under-form links** -- Customize link colors, separator, disable the "Back to" link, or add custom links.
* **Social media icons** -- Display up to 23 social media icon links (Facebook, X, Instagram, LinkedIn, YouTube, GitHub, and more), placed before the form, after the form, or in the footer.
* **Privacy policy link** -- Show or hide the privacy policy link and set its color.
* **Language switcher** -- Show or hide the WordPress language switcher and adjust its icon color and spacing.
* **Custom footer** -- Add a footer with configurable text, font, color, and alignment.
* **Custom CSS** -- Add your own CSS for additional styling.
* **Live preview** -- See your changes in real time within the admin settings page.
* **Disable autofocus** -- Turn off the default autofocus behavior on the login form.
* **Disable error shake** -- Turn off the shake animation on failed login attempts.
* **Disable autocomplete** -- Prevent browsers from auto-filling the login form.
* **Per-language text** -- Customize labels, placeholders, button text, and notices for each language on multilingual sites.
* **"Remember Me" customization** -- Show or hide the "Remember Me" checkbox and set custom label text per language.

= PRO Features =

* **[Pre-designed templates](https://wpcustomlogin.com/?utm_source=WP+Custom+Login)** -- Choose from 20+ ready-made login page designs and apply them with one click.
* **[Advanced backgrounds](https://wpcustomlogin.com/?utm_source=WP+Custom+Login)** -- Use animated gradients, image slideshows, or split-screen layouts as your login page background.
* **[Post-login redirects](https://wpcustomlogin.com/?utm_source=WP+Custom+Login)** -- Redirect users to specific pages after login based on their roles.
* **[Premium support](https://wpcustomlogin.com/?utm_source=WP+Custom+Login)** -- Get direct support from the development team.

== Installation ==

1. Upload the `bm-custom-login` directory to `/wp-content/plugins/`.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to "Settings" > "WP Custom Login" to open the settings page.
4. Use the visual controls to customize your login page. Changes are shown in the live preview on the right side of the settings page.
5. Save your settings. Your customized login page is now live.

== Frequently Asked Questions ==

= How do I access the settings? =

After activation, go to "Settings" > "WP Custom Login" in the WordPress admin. The settings page provides visual controls for every element of the login page, along with a live preview.

= Does this plugin work with WordPress multisite? =

Yes. WP Custom Login supports WordPress multisite networks and can be activated network-wide. Each site in the network can have its own login page customization.

= Can I customize login page text for multiple languages? =

Yes. Labels, placeholders, button text, and custom notices all support per-language configuration. This is useful if your site serves visitors in more than one language.

= Will my customizations be lost if I update the plugin? =

No. All your settings are stored in the WordPress database and are not affected by plugin updates.

= What is the difference between the free and PRO versions? =

The free version gives you full control over colors, fonts, layout, logo, social icons, and form styling. The PRO version adds pre-designed templates, advanced background options (animated gradients, slideshows, split-screen), post-login redirects by user role, and premium support. See the [pricing page](https://wpcustomlogin.com/pricing/?utm_source=WP+Custom+Login) for details.

= Can I add custom CSS to the login page? =

Yes. The settings page includes a custom CSS field where you can add any additional styles that go beyond what the visual controls offer.

== Screenshots ==

1. The settings page with the Design tab open, showing all customizable elements and a live preview of the login page.
2. Background and form container settings with color picker and background image options.
3. The Functionality tab with options to disable autofocus, autocomplete, and the login error shake effect.
4. Under-form links settings with custom link management, text alignment, separator, and color options.
5. Primary button styling with color controls for normal, hover, and focus states, plus font family and weight options.

== Changelog ==

= 3.0.0 (2026-02-16) =
* Complete plugin rebuild with a modern, React-based settings page and live preview
* Add granular styling controls for form container, labels, input fields, checkboxes, and buttons (including separate hover and focus states)
* Add primary and secondary button customization with font, color, size, width, and alignment options
* Add notice and error message styling with support for custom persistent notices
* Add social media icon links (23 platforms) with configurable placement
* Add under-form link customization with custom links, separator styling, and option to disable the "Back to" link
* Add custom footer with configurable text, font, color, and alignment
* Add privacy policy link and language switcher visibility and styling controls
* Add "Remember Me" checkbox show/hide toggle with custom label text
* Add per-language text customization for labels, placeholders, button text, and notices
* Add functionality options: disable autofocus, disable error shake, disable autocomplete
* Add logo alignment, link configuration, and site icon as logo source option
* Improve background image controls with focal point, size, and repeat options
* Migrate existing settings from previous versions automatically

= 2.4.0 (2024-10-16) =
* Create changelog.txt file and add all missing records
* Update readme.txt file, list of contributors, tags, and the plugin author data
* Remove promotional contents
* Drop csstidy entirely and use similar approach for css sanitization as in WordPress core
* Remove invalid plugin uri header
* Bump tested-up-to version to WordPress 6.6
* Fix security issues
* Load plugin stylesheet through `login_enqueue_scripts` hook

= 2.3.2 (2021-07-14) =
* Fix errant comma that caused a 500 error on some servers

= 2.3.1 (2021-07-13) =
* Fix PHP error for missing settings

= 2.3 (2021-03-09) =
* Display the "powered by" text underneath the login form to ensure it is visible. It can be targetted with CSS using `.cl-powered-by`

= 2.2.5 (2020-06-17) =
* Fix implode parameter order

= 2.2.4 (2020-03-05) =
* Update CSSTidy to latest version

= 2.2.3 (2019-08-23) =
* Switch to submit_button function for settings form

= 2.2.2 (2019-04-30) =
* Replace deprecated filter
* Update coding standards

= 2.2.1 (2017-12-01) =
* Remove text shadow on login button so that it's more consistently readable
* Make it clearer what the text link colour changes
* Change CSS label to match the core customizer label
* Remove CSS vendor prefixes that are no longer needed

(For older records, see the `changelog.txt` file).
