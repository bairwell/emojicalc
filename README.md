This is an "EmojiCalculator" written by Richard Bairwell

Composer is only used in development environment for unit testing using PHPUnit 6.

At the time of writing, the code is 69.87% unit tested by lines, 93.33% by classes - only
the App.php file is currently untested.

Vagrant based startup
=====================
The [Vagrant](https://vagrantup.com) install is based on [the PHP7 dev box](https://github.com/rlerdorf/php7dev) , but has been
configured to shutdown the standard nginx webserver and startup the PHP inbuilt webserver
on port 8000 and map that port to the host.

To start as a Vagrant image, just download and run:
```
vagrant up
```
Once the message
```
==> default: Ready - access http://localhost:8000/ to preview
```
appears, you can then access the files as [http://localhost:8000/](http://localhost:8000/).

If you need to change the port number used, just up the ``Vagrantfile`` and change
the ``listening_port`` and the start before running ``vagrant up``.

Non-Vagrant startup
===================
Just download the files onto your machine (which already has) and run a command such as:
```
php -S localhost:8000 emojicalc/public/index.php
```
to launch the PHP inbuilt web server on the localhost only on port 8000 and serve the
files within the public/ folder.

You can then access the files as [http://localhost:8000/](http://localhost:8000/)
