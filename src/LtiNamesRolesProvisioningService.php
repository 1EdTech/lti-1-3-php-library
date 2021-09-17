<?php

namespace Packback\Lti1p3;

class LtiNamesRolesProvisioningService extends LtiAbstractService
{
    public function getScope(): array
    {
        return [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY];
    }

    public function getMembers(): array
    {
        $request = new ServiceRequest(
            LtiServiceConnector::METHOD_GET,
            $this->getServiceData()['context_memberships_url']
        );
        $request->setAccept('application/vnd.ims.lti-nrps.v2.membershipcontainer+json');

        return $this->getAll($request, 'members');
    }
}
