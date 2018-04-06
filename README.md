# orthodox-daily-readings
A Wordpress plugin to post the current day's readings and fasting rule from antiochian.org on your own website.

## Installation
First you must put the plugin's files in the appropriate folder in your Wordpress installation.
1. Download the plugin.
    1. Go to the [latest release page](https://github.com/pschluet/orthodox-daily-readings/releases/latest).
    2. Click on "orthodox-daily-readings.zip" under "Assets".
2. Unzip the file that you downloaded.
3. Upload the "orthodox-daily-readings" folder to your Wordpress plugins directory located at 
```
your_wordpress_root_install_directory/wp-content/plugins
```
Next you need to activate the plugin as follows:
1. Open your Wordpress dashboard.
2. Click on "Plugins" in the dashboard sidebar.
3. Click the "Activate" link under the "Orthodox Daily Readings" plugin title.

## Usage
To show the daily readings and fasting rule, you simply paste the following shortcode on whatever page you want to display them.
```
[orthodox-daily-readings content="all"]
```
If you want more fine-grained control, you can choose to display only specific things by changing the argument to the "content"
attribute in the shortcode. The allowable arguments are:
- "all": show all the content
- "date": show only the date
- "fasting": show only the fasting rule text
- "readings": show only the scripture readings with titles  

For instance, if you want to only show the date and the fasting rule, you can put the following two shortcodes on your page:
```
[orthodox-daily-readings content="date"]
[orthodox-daily-readings content="fasting"]
```

## Bugs and Feature Requests
If you find a bug or you want to suggest feature, please submit the details 
[here](https://github.com/pschluet/orthodox-daily-readings/issues/new). If it is a bug, please include as much detail
as you can including the version of PHP you are running and the version of Wordpress you are running. Thanks!
