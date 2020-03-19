# CHANGELOG

## Unreleased

* Use IP address instead of hostname for the Google Metadata service to avoid slow requests when not on Compute Engine
* The availability status of the Metadata Service is now cached to save requests

## 1.0.1 - 2018-07-28

### Bugfixes

* Changed `Kreait\Error` to `Kreait\GcpMetadata\Error` (noone uses this library yet, so I'll pretend it's a non-breaking change :)

## 1.0 - 2018-07-27

* Initial release

