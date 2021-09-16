<?php

namespace Packback\Lti1p3;

class LtiNamesRolesProvisioningService extends LtiAbstractService
{
    public function getScope()
    {
        return [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY];
    }

    public function getMembers()
    {
        $members = [];

        $nextPage = $this->getServiceData()['context_memberships_url'];

        while ($nextPage) {
            $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $nextPage);
            $request->setAccept('application/vnd.ims.lti-nrps.v2.membershipcontainer+json');
            $page = $this->makeServiceRequest($request);

            $members = array_merge($members, $page['body']['members']);

            $nextPage = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $nextPage = $matches[1];
                    break;
                }
            }
        }

        return $members;
    }
}
