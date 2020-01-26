Shinage Player
==============

This is the player part for the shinage digital signage solution.
See https://github.com/michz/shinage-server for the server.

Usage
-----

You need a webserver that serves the `public` folder on `localhost:8000`.
Moreover you need a webbrowser that shows `http://localhost:8000`.

And you need a cronjob that regularily calls:
`bin/console shinage:synchronize` .

Add a file `.env` to the root that contains following information:

```env
APP_ENV=prod
APP_SECRET=chose_one_but_this_is_not_that_important_for_local_usage
```

License
-------

As usual for shinage digital signage: MIT

