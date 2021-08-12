<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiAssignmentsGradesService
{
    private $service_connector;
    private $service_data;

    public function __construct(ILtiServiceConnector $service_connector, array $service_data)
    {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function putGrade(LtiGrade $grade, LtiLineitem $lineitem = null)
    {
        if (!in_array(LtiConstants::AGS_SCOPE_SCORE, $this->service_data['scope'])) {
            throw new LtiException('Missing required scope', 1);
        }
        if ($lineitem !== null && empty($lineitem->getId())) {
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $score_url = $lineitem->getId();
        } elseif ($lineitem === null && !empty($this->service_data['lineitem'])) {
            $score_url = $this->service_data['lineitem'];
        } else {
            $lineitem = LtiLineitem::new()
                ->setLabel('default')
                ->setScoreMaximum(100);
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $score_url = $lineitem->getId();
        }

        // Place '/scores' before url params
        $pos = strpos($score_url, '?');
        $score_url = $pos === false ? $score_url.'/scores' : substr_replace($score_url, '/scores', $pos, 0);

        return $this->service_connector->makeServiceRequest(
            $this->service_data['scope'],
            LtiServiceConnector::METHOD_POST,
            $score_url,
            $grade,
            'application/vnd.ims.lis.v1.score+json'
        );
    }

    public function findOrCreateLineitem(LtiLineitem $new_line_item)
    {
        $line_items = $this->getLineItems();

        foreach ($line_items as $line_item) {
            if (
                (empty($new_line_item->getResourceId()) && empty($new_line_item->getResourceLinkId())) ||
                (isset($line_item['resourceId']) && $line_item['resourceId'] == $new_line_item->getResourceId()) ||
                (isset($line_item['resourceLinkId']) && $line_item['resourceLinkId'] == $new_line_item->getResourceLinkId())
            ) {
                if (empty($new_line_item->getTag()) || $line_item['tag'] == $new_line_item->getTag()) {
                    return new LtiLineitem($line_item);
                }
            }
        }
        $created_line_item = $this->service_connector->makeServiceRequest(
            $this->service_data['scope'],
            LtiServiceConnector::METHOD_POST,
            $this->service_data['lineitems'],
            $new_line_item,
            'application/vnd.ims.lis.v2.lineitem+json',
            'application/vnd.ims.lis.v2.lineitem+json'
        );

        return new LtiLineitem($created_line_item['body']);
    }

    public function getGrades(LtiLineitem $lineitem)
    {
        $lineitem = $this->findOrCreateLineitem($lineitem);
        // Place '/results' before url params
        $pos = strpos($lineitem->getId(), '?');
        $results_url = $pos === false ? $lineitem->getId().'/results' : substr_replace($lineitem->getId(), '/results', $pos, 0);
        $scores = $this->service_connector->makeServiceRequest(
            $this->service_data['scope'],
            LtiServiceConnector::METHOD_GET,
            $results_url,
            null,
            null,
            'application/vnd.ims.lis.v2.resultcontainer+json'
        );

        return $scores['body'];
    }

    public function getLineItems()
    {
        if (!in_array(LtiConstants::AGS_SCOPE_LINEITEM, $this->service_data['scope'])) {
            throw new LtiException('Missing required scope', 1);
        }
        $line_items = [];

        $next_page = $this->service_data['lineitems'];

        while ($next_page) {
            $page = $this->service_connector->makeServiceRequest(
                $this->service_data['scope'],
                LtiServiceConnector::METHOD_GET,
                $next_page,
                null,
                null,
                'application/vnd.ims.lti-gs.v1.contextgroupcontainer+json'
            );

            $line_items = array_merge($line_items, $page['body']);
            $next_page = false;
            $link = $page['headers']['Link'];

            if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $link, $matches)) {
                $next_page = $matches[1];
            }
        }

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($line_items['body']['id'])) {
            $line_items['body'] = [$line_items['body']];
        }

        return $line_items;
    }
}
