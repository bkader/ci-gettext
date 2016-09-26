# ci-gettext
CodeIgniter using [Gettext](http://php.net/manual/en/book.gettext.php) if *php_gettext* extension is enabled or *[php-gettext](https://launchpad.net/php-gettext/)* library if it is not.

## How to install?

1. Copy _./application/config/gettext.php_ files and make your changes
2. Load Gettext library in _./application/config/autoload.php_.
3. Create your language files like so: *./application/language/english/LC_MESSAGES/messages.po*
4. If you have edited _index.php_, make sure to add lines **322** and **328** to yours.
5. You're done!

## Licenses

All licenses go to their respective owners ([CodeIgniter](http://www.codeigniter.com/) & [Launchpad](https://launchpad.net/php-gettext/)).
