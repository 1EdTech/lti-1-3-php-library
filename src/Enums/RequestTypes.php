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
            RequestTypes::UNSUPPORTED_REQUEST => 'Logging request data: ',
            RequestTypes::SYNC_GRADE_REQUEST => 'Syncing grade for this lti_user_id: ',
            RequestTypes::CREATE_LINEITEM_REQUEST => 'Creating lineitem: ',
            RequestTypes::GET_LINEITEMS_REQUEST => 'Getting lineitems: ',
            RequestTypes::UPDATE_LINEITEM_REQUEST => 'Updating lineitem: ',
            RequestTypes::AUTH_REQUEST => 'Authenticating: ',
        };
    }
}
