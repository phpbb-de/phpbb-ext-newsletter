# phpBB 3.2/3.3 Extension - phpBB.de Newsletter

Adds a newsletter function to the board. Users can (un)subscribe to it from the UCP and unsubscribe via
a link in every email. The newsletters are sent from the ACP.

Author: Oliver Schramm

URL: https://www.phpbb.de

## Install instructions:
1. Download the extension
2. Copy the whole archive content to: /ext/phpbbde/newsletter
3. Go to your phpBB-Board > Admin Control Panel > Customise > Manage extensions > phpBB.de - Newsletter: enable

## Update instructions:
1. Go to you phpBB-Board > Admin Control Panel > Customise > Manage extensions > phpBB.de - Newsletter: disable
2. Delete all files of the extension from /ext/phpbbde/newsletter
3. Upload all the new files to the same locations
4. Go to you phpBB-Board > Admin Control Panel > Customise > Manage extensions > phpBB.de - Newsletter: enable
5. Purge the board cache

## Automated Testing

We use automated unit tests to prevent regressions. Check out our travis build below:

master: [![Build Status](https://travis-ci.org/phpbb-de/phpbb-ext-newsletter.png?branch=master)](http://travis-ci.org/phpbb-de/phpbb-ext-newsletter)

## License

[GPLv2](license.txt)
