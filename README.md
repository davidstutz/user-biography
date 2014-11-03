# Wordpress Plugin: User Biography

A wordpress plugin aiming to provide a timeline-like user biography - users may add biographical parts over time which may then be displayed on the user's page in a timeline.

**Work in progress.**

## Installation

In `wp-content/plugins`, create a new folder `user-biography` and put all files within this repository in this folder. In the backend, go to "Plugins" -> "Installed Plugins" and activate "User Biography".

## Usage

After activating the plugin, navigate to "Users" > "Your Profile". Below "About Yourself" you will find an additional section "Biography":

![Additional "Biography" section in "Your Profile".](screenshot.png?raw=true 'Additional "Biography" section in "Your Profile".')

The form allows to add so-called "Parts" to your biography.

In order to query these parts in your template, query for posts with type `ub_part`.

## License

Copyright (C) 2014 David Stutz

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

See [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).