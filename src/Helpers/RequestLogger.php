<?php

namespace Packback\Lti1p3\Helpers;

use Packback\Lti1p3\Interfaces\IServiceRequest;

class RequestLogger
{
    // Supported request types which map to an error log message
    public const UNSUPPORTED_REQUEST = 0;
    public const SYNC_GRADE_REQUEST = 1;
    public const CREATE_LINEITEM_REQUEST = 2;

    private $errorMessages;

    public function __construct()
    {
        $this->errorMessages = [
            static::UNSUPPORTED_REQUEST => 'Logging request data: ',
            static::SYNC_GRADE_REQUEST => 'Syncing grade for this lti_user_id: ',
            static::CREATE_LINEITEM_REQUEST => 'Creating lineitem for this lti_user_id: ',
        ];
    }

    public function logRequest(
        int $requestType,
        IServiceRequest $request,
        array $responseHeaders,
        ?array $responseBody
    ): void {
        $requestBody = $request->getPayload()['body'];

        $contextArray = [
            'request_method' => $request->getMethod(),
            'request_url' => $request->getUrl(),
            'request_body' => $requestBody,
            'response_headers' => $responseHeaders,
            'response_body' => json_encode($responseBody),
        ];

        $this->errorLog(json_decode($requestBody)->userId, $contextArray);
    }

    /**
     * A wrapper for the PHP error_log function to facilitate testing.
     */
    public function errorLog(
        string $userId,
        array $contextArray
    ): void {
        $logMsg = $this->errorMessages[$requestType];

        error_log($logMsg.$userId.' '.print_r($contextArray, true));
    }
}
