=== FT Calendar ===
Contributors: hallsofmontezuma, fullthrottledevelopment
Tags: calendar, dates, events, times, event manager, scheduling, recurring, recurring events, ec3, event, widget, shortcode, AJAX, sidebar, repeating, repeat, recur, custom, post, types
Requires at least: 3.0
Tested up to: 3.7.1
Stable tag: trunk

A calendar plugin supporting multiple calendars, recurring events, and several different widgets / shortcodes. More info at http://calendar-plugin.com

== Description ==

A calendar plugin supporting multiple calendars, recurring events, and several different widgets / shortcodes. More info at http://calendar-plugin.com

Basic features include:

* Recurring Events
* Multiple Color Labels
* Multiple Shortcodes and Widgets
* Full sized calendar
* Sexy Google Calendar UI
* Events can be attached to any Post, Page, or custom post type
* Premium support and custom development available
* [Quick Start Guide](http://calendar-plugin.com/quick-start-guide)

Premium features available:

* Display schedule information within post content
* iCal feeds
* SMART event ordering for WordPress queries
* RSS 1.0 & 2.0, ATOM, and RDF feeds
* Backup & Export FullThrottle Calendar data to a CSV file
* Import FullThrottle Calendar data from a CSV file
* Import Event Calendar 3 data from the WordPress database

== Installation ==

1. Upload the entire `ft-calendar` folder to your `/wp-content/plugins/` folder.
1. Go to the 'Plugins' page in the menu and activate the plugin.
1. Create at least one calendar.
1. Type `[ftcalendar]` into any Post or Page you want FT Calendar to be displayed in.
1. Visit the FT Calendar Help page inside your WordPress dashboard to discover more shortcodes and widgets

== Frequently Asked Questions ==

= What are the minimum requirements for FT Calendar? =

You must have:

* WordPress 3.0 or later
* PHP 5

= How is FT Calendar Licensed? =

* FT Calendar is GPL

== Changelog ==
= 1.2.9 =
* Fixes for WordPress 3.5 compatibility
* Capability fixes for menus
= 1.2.8 =
* Fixed iCal bug causing the BYDAY argument to not reset upon each new event
* General Code Cleanup
* Change call to bloginfo for Next/Prev day links
* Added filter for daily GMT offset text
* Fixed typo causing issues with translation files
* Added four new filters for the post schedule shortcode: post_schedule_start_date_format, post_schedule_end_date_format, post_schedule_start_time_format, post_schedule_end_time_format
* Added ability to change the dateformat and timeformat to the post schedule shortcode
* Added additional argument (event data array) to the current ftc_custom_replacement_tags filter
* Fixed iCal bug causing single-day all day events to span multiple days
* Fixed iCal/Permalink bug
* Added temporary fix to write-edit-post.js file for people with jquery issues
* Changed parseCSV class name and references to avoid conflicts

= 1.2.7 =
* Added hide_duplicates argument to ftcalendar and Calendar Thumb Widget
* Fixed Full Throttle Client compatibility with Simple Maps
* Fixed header error in wp-admin panel
* Fixed date argument bug in ftcalendar_list
* Added check for GET variable on ftcalendar_list pages to change the start date
* Fixed Post Schedule displaying events that have already passed
* Added ftcalendar_post_schedule shortcode

= 1.2.6 =
* Fixed script and CSS enqueue for shortcodes in WordPress 3.3

= 1.2.5 =
* Code Cleanup
* Fixed timezone bug in iCal feeds
* Fixed bug in legend="off" setting for large calendar
* Fixed bug in recurring events datepicker jQuery call
* Added new filter: ftc_custom_replacement_tags to add custom tags to the ftcalendar_list shortcode
* Modified submenu to work with WP 3.3
* Gave the Add Event UI a little bit of love
* Added clean_post_cache function to event list shortcode/widget, to help reduce PHP memory overhead for large queries.
* Added beta Italian translation

= 1.2.4 =
* Fixed many iCal bugs
* Fixed bug with non-permalink sites using RSS and iCal feeds
* Updated get_ftcal_data_ids filter hooks and added select hook
* Updated POT file
* Added ftcal_through filter, to replace the "-" with the text/symbol of your choice
* Fixed Post Schedule formatting

= 1.2.3 =
* General Code Cleanup
* Fixed bug causing schedule to appear twice on excerpts
* Added functionality to ftcalendar_list shortcode/widget, so it displays events that are current (not yet ended)
* Added filter to force ftcalendar_list to start at 00:00:00 today, instead of "now"
* Updated ftcalendar_list widget to include "0" in the timespan, for "today" events displays (e.g. +0 days)
* Added Dutch tranlsation files (nl_NL) thanks to Helma Paternostre of [paternostre.nl](http://paternostre.nl)
* Added German tranlsation files (de_DE) thanks to Nathaniel Stott of [stott.nl](http://stott.nl)
* Added text to Calendar Data meta box to state which calendar a specific event is listed in
* Localized Javascript files

= 1.2.2 =
* General Code Cleanup
* Changed Upcoming Event Lists to pay attention to TIME not just DAY, so events that happened minutes ago do not appear
* Fixed internationalization bug
* Fixed Widget ID tags
* Fixed EXCERPT bug in Upcoming Event List

= 1.2.1 =
* Fixed permalink bug in Calendar Thumb widget
* Fixed permalink bug in Large Calendar
* Added "date" argument to ftcalendar_list shortcode
* Add POT file for language translations, hopefully will be getting some soon

= 1.2.0 =
* Calendar now considers WordPress "Start Week On" setting under Settings -> General
* Fixed bug causing RSS and iCal feed icons to re-appear when switching calendar views
* Added new shortcode argument %CALNAME% to the 'class' variable
* Added new shortcode argument %CALNAME% & %CALSLUG% *_template variable in ftcalendar_list

= 1.1.13 =
* Added new shortcode argument %CALNAME% to the 'class' variable.
* Added new shortcode argument %CALNAME% *_template variable in ftcalendar_list.

= 1.1.12 =
* Updated main plugin URL
* Fixed timezone bug with event listing on recurring events
* Efficiency updates

= 1.1.11 =
* Added argument to Hide Duplicates for ftcalendar_list shortcode and widget
* Efficiency updates
* Fixed bug with timezone calculation in iCal feeds
* Fixed bug with sorting recurring events times.
* Rebuilt RSS feeds to follow RSS standards
* Added filter for prev/next calendar arrows
* Fixed timezone issue with non-recurring events (if server time is off)
* Fixed small bugs found in ATOM and RDF feeds
* Added filters to modify main get_ftcal_data_ids() query

= 1.1.10 =
* Fixed infinite loop problem caused if you include a ftcalendar_list shortcode inside a post with event details
* Added multiple checkboxes to Widgets to choose multiple calendars
* Added str_ireplace function for older PHP version compatibility
* Fixed bug with day URL on the Month/Week calendar
* Fixed bug with day not changing to the current day being viewed on the Day calendar

= 1.1.9 =
* Fixed iCal feeds for non-permalinked sites
* Added feed-ical.php file, forgot to add it for 1.1.8

= 1.1.8 =
* Fixed RSS feed URLs
* Added iCal feeds
* Fixed bug in current_day class name calculation
* Added ability to display schedule within post content

= 1.1.7 =
* Fixed bug in JavaScript when setting "Repeats Every" for recurring events in Internet Explorer
* Added SMART Event Ordering
* Added upgrade() function for changes made to plugin that require DB updates
* Set Recurring End Dates to NULL if not defined (instead of current datetime)

= 1.1.6 =
* Daily, Weekly, Monthly, and Upcoming event feeds available for all and specific FT Calendar calendars
* Fixed bug in widgets, was incorrectly using term name for the value instead of the term slug
* Fixed weekly recurring bug, calculation errors

= 1.1.5 =
* Fixed bug causing calendar to disappear with the legend turned off
* Fixed bug preventing IE from changing the label color
* Fixed minor error showing when looking at the help page with no calendars defined
* Fixed minor bug causing Legend to display entire list of calendars, even when 'calendars' shortcode arg is set
* Fixed weekly recursion calculation error
* Fixed monthly recursion JS and calculation error when "day of the week" is selected
* Fixed "day" link on Month and Week calendar

= 1.1.4 =
* Added ability to export FT Calendar data as a CSV file
* Added ability to import FT Calendar CSV file
* Added ability to import Event Calendar 3 data from DB table

= 1.1.3 =
* Fixed bug in weekly recurring events
* Fixed bug preventing calendar label color from not appearing properly when editing the taxonomy
* Added missing i18n fields

= 1.1.2 =
* Typo in Event Calendar List argument, named "LINK" but defaulted to "URL". Set both as acceptable arguments.

= 1.1.1 =
* Fixed bug related to recurring monthly and yearly events not displaying properly

= 1.1.0 =
* Initial Premium Release

= 1.0.0 =
* Initial WordPress Release

= 0.3.2 =
* Beta Release

== License ==

FT Calendar
Copyright (C) 2011 Full Throttle Development, LLC.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.
