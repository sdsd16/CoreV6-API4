<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//                        2017-2021

namespace TheNote\core;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\DaylightSensor;
use pocketmine\color\Color;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\data\bedrock\LegacyBlockIdToStringIdMap;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\serializer\PacketBatch;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Config;

use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\DustParticle;
use TheNote\core\blocks\BlockManager;
use TheNote\core\command\CreditsCommand;
use TheNote\core\command\SetstatstextCommand;
use TheNote\core\command\WorldCommand;
use TheNote\core\events\BlocketRecipes;
use TheNote\core\invmenu\InvMenu;
use TheNote\core\invmenu\type\CraftingTableInvMenuType;
use TheNote\core\server\FFAArena;
use TheNote\core\server\generators\ender\EnderGenerator;
use TheNote\core\server\generators\nether\NetherGenerator;
use TheNote\core\server\generators\normal\NormalGenerator;
use TheNote\core\server\generators\void\VoidGenerator;
use TheNote\core\server\structure\StructureManager;
use TheNote\core\session\SessionManager;
use TheNote\core\command\EnderInvSeeCommand;
use TheNote\core\command\InvSeeCommand;
use TheNote\core\command\ItemIDCommand;
use TheNote\core\command\RankShopCommand;
use TheNote\core\command\SeePermsCommand;

use TheNote\core\inventory\BeaconInventory;
use TheNote\core\invmenu\InvMenuHandler;
use TheNote\core\task\ChunkModificationTask;

//Command
use TheNote\core\command\AFKCommand;
use TheNote\core\command\BurnCommand;
use TheNote\core\command\DayCommand;
use TheNote\core\command\DelWarpCommand;
use TheNote\core\command\GiveMoneyCommand;
use TheNote\core\command\HubCommand;
use TheNote\core\command\KickCommand;
use TheNote\core\command\ListWarpCommand;
use TheNote\core\command\MyMoneyCommand;
use TheNote\core\command\NickCommand;
use TheNote\core\command\NightCommand;
use TheNote\core\command\FeedCommand;
use TheNote\core\command\HealCommand;
use TheNote\core\command\NukeCommand;
use TheNote\core\command\PayMoneyCommand;
use TheNote\core\command\SetMoneyCommand;
use TheNote\core\command\SetWarpCommand;
use TheNote\core\command\SizeCommand;
use TheNote\core\command\SurvivalCommand;
use TheNote\core\command\KreativCommand;
use TheNote\core\command\AbenteuerCommand;
use TheNote\core\command\ChatClearCommand;
use TheNote\core\command\TopMoneyCommand;
use TheNote\core\command\TpaacceptCommand;
use TheNote\core\command\TpaCommand;
use TheNote\core\command\TpadenyCommand;
use TheNote\core\command\WarpCommand;
use TheNote\core\command\ZuschauerCommand;
use TheNote\core\command\FlyCommand;
use TheNote\core\command\VanishCommand;
use TheNote\core\command\BoosterCommand;
use TheNote\core\command\NightVisionCommand;
use TheNote\core\command\PayallCommand;
use TheNote\core\command\EnderChestCommand;
use TheNote\core\command\RepairCommand;
use TheNote\core\command\RenameCommand;
use TheNote\core\command\ClanCommand;
use TheNote\core\command\ClearCommand;
use TheNote\core\command\FriendCommand;
use TheNote\core\command\ClearlaggCommand;
use TheNote\core\command\UnnickCommand;
use TheNote\core\command\SignCommand;
use TheNote\core\command\TellCommand;
use TheNote\core\command\ReplyCommand;
use TheNote\core\command\PerkCommand;
use TheNote\core\command\PerkShopCommand;
use TheNote\core\command\PosCommand;
use TheNote\core\command\StatsCommand;
use TheNote\core\command\ServerStatsCommand;
use TheNote\core\command\KitCommand;
use TheNote\core\command\SetHomeCommand;
use TheNote\core\command\DelHomeCommand;
use TheNote\core\command\ListHomeCommand;
use TheNote\core\command\HomeCommand;
use TheNote\core\command\MyCoinsCommand;
use TheNote\core\command\PayCoinsCommand;
use TheNote\core\command\UserdataCommand;
use TheNote\core\command\HeiratenCommand;
use TheNote\core\command\TpallCommand;
use TheNote\core\command\FakeCommand;
use TheNote\core\command\GruppeCommand;
use TheNote\core\command\NoDMCommand;
use TheNote\core\command\AnimationCommand;
use TheNote\core\command\CraftCommand;
use TheNote\core\command\ErfolgCommand;
use TheNote\core\command\KickallCommand;
use TheNote\core\command\VoteCommand;
use TheNote\core\command\GiveCoinsCommand;
use TheNote\core\command\SuperVanishCommand;
use TheNote\core\command\TreeCommand;
use TheNote\core\command\ServermuteCommand;
use TheNote\core\command\BanCommand;
use TheNote\core\command\BanIDListCommand;
use TheNote\core\command\BanListCommand;
use TheNote\core\command\UnbanCommand;
use TheNote\core\command\AdminItemsCommand;
use TheNote\core\command\SudoCommand;
use TheNote\core\command\SeeMoneyCommand;
use TheNote\core\command\TakeMoneyCommand;

//Server
use TheNote\core\events\RegelEvent;
use TheNote\core\formapi\SimpleForm;
use TheNote\core\server\RegelServer;
use TheNote\core\server\Version;

//Events
use TheNote\core\events\ColorChat;
use TheNote\core\events\Particle;
use TheNote\core\events\DeathMessages;
use TheNote\core\events\BanEventListener;
use TheNote\core\events\AdminItemsEvents;
use TheNote\core\events\AntiXrayEvent;
use TheNote\core\events\EconomySell;
use TheNote\core\events\EconomyShop;

//listener
use TheNote\core\listener\UserdataListener;
use TheNote\core\listener\HeiratsListener;
use TheNote\core\listener\BackListener;
use TheNote\core\listener\CollisionsListener;
use TheNote\core\listener\GroupListener;

//Emotes
use TheNote\core\emotes\burb;
use TheNote\core\emotes\geil;
use TheNote\core\emotes\happy;
use TheNote\core\emotes\sauer;
use TheNote\core\emotes\traurig;

//Server
use TheNote\core\server\RestartServer;
use TheNote\core\server\Stats;
use TheNote\core\server\PlotBewertung;
use TheNote\core\server\Rezept;

//Anderes
use TheNote\core\item\ItemManager;
use TheNote\core\blocks\PowerBlock;
use TheNote\core\server\LiftSystem\BlockBreakListener;
use TheNote\core\server\LiftSystem\BlockPlaceListener;
use TheNote\core\server\LiftSystem\PlayerInteractListener;
use TheNote\core\server\LiftSystem\PlayerJumpListener;
use TheNote\core\server\LiftSystem\PlayerToggleSneakListener;

//Task
use TheNote\core\task\ScoreboardTask;
use TheNote\core\task\StatstextTask;
use TheNote\core\task\CallbackTask;
use TheNote\core\task\RTask;
use TheNote\core\task\PingTask;
use TheNote\core\utils\GlobalBlockPalette;
use TheNote\core\utils\ScheduledBlockUpdateLoader;

use const pocketmine\RESOURCE_PATH;

class Main extends PluginBase implements Listener
{

    //PluginVersion
    public static $version = "6.0.8 ALPHA";
    public static $protokoll = "475";
    public static $mcpeversion = "1.18.0";
    public static $dateversion = "05.12.2021";
    public static $plname = "CoreV6";
    public static $configversion = "6.0.8";

    private $clicks;
    private $message = "";
    private $items = [];
    private $debug = false;
    private $default;
    private $padding;
    private $universalMute = false;
    private $min, $max;
    private $multibyte;
    public $queue = [];
    public $anni = 1;
    public $isAdd = [];
    public $myplot;
    public $config;
    public $economyapi;
    protected static $inventories = [];
    public $invite = [];

    public $ores = [14, 15, 21, 22, 41, 42, 56, 57, 73, 129, 133, 152];
    public $cooldown = [];
    public $interactCooldown = [];

    public static $netherLevel;
    public static $overworldLevel;
    public static $endLevel;


    //Configs
    public static $clanfile = "Cloud/players/Clans/";
    public static $freundefile = "Cloud/players/Freunde/";
    public static $gruppefile = "Cloud/players/Gruppe/";
    public static $heifile = "Cloud/players/Heiraten/";
    public static $homefile = "Cloud/players/Homes/";
    public static $logdatafile = "Cloud/players/Logdata/";
    public static $statsfile = "Cloud/players/Stats/";
    public static $userfile = "Cloud/players/User/";
    public static $backfile = "Cloud/players/";
    public static $cloud = "Cloud/";
    public static $setup = "Setup/";
    public static $lang = "Language/";

