## 3.0 to 4.0

### New methods implemented on the `ILtiServiceConnector`

Version 4.0 introduced changes to the `Packback\Lti1p3\Interfaces\ILtiServiceConnector` interface, adding the following methods:

- `setDebuggingMode()`
- `makeRequest()`

## 2.0 to 3.0

### New method implemented on the `ICache`

Version 3.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding one method: `clearAccessToken()`. This method must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.

### Using GuzzleHttp\Client instead of curl

The `Packback\Lti1p3\LtiServiceConnector` now uses Guzzle instead of curl to make requests. This puts control of this client and its configuration in the hands of the developer. The section below contains information on implementing this change.

### Changes to the LtiServiceConnector and LTI services

The implementation of the `Packback\Lti1p3\LtiServiceConnector` changed to act as a general API Client for the various LTI service (Assignment Grades, Names Roles Provisioning, etc.) Specifically, the constructor for the following classes now accept different arguments:

- `LtiAssignmentGradesService`
- `LtiCourseGroupsService`
- `LtiNamesRolesProvisioningService`
- `LtiServiceConnector`

The `LtiServiceConnector` now only accepts an `ICache` and `GuzzleHttp\Client`, and does not need an `ILtiRegistration`. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#installation) contains an example of how to implement the service connector and configure the client.

The other LTI services now accept an `ILtiServiceConnector`, `ILtiRegistration`, and `$serviceData` (the registration was added as a new argument since it is no longer required for the `LtiServiceConnector`).

## 1.0 to 2.0

### Renamed Interfaces

A standard naming convention was implemented for interfaces: `Packback\Lti1p3\Interfaces\IObject`. Any implementations of these interfaces should be renamed:
* `Cache` to `ICache`
* `Cookie` to `ICookie`
* `Database` to `IDatabase`
* `LtiRegistrationInterface` to `ILtiRegistration`
* `LtiServiceConnectorInterface` to `ILtiServiceConnector`
* `MessageValidator` to `IMessageValidator`

### New methods implemented on the `ICache`

Version 2.0 introduced changes to the `Packback\Lti1p3\Interfaces\ICache` interface, adding two new methods: `cacheAccessToken()` and `getAccessToken()`. These methods must be implemented to any custom implementations of the interface. The [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide#cache) contains an example.
