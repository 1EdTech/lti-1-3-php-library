<?php
namespace IMSGlobal\LTI;

class LTI_Names_Roles_Provisioning_Service {

    private $service_connector;
    private $service_data;

    public function __construct(LTI_Service_Connector $service_connector, $service_data) {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function get_members() {
        return $this->service_connector->make_service_request(
            ['https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly'],
            'GET',
            $this->service_data['context_memberships_url']
        );

    }
}
?>