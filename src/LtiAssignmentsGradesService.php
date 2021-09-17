<?php

namespace Packback\Lti1p3;

class LtiAssignmentsGradesService extends LtiAbstractService
{
    public function getScope()
    {
        return $this->getServiceData()['scope'];
    }

    public function putGrade(LtiGrade $grade, LtiLineitem $lineitem = null)
    {
        if (!in_array(LtiConstants::AGS_SCOPE_SCORE, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }
        if ($lineitem !== null && empty($lineitem->getId())) {
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $scoreUrl = $lineitem->getId();
        } elseif ($lineitem === null && !empty($this->getServiceData()['lineitem'])) {
            $scoreUrl = $this->getServiceData()['lineitem'];
        } else {
            $lineitem = LtiLineitem::new()
                ->setLabel('default')
                ->setScoreMaximum(100);
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $scoreUrl = $lineitem->getId();
        }

        // Place '/scores' before url params
        $pos = strpos($scoreUrl, '?');
        $scoreUrl = $pos === false ? $scoreUrl.'/scores' : substr_replace($scoreUrl, '/scores', $pos, 0);

        $request = new ServiceRequest(LtiServiceConnector::METHOD_POST, $scoreUrl);
        $request->setBody($grade);
        $request->setContentType('application/vnd.ims.lis.v1.score+json');

        return $this->makeServiceRequest($request);
    }

    public function findOrCreateLineitem(LtiLineitem $newLineItem)
    {
        $lineitems = $this->getLineItems();

        foreach ($lineitems as $lineitem) {
            if (
                (empty($newLineItem->getResourceId()) && empty($newLineItem->getResourceLinkId())) ||
                (isset($lineitem['resourceId']) && $lineitem['resourceId'] == $newLineItem->getResourceId()) ||
                (isset($lineitem['resourceLinkId']) && $lineitem['resourceLinkId'] == $newLineItem->getResourceLinkId())
            ) {
                if (empty($newLineItem->getTag()) || $lineitem['tag'] == $newLineItem->getTag()) {
                    return new LtiLineitem($lineitem);
                }
            }
        }
        $request = new ServiceRequest(LtiServiceConnector::METHOD_POST, $this->getServiceData()['lineitems']);
        $request->setBody($newLineItem)
            ->setContentType('application/vnd.ims.lis.v2.lineitem+json')
            ->setAccept('application/vnd.ims.lis.v2.lineitem+json');
        $createdLineItems = $this->makeServiceRequest($request);

        return new LtiLineitem($created_lineitem['body']);
    }

    public function getGrades(LtiLineitem $lineitem)
    {
        $lineitem = $this->findOrCreateLineitem($lineitem);
        // Place '/results' before url params
        $pos = strpos($lineitem->getId(), '?');
        $results_url = $pos === false ? $lineitem->getId().'/results' : substr_replace($lineitem->getId(), '/results', $pos, 0);
        $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $results_url);
        $reques->setAccept('application/vnd.ims.lis.v2.resultcontainer+json');
        $scores = $this->makeServiceRequest($request);

        return $scores['body'];
    }

    public function getLineItems()
    {
        if (!in_array(LtiConstants::AGS_SCOPE_LINEITEM, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }
        $lineitems = [];

        $nextPage = $this->getServiceData()['lineitems'];

        while ($nextPage) {
            $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $nextPage);
            $request->setAccept('application/vnd.ims.lti-gs.v1.contextgroupcontainer+json');
            $page = $this->makeServiceRequest($request);

            $lineitems = array_merge($lineitems, $page['body']);
            $nextPage = false;

            // If the "Next" Link is not in the request headers, we can break the loop here.
            if (!isset($page['headers']['Link'])) {
                break;
            }

            $link = $page['headers']['Link'];

            if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $link, $matches)) {
                $nextPage = $matches[1];
            }
        }

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($lineitems['body']['id'])) {
            $lineitems['body'] = [$lineitems['body']];
        }

        return $lineitems;
    }
}
