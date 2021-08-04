<#1>
<?php
if (! $ilDB->tableExists('rep_robj_xcrp_data')) {
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
        'doc_link' => array(
            'type' => 'text',
            'length' => 255,
            'fixed' => false,
            'notnull' => false
        )
    );
    $ilDB->createTable("rep_robj_xcrp_data", $fields);
    $ilDB->addPrimaryKey("rep_robj_xcrp_data", array("id"));
}
?>
<#2>
<?php
if (! $ilDB->tableExists('rep_robj_xcrp_const')) {
    $fields = array(
        'id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true
        ),
        'name' => array(
            'type' => 'text',
            'length' => 10,
            'fixed' => false,
            'notnull' => false
        ),
        'value' => array(
            'type' => 'text',
            'length' => 255,
            'fixed' => false,
            'notnull' => false
        )
    );
    $ilDB->createTable("rep_robj_xcrp_const", $fields);
    $ilDB->addPrimaryKey("rep_robj_xcrp_const", array("id"));
}
if ($ilDB->tableExists('rep_robj_xcrp_const') && !$ilDB->sequenceExists('rep_robj_xcrp_const')) {
    $ilDB->createSequence('rep_robj_xcrp_const');
}

?>