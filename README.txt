=== Islam Companion ===
Contributors: nadirlatif
Tags: islam,quran,sunnat,hadith,religion
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=nadir%40nadirlatif%2eme&lc=SE&item_name=Web%20Innovation&item_number=1&currency_code=SEK&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 1.2.3
License: GPLV2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to read the Holy Quran in over 40 languages. It also allows you to listen to Holy Quran recitation in Arabic and Urdu.

== Description ==
The plugin displays verses from the Holy Quran on a dashboard widget. The user can use navigation links to read the verses. The plugin also displays an audio player that recites the quranic verses in Arabic and Urdu. You can also find the meaning of a word using an online dictionary. The settings page allows configuration of Language, Narrator and Division. Division option makes it easier to organize the Holy Quran reading. The reading can be organized with following divisions: Sura, Hizb, Juz, Page and Manzil 

Currently the plugin supports following languages: Amharic, Arabic, Bosnian, Bengali, Bulgarian, Amazigh, Czech, German, Divehi, Spanish, English, Persian, French, Hindi, Hausa, Indonesian, Italian, Japanese, Korean, Kurdish, Malayalam, Malay, Dutch, Norwegian, Portuguese, Polish, Russian, Romanian, Swedish, Somali, Sindhi, Albanian, Swahili, Turkish, Tajik, Tamil, Tatar, Thai, Uzbek, Urdu, Uyghur and Chinese.

You can support the plugin by sending a donation via Paypal or reporting your suggestions or bugs to http://wordpress.org/support/plugin/islam-companion.

== Installation ==
Search for Islam Companion on https://wordpress.org/plugins/ or login to your wordpress blog and go to Plugins then Add New and then search for Islam Companion and Install. Another option is to install the plugin manually by following the instructions on http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation

== Frequently Asked Questions ==
1. What does this plugin do. Currently the Islam Companion plugin displays a Holy Quran navigator on a Dashboard widget. The verses are displayed according to the user settings. The user can specify the language, narrator and division from the settings page. The plugin also displays an audio player that recites the Quranic verses in Arabic and Urdu 

2. Where does the plugin get its data. The plugin uses Holy Quran translations from http://tanzil.net/trans/ and Holy Quran audio from http://www.quranurdu.com/

3. How does the online dictionary option work. On the Dashboard widget you have to select a word and then click on the dictionary icon. This will open the online dictionary in a new tab. The online dictionary will show the meaning of the selected word 

== Changelog ==

= 1.2.3 =
* Corrected minor bugs
* Added error handling to API functions
* Simplified API functions

= 1.2.2 =
* Corrected session_start error

= 1.2.1 =
* Corrected html closing tag error
* Corrected error in saving settings from Dashboard widget

= 1.2.0 =
* Added options for navigating the Holy Quran from the Dashboard widget
* Removed some of the navigation options from the settings page
* Updated the remote API
* Changed verse number to decimal format for all left to right languages that do not have browser support for numbering
* Added remote API to version control

= 1.1.0 =
* Added option for selecting Holy Quran divisions on the settings page
* Updated the Holy Quran Dashboard widget layout
* Updated remote API to object oriented class based format
* Unit tested the remote API

= 1.0.8 =
* Update api server url

= 1.0.7 =
* Update online dictionary icon so it gets the dictionary link from database
* Removed option for entering online dictionary url

= 1.0.6 =
* Added option for searching for a word using an online dictionary
* Added option to the settings page for setting online dictionary url

= 1.0.5 =
* Replaced option for selecting ayat with option for selecting ruku
* Added audio player for listening to Quranic Verses
* Added multi user and multi site support. It allows each user to have his own plugin settings
* Added internationalization and localization to the plugin. The plugin text is displayed in the users own language. Currently the plugin only contains translations in Urdu language. Translations in other languages can easily be created
* Added css classes for displaying verses with bullet numbering in the language of the user. If the user language is not supported then a default numbered bullet is displayed
* Uploaded media files to content delivery network
* Secured the plugin code by adding try/catch statements, exception throwing, exception handling and error logging
* Removed addslashes function on line 246 in file class-islam-companion-settings.php
* Corrected PayPal donation link
* Updated plugin description
* Renamed "Message of the day" feature to "Holy Quran Dashboard Widget" 

= 1.0.4 =
* Corrected layout bug in admin dashboard widget 
* Added default settings for plugin

= 1.0.3 =
* Added next and previous links to admin dashboard widget. the user can browser Quranic verses using these links
* Added meta information of the Quranic verses to the admin dashboard widget

= 1.0.2 =
* Added option under settings for saving the surah, verse and verse count
* Updated dashboard widget so it displays Quranic verses according to the settings

= 1.0.1 =
* Moved message for the day text to admin dashboard widget
* Corrected bugs in displaying message for the day

= 1.0.0 =
* Added option for configuring language
* Added function that displays Quranic verse at top of the admin page

== Upgrade Notice ==

= 1.2.3 =
* Corrected minor bugs
* Organized source code

= 1.2.2 =
* Corrected minor bugs

= 1.2.1 =
* Corrected minor bugs

= 1.2.0 =
* Added options for navigating the Holy Quran from the Dashboard widget
* Removed some of the navigation options from the settings page
* Updated the remote API
* Changed verse number to decimal format for all left to right languages that do not have browser support for numbering

= 1.1.0 =
* Added option for selecting Holy Quran divisions
* Updated the Holy Quran Dashboard widget layout

= 1.0.8 =
* Update api server url

= 1.0.7 =
* Update online dictionary icon so it gets the dictionary link from database
* Removed option for entering online dictionary url

= 1.0.6 =
* Added option for searching for a word using an online dictionary
* Added option to the settings page for setting online dictionary url

= 1.0.5 =
* Added audio player for listening to Quranic Verses in Arabic and Urdu languages
* Updated settings page and replaced option for selecting ayat with option for selecting ruku
* Added multi user and multi site support. It allows each user to have his own plugin settings
* Added internationalization and localization to the plugin. The plugin text is displayed in the users own language. Currently the plugin only contains translations in Urdu language. Translations in other languages can easily be created
* Secured the plugin code by adding error handling and logging

= 1.0.4 =
* Corrected layout bug in admin dashboard widget 
* Added default settings for plugin

= 1.0.3 =
* Updated Holy Quran dashboard widget so it allows user to browse the Quranic verses using next,prev links
* Add verse information to the Holy Quran dashboard widget

= 1.0.2 =
* Updated Holy Quran dashboard widget so it displays the verses according to the configured settings
* Added option under settings for saving the surah, verse and verse count

= 1.0.1 =
* Message for the day text is now displayed in admin dashboard widget
* Corrected bugs in displaying message for the day

== Screenshots ==

1. This screenshot shows how the plugin displays verses from the Holy Quran on the admin dashboard. The user can browse the verses using navigation links. The audio player recites the Holy Quran verses in Arabic and Urdu
2. This screenshot shows how to change the settings for the plugin. The language, narrator and division can be configured from here