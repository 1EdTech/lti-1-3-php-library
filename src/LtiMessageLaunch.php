<?php

namespace Packback\Lti1p3;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use Packback\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;

class LtiMessageLaunch
{
    public const TYPE_DEEPLINK = 'LtiDeepLinkingRequest';
    public const TYPE_SUBMISSIONREVIEW = 'LtiSubmissionReviewRequest';
    public const TYPE_RESOURCELINK = 'LtiResourceLinkRequest';

    public const ERR_FETCH_PUBLIC_KEY = 'Failed to fetch public key.';
    public const ERR_NO_PUBLIC_KEY = 'Unable to find public key.';
    public const ERR_STATE_NOT_FOUND = 'State not found. Please make sure you have cookies enabled in this browser.';
    public const ERR_MISSING_ID_TOKEN = 'Missing id_token.';
    public const ERR_INVALID_ID_TOKEN = 'Invalid id_token, JWT must contain 3 parts';
    public const ERR_MISSING_NONCE = 'Missing Nonce.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';
    public const ERR_MISSING_REGISTRATION = 'Registration not found. Please have your admin confirm your Issuer URL, client ID, and deployment ID.';
    public const ERR_CLIENT_NOT_REGISTERED = 'Client id not registered for this issuer.';
    public const ERR_NO_KID = 'No KID specified in the JWT Header.';
    public const ERR_INVALID_SIGNATURE = 'Invalid signature on id_token';
    public const ERR_MISSING_DEPLOYEMENT_ID = 'No deployment ID was specified';
    public const ERR_NO_DEPLOYMENT = 'Unable to find deployment.';
    public const ERR_INVALID_MESSAGE_TYPE = 'Invalid message type';
    public const ERR_VALIDATOR_CONFLICT = 'Validator conflict.';
    public const ERR_UNRECOGNIZED_MESSAGE_TYPE = 'Unrecognized message type.';
    public const ERR_INVALID_MESSAGE = 'Message validation failed.';

    private $db;
    private $cache;
    private $cookie;
    private $serviceConnector;
    private $request;
    private $jwt;
    private $registration;
    private $launch_id;

    /**
     * Constructor.
     *
     * @param IDatabase            $database         instance of the database interface used for looking up registrations and deployments
     * @param ICache               $cache            instance of the Cache interface used to loading and storing launches
     * @param ICookie              $cookie           instance of the Cookie interface used to set and read cookies
     * @param ILtiServiceConnector $serviceConnector instance of the LtiServiceConnector used to by LTI services to make API requests
     */
    public function __construct(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null,
        ILtiServiceConnector $serviceConnector = null
    ) {
        $this->db = $database;

        $this->launch_id = uniqid('lti1p3_launch_', true);

        $this->cache = $cache;
        $this->cookie = $cookie;
        $this->serviceConnector = $serviceConnector;
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null,
        ILtiServiceConnector $serviceConnector = null
        ) {
        return new LtiMessageLaunch($database, $cache, $cookie, $serviceConnector);
    }

