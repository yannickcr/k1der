# K1der.net

This repository is an archive of the [k1der.net](http://k1der.net) website source code.

## [v1](https://github.com/yannickcr/k1der.net/tree/v1) (2001-2002)

This is a just a capture of the original homepage as the source code was lost in 2003 due to a [hard drive crash](./medias/galeries/sofia-lan-iv/photos/img046.jpg). [Thanks IBM](https://en.wikipedia.org/wiki/Deskstar#IBM_Deskstar_75GXP_failures) :(

The original website was in static HTML. The updates were done by editing the HTML by hand in Frontpage, copying everything on a 3½-inch floppy disk and uploading everything on an FTP using the school computer (first on a free hosting on Respublica then on Multimania).

## [v2](https://github.com/yannickcr/k1der.net/tree/v2) (2002-2005)

First attempt to write some PHP. It was done with Dreamweaver and directly uploaded with it with the local<->ftp sync functionality (developing in production, it was the style at the time :D )

You can still run this projet on PHP5 (tested with 5.6.30) with a few tweaks:

- `error_reporting = E_ERROR`
- `short_open_tag = On`
- Was designed to work with `register_globals = On`, but since this option is not avaible anymore you'll need to put `extract($_REQUEST)` at the top of some files;

The SQL Schema is available in `k1der1.sql`. The schema was extracted without any data and I doubt the website would work with an empty database (I did not test it). You may need to manually add some records into the database to make it work.

During it's lifetime the project was converted between different encodings and some special characters were lost during the conversions. It is very visible in the (french) comments in the code.

## [v3](https://github.com/yannickcr/k1der.net/tree/main) (2005-2007)

This is the source code that still power [k1der.net](http://k1der.net) to this day.

It's a full rewrite of the original website, still in PHP but with an attempt to have a better structure with some url rewrite, a routing system (in xml :| ) and a templating system. Everything is splitted into modules that can be activated/deactivated depending of your needs. Original README can be found [here](README).

Like the v2, the project should run on PHP5 (tested with 5.6.30) if you set `error_reporting` to `E_ERROR` (there is a lots of `E_DEPRECATED`!).

The SQL Schema is available in `k1dernet.sql`. The schema was extracted without any data and I doubt the website would work with an empty database (I did not test it). You may need to manually add some records into the database to make it work.
