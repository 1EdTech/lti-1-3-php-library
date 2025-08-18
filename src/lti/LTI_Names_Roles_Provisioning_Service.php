<?php
namespace IMSGlobal\LTI;

class LTI_Names_Roles_Provisioning_Service {

    private LTI_Service_Connector $service_connector;

    /** @var array<string> $service_data */
    private $service_data;

    /**
     * @param array<string> $service_data
     */
    public function __construct(
        LTI_Service_Connector $service_connector,
        array $service_data
    ) {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    /**
     * @return array<string>
     */
    public function get_members() {

        $members = [];

        $next_page = $this->service_data['context_memberships_url'];

        while ($next_page) {
            $page = $this->service_connector->make_service_request(
                ['https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly'],
                'GET',
                $next_page,
                null,
                'application/json',
                'application/vnd.ims.lti-nrps.v2.membershipcontainer+json'
            );

            $members = array_merge($members, $page['body']['members']);

            $next_page = false;
            foreach($page['headers'] as $header) {
                if (preg_match(LTI_Service_Connector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $next_page = $matches[1];
                    break;
                }
            }
        }

        return $members;
    }
}