    /**
     * Load an LtiMessageLaunch from a Cache using a launch id.
     *
     * @param string    $launch_id the launch id of the LtiMessageLaunch object that is being pulled from the cache
     * @param IDatabase $database  instance of the database interface used for looking up registrations and deployments
     * @param ICache    $cache     Instance of the Cache interface used to loading and storing launches. If non is provided launch data will be store in $_SESSION.
     *
     * @throws LtiException will throw an LtiException if validation fails or launch cannot be found
     *
     * @return LtiMessageLaunch a populated and validated LtiMessageLaunch
     */
    public static function fromCache($launch_id,
        IDatabase $database,
        ICache $cache = null,
        ILtiServiceConnector $serviceConnector = null)
    {
        $new = new LtiMessageLaunch($database, $cache, null, $serviceConnector);
        $new->launch_id = $launch_id;
        $new->jwt = ['body' => $new->cache->getLaunchData($launch_id)];

        return $new->validateRegistration();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @param array|string $request An array of post request parameters. If not set will default to $_POST.
     *
     * @throws LtiException will throw an LtiException if validation fails
     *
     * @return LtiMessageLaunch will return $this if validation is successful
     */
    public function validate(array $request = null)
    {
        if ($request === null) {
            $request = $_POST;
        }
        $this->request = $request;

        return $this->validateState()
            ->validateJwtFormat()
            ->validateNonce()
            ->validateRegistration()
            ->validateJwtSignature()
            ->validateDeployment()
            ->validateMessage()
            ->cacheLaunchData();
    }

    /**
     * Returns whether or not the current launch can use the names and roles service.
     *
     * @return bool returns a boolean indicating the availability of names and roles
     */
    public function hasNrps()
    {
        return !empty($this->jwt['body'][LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url']);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     *
     * @return LtiNamesRolesProvisioningService an instance of the names and roles service that can be used to make calls within the scope of the current launch
     */
    public function getNrps()
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::NRPS_CLAIM_SERVICE]);
    }

    /**
     * Returns whether or not the current launch can use the groups service.
     *
     * @return bool returns a boolean indicating the availability of groups
     */
    public function hasGs()
    {
        return !empty($this->jwt['body'][LtiConstants::GS_CLAIM_SERVICE]['context_groups_url']);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     *
     * @return LtiCourseGroupsService an instance of the groups service that can be used to make calls within the scope of the current launch
     */
    public function getGs()
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::GS_CLAIM_SERVICE]);
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     *
     * @return bool returns a boolean indicating the availability of assignments and grades
     */
    public function hasAgs()
    {
        return !empty($this->jwt['body'][LtiConstants::AGS_CLAIM_ENDPOINT]);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     *
     * @return LtiAssignmentsGradesService an instance of the assignments an grades service that can be used to make calls within the scope of the current launch
     */
    public function getAgs()
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::AGS_CLAIM_ENDPOINT]);
    }

    /**
     * Returns whether or not the current launch is a deep linking launch.
     *
     * @return bool returns true if the current launch is a deep linking launch
     */
    public function isDeepLinkLaunch()
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_DEEPLINK;
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     *
     * @return LtiDeepLink an instance of a deep link to construct a deep linking response for the current launch
     */
    public function getDeepLink()
    {
        return new LtiDeepLink(
            $this->registration,
            $this->jwt['body'][LtiConstants::DEPLOYMENT_ID],
            $this->jwt['body'][LtiConstants::DL_DEEP_LINK_SETTINGS]);
    }

    /**
     * Returns whether or not the current launch is a submission review launch.
     *
     * @return bool returns true if the current launch is a submission review launch
     */
    public function isSubmissionReviewLaunch()
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_SUBMISSIONREVIEW;
    }

    /**
     * Returns whether or not the current launch is a resource launch.
     *
     * @return bool returns true if the current launch is a resource launch
     */
    public function isResourceLaunch()
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_RESOURCELINK;
    }

    /**
     * Fetches the decoded body of the JWT used in the current launch.
     *
     * @return array|object returns the decoded json body of the launch as an array
     */
    public function getLaunchData()
    {
        return $this->jwt['body'];
    }

    /**
     * Get the unique launch id for the current launch.
     *
     * @return string a unique identifier used to re-reference the current launch in subsequent requests
     */
    public function getLaunchId()
    {
        return $this->launch_id;
    }

    private function getPublicKey()
    {
        $keySetUrl = $this->registration->getKeySetUrl();
        $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $keySetUrl);

        // Download key set
        $response = $this->serviceConnector->makeRequest($request);
        $publicKeySet = LtiServiceConnector::getResponseBody($response);

        if (empty($publicKeySet)) {
            // Failed to fetch public keyset from URL.
            throw new LtiException(static::ERR_FETCH_PUBLIC_KEY);
        }

        // Find key used to sign the JWT (matches the KID in the header)
        foreach ($publicKeySet['keys'] as $key) {
            if ($key['kid'] == $this->jwt['header']['kid']) {
                try {
                    return openssl_pkey_get_details(
                        JWK::parseKeySet([
                            'keys' => [$key],
                        ])[$key['kid']]
                    );
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        // Could not find public key with a matching kid and alg.
        throw new LtiException(static::ERR_NO_PUBLIC_KEY);
    }

    private function cacheLaunchData()
    {
        $this->cache->cacheLaunchData($this->launch_id, $this->jwt['body']);

        return $this;
    }

    private function validateState()
    {
        // Check State for OIDC.
        if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$this->request['state']) !== $this->request['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    private function validateJwtFormat()
    {
        $jwt = $this->request['id_token'] ?? null;

        if (empty($jwt)) {
            throw new LtiException(static::ERR_MISSING_ID_TOKEN);
        }

        // Get parts of JWT.
        $jwt_parts = explode('.', $jwt);

        if (count($jwt_parts) !== 3) {
            // Invalid number of parts in JWT.
            throw new LtiException(static::ERR_INVALID_ID_TOKEN);
        }

        // Decode JWT headers.
        $this->jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
        // Decode JWT Body.
        $this->jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);

        return $this;
    }

    private function validateNonce()
    {
        if (!isset($this->jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }
        if (!$this->cache->checkNonce($this->jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

    private function validateRegistration()
    {
        // Find registration.
        $client_id = is_array($this->jwt['body']['aud']) ? $this->jwt['body']['aud'][0] : $this->jwt['body']['aud'];
        $this->registration = $this->db->findRegistrationByIssuer($this->jwt['body']['iss'], $client_id);

        if (empty($this->registration)) {
            throw new LtiException(static::ERR_MISSING_REGISTRATION);
        }

        // Check client id.
        if ($client_id !== $this->registration->getClientId()) {
            // Client not registered.
            throw new LtiException(static::ERR_CLIENT_NOT_REGISTERED);
        }

        return $this;
    }

    private function validateJwtSignature()
    {
        if (!isset($this->jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $public_key = $this->getPublicKey();

        // Validate JWT signature
        try {
            JWT::decode($this->request['id_token'], $public_key['key'], ['RS256']);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE);
        }

        return $this;
    }

    private function validateDeployment()
    {
        if (!isset($this->jwt['body'][LtiConstants::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        // Find deployment.
        $client_id = is_array($this->jwt['body']['aud']) ? $this->jwt['body']['aud'][0] : $this->jwt['body']['aud'];
        $deployment = $this->db->findDeployment($this->jwt['body']['iss'], $this->jwt['body'][LtiConstants::DEPLOYMENT_ID], $client_id);

        if (empty($deployment)) {
            // deployment not recognized.
            throw new LtiException(static::ERR_NO_DEPLOYMENT);
        }

        return $this;
    }

    private function validateMessage()
    {
        if (empty($this->jwt['body'][LtiConstants::MESSAGE_TYPE])) {
            // Unable to identify message type.
            throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }

        /**
         * @todo Fix this nonsense
         */

        // Create instances of all validators
        $validators = [
            new DeepLinkMessageValidator(),
            new ResourceMessageValidator(),
            new SubmissionReviewMessageValidator(),
        ];

        $message_validator = false;
        foreach ($validators as $validator) {
            if ($validator->canValidate($this->jwt['body'])) {
                if ($message_validator !== false) {
                    // Can't have more than one validator apply at a time.
                    throw new LtiException(static::ERR_VALIDATOR_CONFLICT);
                }
                $message_validator = $validator;
            }
        }

        if ($message_validator === false) {
            throw new LtiException(static::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        }

        if (!$message_validator->validate($this->jwt['body'])) {
            throw new LtiException(static::ERR_INVALID_MESSAGE);
        }

        return $this;
    }
}
