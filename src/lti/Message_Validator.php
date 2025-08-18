<?php
namespace IMSGlobal\LTI;

interface Message_Validator {
    /**
     * Validate a message.
     * @param array<mixed> $jwt_body Body of the JWT in the message.
     * @return bool TRUE for valid messages.
     */
    public function validate(array $jwt_body): bool;

    /**
     * Determine if a message is sufficiently well-formed that it can be validated.
     * @param array<mixed> $jwt_body Body of the JWT in the message.
     * @return bool TRUE for messages that can be validated.
     */
    public function can_validate(array $jwt_body): bool;
}
