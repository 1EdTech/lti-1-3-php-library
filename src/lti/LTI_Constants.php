<?php

namespace IMSGlobal\LTI;

class LTI_Constants
{
    CONST V1_3 = '1.3.0';

    // Required message claims
    const MESSAGE_TYPE = 'https://purl.imsglobal.org/spec/lti/claim/message_type';
    const VERSION = 'https://purl.imsglobal.org/spec/lti/claim/version';
    const DEPLOYMENT_ID = 'https://purl.imsglobal.org/spec/lti/claim/deployment_id';
    const TARGET_LINK_URI = 'https://purl.imsglobal.org/spec/lti/claim/target_link_uri';
    const RESOURCE_LINK = 'https://purl.imsglobal.org/spec/lti/claim/resource_link';
    const ROLES = 'https://purl.imsglobal.org/spec/lti/claim/roles';

    // Optional message claims
    const CONTEXT = 'https://purl.imsglobal.org/spec/lti/claim/context';
    const TOOL_PLATFORM = 'https://purl.imsglobal.org/spec/lti/claim/tool_platform';
    const ROLE_SCOPE_MENTOR = 'https://purlimsglobal.org/spec/lti/claim/role_scope_mentor';
    const LAUNCH_PRESENTATION = 'https://purl.imsglobal.org/spec/lti/claim/launch_presentation';
    const LIS = 'https://purl.imsglobal.org/spec/lti/claim/lis';
    const CUSTOM = 'https://purl.imsglobal.org/spec/lti/claim/custom';

    // LTI DL
    const DL_CONTENT_ITEMS = 'https://purl.imsglobal.org/spec/lti-dl/claim/content_items';
    const DL_DATA = 'https://purl.imsglobal.org/spec/lti-dl/claim/data';
    const DL_DEEP_LINK_SETTINGS = 'https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings';

    // LTI NRPS
    const NRPS_NAMESROLESPROVISIONINGSERVICE = 'https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice';
    const NRPS_CONTEXT_MEMBERSHIP_READ_ONLY = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';

    // LTI AGS
    const AGS_ENDPOINT = 'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint';
    const AGS_LINEITEM = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';
    const AGS_LINEITEM_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';
    const AGS_RESULT_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';
    const AGS_SCORE = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';

    // User Vocab
    const SYSTEM_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator';
    const SYSTEM_NONE = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#None';
    const SYSTEM_ACCOUNTADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#AccountAdmin';
    const SYSTEM_CREATOR = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Creator';
    const SYSTEM_SYSADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#SysAdmin';
    const SYSTEM_SYSSUPPORT = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#SysSupport';
    const SYSTEM_USER = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#User';
    const INSTITUTION_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator';
    const INSTITUTION_FACULTY = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Faculty';
    const INSTITUTION_GUEST = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Guest';
    const INSTITUTION_NONE = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#None';
    const INSTITUTION_OTHER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Other';
    const INSTITUTION_STAFF = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Staff';
    const INSTITUTION_STUDENT = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Student';
    const INSTITUTION_ALUMNI = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Alumni';
    const INSTITUTION_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Instructor';
    const INSTITUTION_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Learner';
    const INSTITUTION_MEMBER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Member';
    const INSTITUTION_MENTOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Mentor';
    const INSTITUTION_OBSERVER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Observer';
    const INSTITUTION_PROSPECTIVESTUDENT = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#ProspectiveStudent';
    const MEMBERSHIP_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Administrator';
    const MEMBERSHIP_CONTENTDEVELOPER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper';
    const MEMBERSHIP_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
    const MEMBERSHIP_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner';
    const MEMBERSHIP_MENTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor';
    const MEMBERSHIP_MANAGER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Manager';
    const MEMBERSHIP_MEMBER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Member';
    const MEMBERSHIP_OFFICER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Officer';

    // Context Vocab
    const COURSE_TEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseTemplate';
    const COURSE_OFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering';
    const COURSE_SECTION = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection';
    const COURSE_GROUP = 'http://purl.imsglobal.org/vocab/lis/v2/course#Group';
}
