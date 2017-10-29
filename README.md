# ci-gettext
CodeIgniter using [Gettext](http://php.net/manual/en/book.gettext.php) if *php_gettext* extension is enabled or *[php-gettext](https://launchpad.net/php-gettext/)* library if it is not.

## How to install?

1. Copy _./application/config/gettext.php_ files and make your changes
2. Load Gettext library in _./application/config/autoload.php_.
3. Create your language files like so: *./application/language/english/LC_MESSAGES/messages.po*
4. If you have edited _index.php_, make sure to add lines **[322](https://github.com/bkader/ci-gettext/blob/5ddb491ddf829f70787ac12d9397398001332e91/index.php#L322)** and **[323](https://github.com/bkader/ci-gettext/blob/5ddb491ddf829f70787ac12d9397398001332e91/index.php#L323)** to yours.
5. You're done!

## Notes

1. All you languages folder must contain a **LC_MESSAGES** folder inside which resides **.MO** files.
2. **Gettext_helper** provided comes with handy functions that you can use instead of default ***gettext($msgid){}***. It comes also with functions providing details about **current**, **default** and **client**'s language : ***(current|default|client)_language***.

## Licenses

All credits go to their respective owners [CodeIgniter](http://www.codeigniter.com/) & [Launchpad](https://launchpad.net/php-gettext/) and a little bit of the rest for [Me](https://github.com/bkader/) :D.
