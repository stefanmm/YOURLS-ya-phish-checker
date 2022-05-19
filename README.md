# YA Phish Checker (Yet Another Phishing Checker)
YOURLS plugin. Check for phishing URLs using ipqualityscore API. You can use their free API, but you will be limited to 5.000 API calls per month. Account is required.

This is a fork of the [YOURLS-Phishtank-2.0](https://github.com/joshp23/YOURLS-Phishtank-2.0) plugin.

## Features
Everything is pretty much the same as for Phishtank-2.0:

1. Checks URL submissions against [ipqualityscore's blacklist](https://www.ipqualityscore.com/), and blocks any blacklisted submissions
2. Will optionally re-check old links when they are clicked to see if they have been blacklisted since 1st submitted
3. Can delete or preserve and intercept old links that fail recheck
4. You can customize the intercept page, or use your own url
5. Uses the YOURLS admin section for option setting. No config files
6. Integrates with the [Compliance](https://github.com/joshp23/YOURLS-Compliance) flaglist to track links that have "gone bad"
7. Option to set default error message

## Requirements
1. A working YOURLS installation
2. A ipqualityscore API key (free is limited to 5.000 API calls per month)

## Installation
1. Download, extract, and copy the ya-phish-checker folder to your YOURLS/user/plugins/ folder
2. Enable YA Phish Checker in the "Manage Plugins" page under the Admin section of YOURLS
3. Visit the new Options page for YA Phish Checker, enter in your API key, edit other settings if you want (default values are fine)

## Credits
[YOURLS-Phishtank-2.0](https://github.com/joshp23/YOURLS-Phishtank-2.0)

## Disclaimer
This plugin is offered "as is", and may or may not work for you. Give it a try, and have fun!

## Hire Me
1. [Portfolio](https://www.stefanmarjanov.com/)
2. [Upwork](https://www.upwork.com/freelancers/~018bacd68cbacc8e9d)

```
Copyright (C) 2022 Stefan Marjanov

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```