    //Anderes
    public static $instance;
    public static $restart;
    public $players = [];
    public $bank;
    public $win = null;
    public $price = null;
    public $economy;
    private $lastSent;
    private $sessions = [];
    public $lists = [];
    public $clearItems;
    public $sellSign;
    public $shopSign;
    private $sessionManager;
    private $scheduledBlockUpdateLoader;
    private $palette;
    public const GEOMETRY = '{"format_version": "1.12.0", "minecraft:geometry": [{"description": {"identifier": "geometry.skull", "texture_width": 64, "texture_height": 64, "visible_bounds_width": 2, "visible_bounds_height": 4, "visible_bounds_offset": [0, 0, 0]}, "bones": [{"name": "Head", "pivot": [0, 24, 0], "cubes": [{"origin": [-4, 0, -4], "size": [8, 8, 8], "uv": [0, 0]}, {"origin": [-4, 0, -4], "size": [8, 8, 8], "inflate": 0.5, "uv": [32, 0]}]}]}]}';
	private BlockManager $blockManager;

	final public static function getPacketsFromBatch(PacketBatch $packet)
	{
		$stream = new BinaryStream($packet->pay);
		while (!$stream->feof()) {
			yield $stream->getString();
		}
	}
    public static function getInstance()
    {
        return self::$instance;
    }

    public static function getMain(): self
    {
        return self::$instance;
    }
	public static function registerRuntimeIds(): void{
		$legacyBlockIdToStringIdMap = LegacyBlockIdToStringIdMap::getInstance();
		$runtimeBlockMapping = RuntimeBlockMapping::getInstance();
		$registerMapping = new \ReflectionMethod($runtimeBlockMapping, 'registerMapping');
		$registerMapping->setAccessible(true);

		$metaMap = [];
		foreach($runtimeBlockMapping->getBedrockKnownStates() as $runtimeId => $state){
			$legacyId = $legacyBlockIdToStringIdMap->stringToLegacy($state->getString("name"));
			if($legacyId === null or $legacyId <= BlockLegacyIds::LIT_BLAST_FURNACE){
				continue;
			}

			$metaMap[$legacyId] ??= 0;

			$meta = $metaMap[$legacyId]++;
			if($meta > 15){
				continue;
			}

			$registerMapping->invoke($runtimeBlockMapping, $runtimeId, $legacyId, $meta);
		}
	}

    public function onLoad() : void
    {
		self::registerRuntimeIds();

		$asyncPool = $this->getServer()->getAsyncPool();
		$asyncPool->addWorkerStartHook(static function(int $worker) use($asyncPool): void{
			$asyncPool->submitTaskToWorker(new class extends AsyncTask{
				public function onRun(): void{
					Main::registerRuntimeIds();
				}
			}, $worker);
		});
        $start = (bool) !(self::$instance instanceof $this);
        self::$instance = $this;

		$start = !isset(Main::$instance);
		Main::$instance = $this;

		if ($start) {
			$generators = [
				"ender" => EnderGenerator::class,
				"void" => VoidGenerator::class,
				"nether" => NetherGenerator::class,
				"normal" => NormalGenerator::class
			];

			foreach ($generators as $name => $class) {
				GeneratorManager::getInstance()->addGenerator($class, $name, fn() => null, true);
			}
		}

        if (!$this->isSpoon()) {
            @mkdir($this->getDataFolder() . "Setup");
            @mkdir($this->getDataFolder() . "Cloud");
            @mkdir($this->getDataFolder() . "Language");
            @mkdir($this->getDataFolder() . "Cloud/players/");
            @mkdir($this->getDataFolder() . "Cloud/players/User/");
            @mkdir($this->getDataFolder() . "Cloud/players/Logdata/");
            @mkdir($this->getDataFolder() . "Cloud/players/Gruppe/");
            @mkdir($this->getDataFolder() . "Cloud/players/Heiraten/");
            @mkdir($this->getDataFolder() . "Cloud/players/Freunde/");
            @mkdir($this->getDataFolder() . "Cloud/players/Clans");
            @mkdir($this->getDataFolder() . "Cloud/players/Homes");
            @mkdir($this->getDataFolder() . "Cloud/players/Stats");

            $this->saveResource("liesmich.txt", true);
            $this->saveResource("Setup/settings.json", false);
            $this->saveResource("Setup/discordsettings.yml", false);
            $this->saveResource("Setup/Config.yml", false);
            $this->saveResource("Setup/PerkSettings.yml", false);
            $this->saveResource("Setup/starterkit.yml", false);
            $this->saveResource("Setup/kitsettings.yml", false);
            $this->saveResource("permissions.md", false);
            $this->saveResource("Language/LangConfig.yml", false);
            $this->saveResource("Language/Lang_deu.json", true);
            $this->groupsgenerate();
            $this->configgenerate();
            $this->sessionManager = new SessionManager();
            ItemManager::init();
			$this->blockManager = new BlockManager();
			if (!file_exists($this->getDataFolder() . "Setup/Config.yml")) {
                rename("Setup/Config.yml", "Setup/ConfigOLD.yml");
                $this->getLogger()->alert("§cDie Config.yml ist nicht vorhanden! Der Server wird automatisch neugestartet!");
                $this->saveResource("Setup/Config.yml", true);
                $this->getServer()->shutdown();
            }
            self::$instance = $this;
            if (isset($c["API-Key"])) {
                if (trim($c["API-Key"]) != "") {
                    if (!is_dir($this->getDataFolder() . "Setup/")) {
                        mkdir($this->getDataFolder() . "Setup/");
                    }
                    file_put_contents($this->getDataFolder() . "Setup/minecraftpocket-servers.com.vrc", "{\"website\":\"http://minecraftpocket-servers.com/\",\"check\":\"http://minecraftpocket-servers.com/api-vrc/?object=votes&element=claim&key=" . $c["API-Key"] . "&username={USERNAME}\",\"claim\":\"http://minecraftpocket-servers.com/api-vrc/?action=post&object=votes&element=claim&key=" . $c["API-Key"] . "&username={USERNAME}\"}");
                }
            }

        }
    }

    public function onEnable() : void
    {

		$this->blockManager->startup();
        if (!$this->isSpoon()) {
            $this->default = "";
            $this->reload();
            if (strlen($this->default) > 1) {
                $this->getLogger()->warning("The \"normal\" property in config.yml has an error - the value is too long! Assuming as \"_\".");
                $this->default = "_";
            }
            $this->padding = "";
            $this->min = 3;
            $this->max = 16;
            if ($this->max === -1 or $this->max === "-1") {
                $this->max = PHP_INT_MAX;
            }
            $this->multibyte = function_exists("mb_substr") and function_exists("mb_strlen");

            self::$instance = $this;

            $config = new Config($this->getDataFolder() . Main::$setup . "settings.json", Config::JSON);
            $configs = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);

            if ($config->get("Config") == null) {
                $this->saveResource("Setup/settings.json", true);
                $this->getLogger()->info("§cDa die Settings.json fehlerhaft gespeichert wurde wurde sie ersetzt! ");
            }
            if ($configs->get("ConfigVersion") == Main::$configversion) {
                $this->getLogger()->info("");
            } else {
                $this->getLogger()->info("Die Config.yml ist veraltet! Daher wurde eine neue erstellt und die alte zu : ConfigOLD geändert!");
                rename($this->getDataFolder() . Main::$setup . "Config.yml", $this->getDataFolder() . Main::$setup . "ConfigOLD.yml");
                $this->saveResource("Setup/Config.yml", true);
            }
			//$this->buildBlockIdTable();
            $this->sellSign = new Config($this->getDataFolder() . Main::$lang . "SellSign.yml", Config::YAML, array(
                "sell" => array(
                    "§f[§cVerkaufen§f]",
                    "§ePreis §f: {price}$",
                    "§e {item}",
                    "§eMenge §f: {amount}"
                )
            ));
            $this->sellSign->save();
            $this->shopSign = new Config($this->getDataFolder() . Main::$lang . "ShopSign.yml", Config::YAML, array(
                "shop" => array(
                    "§f[§aKaufen§f]",
                    "§ePreis §f: {price} §e",
                    "§e {item}",
                    "§eMenge §f: §e {amount}"
                )
            ));
            $this->shopSign->save();
			$this->myplot = $this->getServer()->getPluginManager()->getPlugin("MyPlot");
			$this->economyapi = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			$this->multiworld = $this->getServer()->getPluginManager()->getPlugin("MultiWorld");
			$this->starterkit = $this->getServer()->getPluginManager()->getPlugin("StarterKit");
			$this->world = $this->getServer()->getPluginManager()->getPlugin("Worlds");

			$this->config = new Config($this->getDataFolder() . Main::$cloud . "Count.json", Config::JSON);


            $serverstats = new Config($this->getDataFolder() . Main::$cloud . "stats.json", Config::JSON);
            $serverstats->set("aktiviert", $serverstats->get("aktivieret") + 1);
            $serverstats->save();
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getServer()->getNetwork()->setName($configs->get("networkname"));
            $this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
            $this->getLogger()->info($config->get("prefix") . "§6Wird Geladen...");

            //Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("clear"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("version"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("tell"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("unban"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("banlist"));
            Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("kick"));


