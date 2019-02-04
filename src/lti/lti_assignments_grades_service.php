<?php
namespace IMSGlobal\LTI;

class LTI_Assignments_Grades_Service {

    private $service_connector;
    private $service_data;

    public function __construct(LTI_Service_Connector $service_connector, $service_data) {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function put_grade(LTI_Grade $grade) {
        return $this->service_connector->make_service_request(
            $this->service_data['scope'],
            'POST',
            $this->service_data['lineitem'] . '/scores',
            $grade,
            'application/vnd.ims.lis.v1.score+json'
        );

    }
}
?>