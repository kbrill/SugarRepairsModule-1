<?php

require_once('modules/supp_SugarRepairs/Classes/Repairs/supp_ProcessAuthorRepairs.php');

/**
 * @group support
 * @group processAuthor
 */
class suppSugarRepairsProcessAuthorRepairsTest extends Sugar_PHPUnit_Framework_TestCase
{

    protected $reportIDs = array();

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $sql_setup = array();

        // bean for disabling definition
        // testDisablePADefinition
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '38c90c70-7788-13a2-668d-513e2b8df5e1';
        $bean->new_with_id = true;
        $bean->name = 'Example Record';
        $bean->prj_status = "ACTIVE";
        $bean->save();

        // event test records
        // testSetEventDefinition
        // testRepairEventCriteria (false positive test)
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '9ff025b6-e576-11e5-9261-fe49746prjid';
        $bean->new_with_id = true;
        $bean->name = 'Test Working Record for Start Event';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_event_definition` (`id`,`deleted`,`prj_id`,`evn_status`,`evn_type`,`evn_module`,`evn_criteria`)
            VALUES ('9ff025b6-e576-11e5-9261-fe497468edid',0,'9ff025b6-e576-11e5-9261-fe49746prjid','ACTIVE','START','Accounts','[{\"expType\":\"MODULE\",\"expSubtype\":\"DropDown\",\"expLabel\":\"Industry is equal to Apparel\",\"expValue\":\"Apparel\",\"expOperator\":\"equals\",\"expModule\":\"Accounts\",\"expField\":\"industry\"},{\"expType\":\"LOGIC\",\"expLabel\":\"OR\",\"expValue\":\"OR\"},{\"expType\":\"USER_ROLE\",\"expLabel\":\"Supervisor has not role Administrator\",\"expValue\":\"is_admin\",\"expOperator\":\"not_equals\",\"expField\":\"supervisor\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('9ff025b6-e576-11e5-9261-fe497468afid',0,'9ff025b6-e576-11e5-9261-fe49746prjid','9ff025b6-e576-11e5-9261-fe497468edid');
        ";

        // testRepairEventCriteria
        // dropdown missing a value in event criteria
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '46d69d50-e58c-11e5-9261-fe49746prjid';
        $bean->new_with_id = true;
        $bean->name = 'Test DD Field Missing Value';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_event_definition` (`id`,`deleted`,`prj_id`,`evn_status`,`evn_type`,`evn_module`,`evn_criteria`)
            VALUES ('38047c8e-e58c-11e5-9261-fe497468edid',0,'46d69d50-e58c-11e5-9261-fe49746prjid','ACTIVE','START','Accounts','[{\"expType\":\"MODULE\",\"expSubtype\":\"DropDown\",\"expLabel\":\"Industry is equal to nonexistantvalue56\",\"expValue\":\"nonexistantvalue56\",\"expOperator\":\"equals\",\"expModule\":\"Accounts\",\"expField\":\"industry\"},{\"expType\":\"LOGIC\",\"expLabel\":\"OR\",\"expValue\":\"OR\"},{\"expType\":\"USER_ROLE\",\"expLabel\":\"Supervisor has not role Administrator\",\"expValue\":\"is_admin\",\"expOperator\":\"not_equals\",\"expField\":\"supervisor\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('8236146e-e58e-11e5-9261-fe497468afid',0,'46d69d50-e58c-11e5-9261-fe49746prjid','38047c8e-e58c-11e5-9261-fe497468edid');
        ";

        // self related dropdown missing a value in event criteria
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '46d69d51-e58c-11e5-9261-fe49746prjid';
        $bean->new_with_id = true;
        $bean->name = 'Test Self Related DD Field Missing Value';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_event_definition` (`id`,`deleted`,`prj_id`,`evn_status`,`evn_type`,`evn_module`,`evn_criteria`) 
            VALUES ('3c8704ca-e58c-11e5-9261-fe497468edid',0,'46d69d51-e58c-11e5-9261-fe49746prjid','ACTIVE','START','Accounts','[{\"expType\":\"MODULE\",\"expSubtype\":\"DropDown\",\"expLabel\":\"Industry is equal to nonexistantvalue56\",\"expValue\":\"nonexistantvalue56\",\"expOperator\":\"equals\",\"expModule\":\"member_of\",\"expField\":\"industry\"},{\"expType\":\"LOGIC\",\"expLabel\":\"OR\",\"expValue\":\"OR\"},{\"expType\":\"USER_ROLE\",\"expLabel\":\"Supervisor has not role Administrator\",\"expValue\":\"is_admin\",\"expOperator\":\"not_equals\",\"expField\":\"supervisor\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('87a549d8-e58e-11e5-9261-fe497468afid',0,'46d69d51-e58c-11e5-9261-fe49746prjid','3c8704ca-e58c-11e5-9261-fe497468edid');
        ";

        // related dropdown missing a value in event criteria
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '46d69d52-e58c-11e5-9261-fe49746prjid';
        $bean->new_with_id = true;
        $bean->name = 'Test Related DD Field Missing Value';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_event_definition` (`id`,`deleted`,`prj_id`,`evn_status`,`evn_type`,`evn_module`,`evn_criteria`) 
            VALUES ('4290f060-e58c-11e5-9261-fe497468edid',0,'46d69d52-e58c-11e5-9261-fe49746prjid','ACTIVE','START','Accounts','[{\"expType\":\"MODULE\",\"expSubtype\":\"DropDown\",\"expLabel\":\"Lead Source is equal to nonexistantvalue56\",\"expValue\":\"nonexistantvalue56\",\"expOperator\":\"equals\",\"expModule\":\"contacts\",\"expField\":\"lead_source\"},{\"expType\":\"LOGIC\",\"expLabel\":\"OR\",\"expValue\":\"OR\"},{\"expType\":\"USER_ROLE\",\"expLabel\":\"Supervisor has not role Administrator\",\"expValue\":\"is_admin\",\"expOperator\":\"not_equals\",\"expField\":\"supervisor\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('8b0b2fde-e58e-11e5-9261-fe497468afid',0,'46d69d52-e58c-11e5-9261-fe49746prjid','4290f060-e58c-11e5-9261-fe497468edid');
        ";

        // non-existant field in event criteria
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '46d69d53-e58c-11e5-9261-fe49746prjid';
        $bean->new_with_id = true;
        $bean->name = 'Test Field doesnt Exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_event_definition` (`id`,`deleted`,`prj_id`,`evn_status`,`evn_type`,`evn_module`,`evn_criteria`) 
            VALUES ('46d69d50-e58c-11e5-9261-fe497468edid',0,'46d69d53-e58c-11e5-9261-fe49746prjid','ACTIVE','START','Accounts','[{\"expType\":\"MODULE\",\"expSubtype\":\"DropDown\",\"expLabel\":\"nonexistantfield56 is equal to 3\",\"expValue\":\"3\",\"expOperator\":\"equals\",\"expModule\":\"Accounts\",\"expField\":\"nonexistantfield56_c\"},{\"expType\":\"LOGIC\",\"expLabel\":\"OR\",\"expValue\":\"OR\"},{\"expType\":\"USER_ROLE\",\"expLabel\":\"Supervisor has not role Administrator\",\"expValue\":\"is_admin\",\"expOperator\":\"not_equals\",\"expField\":\"supervisor\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('8e736524-e58e-11e5-9261-fe497468afid',0,'46d69d53-e58c-11e5-9261-fe49746prjid','46d69d50-e58c-11e5-9261-fe497468edid');
        ";

        // action test records
        // testSetActionDefinition
        // testRepairActivities (false positive test)
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '06082fac-ebca-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'Test Working Record for Set Field Action';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('f6c394c0-eb0e-11e5-b792-460e741c2f98',0,'Change Field','Accounts','[{\"name\":\"Industry\",\"field\":\"industry\",\"value\":\"Apparel\",\"type\":\"DropDown\"},{\"name\":\"Type\",\"field\":\"account_type\",\"value\":\"Analyst\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"test\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('f6c394c0-eb0e-11e5-b792-460e741c2f98','Change Field',0,'06082fac-ebca-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('647e62a2-ec6c-11e5-a19f-342d44d047f0',0,'06082fac-ebca-11e5-a19f-342d44d047f0','f6c394c0-eb0e-11e5-b792-460e741c2f98');
        ";

        // testRepairActivities

        // required dropdown field on activity form
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '4a40ed2a-ebcb-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'required dropdown field on activity form';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_required_fields`)
            VALUES ('df2b96fe-ebcd-11e5-a19f-342d44d047f0',0,'Required Field Form','WyJpbmR1c3RyeSIsInJhdGluZyIsIm5vbmV4aXN0YW50ZmllbGQ1Nl9jIl0=');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('df2b96fe-ebcd-11e5-a19f-342d44d047f0','Required Field Form',0,'4a40ed2a-ebcb-11e5-a19f-342d44d047f0','USERTASK','');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('501d46cc-ec6f-11e5-a19f-342d44d047f0',0,'4a40ed2a-ebcb-11e5-a19f-342d44d047f0','df2b96fe-ebcd-11e5-a19f-342d44d047f0');
        ";

        // add related record action with dropdown field that doesnt exist
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '06877022-ebcb-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'add related record action with dropdown field that doesnt exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('ff2b4dee-ebca-11e5-a19f-342d44d047f0',0,'Add Related Record','leads','[{\"name\":\"Last Name\",\"field\":\"last_name\",\"value\":\"testerson\",\"type\":\"TextField\"},{\"name\":\"Assigned to\",\"field\":\"assigned_user_id\",\"value\":\"currentuser\",\"type\":\"user\",\"label\":\"Current user\"},{\"name\":\"Fake Field\",\"field\":\"nonexistantfield56_c\",\"value\":\"\",\"type\":\"DropDown\"},{\"name\":\"Status\",\"field\":\"status\",\"value\":\"Assigned\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('ff2b4dee-ebca-11e5-a19f-342d44d047f0','Add Related Record',0,'06877022-ebcb-11e5-a19f-342d44d047f0','SCRIPTTASK','ADD_RELATED_RECORD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('b159c37a-ec6f-11e5-a19f-342d44d047f0',0,'06877022-ebcb-11e5-a19f-342d44d047f0','ff2b4dee-ebca-11e5-a19f-342d44d047f0');
        ";

        // add related record action with dropdown field value not in list
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '68dd07cc-ec71-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'add related record action with dropdown field value not in list';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('ac2e2510-ec71-11e5-a19f-342d44d047f0',0,'Add Related Record','leads','[{\"name\":\"Last Name\",\"field\":\"last_name\",\"value\":\"testerson\",\"type\":\"TextField\"},{\"name\":\"Assigned to\",\"field\":\"assigned_user_id\",\"value\":\"currentuser\",\"type\":\"user\",\"label\":\"Current user\"},{\"name\":\"Lead Source\",\"field\":\"lead_source\",\"value\":\"nonexistantvalue56\",\"type\":\"DropDown\"},{\"name\":\"Status\",\"field\":\"status\",\"value\":\"Assigned\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('ac2e2510-ec71-11e5-a19f-342d44d047f0','Add Related Record',0,'68dd07cc-ec71-11e5-a19f-342d44d047f0','SCRIPTTASK','ADD_RELATED_RECORD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('bbdffe84-ec71-11e5-a19f-342d44d047f0',0,'68dd07cc-ec71-11e5-a19f-342d44d047f0','ac2e2510-ec71-11e5-a19f-342d44d047f0');
        ";

        // add self-related record action with dropdown field that doesnt exist
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = 'ce60db0a-ec71-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'add self-related record action with dropdown field that doesnt exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('76106654-ec72-11e5-a19f-342d44d047f0',0,'Add Related Record','members','[{\"name\":\"Assigned to\",\"field\":\"assigned_user_id\",\"value\":\"currentuser\",\"type\":\"user\",\"label\":\"Current user\"},{\"name\":\"Fake Field\",\"field\":\"nonexistantfield56_c\",\"value\":\"Banking\",\"type\":\"DropDown\"},{\"name\":\"Name\",\"field\":\"name\",\"value\":\"test\",\"type\":\"Name\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('76106654-ec72-11e5-a19f-342d44d047f0','Add Related Record',0,'ce60db0a-ec71-11e5-a19f-342d44d047f0','SCRIPTTASK','ADD_RELATED_RECORD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('81828b52-ec72-11e5-a19f-342d44d047f0',0,'ce60db0a-ec71-11e5-a19f-342d44d047f0','76106654-ec72-11e5-a19f-342d44d047f0');
        ";

        // add self-related record action with dropdown field value not in list
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = 'ba6b467a-ec72-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'add self-related record action with dropdown field value not in list';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('ca9cdf4a-ec72-11e5-a19f-342d44d047f0',0,'Add Related Record','members','[{\"name\":\"Assigned to\",\"field\":\"assigned_user_id\",\"value\":\"currentuser\",\"type\":\"user\",\"label\":\"Current user\"},{\"name\":\"Industry\",\"field\":\"industry\",\"value\":\"nonexistantvalue56\",\"type\":\"DropDown\"},{\"name\":\"Name\",\"field\":\"name\",\"value\":\"test\",\"type\":\"Name\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('ca9cdf4a-ec72-11e5-a19f-342d44d047f0','Add Related Record',0,'ba6b467a-ec72-11e5-a19f-342d44d047f0','SCRIPTTASK','ADD_RELATED_RECORD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('d46a161e-ec72-11e5-a19f-342d44d047f0',0,'ba6b467a-ec72-11e5-a19f-342d44d047f0','ca9cdf4a-ec72-11e5-a19f-342d44d047f0');
        ";

        // change field in current module with dropdown field that doesnt exist
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '9dd09126-ec88-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in current module with dropdown field that doesnt exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('ea7732fa-ec88-11e5-a19f-342d44d047f0',0,'Add Related Record','Accounts','[{\"name\":\"Fake Field\",\"field\":\"nonexistantfield56_c\",\"value\":\"Apparel\",\"type\":\"DropDown\"},{\"name\":\"Type\",\"field\":\"account_type\",\"value\":\"Analyst\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"test\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('ea7732fa-ec88-11e5-a19f-342d44d047f0','Add Related Record',0,'9dd09126-ec88-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('f0816562-ec88-11e5-a19f-342d44d047f0',0,'9dd09126-ec88-11e5-a19f-342d44d047f0','ea7732fa-ec88-11e5-a19f-342d44d047f0');
        ";

        // change field in current module with dropdown field value not in list
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '04509716-ec89-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in current module with dropdown field value not in list';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('24df04c2-ec89-11e5-a19f-342d44d047f0',0,'Add Related Record','Accounts','[{\"name\":\"Industry\",\"field\":\"industry\",\"value\":\"nonexistantvalue56\",\"type\":\"DropDown\"},{\"name\":\"Type\",\"field\":\"account_type\",\"value\":\"Analyst\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"test\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('24df04c2-ec89-11e5-a19f-342d44d047f0','Add Related Record',0,'04509716-ec89-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('2c9b2e3e-ec89-11e5-a19f-342d44d047f0',0,'04509716-ec89-11e5-a19f-342d44d047f0','24df04c2-ec89-11e5-a19f-342d44d047f0');
        ";

        // change field in related module with dropdown field that doesnt exist
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '64dc5b98-ec88-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in related module with dropdown field that doesnt exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('73d6b15c-ec88-11e5-a19f-342d44d047f0',0,'Add Related Record','campaign_accounts','[{\"name\":\"Description \",\"field\":\"content\",\"value\":\"test\",\"type\":\"TextArea\"},{\"name\":\"Fake Field\",\"field\":\"nonexistantfield56_c\",\"value\":\"Inactive\",\"type\":\"DropDown\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('73d6b15c-ec88-11e5-a19f-342d44d047f0','Add Related Record',0,'64dc5b98-ec88-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('7a2f4c80-ec88-11e5-a19f-342d44d047f0',0,'64dc5b98-ec88-11e5-a19f-342d44d047f0','73d6b15c-ec88-11e5-a19f-342d44d047f0');
        ";

        // change field in related module with dropdown field value not in list
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = '4a3ec220-ec89-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in related module with dropdown field value not in list';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('53999002-ec89-11e5-a19f-342d44d047f0',0,'Add Related Record','campaign_accounts','[{\"name\":\"Description \",\"field\":\"content\",\"value\":\"test\",\"type\":\"TextArea\"},{\"name\":\"Status\",\"field\":\"status\",\"value\":\"nonexistantvalue56\",\"type\":\"DropDown\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('53999002-ec89-11e5-a19f-342d44d047f0','Add Related Record',0,'4a3ec220-ec89-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('7d4f167e-ec89-11e5-a19f-342d44d047f0',0,'4a3ec220-ec89-11e5-a19f-342d44d047f0','53999002-ec89-11e5-a19f-342d44d047f0');
        ";

        // change field in self related module with dropdown field that doesnt exist
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = 'cc88007a-ec89-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in self related module with dropdown field that doesnt exist';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('589b3654-ec8a-11e5-a19f-342d44d047f0',0,'Add Related Record','member_of','[{\"name\":\"Fake Field\",\"field\":\"nonexistantfield56_c\",\"value\":\"Apparel\",\"type\":\"DropDown\"},{\"name\":\"Type\",\"field\":\"account_type\",\"value\":\"Analyst\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"test\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('589b3654-ec8a-11e5-a19f-342d44d047f0','Add Related Record',0,'cc88007a-ec89-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('738f31f4-ec8a-11e5-a19f-342d44d047f0',0,'cc88007a-ec89-11e5-a19f-342d44d047f0','589b3654-ec8a-11e5-a19f-342d44d047f0');
        ";

        // change field in self related module with dropdown field value not in list
        $bean = BeanFactory::newBean("pmse_Project");
        $bean->id = 'b8ea5314-ec8a-11e5-a19f-342d44d047f0';
        $bean->new_with_id = true;
        $bean->name = 'change field in self related module with dropdown field value not in list';
        $bean->prj_status = "ACTIVE";
        $bean->prj_module = "Accounts";
        $bean->save();

        $sql_setup[] = "
            INSERT INTO `pmse_bpm_activity_definition` (`id`,`deleted`,`name`,`act_field_module`,`act_fields`)
            VALUES ('bf8bef84-ec8a-11e5-a19f-342d44d047f0',0,'Add Related Record','member_of','[{\"name\":\"Industry\",\"field\":\"industry\",\"value\":\"nonexistantvalue56\",\"type\":\"DropDown\"},{\"name\":\"Type\",\"field\":\"account_type\",\"value\":\"Analyst\",\"type\":\"DropDown\"},{\"name\":\"Website\",\"field\":\"website\",\"value\":\"test\",\"type\":\"URL\"}]');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_activity` (`id`,`name`,`deleted`,`prj_id`,`act_task_type`,`act_script_type`)
            VALUES ('bf8bef84-ec8a-11e5-a19f-342d44d047f0','Add Related Record',0,'b8ea5314-ec8a-11e5-a19f-342d44d047f0','SCRIPTTASK','CHANGE_FIELD');
        ";
        $sql_setup[] = "
            INSERT INTO `pmse_bpmn_flow` (`id`,`deleted`,`prj_id`,`flo_element_origin`)
            VALUES ('c5dc7228-ec8a-11e5-a19f-342d44d047f0',0,'b8ea5314-ec8a-11e5-a19f-342d44d047f0','bf8bef84-ec8a-11e5-a19f-342d44d047f0');
        ";

        // execute sql statements
        foreach ($sql_setup as $q_setup) {
            $res = $GLOBALS['db']->query($q_setup);
        }

    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        $sql_teardown = array();

        $sql_teardown[] = "
            DELETE FROM pmse_bpm_event_definition
            WHERE id in (
                '9ff025b6-e576-11e5-9261-fe497468edid',
                '38047c8e-e58c-11e5-9261-fe497468edid',
                '3c8704ca-e58c-11e5-9261-fe497468edid',
                '4290f060-e58c-11e5-9261-fe497468edid',
                '46d69d50-e58c-11e5-9261-fe497468edid'
            )
        ";
        $sql_teardown[] = "
            DELETE FROM pmse_project
            WHERE id in (
                '38c90c70-7788-13a2-668d-513e2b8df5e1',
                '9ff025b6-e576-11e5-9261-fe49746prjid',
                '46d69d50-e58c-11e5-9261-fe49746prjid',
                '46d69d51-e58c-11e5-9261-fe49746prjid',
                '46d69d52-e58c-11e5-9261-fe49746prjid',
                '46d69d53-e58c-11e5-9261-fe49746prjid',
                '06082fac-ebca-11e5-a19f-342d44d047f0',
                '4a40ed2a-ebcb-11e5-a19f-342d44d047f0',
                '06877022-ebcb-11e5-a19f-342d44d047f0',
                '68dd07cc-ec71-11e5-a19f-342d44d047f0',
                'ce60db0a-ec71-11e5-a19f-342d44d047f0',
                'ba6b467a-ec72-11e5-a19f-342d44d047f0',
                '9dd09126-ec88-11e5-a19f-342d44d047f0',
                '04509716-ec89-11e5-a19f-342d44d047f0',
                '64dc5b98-ec88-11e5-a19f-342d44d047f0',
                '4a3ec220-ec89-11e5-a19f-342d44d047f0',
                'cc88007a-ec89-11e5-a19f-342d44d047f0',
                'b8ea5314-ec8a-11e5-a19f-342d44d047f0'
            )
        ";
        $sql_teardown[] = "
            DELETE FROM pmse_bpmn_flow
            WHERE id in (
                '9ff025b6-e576-11e5-9261-fe497468afid',
                '8236146e-e58e-11e5-9261-fe497468afid',
                '87a549d8-e58e-11e5-9261-fe497468afid',
                '8b0b2fde-e58e-11e5-9261-fe497468afid',
                '8e736524-e58e-11e5-9261-fe497468afid',
                '647e62a2-ec6c-11e5-a19f-342d44d047f0',
                '501d46cc-ec6f-11e5-a19f-342d44d047f0',
                'b159c37a-ec6f-11e5-a19f-342d44d047f0',
                'bbdffe84-ec71-11e5-a19f-342d44d047f0',
                '81828b52-ec72-11e5-a19f-342d44d047f0',
                'd46a161e-ec72-11e5-a19f-342d44d047f0',
                'f0816562-ec88-11e5-a19f-342d44d047f0',
                '2c9b2e3e-ec89-11e5-a19f-342d44d047f0',
                '7a2f4c80-ec88-11e5-a19f-342d44d047f0',
                '7d4f167e-ec89-11e5-a19f-342d44d047f0',
                '738f31f4-ec8a-11e5-a19f-342d44d047f0',
                'c5dc7228-ec8a-11e5-a19f-342d44d047f0'
            )
        ";

        $sql_teardown[] = "
            DELETE FROM pmse_bpm_activity_definition
            WHERE id in (
                'f6c394c0-eb0e-11e5-b792-460e741c2f98',
                'df2b96fe-ebcd-11e5-a19f-342d44d047f0',
                'ff2b4dee-ebca-11e5-a19f-342d44d047f0',
                'ac2e2510-ec71-11e5-a19f-342d44d047f0',
                '76106654-ec72-11e5-a19f-342d44d047f0',
                'ca9cdf4a-ec72-11e5-a19f-342d44d047f0',
                'ea7732fa-ec88-11e5-a19f-342d44d047f0',
                '24df04c2-ec89-11e5-a19f-342d44d047f0',
                '73d6b15c-ec88-11e5-a19f-342d44d047f0',
                '53999002-ec89-11e5-a19f-342d44d047f0',
                '589b3654-ec8a-11e5-a19f-342d44d047f0',
                'bf8bef84-ec8a-11e5-a19f-342d44d047f0'
            )
        ";

        $sql_teardown[] = "
            DELETE FROM pmse_bpmn_activity
            WHERE id in (
                'f6c394c0-eb0e-11e5-b792-460e741c2f98',
                'df2b96fe-ebcd-11e5-a19f-342d44d047f0',
                'ff2b4dee-ebca-11e5-a19f-342d44d047f0',
                'ac2e2510-ec71-11e5-a19f-342d44d047f0',
                '76106654-ec72-11e5-a19f-342d44d047f0',
                'ca9cdf4a-ec72-11e5-a19f-342d44d047f0',
                'ea7732fa-ec88-11e5-a19f-342d44d047f0',
                '24df04c2-ec89-11e5-a19f-342d44d047f0',
                '73d6b15c-ec88-11e5-a19f-342d44d047f0',
                '53999002-ec89-11e5-a19f-342d44d047f0',
                '589b3654-ec8a-11e5-a19f-342d44d047f0',
                'bf8bef84-ec8a-11e5-a19f-342d44d047f0'
            )
        ";

        foreach ($sql_teardown as $q_teardown) {
            $res = $GLOBALS['db']->query($q_teardown);
        }
    }

    /**
     * Test for setting the new event definition
     * @covers supp_ProcessAuthorRepairs::setEventDefinition
     */
    public function testSetEventDefinition()
    {
        $eventId = "9ff025b6-e576-11e5-9261-fe497468edid";
        $new_evn_criteria = '[{"expType":"MODULE","expSubtype":"DropDown","expLabel":"Industry is equal to "Other"","expValue":"Other","expOperator":"equals","expModule":"Accounts","expField":"industry"}]';

        $supp_ProcessAuthorRepairsTest = new supp_ProcessAuthorRepairs();
        $supp_ProcessAuthorRepairsTest->setTesting(false);
        $results = $supp_ProcessAuthorRepairsTest->setEventDefinition($eventId, $new_evn_criteria);

        // should return true
        $this->assertTrue($results);

        $sql = "
            SELECT evn_criteria 
            FROM pmse_bpm_event_definition
            WHERE id = '$eventId'
        ";
        $returnedCriteria = html_entity_decode($GLOBALS['db']->getOne($sql));

        // should return updated criteria
        $this->assertEquals($new_evn_criteria, $returnedCriteria);
    }

    /**
     * Test for setting the new action definition
     * @covers supp_ProcessAuthorRepairs::setActionDefinition
     */
    public function testSetActionDefinition()
    {
        $actionId = "f6c394c0-eb0e-11e5-b792-460e741c2f98";
        $new_action_fields = '[{"name":"Industry","field":"industry","value":"Apparel","type":"DropDown"},{"name":"Website","field":"website","value":"test","type":"URL"}]';

        $supp_ProcessAuthorRepairsTest = new supp_ProcessAuthorRepairs();
        $supp_ProcessAuthorRepairsTest->setTesting(false);
        $results = $supp_ProcessAuthorRepairsTest->setActionDefinition($actionId, $new_action_fields);

        // should return true
        $this->assertTrue($results);

        $sql = "
            SELECT act_fields 
            FROM pmse_bpm_activity_definition
            WHERE id = '$actionId'
        ";
        $returnedCriteria = html_entity_decode($GLOBALS['db']->getOne($sql));

        // should return updated criteria
        $this->assertEquals($new_action_fields, $returnedCriteria);
    }

    /**
     * Test for disabling a process author definition
     * @covers supp_Repairs::disablePADefinition
     */
    public function testDisablePADefinition()
    {
        $supp_ProcessAuthorRepairsTest = new supp_ProcessAuthorRepairs();
        $supp_ProcessAuthorRepairsTest->setTesting(false);
        $supp_ProcessAuthorRepairsTest->disablePADefinition("38c90c70-7788-13a2-668d-513e2b8df5e1");

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "38c90c70-7788-13a2-668d-513e2b8df5e1");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
    }

    /**
     * Test for fixing start criteria
     * @covers supp_ProcessAuthorRepairs::repairEventCriteria
     */
    public function testRepairEventCriteria()
    {
        $supp_ProcessAuthorRepairsTest = new supp_ProcessAuthorRepairs();
        $supp_ProcessAuthorRepairsTest->setTesting(false);
        $supp_ProcessAuthorRepairsTest->repairEventCriteria();

        // 4 broken records should be issues
        $this->assertGreaterThanOrEqual(4, count($supp_ProcessAuthorRepairsTest->foundIssues));

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "46d69d50-e58c-11e5-9261-fe49746prjid");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "46d69d51-e58c-11e5-9261-fe49746prjid");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "46d69d52-e58c-11e5-9261-fe49746prjid");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "46d69d53-e58c-11e5-9261-fe49746prjid");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "9ff025b6-e576-11e5-9261-fe49746prjid");
        $this->assertEquals("ACTIVE", $paDefinition->prj_status);
    }

    /**
     * Test for fixing action fields
     * @covers supp_ProcessAuthorRepairs::repairActivities
     */
    public function testRepairActivities()
    {
        $supp_ProcessAuthorRepairsTest = new supp_ProcessAuthorRepairs();
        $supp_ProcessAuthorRepairsTest->setTesting(false);
        $supp_ProcessAuthorRepairsTest->repairActivities();

        // 11 broken records should be issues
        $this->assertGreaterThanOrEqual(11, count($supp_ProcessAuthorRepairsTest->foundIssues));

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "4a40ed2a-ebcb-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "06877022-ebcb-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "68dd07cc-ec71-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "ce60db0a-ec71-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "ba6b467a-ec72-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "9dd09126-ec88-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "04509716-ec89-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "64dc5b98-ec88-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "4a3ec220-ec89-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "cc88007a-ec89-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);
        
        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "b8ea5314-ec8a-11e5-a19f-342d44d047f0");
        $this->assertEquals("INACTIVE", $paDefinition->prj_status);

        $paDefinition = BeanFactory::retrieveBean('pmse_Project', "06082fac-ebca-11e5-a19f-342d44d047f0");
        $this->assertEquals("ACTIVE", $paDefinition->prj_status);
    }
}
