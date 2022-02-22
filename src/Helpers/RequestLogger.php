<?php

namespace Packback\Lti1p3\Helpers;

use Packback\Lti1p3\Interfaces\IServiceRequest;

class RequestLogger
{
    // Supported request types which map to an error log message
    public const UNSUPPORTED_REQUEST = 0;
    public const SYNC_GRADE_REQUEST = 1;
    public const CREATE_LINEITEM_REQUEST = 2;

    public function logRequest(
        int $requestType,
        IServiceRequest $request,
        array $responseHeaders,
        ?array $responseBody
    ): void {
        $errorLogMsg = $this->getErrorLogMsg($requestType);

        error_log($errorLogMsg.
            json_decode($request->getPayload()['body'])->userId.' '.print_r([
                'request_method' => $request->getMethod(),
                'request_url' => $request->getUrl(),
                'request_body' => $request->getPayload()['body'],
                'response_headers' => $responseHeaders,
                'response_body' => json_encode($responseBody),
            ], true));
    }

    public function getErrorLogMsg(int $requestType): string
    {
        switch ($requestType) {
            case static::SYNC_GRADE_REQUEST:
                return 'Syncing grade for this lti_user_id: ';
            case static::CREATE_LINEITEM_REQUEST:
                return 'Creating lineitem for this lti_user_id: ';
            default:
                return 'Logging request data: ';
        }
    }
}
