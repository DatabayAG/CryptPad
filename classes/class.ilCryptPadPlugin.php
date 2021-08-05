<?php

include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");

/**
 */
class ilCryptPadPlugin extends ilRepositoryObjectPlugin
{
    const ID = "xcrp";

    /** @var string */
    private const CTYPE = 'Services';
    /** @var string */
    private const CNAME = 'Repository';
    /** @var string */
    private const SLOT_ID = 'robj';
    /** @var string */
    private const PNAME = 'CryptPad';
    /** @var self */
    private static $instance = null;
    /** @var bool */
    protected static $initialized = false;
    private $server;
    /** @var \ILIAS\DI\Container */
    protected $dic;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        global $DIC;

        parent::__construct();

        $this->dic = $DIC;
    }

    /**
     * {@inheritdoc}
     */
    protected function init() : void
    {
        parent::init();
        $this->registerAutoloader();

        if (!self::$initialized) {
            self::$initialized = true;
        }
    }

    // must correspond to the plugin subdirectory
    public function getPluginName() : string
    {
        return "CryptPad";
    }

    protected function uninstallCustom() : void
    {
        // TODO: Nothing to do here.
    }


    /**
     * Registers the plugin autoloader.
     */
    public function registerAutoloader() : void
    {
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public static function getInstance() : self
    {
        return self::$instance ?? (self::$instance = ilPluginAdmin::getPluginObject(
            self::CTYPE,
            self::CNAME,
            self::SLOT_ID,
            self::PNAME
        ));
    }

    public function isAtLeastIlias6() : bool
    {
        return version_compare(ILIAS_VERSION_NUMERIC, '6.0.0', '>=');
    }
}
