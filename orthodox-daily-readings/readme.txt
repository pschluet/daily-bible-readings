=== Orthodox Daily Readings ===
Contributors: paulschlueter
Tags: orthodox, bible, scripture, lectionary, gospel, epistle, fasting
Donate link: paypal.me/PaulSchlueter
Requires at least: 4.9.5
Tested up to: 4.9
Requires PHP: 5.4.45
Stable tag: trunk
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Show the current day\'s scripture readings and fasting rule from antiochian.org on your own website.

== Description ==
To show the daily readings and fasting rule, you simply paste the following shortcode on whatever page you want to display them. The plugin automatically gets new content from antiochian.org once a day so it is always up to date.
```
[orthodox-daily-readings content=\"all\"]
```
[Click here](http://allsaintsorthodox.org/daily-readings/) to see a live demo.

If you want more fine-grained control, you can choose to display only specific things by changing the argument to the \"content\"
attribute in the shortcode. The allowable arguments are:
* \"all\": show all the content
* \"date\": show only the date
* \"fasting\": show only the fasting rule text
* \"readings\": show only the scripture readings with titles  

For instance, if you want to only show the date and the fasting rule, you can put the following two shortcodes on your page:
```
[orthodox-daily-readings content=\"date\"]
[orthodox-daily-readings content=\"fasting\"]
```

Initially, the readings only show short snippets of the reading text. Clicking "Read More" dynamically reveals the rest of the text without loading a new page.

== Installation ==
Upload the \"orthodox-daily-readings\" folder to your Wordpress plugins directory located at 
```
your_wordpress_root_install_directory/wp-content/plugins
```
Next you need to activate the plugin as follows:
1. Open your Wordpress dashboard.
2. Click on \"Plugins\" in the dashboard sidebar.
3. Click the \"Activate\" link under the \"Orthodox Daily Readings\" plugin title.

== Frequently Asked Questions ==
= Why Am I Seeing Readings for the Wrong Date? =

Make sure your timezone is set correctly in your Wordpress settings. The plugin updates the readings at 12:05 am (near midnight) local time in whatever time zone you have selected in the settings.

= Where Can I Submit Bugs or Feature Requests? =

You can tell the author about bugs or feature requests [here](https://github.com/pschluet/orthodox-daily-readings/issues/new).

= Can I See The Code? =

Yes! This is an open-source project hosted on GitHub [here](https://github.com/pschluet/orthodox-daily-readings).

== Screenshots ==
1. Example Display with All Content
2. Reading 1 Expanded
3. Only Readings
4. Only Date and Fasting Rule

== Changelog ==
= 1.0.0 =
Initial Release

== Upgrade Notice ==
= 1.0.0 =
Initial Release