<?php

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");

/**
 */
class ilCryptPadPlugin extends ilRepositoryObjectPlugin
{
    const ID = "xcrp";

    // must correspond to the plugin subdirectory
    function getPluginName()
    {
        return "CryptPad";
    }

    protected function uninstallCustom() {
        // TODO: Nothing to do here.
    }
}
?>