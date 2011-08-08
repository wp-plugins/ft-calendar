=== FT Calendar ===
Contributors: layotte, blepoxp, fullthrottledevelopment
Tags: calendar, dates, events, times, event manager, scheduling, recurring, recurring events, ec3, event, widget, shortcode, AJAX, sidebar, repeating, repeat, recur, custom, post, types
Requires at least: 3.0
Tested up to: 3.2
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

* Considers "Week Starts On" setting in WordPress
* Display schedule information within post content
* iCal, RSS 1.0 & 2.0, ATOM, and RDF feeds
* SMART event ordering for WordPress queries
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
= 1.0.4.0 =
* Add POT file for language translations, hopefully will be getting some soon

= 1.0.3.8 =
* Fixed permalink bug in Calendar Thumb widget

= 1.0.3.7 =
* Added new shortcode argument %CALNAME% to the 'class' variable
* Added new shortcode argument %CALNAME% & %CALSLUG% *_template variable in ftcalendar_list
* Added ftc filters for sql queries on the calendars

= 1.0.3.6 =
* Updated main plugin URL
* Fixed timezone bug with event listing on recurring events
* Efficiency updates
* Added filter to change calendar arrows

= 1.0.3.5 =
* Efficiency updates
* Fixed bug with sorting recurring events times
* Fixed timezone issue with non-recurring events (if server time is off)
* Removed Affiliate ad from post edit screen
* Changed "support" link default setting to false

= 1.0.3.4 =
* Added str_ireplace function for older PHP version compatibility
* Fixed bug with day URL on the Month/Week calendar
* Fixed bug with day not changing to the current day being viewed on the Day calendar

= 1.0.3.3 =
* Fixed bug in current_day class name calculation

= 1.0.3.2 =
* Fixed bug in JavaScript when setting "Repeats Every" for recurring events in Internet Explorer.
* Added upgrade() function for changes made to plugin that require DB updates
* Fixed bug in widgets, was incorrectly using term name for the value instead of the term slug
* Fixed weekly recurring bug, calculation errors

= 1.0.3.1 =
* Fixed bug causing calendar to disappear with the legend turned off
* Fixed bug preventing IE from changing the label color
* Fixed minor error showing when looking at the help page with no calendars defined
* Fixed minor bug causing Legend to display entire list of calendars, even when 'calendars' shortcode arg is set
* Fixed weekly recursion calculation error
* Fixed monthly recursion JS and calculation error when "day of the week" is selected
* Fixed "day" link on Month and Week calendar

= 1.0.3 =
* Fixed bug in weekly recurring events
* Fixed bug preventing calendar label color from not appearing properly when editing the taxonomy
* Added missing i18n fields

= 1.0.2 =
* Typo in Event Calendar List argument, named "LINK" but defaulted to "URL". Set both as acceptable arguments.

= 1.0.1 =
* Fixed bug related to recurring monthly and yearly events not displaying properly

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
