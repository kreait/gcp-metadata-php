# GCP Metadata

> Get the metadata from a Google Cloud Platform environment.

[![Current version](https://img.shields.io/packagist/v/kreait/gcp-metadata.svg)](https://packagist.org/packages/kreait/gcp-metadata)
[![Supported PHP version](https://img.shields.io/packagist/php-v/kreait/gcp-metadata.svg)]()
[![GitHub license](https://img.shields.io/github/license/kreait/gcp-metadata-php.svg)](https://github.com/kreait/gcp-metadata-php/blob/master/LICENSE)

```bash
$ composer install kreait/gcp-metadata
```

```php
$metadata = new \Kreait\GcpMetadata();
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
if ($metadata->isAvailable()) {
    echo $metadata->instance('hostname');
}

try {
    echo $metadata->instance('hostname');
catch (\Kreait\GCPMetadata\Error $e) {
    echo $e->getMessage();
}
```
