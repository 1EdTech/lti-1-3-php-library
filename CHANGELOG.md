## 3.0

* Added a new method to `ICache`: `clearAccessToken()`.
* Modified the constructor arguments for `LtiServiceConnector`, `LtiAssignmentGradesService`, `LtiCourseGroupsService`, and `LtiNamesRolesProvisioningService`.

## 2.0

* A standard naming convention was implemented for all interfaces.
* Added two new methods to `ICache`: `cacheAccessToken()` and `getAccessToken()`.
* Optimized how the `LtiServiceConnector` caches access tokens to reduce the likelihood of being rate limited by Canvas.

For upgrading from 1.0 to 2.0, view the [Upgrade Guide](UPGRADES.md)

## 1.0

* Initial release. View the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide).
