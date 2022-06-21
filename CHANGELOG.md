# CHANGELOG

## 1.3.2 - 2022-06-22

* Raised minimum version of Guzzle to address [CVE-2022-31090](https://github.com/advisories/GHSA-25mq-v84q-4j7r) 
  and [CVE-2022-31091](https://github.com/advisories/GHSA-q559-8m2m-g699)

## 1.3.1 - 2022-06-13

* Raised minimum version of Guzzle to address [CVE-2022-31042](https://github.com/advisories/GHSA-f2wf-25xc-69c9)

## 1.3.0 - 2022-05-26

* Dropped support for PHP <7.4
* Raised minimum version of Guzzle to address [CVE-2022-29248](https://github.com/advisories/GHSA-cwmx-hcrq-mhc3)

## 1.2.0 - 2021-05-26

* Added support for Guzzle 7
* Raised minimum required Guzzle 6 version from 6.0 to 6.3.3
* Added support for PHP 8
* Dropped support for PHP <7.2

## 1.1.0 - 2020-03-19

* Use IP address instead of hostname for the Google Metadata service to avoid slow requests when not on Compute Engine
* The availability status of the Metadata Service is now cached to save requests
* Configured the underlying HTTP client to be less resilient, but faster

## 1.0.1 - 2018-07-28

### Bugfixes

* Changed `Kreait\Error` to `Kreait\GcpMetadata\Error` (noone uses this library yet, so I'll pretend it's a non-breaking change :)

## 1.0 - 2018-07-27

* Initial release