            $this->config = new Config($this->getDataFolder() . Main::$cloud . "Count.json", Config::JSON);
            $this->getLogger()->info($config->get("prefix") . "§6Plugins wurden Erfolgreich geladen!");
            $this->bank = new Config($this->getDataFolder() . "bank.json", Config::JSON);
            $votes = new Config($this->getDataFolder() . Main::$setup . "vote.yml", Config::YAML);

            //Commands
            $this->getServer()->getCommandMap()->register("gma", new AbenteuerCommand($this));
            //$this->getServer()->getCommandMap()->register("adminitem", new AdminItemsCommand($this));
            $this->getServer()->getCommandMap()->register("chatclear", new ChatClearCommand($this));
            $this->getServer()->getCommandMap()->register("clan", new ClanCommand($this));
            $this->getServer()->getCommandMap()->register("clear", new ClearCommand($this));
            $this->getServer()->getCommandMap()->register("clearlagg", new ClearlaggCommand($this));
            $this->getServer()->getCommandMap()->register("craft", new CraftCommand($this));
            $this->getServer()->getCommandMap()->register("day", new DayCommand($this));
            $this->getServer()->getCommandMap()->register("delhome", new DelHomeCommand($this));
            //$this->getServer()->getCommandMap()->register("ec", new EnderChestCommand($this));
            $this->getServer()->getCommandMap()->register("erfolg", new ErfolgCommand($this));
            $this->getServer()->getCommandMap()->register("fake", new FakeCommand($this));
            $this->getServer()->getCommandMap()->register("feed", new FeedCommand($this));
            $this->getServer()->getCommandMap()->register("fly", new FlyCommand($this));
            $this->getServer()->getCommandMap()->register("friend", new FriendCommand($this));
            $this->getServer()->getCommandMap()->register("givecoins", new GiveCoinsCommand($this));
            $this->getServer()->getCommandMap()->register("group", new GruppeCommand($this));
            $this->getServer()->getCommandMap()->register("heal", new HealCommand($this));
            $this->getServer()->getCommandMap()->register("heiraten", new HeiratenCommand($this));
            $this->getServer()->getCommandMap()->register("home", new HomeCommand($this));
            $this->getServer()->getCommandMap()->register("kickall", new KickallCommand($this));
            $this->getServer()->getCommandMap()->register("gmc", new KreativCommand($this));
            $this->getServer()->getCommandMap()->register("listhome", new ListHomeCommand($this));
            $this->getServer()->getCommandMap()->register("mycoins", new MyCoinsCommand($this));
            $this->getServer()->getCommandMap()->register("nick", new NickCommand($this));
            $this->getServer()->getCommandMap()->register("night", new NightCommand($this));
            // $this->getServer()->getCommandMap()->register("nightvision", new NightVisionCommand($this)); ##Keine Funktion Effekts
            $this->getServer()->getCommandMap()->register("notell", new NoDMCommand($this));
            $this->getServer()->getCommandMap()->register("nuke", new NukeCommand($this));
            $this->getServer()->getCommandMap()->register("payall", new PayallCommand($this));
            $this->getServer()->getCommandMap()->register("paycoins", new PayCoinsCommand($this));
            $this->getServer()->getCommandMap()->register("perk", new PerkCommand($this));
            $this->getServer()->getCommandMap()->register("perkshop", new PerkShopCommand($this));
            $this->getServer()->getCommandMap()->register("position", new PosCommand($this));
            $this->getServer()->getCommandMap()->register("rename", new RenameCommand($this));
            $this->getServer()->getCommandMap()->register("repair", new RepairCommand($this));
            $this->getServer()->getCommandMap()->register("reply", new ReplyCommand($this));
            $this->getServer()->getCommandMap()->register("serverstats", new ServerStatsCommand($this));
            $this->getServer()->getCommandMap()->register("sethome", new SetHomeCommand($this));
            $this->getServer()->getCommandMap()->register("servermute", new ServermuteCommand($this));
            $this->getServer()->getCommandMap()->register("sign", new SignCommand($this));
            $this->getServer()->getCommandMap()->register("size", new SizeCommand($this));
            $this->getServer()->getCommandMap()->register("stats", new StatsCommand($this));
            $this->getServer()->getCommandMap()->register("sudo", new SudoCommand($this));
            $this->getServer()->getCommandMap()->register("supervanish", new SuperVanishCommand($this));
            $this->getServer()->getCommandMap()->register("gms", new SurvivalCommand($this));
            $this->getServer()->getCommandMap()->register("tell", new TellCommand($this));
            $this->getServer()->getCommandMap()->register("tpall", new TpallCommand($this));
            $this->getServer()->getCommandMap()->register("unnick", new UnnickCommand($this));
            $this->getServer()->getCommandMap()->register("userdata", new UserdataCommand($this));
            //$this->getServer()->getCommandMap()->register("vanish", new VanishCommand($this));
            $this->getServer()->getCommandMap()->register("gmspc", new ZuschauerCommand($this));
            $this->getServer()->getCommandMap()->register("setwarp", new SetWarpCommand($this));
            $this->getServer()->getCommandMap()->register("delwarp", new DelWarpCommand($this));
            $this->getServer()->getCommandMap()->register("listwarp", new ListWarpCommand($this));
            $this->getServer()->getCommandMap()->register("warp", new WarpCommand($this));
            $this->getServer()->getCommandMap()->register("burn", new BurnCommand($this));
            $this->getServer()->getCommandMap()->register("kick", new KickCommand($this));
            $this->getServer()->getCommandMap()->register("afk", new AFKCommand($this));
            $this->getServer()->getCommandMap()->register("tpa", new TpaCommand($this));
            $this->getServer()->getCommandMap()->register("tpaccept", new TpaacceptCommand($this));
            $this->getServer()->getCommandMap()->register("tpadeny", new TpadenyCommand($this));
            $this->getServer()->getCommandMap()->register("hub", new HubCommand($this));
            //$this->getServer()->getCommandMap()->register("seeperms", new SeePermsCommand($this));
            $this->getServer()->getCommandMap()->register("id", new ItemIDCommand($this));
            //$this->getServer()->getCommandMap()->register("enderinvsee", new EnderInvSeeCommand($this));
            //$this->getServer()->getCommandMap()->register("invsee", new InvSeeCommand($this));
            //$this->getServer()->getCommandMap()->register("head", new HeadCommand($this));
            $this->getServer()->getCommandMap()->register("credits", new CreditsCommand($this));
            $this->getServer()->getCommandMap()->register("setstatstext", new SetstatstextCommand($this));
			if ($configs->get("PowerBlock") == true) {
				$this->getServer()->getPluginManager()->registerEvents(new PowerBlock($this), $this);
			}
			if ($configs->get("BoosterCommand") == true) {
				$this->getServer()->getCommandMap()->register("booster", new BoosterCommand($this));
			}
			if ($configs->get("Kits") == true) {
				$this->getServer()->getCommandMap()->register("kit", new KitCommand($this));
			}
			if ($configs->get("VoteSystem") == true) {
				$this->getServer()->getCommandMap()->register("vote", new VoteCommand($this));
			} elseif ($votes->get("votes") == false) {
				$this->getLogger()->info("Voten ist Deaktiviert! Wenn du es Nutzen möchtest Aktiviere es in den Einstelungen..");
			}
			if ($configs->get("BanSystem") == true) {
				$this->getServer()->getCommandMap()->register("unban", new UnbanCommand($this));
				$this->getServer()->getCommandMap()->register("animation", new AnimationCommand($this));
				$this->getServer()->getCommandMap()->register("ban", new BanCommand($this));
				$this->getServer()->getCommandMap()->register("banids", new BanIDListCommand($this));
				$this->getServer()->getCommandMap()->register("banlist", new BanListCommand($this));
			}
            if ($configs->get("RankShopCommand") == true) {
                $this->getServer()->getCommandMap()->register("rankshop", new RankShopCommand($this));
            }
			if ($this->multiworld or $this->world === null) {
				foreach (scandir($this->getServer()->getDataPath() . "worlds") as $file) {
					if (Server::getInstance()->getWorldManager()->isWorldGenerated($file)) {
						$this->getServer()->getWorldManager()->loadWorld($file);

					}
				}
				$this->getServer()->getCommandMap()->register("world", new WorldCommand($this));
			} else {
				$this->getLogger()->info("Da MultiWorld bereits Installiert wurde ist das Interne WorldSystem Deaktiviert");
			}
            if ($this->economyapi === null) {
                $this->getServer()->getCommandMap()->register("mymoney", new MyMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("pay", new PayMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("seemoney", new SeeMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("setmoney", new SetMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("takemoney", new TakeMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("givemoney", new GiveMoneyCommand($this));
                $this->getServer()->getCommandMap()->register("topmoney", new TopMoneyCommand($this));
				$this->getServer()->getPluginManager()->registerEvents(new EconomySell($this), $this);
				$this->getServer()->getPluginManager()->registerEvents(new EconomyShop($this), $this);
                $this->getLogger()->info("EconomyAPI ist nicht installiert daher wird das Interne Economysystem genutzt");
            }

			//LiftSystem
			if ($this->myplot === null) {
				$this->getLogger()->info("MyPlot ist nicht installiert daher wurde das Liftsystem Deaktiviert!");
			} else {
				$this->getServer()->getPluginManager()->registerEvents(new BlockBreakListener($this), $this);
				$this->getServer()->getPluginManager()->registerEvents(new BlockPlaceListener($this), $this);
				$this->getServer()->getPluginManager()->registerEvents(new PlayerInteractListener($this), $this);
				$this->getServer()->getPluginManager()->registerEvents(new PlayerJumpListener($this), $this);
				$this->getServer()->getPluginManager()->registerEvents(new PlayerToggleSneakListener($this), $this);
			}
			//Emotes
			if($configs->get("Emotes") === true){
				$this->getServer()->getCommandMap()->register("burb", new burb($this));
				$this->getServer()->getCommandMap()->register("geil", new geil($this));
				$this->getServer()->getCommandMap()->register("happy", new happy($this));
				$this->getServer()->getCommandMap()->register("sauer", new sauer($this));
				$this->getServer()->getCommandMap()->register("traurig", new traurig($this));

			}

            //Events
            //$this->getServer()->getPluginManager()->registerEvents(new BanEventListener($this), $this); #spieler muss gekickt werden was noch nicht klappt
            $this->getServer()->getPluginManager()->registerEvents(new ColorChat($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new DeathMessages($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new Particle($this), $this);
            //$this->getServer()->getPluginManager()->registerEvents(new AdminItemsEvents($this), $this);

            //$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
            //$this->getServer()->getPluginManager()->registerEvents(new EventsListener(), $this);
            //$this->getServer()->getPluginManager()->registerEvents(new Eventsettings($this), $this);
            //$this->getServer()->getPluginManager()->registerEvents(new FFAArena(), $this);
			$this->getServer()->getPluginManager()->registerEvents(new BlocketRecipes($this), $this);

            if ($configs->get("AntiXray") == true) {
            	$this->getLogger()->alert("AntiXray ist momentan nicht verfügbar");
                //$this->getServer()->getPluginManager()->registerEvents(new AntiXrayEvent($this), $this);
            } elseif ($configs->get("AntiXray") == false) {
                $this->getLogger()->info("AntiXray ist Deaktiviert! Wenn du es Nutzen möchtest Aktiviere es in den Einstelungen.");
            }

            //listener
            $this->getServer()->getPluginManager()->registerEvents(new BackListener($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new CollisionsListener($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new GroupListener($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new HeiratsListener($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new UserdataListener($this), $this);

            //Server
            //$this->getServer()->getPluginManager()->registerEvents(new PlotBewertung($this), $this);
            //$this->getServer()->getCommandMap()->register("restart", new RestartServer($this));
            $this->getServer()->getPluginManager()->registerEvents(new Rezept($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new Stats($this), $this);
            if ($configs->get("Regeln") == true) {
                $this->getServer()->getCommandMap()->register("regeln", new RegelServer($this));
                $this->getServer()->getPluginManager()->registerEvents(new RegelEvent($this), $this);
            }
            $this->getServer()->getCommandMap()->register("version", new Version($this));

            //Task
			$this->getScheduler()->scheduleRepeatingTask(new StatstextTask($this), 60);
			//$this->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this, "particle"]), 10);
            $this->getScheduler()->scheduleDelayedTask(new RTask($this), (20 * 60 * 10));
            $this->getScheduler()->scheduleRepeatingTask(new PingTask($this), 20);
			$this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask($this), 20);


			$this->getLogger()->info($config->get("prefix") . "§6Die Commands wurden Erfolgreich Regestriert");
            $this->getLogger()->info($config->get("prefix") . "§6Die Core ist nun Einsatzbereit!");
            $this->Banner();

		}
    }

    public function isSpoon()
    {
        if (!$this->getDescription()->getVersion() == Main::$version || $this->getDescription()->getName() !== "CoreV6") {
            $this->getLogger()->error("Du benutzt keine Originale Version der Core!");
            return false;
        }
        return false;
    }

    private function Banner()
    {
        $banner = strval(
            "\n" .
            "╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗\n" .
            "╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝\n" .
            "  ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗ \n" .
            "  ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝ \n" .
            "  ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗\n" .
            "  ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝\n" .
            "Copyright by TheNote! Als eigenes ausgeben Verboten!\n" .
            "                      2017-2021                       "
        );
        $this->getLogger()->info($banner);
    }
	public function onPreLogin(PlayerPreLoginEvent $event): void
	{
		$this->correctName($event->getPlayerInfo()->getUsername());

	}
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {

		//Allgemeines
        $player = $event->getPlayer();
        $fj = date('d.m.Y H:I') . date_default_timezone_set("Europe/Berlin");

        //Configs
        $gruppe = new Config($this->getDataFolder() . Main::$gruppefile . $player->getName() . ".json", Config::JSON);
        $log = new Config($this->getDataFolder() . Main::$logdatafile . $player->getName() . ".json", Config::JSON);
        $stats = new Config($this->getDataFolder() . Main::$statsfile . $player->getName() . ".json", Config::JSON);
        $user = new Config($this->getDataFolder() . Main::$userfile . $player->getName() . ".json", Config::JSON);
        $sstats = new Config($this->getDataFolder() . Main::$cloud . "stats.json", Config::JSON);
        $hei = new Config($this->getDataFolder() . Main::$heifile . $player->getName() . ".json", Config::JSON);
        $settings = new Config($this->getDataFolder() . Main::$setup . "settings.json", Config::JSON);
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        $cfg = new Config($this->getDataFolder() . Main::$setup . "starterkit.yml", Config::YAML, array());
        $money = new Config($this->getDataFolder() . Main::$cloud . "Money.yml", Config::YAML);
        $groups = new Config($this->getDataFolder() . Main::$cloud . "groups.yml", Config::YAML);
        $playerdata = new Config($this->getDataFolder() . Main::$cloud . "players.yml", Config::YAML);
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        //Discord
        if ($dcsettings->get("DC") == true) {
            $all = $this->getServer()->getOnlinePlayers();
            $playername = $event->getPlayer()->getName();
            $prefix = $playerdata->getNested($player->getName() . ".group");
            $slots = $settings->get("slots");
            $chatprefix = $dcsettings->get("chatprefix");
            $ar = getdate();
            $time = $ar['hours'] . ":" . $ar['minutes'];
            $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Joinmsg"));
            $stp2 = str_replace("{count}", count($all), $stp1);
            $stp3 = str_replace("{slots}", $slots, $stp2);
            $format = str_replace("{gruppe}", $prefix, $stp3);
            $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
            $this->sendMessage($format, $msg);
        }

        //Weiteres
        $log->set("Name", $player->getName());
        //$log->set("last-IP", $player->getAdress());
        $log->set("last-XboxID", $player->getPlayerInfo()->getXuid());
        $log->set("last-online", $fj);
        if ($user->get("heistatus") === false) {
            $player->sendMessage($settings->get("heirat") . "Du bist nicht verheiratet!");
        }
        if ($gruppe->get("Clanstatus") === false) {
            $player->sendMessage($settings->get("clans") . "Du bist im keinem Clan!");
        }

        //$this->TotemEffect($player);
        //$this->addStrike($player);

        //Spieler Erster Join
        if ($user->get("register") == null or false) {

            //StarterKit
            $player = $event->getPlayer();
            $ainv = $player->getArmorInventory();
            if ($config->get("StarterKit") == true) {
                if ($cfg->get("Inventory", false)) {
                    foreach ($cfg->get("Slots", []) as $item) {
                    	$result = ItemFactory::getInstance()->get($item["id"], $item["damage"], $item["count"]);
                        $result->setCustomName($item["name"]);
                        $result->setLore([$item["lore"]]);
                        $player->getInventory()->setItem($item["slot"], $result);
                    }
                }
                if ($cfg->get("Armor", false)) {
                    $data = $cfg->get("helm");
                    $item = ItemFactory::getInstance()->get($data["id"]);
                    $item->setCustomName($data["name"]);
                    $item->setLore([$data["lore"]]);
                    $ainv->setHelmet($item);

                    $data = $cfg->get("chest");
                    $item = ItemFactory::getInstance()->get($data["id"]);
                    $item->setCustomName($data["name"]);
                    $item->setLore([$data["lore"]]);
                    $ainv->setChestplate($item);

                    $data = $cfg->get("leggins");
                    $item = ItemFactory::getInstance()->get($data["id"]);
                    $item->setCustomName($data["name"]);
                    $item->setLore([$data["lore"]]);
                    $ainv->setLeggings($item);

                    $data = $cfg->get("boots");
                    $item =  ItemFactory::getInstance()->get($data["id"]);
                    $item->setCustomName($data["name"]);
                    $item->setLore([$data["lore"]]);
                    $ainv->setBoots($item);
                }
            }
            //Groupsystem
            $defaultgroup = $groups->get("DefaultGroup");
            $player = $event->getPlayer();
            $name = $player->getName();
            if (!$playerdata->exists($name)) {
                $groupprefix = $groups->getNested("Groups." . $defaultgroup . ".groupprefix");
                $playerdata->setNested($name . ".groupprefix", $groupprefix);
                $playerdata->setNested($name . ".group", $defaultgroup);
                $perms = $playerdata->getNested("{$name}.permissions", []);
                $perms[] = "CoreV5";
                $playerdata->setNested("{$name}.permissions", $perms);
                $playerdata->save();
            }
            $playergroup = $playerdata->getNested($name . ".group");
            $nametag = str_replace("{name}", $player->getName(), $groups->getNested("Groups.{$playergroup}.groupprefix"));
            $displayname = str_replace("{name}", $player->getName(), $groups->getNested("Groups.{$playerdata->getNested($name.".group")}.displayname"));
            $player->setNameTag($nametag);
            $player->setDisplayName($displayname);

            //Group Perms
            $permissionlist = (array)$groups->getNested("Groups." . $playergroup . ".permissions", []);
            foreach ($permissionlist as $name => $data) {
                $player->addAttachment($this)->setPermission($data, true);
            }

            //Economy
            $amount = $config->get("DefaultMoney");
            if ($money->getNested("money." . $player->getName()) == null) {
                $money->setNested("money." . $player->getName(), $amount);
                $money->save();
            }

            //Resgister
            $sstats->set("Users", $sstats->get("Users") + 1);
            $sstats->save();
            $log->set("first-join", $fj);
            //$log->set("first-ip", $player->getAddress());
            $log->set("first-XboxID", $player->getXuid());
            $log->set("first-uuid", $player->getUniqueId());
            $log->save();
            $gruppe->set("Nick", false);
            $gruppe->set("NickPlayer", false);
            $gruppe->set("Nickname", $player->getName());
            $gruppe->set("ClanStatus", false);
            $gruppe->save();
            $user->set("Clananfrage", false);
            $user->set("Clan", "");
            $user->set("register", true);
            $hei->set("antrag", null);
            $hei->set("antrag-abgelehnt", 0);
            $hei->set("heiraten", null);
            $hei->set("heiraten-hit", 0);
            $hei->set("geschieden", 0);
            $hei->save();
            $user->set("scoreboard", 2);
            $user->set("coins", 100);
            $user->set("nodm", false);
            $user->set("rulesaccpet", false);
            $user->set("clananfrage", false);
            $user->set("heistatus", false);
            $user->set("accept", false);
            $user->set("starterkit", true);
            $user->set("explode", false);
            $user->set("angry", false);
            $user->set("redstone", false);
            $user->set("smoke", false);
            $user->set("lava", false);
            $user->set("heart", false);
            $user->set("flame", false);
            $user->set("portal", false);
            $user->set("spore", false);
            $user->set("splash", false);
            $user->set("explodeperkpermission", false);
            $user->set("angryperkpermission", false);
            $user->set("redstoneperkpermission", false);
            $user->set("smokeperkpermission", false);
            $user->set("lavaperkpermission", false);
            $user->set("heartperkpermission", false);
            $user->set("flameperkpermission", false);
            $user->set("portalperkpermission", false);
            $user->set("sporeperkpermission", false);
            $user->set("splashperkpermission", false);
            $user->set("afkmove", false);
            $user->set("afkchat", false);
            $user->save();
            $stats->set("joins", 0);
            $stats->set("break", 0);
            $stats->set("place", 0);
            $stats->set("drop", 0);
            $stats->set("pick", 0);
            $stats->set("interact", 0);
            $stats->set("jumps", 0);
            $stats->set("messages", 0);
            $stats->set("votes", 0);
            $stats->set("consume", 0);
            $stats->set("kicks", 0);
            $stats->set("erfolge", 0);
            $stats->set("movefly", 0);
            $stats->set("movewalk", 0);
            $stats->set("jumperfolg", false); //10000
            $stats->set("breakerfolg", false); //1000000
            $stats->set("placeerfolg", false); //1000000
            $stats->set("messageerfolg", false); //1000000
            $stats->set("joinerfolg", false); //10000
            $stats->set("kickerfolg", false); //1000
            $stats->save();

            //DiscordMessgae
            if ($dcsettings->get("DC") == true) {
                $nickname = $player->getName();
                $this->getServer()->broadcastMessage($settings->get("prefix") . "§e" . $player->getName() . " ist neu auf dem Server! §cWillkommen");
                $time = date('d.m.Y H:I') . date_default_timezone_set("Europe/Berlin");
                $format =  "**__WILLKOMMEN__ : {time} : Spieler : {player} ist NEU auf dem Server " . $this->getServer()->getIp() .":" . $this->getServer()->getPort() . " und ist __Herzlichst Willkommen!__**";
                $msg = str_replace("{time}", $time, str_replace("{player}", $nickname, $format));
                $this->sendMessage($nickname, $msg);
            }
            //Regeln
            if ($config->get("Regeln") == true) {
                $form = new SimpleForm(function (Player $player, int $data = null) {

                    $result = $data;
                    if ($result === null) {
                        return true;
                    }
                    switch ($result) {
                        case 0:
                            $player->sendMessage("§eWir haben auch ein Discordserver : §d");
                            break;
                        case 1:
                            $player->kick("§cDu hättest dich besser entscheiden sollen :P", false);
                    }
                });
                $form->setTitle("§0======§f[§cWillkommen]§0======");
                $form->setContent("§eHerzlich willkommen " . $groups->get("nickname") . " wir wünschen dir Viel Spaß auf " . "! Bevor du loslegst zu Spielen solltest du Zuerst unsere Regeln sowie die Datenschutzgrundverordung durschlesen. Wenn du Hilfe brauchst schau einfach bei /hilfe nach dort findest du einige sachen die dir helfen können.\n\n Wir wünschen dir einen Guten Start!");
                $form->addButton("§0Alles Klar!");
                $form->addButton("§0Juckt mich Nicht");
                $form->sendToPlayer($player);
            }
        }

        //JoinMessages
        $all = $this->getServer()->getOnlinePlayers();
        $prefix = $playerdata->getNested($player->getName() . ".groupprefix");
        $slots = $settings->get("slots");
        $spielername = $gruppe->get("Nickname");
        if ($config->get("JoinTitle") == true) { //JoinTitle
            $subtitle = str_replace("{player}", $player->getName(), $config->get("Subtitlemsg"));
            $title = str_replace("{player}", $player->getName(), $config->get("Titlemsg"));
            $player->sendTitle($title);
            $player->sendSubTitle($subtitle);
        }
        if ($config->get("JoinTip") == true) { //JoinTip
            $tip = str_replace("{player}", $player->getName(), $config->get("Tipmsg"));
            $player->sendTip($tip);
        }
        if ($config->get("JoinMessage") == true) { //Joinmessage
            if ($gruppe->get("Nickname") == null) {
                $stp1 = str_replace("{player}", $player->getName(), $config->get("Joinmsg"));
            } else {
                $stp1 = str_replace("{player}", $spielername, $config->get("Joinmsg"));
            }
            $stp2 = str_replace("{count}", count($all), $stp1);
            $stp3 = str_replace("{slots}", $slots, $stp2);
            $joinmsg = str_replace("{prefix}", $prefix, $stp3);
            $event->setJoinMessage($joinmsg);
        } else {
            $event->setJoinMessage("");
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event)
    {

		$player = $event->getPlayer();
        //Configs
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        $gruppe = new Config($this->getDataFolder() . Main::$gruppefile . $player->getName() . ".json", Config::JSON);
        $playerdata = new Config($this->getDataFolder() . Main::$cloud . "players.yml", Config::YAML);
        $config = new Config($this->getDataFolder() . Main::$setup . "Config" . ".yml", Config::YAML);
        $settings = new Config($this->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
        $chatprefix = $dcsettings->get("chatprefix");
        $prefix = $playerdata->getNested($player->getName() . ".groupprefix");
        $spielername = $gruppe->get("Nickname");
        $all = $this->getServer()->getOnlinePlayers();
        $playername = $event->getPlayer()->getName();
        $group = $playerdata->getNested($player->getName() . ".group");
        $slots = $settings->get("slots");
        //Discord
        if ($dcsettings->get("DC") == true) {
            $ar = getdate();
            $time = $ar['hours'] . ":" . $ar['minutes'];
            $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Quitmsg"));
            $stp2 = str_replace("{count}", count($all), $stp1);
            $stp3 = str_replace("{slots}", $slots, $stp2);
            $format = str_replace("{gruppe}", $group, $stp3);
            $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
            $this->sendMessage($format, $msg);
        }
        //QuitMessage
        if ($config->get("QuitMessage") == true) {
            $stp1 = str_replace("{player}", $spielername, $config->get("Quitmsg"));
            $stp2 = str_replace("{count}", count($all), $stp1);
            $stp3 = str_replace("{slots}", $slots, $stp2);
            $quitmsg = str_replace("{prefix}", $prefix, $stp3);
            $event->setQuitMessage($quitmsg);
        } else {
            $event->setQuitMessage("");
        }
    }

    /*public function TotemEffect(Player $player)
    {
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        if ($config->get("totem") == true) {
            $original = $player->getInventory()->getItemInHand();
            $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(450, 0, 1));
            $player->broadcastAnimation(new OnScreenTextureAnimationPacket());
            //$pk = ;
            $pk->evid = OnScreenTextureAnimationPacket::EVENT_SOUND_TOTEM;
            $pk->position = $player->getEyePos();
            $pk->data = 0;
            $player->sendData((array)$pk);
            $player->getInventory()->setItemInHand($original);
        }
    }*/

    public function addStrike(Player $player)
	{
		$config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
		if ($config->get("blitze") == true) {
			$pos = $player->getPosition();
			$light = new AddActorPacket();
			$light->type = "minecraft:lightning_bolt";
			$light->actorRuntimeId = 1;
			$light->metadata = [];
			$light->motion = null;
			$light->yaw = $player->getLocation()->getYaw();
			$light->pitch = $player->getLocation()->getPitch();
			$light->position = new Vector3($pos->getX(), $pos->getY(), $pos->getZ());
			$block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
			$particle = new BlockBreakParticle($block);
			$player->getWorld()->addParticle($pos->asVector3(), $particle, $player->getWorld()->getPlayers());
			$sound = new PlaySoundPacket();
			$sound->soundName = "ambient.weather.thunder";
			$sound->x = $pos->getX();
			$sound->y = $pos->getY();
			$sound->z = $pos->getZ();
			$sound->volume = 1;
			$sound->pitch = 1;
			Server::getInstance()->broadcastPackets($player->getWorld()->getPlayers(), [$light, $sound]);
		}
		$packet = new PlaySoundPacket();
		$packet->soundName = "ambient.weather.thunder";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->getNetworkSession()->sendDataPacket($packet);
	}
    public function particle()
    {

        $level = $this->getServer()->getWorldManager()->getDefaultWorld();
        $pos = $level->getSafeSpawn();
        $count = 100;
		$particle = new DustParticle(Color::mix(Color::fromARGB("§6")));
        //$particle = new DustParticle($pos); //, mt_rand(), mt_rand(), mt_rand(), mt_rand());
        for ($yaw = 0, $y = $pos->y; $y < $pos->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20) {
            $x = -sin($yaw) + $pos->x;
            $z = cos($yaw) + $pos->z;
			$particle->encode($pos);

			//$particle->setCompunets($x, $y, $z);
            $level->addParticle($pos,$particle);
        }
    }

    public function reload()
    {

        $this->saveDefaultConfig();
        if (!is_dir($this->getDataFolder() . "Setup/")) {
            mkdir($this->getDataFolder() . "Setup/");
        }
        $this->lists = [];
        foreach (scandir($this->getDataFolder() . "Setup/") as $file) {
            $ext = explode(".", $file);
            $ext = (count($ext) > 1 && isset($ext[count($ext) - 1]) ? strtolower($ext[count($ext) - 1]) : "");
            if ($ext == "vrc") {
                $this->lists[] = json_decode(file_get_contents($this->getDataFolder() . "Setup/$file"), true);
            }
        }
        $config = new Config($this->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
        $prefix = $config->get("voten");

        $this->reloadConfig();
        $config = $this->getConfig()->getAll();
        $this->message = $prefix . "§6Danke das du für uns abgestimmt hast :D";
        $this->items = [];
        $this->debug = isset($config["Debug"]) && $config["Debug"] === true ? true : false;
    }

    public function rewardPlayer($player, $multiplier)
    {
        $settings = new Config($this->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
		$config = new Config($this->getDataFolder() . Main::$setup . "Config" . ".yml", Config::YAML);
		$dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
		$chatprefix = $dcsettings->get("chatprefix");
		$ar = getdate();

		$prefix = $settings->get("voten");
        if (!$player instanceof Player) {
            return;
        }
        if ($multiplier < 1) {
            $player->sendMessage($prefix . "§6Vote hier -> " . $config->get("VoteLink"));
            return;
        }
        $clones = [];
        $player->sendMessage($prefix . "§6Danke das du für uns abgestimmt hast :D " . ($multiplier == 1 ? "" : "s") . "!");
        $this->getServer()->broadcastMessage($prefix . $player->getNameTag() . " §r§dhat für uns abgestimmt! Danke :D");
        $configs = new Config($this->getDataFolder() . Main::$statsfile . $player->getPlayerInfo()->getUsername() . ".json", Config::JSON);
        $configs->set("votes", $configs->get("votes") + 1);
        $configs->save();
		if ($dcsettings->get("DC") == true) {
			$ar = getdate();
			$time = $ar['hours'] . ":" . $ar['minutes'];
			$format = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Votemsg"));
			$msg = str_replace("{time}", $time, str_replace("{player}", $player->getName(), $format));
			$this->sendMessage($format, $msg);

		}
    }

    public function onQuery(QueryRegenerateEvent $event)
    {
        $all = $this->getServer()->getOnlinePlayers();
        $count = count($all);
        $countdata = new Config($this->getDataFolder() . Main::$cloud . "Count.json", Config::JSON);
        $countdata->set("players", $count);
        $countdata->save();
        $online = new Config($this->getDataFolder() . Main::$cloud . "Count.json", Config::JSON);
        $event->getQueryInfo()->setPlayerCount($online->get("players"));


	}

    public function onPlayerLogin(PlayerLoginEvent $event)
    {
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        if ($config->get("defaultspawn") == true) {
            $event->getPlayer()->teleport($this->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        }
		$this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask($this, $event->getPlayer()), 20);

	}

    public function correctName($name): bool|string
	{
        if ($this->multibyte and mb_strlen($name) !== strlen($name)) {
            $length = mb_strlen($name, "UTF-8");
            $new = "";
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($name, $i, 1, "UTF-8");
                if (strlen($char) > 1) {
                    $char = $this->default;
                }
                $new .= $char;
            }
            $name = $new;
        }
        $name = preg_replace('/[^A-Za-z0-9_]/', $this->default, $name);
        $name = substr($name, 0, min($this->max, strlen($name)));
        if ($this->padding !== "") {
            while (strlen($name) < $this->min) {
                $name .= $this->padding;
            }
        }
        return $name;
    }


    public static function num_getOrdinal($num)
    {
        $rounded = $num % 100;
        if (3 < $rounded and $rounded < 21) {
            return "th";
        }
        $unit = $rounded % 10;
        if ($unit === 1) {
            return "st";
        }
        if ($unit === 2) {
            return "nd";
        }
        return $unit === 3 ? "rd" : "th";
    }

    public function onItemSpawn(ItemSpawnEvent $event)
    {
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        if ($config->get("inames") == true) {
            $entity = $event->getEntity();
            $item = $entity->getItem();
            $name = $item->getName();
            $entity->setNameTag($name);
            $entity->setNameTagVisible(true);
            $entity->setNameTagAlwaysVisible(true);
        }
    }

    public function onChat(PlayerChatEvent $event)
    {
        $config = new Config($this->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
        $voteconfig = new Config($this->getDataFolder() . Main::$setup . "Config" . ".yml", Config::YAML);
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        $playerdata = new Config($this->getDataFolder() . Main::$cloud . "players.yml", Config::YAML);
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $playername = $event->getPlayer()->getName();
        $prefix = $playerdata->getNested($player->getName() . ".group");
        $chatprefix = $dcsettings->get("chatprefix");
        $ar = getdate();

        $stats = new Config($this->getDataFolder() . Main::$statsfile . $player->getPlayerInfo()->getUsername() . ".json", Config::JSON);
        if ($voteconfig->get("MussVoten") == true) {
            if ($stats->get("votes") < $voteconfig->get("Mindestvotes")) {
                $player->sendMessage($config->get("error") . "§cDu musst mindestens 1x Gevotet haben um auf dem Server Schreiben zu können! §f-> §e" . $voteconfig->get("votelink"));
                $event->cancel();
                return true;
            } else {
                $event->uncancel();
                if ($dcsettings->get("DC") == true) {
                    $ar = getdate();
                    $time = $ar['hours'] . ":" . $ar['minutes'];
                    $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Chatmsg"));
                    $stp3 = str_replace("{msg}", $message, $stp1);
                    $format = str_replace("{gruppe}", $prefix, $stp3);
                    $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
                    $this->sendMessage($format, $msg);

                }
            }
        } elseif ($voteconfig->get("MussVoten") == false) {
            if ($dcsettings->get("DC") == true) {
                $time = $ar['hours'] . ":" . $ar['minutes'];
                $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Chatmsg"));
                $stp3 = str_replace("{msg}", $message, $stp1);
                $format = str_replace("{gruppe}", $prefix, $stp3);
                $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
                $this->sendMessage($format, $msg);
            }
        }
        $msg = $event->getMessage();
        $p = $event->getPlayer();
        $money = new Config($this->getDataFolder() . Main::$cloud . "Money.yml", Config::YAML);

        if ($this->win != null && $this->price != null) {
            if ($msg == $this->win) {
                $this->getServer()->broadcastMessage($config->get("info") . "§7Der Spieler §6" . $p->getNameTag() . " §7hat das Wort: §e" . $this->win . " §7entschlüsselt und hat §a" . $this->price . "€ §7gewonnen!");
                if ($this->economyapi == null) {
                    $money->setNested("money." . $p->getName(), $money->getNested("money." . $p->getName()) + $this->price);
                    $money->save();
                } else {
                    $this->economy->addMoney($p->getName(), $this->price);
                }
                $this->win = null;
                $this->price = null;
                $event->cancel();
            }
        }
        return true;
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        $playerdata = new Config($this->getDataFolder() . Main::$cloud . "players.yml", Config::YAML);
        if ($dcsettings->get("DC") == true) {
            $playername = $event->getPlayer()->getName();
            $prefix = $playerdata->getNested($event->getPlayer()->getName() . ".group");
            $chatprefix = $dcsettings->get("chatprefix");
            $ar = getdate();
            $time = $ar['hours'] . ":" . $ar['minutes'];
            $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Deathmsg"));
            $format = str_replace("{gruppe}", $prefix, $stp1);
            $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
            $this->sendMessage($format, $msg);
        }
        if ($config->get("keepinventory") == true) {
            $event->setKeepInventory(true);
        } elseif ($config->get("keepinventory") == false) {
            $event->setKeepInventory(false);
        }
    }

    public function onKick(PlayerKickEvent $event)
    {
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        $playerdata = new Config($this->getDataFolder() . Main::$cloud . "players.yml", Config::YAML);
        if ($dcsettings->get("DC") == true) {
            $playername = $event->getPlayer()->getName();
            $prefix = $playerdata->getNested($event->getPlayer()->getName() . ".group");
            $chatprefix = $dcsettings->get("chatprefix");
            $ar = getdate();
            $time = $ar['hours'] . ":" . $ar['minutes'];
            $stp1 = str_replace("{dcprefix}", $chatprefix, $dcsettings->get("Kickmsg"));
            $format = str_replace("{gruppe}", $prefix, $stp1);
            $msg = str_replace("{time}", $time, str_replace("{player}", $playername, $format));
            $this->sendMessage($format, $msg);
        }
    }

    public function backFromAsync($player, $result)
    {
        if ($player === "nolog") {
            return;
        } elseif ($player === "CONSOLE") {
            $player = new ConsoleCommandSender();
        } else {
            $playerinstance = $this->getServer()->getPlayerExact($player);
            if ($playerinstance === null) {
                return;
            } else {
                $player = $playerinstance;
            }
        }
    }

    public function onMessage(CommandSender $sender, Player $receiver): void
    {
        $this->lastSent[$receiver->getName()] = $sender->getName();
    }

    public function getLastSent(string $name): string
    {
        return $this->lastSent[$name] ?? "";
    }

    public function sendMessage($player, string $msg):bool
    {
        $dcsettings = new Config($this->getDataFolder() . Main::$setup . "discordsettings" . ".yml", Config::YAML);
        $name = $dcsettings->get("chatname");
        $webhook = $dcsettings->get("webhookurl");
        $curlopts = [
            "content" => $msg,
            "username" => $name
        ];
        if ($dcsettings->get("DC") == true) {
            $this->getServer()->getAsyncPool()->submitTask(new task\SendAsyncTask($player, $webhook, serialize($curlopts)));
        }
        return true;
    }

    public function getClicks(Player $player)
    {
        if (!isset($this->clicks[$player->getName()])) return 0;
        $time = $this->clicks[$player->getName()][0];
        $clicks = $this->clicks[$player->getName()][1];
        if ($time !== time()) {
            unset($this->clicks[$player->getName()]);
            return 0;
        }
        return $clicks;
    }

    public function addClick(Player $player)
    {
        if (!isset($this->clicks[$player->getName()])) $this->clicks[$player->getName()] = [time(), 0];
        $time = $this->clicks[$player->getName()][0];
        $clicks = $this->clicks[$player->getName()][1];
        if ($time !== time()) {
            $time = time();
            $clicks = 0;
        }
        $clicks++;
        $this->clicks[$player->getName()] = [$time, $clicks];
    }

    public function getElevators(Block $block, string $where = "", bool $searchForPrivate = false): int
    {
        if (!$searchForPrivate) {
            $blocks = DaylightSensor::class;
        } else {
            $blocks = [ItemIds::DAYLIGHT_DETECTOR, ItemIds::DAYLIGHT_DETECTOR_INVERTED];
        }
        $count = 0;
        if ($where === "up") {
            $y = $block->getPosition()->getY() + 1;
            while ($y < $block->getPosition()->getWorld()->getMaxY()) {
                $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
                if (in_array($blockToCheck->getId(), $blocks)) {
                    $count = $count + 1;
                }
                $y++;
            }
        } elseif ($where === "down") {
            $y = $block->getPosition()->getY() - 1;
            while ($y >= 0) {
                $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
                if (in_array($blockToCheck->getId(), $blocks)) {
                    $count = $count + 1;
                }
                $y--;
            }
        } else {
            $y = 0;
            while ($y < $block->getPosition()->getWorld()->getMaxY()) {
                $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
                if (in_array($blockToCheck->getId(), $blocks)) {
                    $count = $count + 1;
                }
                $y++;
            }
        }
        return $count;
    }


    public function getNextElevator(Block $block, string $where = "", bool $searchForPrivate = false): ?Block
    {
        if (!$searchForPrivate) {
            $blocks = [ItemIds::DAYLIGHT_DETECTOR];
        } else {
            $blocks = [ItemIds::DAYLIGHT_DETECTOR, ItemIds::DAYLIGHT_DETECTOR_INVERTED];
        }
        $elevator = null;
        if ($where === "up") {
            $y = $block->getPosition()->getY() + 1;
            while ($y < $block->getPosition()->getWorld()->getMaxY()) {
                $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
                if (in_array($blockToCheck->getId(), $blocks)) {
                    $elevator = $blockToCheck;
                    break;
                }
                $y++;
            }
        } else {
            $y = $block->getPosition()->getY() - 1;
            while ($y >= 0) {
                $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
                if (in_array($blockToCheck->getId(), $blocks)) {
                    $elevator = $blockToCheck;
                    break;
                }
                $y--;
            }
        }
        if ($elevator === null) return null;

        if ($this->config->get("checkFloor") !== true) return $elevator;

        $block1 = $elevator->getPosition()->getWorld()->getBlock(new Vector3($elevator->getPosition()->getX(), $elevator->getPosition()->getY() + 1, $elevator->getPosition()->getZ()));
        $block2 = $elevator->getPosition()->getWorld()->getBlock(new Vector3($elevator->getPosition()->getX(), $elevator->getPosition()->getY() + 2, $elevator->getPosition()->getZ()));
        if ($block1->getId() !== 0 || $block2->getId() !== 0) return $block;

        $blocksToCheck = [];

        $blocksToCheck[] = $block1->getPosition()->getWorld()->getBlock(new Vector3($block1->getPosition()->getX() + 1, $block1->getPosition()->getY(), $block1->getPosition()->getZ()));
        $blocksToCheck[] = $block1->getPosition()->getWorld()->getBlock(new Vector3($block1->getPosition()->getX() - 1, $block1->getPosition()->getY(), $block1->getPosition()->getZ()));
        $blocksToCheck[] = $block1->getPosition()->getWorld()->getBlock(new Vector3($block1->getPosition()->getX(), $block1->getPosition()->getY(), $block1->getPosition()->getZ() + 1));
        $blocksToCheck[] = $block1->getPosition()->getWorld()->getBlock(new Vector3($block1->getPosition()->getX(), $block1->getPosition()->getY(), $block1->getPosition()->getZ() - 1));

        $blocksToCheck[] = $block2->getPosition()->getWorld()->getBlock(new Vector3($block2->getPosition()->getX() + 1, $block2->getPosition()->getY(), $block2->getPosition()->getZ()));
        $blocksToCheck[] = $block2->getPosition()->getWorld()->getBlock(new Vector3($block2->getPosition()->getX() - 1, $block2->getPosition()->getY(), $block2->getPosition()->getZ()));
        $blocksToCheck[] = $block2->getPosition()->getWorld()->getBlock(new Vector3($block2->getPosition()->getX(), $block2->getPosition()->getY(), $block2->getPosition()->getZ() + 1));
        $blocksToCheck[] = $block2->getPosition()->getWorld()->getBlock(new Vector3($block2->getPosition()->getX(), $block2->getPosition()->getY(), $block2->getPosition()->getZ() - 1));

        $deniedBlocks = [ItemIds::LAVA, ItemIds::FLOWING_LAVA, ItemIds::WATER, ItemIds::FLOWING_WATER];
        foreach ($blocksToCheck as $blockToCheck) {
            if (in_array($blockToCheck->getId(), $deniedBlocks)) return $block;
        }

        return $elevator;
    }

    public function getFloor(Block $block, bool $searchForPrivate = false): int
    {
        if (!$searchForPrivate) {
            $blocks = [ItemIds::DAYLIGHT_SENSOR];
        } else {
            $blocks = [ItemIds::DAYLIGHT_SENSOR, ItemIds::DAYLIGHT_SENSOR_INVERTED];
        }
        $sw = 0;
        $y = -1;
        while ($y < $block->getPosition()->getWorld()->getMaxY()) {
            $y++;
            $blockToCheck = $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $y, $block->getPosition()->getZ()));
            if (!in_array($blockToCheck->getId(), $blocks)) continue;
            $sw++;
            if ($blockToCheck === $block) break;
        }
        return $sw;
    }


    //AntiXray
    public static function getInvolvedBlocks($blocks): array
    {
        $finalBlocks = [];

        foreach ($blocks as $key => $block) {
            $finalBlocks[] = $block;
            foreach (ChunkModificationTask::BLOCK_SIDES as $side) {
                $side = $blocks[$key]->getSide($side);

                foreach (ChunkModificationTask::BLOCK_SIDES as $side_2)
                    $finalBlocks[] = $side->getSide($side_2);

                $finalBlocks[] = $side;
            }
        }
        return $finalBlocks;
    }

    public function isMuted(): bool
    {
        return $this->universalMute;
    }

    public function setMuted(bool $bool = true): void
    {
        $this->universalMute = $bool;
    }

    public function getSessionById(int $id)
    {
        if (isset($this->sessions[$id])) {
            return $this->sessions[$id];
        } else {
            return null;
        }
    }

    public function destroySession(Player $player): bool
    {
        if (isset($this->sessions[$player->getId()])) {
            unset($this->sessions[$player->getId()]);
            return true;
        }
        return false;
    }

    public function getSessionByName(string $name)
    {
        foreach ($this->sessions as $session) {
            if ($session->getPlayer()->getName() == $name) {
                return $session;
            }
        }
        return null;
    }

    //Redstone

    public function getScheduledBlockUpdateLoader(): ScheduledBlockUpdateLoader
    {
        return $this->scheduledBlockUpdateLoader;
    }

    public function getGlobalBlockPalette(): GlobalBlockPalette
    {
        return $this->palette;
    }

    /*public function onDisable() : void
    {
        $config = new Config($this->getDataFolder() . Main::$setup . "Config.yml", Config::YAML);
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->transfer($config->get("rejoinserverip"), $config->get("rejoinserverport"));
        }
    }*/

    //TPASystem
    public function setInvite(Player $sender, Player $target): void
    {
        $this->invite[$target->getName()] = $sender->getName();
    }

    public function getInvite($name): string
    {
        return $this->invite[$name];
    }

    public function getInviteControl(string $name): bool
    {
        return isset($this->invite[$name]);
    }

    //Configs
    public function groupsgenerate()
    {
        if (!file_exists($this->getDataFolder() . Main::$cloud . "groups.yml")) {
            $groups = new Config($this->getDataFolder() . Main::$cloud . "groups.yml", Config::YAML);
            $groups->set("DefaultGroup", "normal");

            $groups->setNested("Groups.normal.groupprefix", "§f[§eSpieler§f]§7");
            $groups->setNested("Groups.normal.format1", "§f[§eSpieler§f] §7{name} §r§f|§7 {msg}");
            $groups->setNested("Groups.normal.format2", "§f[§eSpieler§f] {clan} §7{name} §r§f|§7 {msg}");
            $groups->setNested("Groups.normal.format3", "§f[§eSpieler§f] {heirat} §7{name} §r§f|§7 {msg}");
            $groups->setNested("Groups.normal.format4", "§f[§eSpieler§f] {heirat} {clan} §7{name} §r§f|§7 {msg}");
            $groups->setNested("Groups.normal.nametag", "§f[§eSpieler§f] §7{name}");
            $groups->setNested("Groups.normal.displayname", "§eS§f:§7{name}");
            $groups->setNested("Groups.normal.permissions", ["CoreV5"]);

            $groups->setNested("Groups.premium.groupprefix", "§f[§6Premium§f]§6");
            $groups->setNested("Groups.premium.format1", "§f[§6Premium§f] §6{name} §r§f|§6 {msg}");
            $groups->setNested("Groups.premium.format2", "§f[§6Premium§f] {clan} §6{name} §r§f|§6 {msg}");
            $groups->setNested("Groups.premium.format3", "§f[§6Premium§f] {heirat} §6{name} §r§f|§6 {msg}");
            $groups->setNested("Groups.premium.format4", "§f[§6Premium§f] {heirat} {clan} §6{name} §r§f|§6 {msg}");
            $groups->setNested("Groups.premium.nametag", "§f[§6Premium§f] §6{name}");
            $groups->setNested("Groups.premium.displayname", "§6P§f:§6{name}");
            $groups->setNested("Groups.premium.permissions", ["CoreV5"]);

            $groups->setNested("Groups.owner.groupprefix", "§f[§4Owner§f]§c");
            $groups->setNested("Groups.owner.format1", "§f[§4Owner§f] §c{name} §r§f|§c {msg}");
            $groups->setNested("Groups.owner.format2", "§f[§4Owner§f] {clan} §c{name} §r§f|§c {msg}");
            $groups->setNested("Groups.owner.format3", "§f[§4Owner§f] {heirat} §c{name} §r§f|§c {msg}");
            $groups->setNested("Groups.owner.format4", "§f[§4Owner§f] {heirat} {clan} §c{name} §r§f|§c {msg}");
            $groups->setNested("Groups.owner.nametag", "§f[§4Owner§f] §c{name}");
            $groups->setNested("Groups.owner.displayname", "§4O§f:§c{name}");
            $groups->setNested("Groups.owner.permissions", ["CoreV5"]);

            //Defaultgroup
            $groups->set("DefaultGroup", "normal");
            $groups->save();
        }
    }

    public function configgenerate()
    {
        if (!file_exists($this->getDataFolder() . Main::$cloud . "Money.yml")) {
            $money = new Config($this->getDataFolder() . "Money.yml", Config::YAML);
            $money->setNested("money.CoreV6.", 1000);
            $money->save();
        }
    }
}