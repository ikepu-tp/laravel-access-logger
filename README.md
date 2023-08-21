# Laravel Access Logger

This library stores user access logs in Laravel. You can choose between log files and databases for storage.

## How to use

1. First of all, migrate.
2. Configure driver to be `database` or `file`
3. Configure keys such as `web` and `api`.
   1. If you want to store file, also configure `logging.php`.
   2. Add the channels whose names are keys to `logging.php` by key. (see. example below.)
4. Configure guard which is used to store `user_id` (`getKey()`).
   1. Configure guard to be `false` and guards by key if you want to separate by key.
5. Configure except which is used to store request bodies(`$request->input()`) as `array`.
    This will be set `$request->except()`.
6. Add `\ikepu_tp\AccessLogger\app\Http\Middleware\AccessLoggerMiddleware::class` to `Kernel.php`.
7. Add `Route::resource("logs", ikepu_tp\AccessLogger\app\Http\Controllers\LogController::class)->names("accessLogger")->only(["index",]);` to `/route/web.php` if your admin want to see logs of all users. (This view shows all logs of all users. So DO NOT show for no-admin users.)

### example of `logging.php`

```php
'web' => [
    'driver' => 'daily',
    'path' => storage_path('logs/web/laravel.log'),
    'days' => 14,
    'replace_placeholders' => true,
],
```

## Contributing

Thank you for your contributions. If you find bugs, let me know them with issues.

## License

Copyright (c) 2023 Yuma Ikeda.

This is open-sourced software licensed under the [MIT license](LICENSE).
