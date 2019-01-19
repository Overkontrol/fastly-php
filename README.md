Fastly PHP Client 
====

Installation via Composer
-------------------------
The recommended method to install _Fastly-PHP_ is through [Composer](http://getcomposer.org).

1. Add ``ovk/fastly-php`` as a dependency in your project's ``composer.json``:

    ```json
        {
            "require": {
                "ovk/fastly-php": "~1.0.0"
            }
        }
    ```

2. Download and install Composer:

    ```bash
        curl -s http://getcomposer.org/installer | php
    ```

3. Install your dependencies:

    ```bash
        php composer.phar install --no-dev
    ```

4. Require Composer's autoloader

    Composer also prepares an autoload file that's capable of autoloading all of the classes in any of the libraries that it downloads. To use it, just add the following line to your code's bootstrap process:

    ```php
        <?php
        require 'vendor/autoload.php';

        $adapter = new GuzzleAdapter('fastly-api-key');
        $client = new Fastly($adapter, "default-service-id");
    ```
You can find out more on how to install Composer, configure autoloading, and other best-practices for defining dependencies at [getcomposer.org](http://getcomposer.org).

You'll notice that the installation command specified `--no-dev`.  This prevents Composer from installing the various testing and development dependencies.  For average users, there is no need to install the test suite. If you wish to contribute to development, just omit the `--no-dev` flag to be able to run tests.

Changelog v1.0.0
---
- Added Soft-purge support
- Added support to native batch hard purge
- Implemented async requests to speed up the purge process
- Changed the output: It is always a decoded json from fastly's response. 


Example
---

```php
$adapter = new GuzzleAdapter('fastly-api-key');
$fastly = new Fastly($adapter, 'my-service-id');

$result = $fastly->send('GET', 'stats?from=1+day+ago');

$result = $fastly->purgeAll();
```

```php
// Purge multiple urls
$result = $fastly->purge(['url1', 'url2', 'url3']);
```

```php
// Purge multiple tags
$result = $fastly->purgeKey(['tag1', 'tag2', 'tag3']);
```

```php
// Softpurge multiple tags
$result = $fastly->softPurgeKey(['tag1', 'tag2', 'tag3']);
```

To target a different service than the default configured:
```php
$result = $fastly->service('another-service-id')->softPurgeKey(['tag1', 'tag2', 'tag3']);
```

``$result`` is always an array of decodified fastly's json response.

To retreive errors:
```php
$errors = $fastly->getError();
```

Adapters
---
This packages uses [Guzzle](https://github.com/guzzle/guzzle) as the default http client.

To use a different http client an adapter class that implements implementing ``Fastly\Adapter\AdapterInterface`` should be provided.


License
-----

This package uses the MIT License (MIT). Please see [`LICENSE`](LICENSE) for more information.
