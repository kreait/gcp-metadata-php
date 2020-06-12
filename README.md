# GCP Metadata

> Get the metadata from a Google Cloud Platform environment.

[![Current version](https://img.shields.io/packagist/v/kreait/gcp-metadata.svg)](https://packagist.org/packages/kreait/gcp-metadata)
[![Supported PHP version](https://img.shields.io/packagist/php-v/kreait/gcp-metadata.svg)]()
[![GitHub license](https://img.shields.io/github/license/kreait/gcp-metadata-php.svg)](https://github.com/kreait/gcp-metadata-php/blob/main/LICENSE)
[![Unit Tests](https://github.com/kreait/gcp-metadata-php/workflows/Unit%20Tests/badge.svg)](https://github.com/kreait/gcp-metadata-php/actions)

```bash
$ composer install kreait/gcp-metadata
```

```php
use Kreait\GcpMetadata;

$metadata = new GcpMetadata();
```

#### Check if the metadata server is available

```php
$isAvailable = $metadata->isAvailable();
```

#### Get all available instance properties

```php
$data = $metadata->instance();
```

#### Get all available project properties

```php
$data = $metadata->project();
```

#### Access a specific property

```php
$data = $metadata->instance('hostname');
```

#### Wrap queries in a try/catch block if you don't check for availability

```php
use Kreait\GcpMetadata;

$metadata = new GcpMetadata();

if ($metadata->isAvailable()) {
    echo $metadata->instance('hostname');
}

try {
    echo $metadata->instance('hostname');   
} catch (GcpMetadata\Error $e) {
    echo $e->getMessage();
}
```
