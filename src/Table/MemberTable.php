<?php

declare(strict_types=1);

namespace CryptPad\Table;

use ilExAssignment;
use ilPlugin;
use ilTable2GUI;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use CryptPad\Model\Member;

class MemberTable extends ilTable2GUI
{
    /**
     * @var Member[]
     */
    private $members;
    /**
     * @var ilPlugin
     */
    protected $plugin;

    public function __construct($a_parent_obj, $plugin, $members)
    {

        parent::__construct($a_parent_obj);
        $this->plugin = $plugin;
        $this->members = $members;

        $this->setId('xcrp_robj_members');
        $this->setTitle($plugin->txt('MemberTable'));
        $this->setStyle('table', 'fullwidth');

        $this->addColumn('', '', '1%', true);
        $this->addColumn($plugin->txt('User'), 'user');
        $this->addColumn($plugin->txt('Rights'), 'rights');

        $this->addCommandButton('MemberController.saveAllMembers', $this->plugin->txt("saveAllMembers"));

        $this->addMultiItemSelectionButton(
            'action',
            [
                'save'   => $plugin->txt('save-rights'),
                'delete'   => $plugin->txt('delete-member'),
            ],
            'MemberController.multiCommand',
            $plugin->txt('submit'),
            $plugin->txt('pleaseSelect')
        );
        $this->setSelectAllCheckbox('id');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

        $this->setRowTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/CryptPad/templates/tpl.il_xcrp_member_table_row.html');
        $this->setDefaultOrderField('user');
        $this->setDefaultOrderDirection('desc');
        $this->setEnableHeader(true);

        //$this->determineOffsetAndOrder();
        $this->setLimit(10);
        $data = $this->getTableData();
        //$data = $this->sortTableData($data, $plugin);
        $this->setData($data);
    }

    public function getTableData() : array
    {
        $data = [];
        foreach ($this->members as $key => $member) {
            $user = new \ilObjUser($member->getUserId());
            $select = new \ilSelectInputGUI();
            $select->setOptions([
                "" => $this->plugin->txt("placeholder-select-rights"),
                "read" => $this->plugin->txt("read"),
                "write" =>$this->plugin->txt("write")
            ]);
            $val = $member->getRights()?: "";
            $select->setValue($val);
            $data[$key] = [
                'checkbox'              => \ilUtil::formCheckbox(false, 'id[]', $member->getId()),
                'user' => $user->getLastname() . ', ' . $user->getFirstname(),
                'userid' => $member->getId(),
                "noright" => !$member->getRights() ? "selected" : "",
                "read" => $member->getRights() === "read" ? "selected" : "",
                "write" => $member->getRights() === "write" ? "selected" : ""
            ];
        }

        return $data;
    }
}
