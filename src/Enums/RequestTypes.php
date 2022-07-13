<?php

namespace Packback\Lti1p3\Enums;

/**
 * Supported request types which map to an error log message.
 */
enum RequestTypes
{
    case UNSUPPORTED_REQUEST;
    case SYNC_GRADE_REQUEST;
    case CREATE_LINEITEM_REQUEST;
    case GET_LINEITEMS_REQUEST;
    case UPDATE_LINEITEM_REQUEST;
    case AUTH_REQUEST;

    public function error(): string
    {
        return match ($this) {
            RequestType::UNSUPPORTED_REQUEST => 'Logging request data: ',
            RequestType::SYNC_GRADE_REQUEST => 'Syncing grade for this lti_user_id: ',
            RequestType::CREATE_LINEITEM_REQUEST => 'Creating lineitem: ',
            RequestType::GET_LINEITEMS_REQUEST => 'Getting lineitems: ',
            RequestType::UPDATE_LINEITEM_REQUEST => 'Updating lineitem: ',
            RequestType::AUTH_REQUEST => 'Authenticating: ',
        };
    }
}
