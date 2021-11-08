## 4.1.0

* Allow getGrades() without a line item ([#34](https://github.com/packbackbooks/lti-1-3-php-library/pull/34))
* Fix fetching of line items and eliminate a PHP warning for missing key ([#35](https://github.com/packbackbooks/lti-1-3-php-library/pull/35))
* Include resourceLinkId attribute when creating a line item ([#36](https://github.com/packbackbooks/lti-1-3-php-library/pull/36))

## 4.0.0

* Added a new method to `ILtiServiceConnector`: `setDebuggingMode()`. ([#32](https://github.com/packbackbooks/lti-1-3-php-library/pull/32))

## 3.0.3

* Adds response/request logging to LtiServiceConnector ([#31](https://github.com/packbackbooks/lti-1-3-php-library/pull/31))

Note: Upgrade to 4.0.0 for this logging to be disabled by default.

## 3.0.2

* Fix grades with a score of 0 not being synced ([#30](https://github.com/packbackbooks/lti-1-3-php-library/pull/30))

## 3.0.1

* Fix a few minor errors related to array indexes. ([#27](https://github.com/packbackbooks/lti-1-3-php-library/pull/27), [#28](https://github.com/packbackbooks/lti-1-3-php-library/pull/28), [#29](https://github.com/packbackbooks/lti-1-3-php-library/pull/29))
* Increase test coverage on the LtiMessageLaunch ([#28](https://github.com/packbackbooks/lti-1-3-php-library/pull/28))

## 3.0.0

* Added a new method to `ICache`: `clearAccessToken()`.
* Modified the constructor arguments for `LtiServiceConnector`, `LtiAssignmentGradesService`, `LtiCourseGroupsService`, and `LtiNamesRolesProvisioningService`.

## 2.0.3

* Makes an optimization to the logic added in 2.0.2. ([#19](https://github.com/packbackbooks/lti-1-3-php-library/pull/19))

## 2.0.2

* Fixes pagination of lineitems in the `LtiAssignmentGradesService` and makes it possible to get all lineitems with a single function call. ([#17](https://github.com/packbackbooks/lti-1-3-php-library/pull/17))

## 2.0.1

* Fixes a bug in the `LtiServiceConnector` that was causing double-encoded JSON to be sent in POST bodies, introduced in 2.0.0. ([#15](https://github.com/packbackbooks/lti-1-3-php-library/pull/15))

## 2.0.0

* A standard naming convention was implemented for all interfaces.
* Added two new methods to `ICache`: `cacheAccessToken()` and `getAccessToken()`.
* Optimized how the `LtiServiceConnector` caches access tokens to reduce the likelihood of being rate limited by Canvas.

For upgrading from 1.0 to 2.0, view the [Upgrade Guide](UPGRADES.md)

## 1.1.1

* Added a `text` parameter to `LtiDeepLinkResource` ([#5](https://github.com/packbackbooks/lti-1-3-php-library/pull/5))

## 1.1.0

* Added a custom Canvas extension to `LtiGrade` ([#3](https://github.com/packbackbooks/lti-1-3-php-library/pull/3))

## 1.0.0

* Initial release. View the [Laravel Implementation Guide](https://github.com/packbackbooks/lti-1-3-php-library/wiki/Laravel-Implementation-Guide).
