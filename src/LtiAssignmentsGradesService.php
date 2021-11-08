<?php

namespace Packback\Lti1p3;

class LtiAssignmentsGradesService extends LtiAbstractService
{
    public const CONTENTTYPE_SCORE = 'application/vnd.ims.lis.v1.score+json';
    public const CONTENTTYPE_LINEITEM = 'application/vnd.ims.lis.v2.lineitem+json';
    public const CONTENTTYPE_LINEITEMCONTAINER = 'application/vnd.ims.lis.v2.lineitemcontainer+json';
    public const CONTENTTYPE_RESULTCONTAINER = 'application/vnd.ims.lis.v2.resultcontainer+json';

    public function getScope(): array
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
        $request->setContentType(static::CONTENTTYPE_SCORE);

        return $this->makeServiceRequest($request);
    }

    public function findOrCreateLineitem(LtiLineitem $newLineItem)
    {
        $lineitems = $this->getLineItems();

        foreach ($lineitems as $lineitem) {
            if ($this->isMatchingLineitem($lineitem, $newLineItem)) {
                return new LtiLineitem($lineitem);
            }
        }

        $request = new ServiceRequest(LtiServiceConnector::METHOD_POST, $this->getServiceData()['lineitems']);
        $request->setBody($newLineItem)
            ->setContentType(static::CONTENTTYPE_LINEITEM)
            ->setAccept(static::CONTENTTYPE_LINEITEM);
        $createdLineItems = $this->makeServiceRequest($request);

        return new LtiLineitem($createdLineItems['body']);
    }

    public function getGrades(LtiLineitem $lineitem = null)
    {
        if ($lineitem !== null) {
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $resultsUrl = $lineitem->getId();
        } else {
            if (empty($this->getServiceData()['lineitem'])) {
                throw new Exception('Missing Line item');
            }
            $resultsUrl = $this->getServiceData()['lineitem'];
        }

        // Place '/results' before url params
        $pos = strpos($resultsUrl, '?');
        $resultsUrl = $pos === false ? $resultsUrl.'/results' : substr_replace($resultsUrl, '/results', $pos, 0);

        $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $resultsUrl);
        $request->setAccept(static::CONTENTTYPE_RESULTCONTAINER);
        $scores = $this->makeServiceRequest($request);

        return $scores['body'];
    }

    public function getLineItems(): array
    {
        if (!in_array(LtiConstants::AGS_SCOPE_LINEITEM, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }

        $request = new ServiceRequest(
            LtiServiceConnector::METHOD_GET,
            $this->getServiceData()['lineitems']
        );
        $request->setAccept(static::CONTENTTYPE_LINEITEMCONTAINER);

        $lineitems = $this->getAll($request);

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($lineitems['body']['id'])) {
            $lineitems['body'] = [$lineitems['body']];
        }

        return $lineitems;
    }

    private function isMatchingLineitem(array $lineitem, LtiLineitem $newLineItem): bool
    {
        return $newLineItem->getTag() == ($lineitem['tag'] ?? null) &&
            $newLineItem->getResourceId() == ($lineitem['resourceId'] ?? null) &&
            $newLineItem->getResourceLinkId() == ($lineitem['resourceLinkId'] ?? null);
    }
}
