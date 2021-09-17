<?php

namespace Packback\Lti1p3;

class LtiCourseGroupsService extends LtiAbstractService
{
    public function getScope()
    {
        return $this->getServiceData()['scope'];
    }

    public function getGroups()
    {
        $groups = [];

        $nextPage = $this->getServiceData()['context_groups_url'];

        while ($nextPage) {
            $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $nextPage);
            $request->setAccept('application/vnd.ims.lti-gs.v1.contextgroupcontainer+json');
            $page = $this->makeServiceRequest($request);

            $groups = array_merge($groups, $page['body']['groups']);

            $nextPage = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $nextPage = $matches[1];
                    break;
                }
            }
        }

        return $groups;
    }

    public function getSets()
    {
        $sets = [];

        // Sets are optional.
        if (!isset($this->getServiceData()['context_group_sets_url'])) {
            return [];
        }

        $nextPage = $this->getServiceData()['context_group_sets_url'];

        while ($nextPage) {
            $request = new ServiceRequest(LtiServiceConnector::METHOD_GET, $nextPage);
            $request->setAccept('application/vnd.ims.lti-gs.v1.contextgroupcontainer+json');
            $page = $this->makeServiceRequest($request);

            $sets = array_merge($sets, $page['body']['sets']);

            $nextPage = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $nextPage = $matches[1];
                    break;
                }
            }
        }

        return $sets;
    }

    public function getGroupsBySet()
    {
        $groups = $this->getGroups();
        $sets = $this->getSets();

        $groupsBySet = [];
        $unsetted = [];

        foreach ($sets as $key => $set) {
            $groupsBySet[$set['id']] = $set;
            $groupsBySet[$set['id']]['groups'] = [];
        }

        foreach ($groups as $key => $group) {
            if (isset($group['set_id']) && isset($groupsBySet[$group['set_id']])) {
                $groupsBySet[$group['set_id']]['groups'][$group['id']] = $group;
            } else {
                $unsetted[$group['id']] = $group;
            }
        }

        if (!empty($unsetted)) {
            $groupsBySet['none'] = [
                'name' => 'None',
                'id' => 'none',
                'groups' => $unsetted,
            ];
        }

        return $groupsBySet;
    }
}
