<#1>
<?php
$fields = array(
    'id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ),
    'is_online' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => false
    ),
    'doc_type' => array(
        'type' => 'text',
        'length' => 10,
        'fixed' => false,
        'notnull' => false
    ),
    'doc_id' => array(
        'type' => 'text',
        'length' => 24,
        'fixed' => false,
        'notnull' => false
    )
);
//if ($ilDB->tableExists('rep_robj_xcrp_data')) {
//    $ilDB->dropTable('rep_robj_xcrp_data');
//}
$ilDB->createTable("rep_robj_xcrp_data", $fields);
$ilDB->addPrimaryKey("rep_robj_xcrp_data", array("id"));
?>
<#2>
<?php
$fields = array(
    'id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ),
    'obj_id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => false
    ),
    'user_id' => array(
        'type' => 'integer',
        'length' => 4,
        'notnull' => false
    ),
    'rights' => array(
        'type' => 'text',
        'length' => 10,
        'fixed' => false,
        'notnull' => false
    )
);
//if ($ilDB->tableExists('rep_robj_xcrp_members')) {
//    $ilDB->dropTable('rep_robj_xcrp_members');
//}
$ilDB->createTable("rep_robj_xcrp_members", $fields);
$ilDB->addPrimaryKey("rep_robj_xcrp_members", array("id"));
?>
