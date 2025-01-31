<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\generator\Flat;
use pocketmine\world\generator\normal\Normal;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use TheNote\core\Main;
use TheNote\core\server\generators\ender\EnderGenerator;
use TheNote\core\server\generators\nether\NetherGenerator;
use TheNote\core\server\generators\normal\NormalGenerator;
use TheNote\core\server\generators\void\VoidGenerator;

class WorldCommand extends Command
{
    private $plugin;

    public const GENERATOR_NORMAL = 0;
    public const GENERATOR_NORMAL_CUSTOM = 1;
    public const GENERATOR_HELL = 2;
    public const GENERATOR_ENDER = 3;
    public const GENERATOR_FLAT = 4;
    public const GENERATOR_VOID = 5;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
        parent::__construct("world", $config->get("prefix") . "§aManage die Welten", "/world");
        $this->setPermission("core.command.world");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        $config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);

        /*if (!$sender instanceof Player) {
            $sender->sendMessage($config->get("error") . "§cDiesen Command kannst du nur Ingame benutzen");
            return false;
        }*/
        if (empty($args[0])) {
            $sender->sendMessage($config->get("info") . "§cBenutze : /world (help) für Hilfe");
            return false;
        }
        $levels = [];
        if ($args[0] == "help") {
            $sender->sendMessage($config->get("world") . "§6Hilfe");
            $sender->sendMessage("§e/world teleport (worldname)");
            $sender->sendMessage("§e/world create (name) (generator) (seed)");
            $sender->sendMessage("§e-> normal|nether|ender|void|flat|vanilla");
            $sender->sendMessage("§e/world delete (worldname)");
            $sender->sendMessage("§e/world list");
        }
        if ($args[0] == "teleport" and "tp") {
			if (!$sender instanceof Player) {
				$sender->sendMessage($config->get("error") . "§cDiesen Command kannst du nur Ingame benutzen");
				return false;
			}
            if (!isset($args[1])) {
                $sender->sendMessage($config->get("info") . "Nutze : /world teleport [worldname]");
                return false;
            }

            if (!$this->plugin->getServer()->getWorldManager()->isWorldGenerated($args[1])) {
                $sender->sendMessage($config->get("error") . "§cDie Welt mit dem Namen §f:§e " . $args[1] . " §cexistiert nicht!");
                return false;
            }
			$level = $this->plugin->getServer()->getWorldManager()->getWorldByName($args[1]);
			if (!$this->plugin->getServer()->getWorldManager()->isWorldLoaded($args[1])) {
				$this->plugin->getServer()->getWorldManager()->loadWorld($args[1]);
			}
			if (!$sender->teleport($level->getSafeSpawn())) {
				$sender->sendMessage($config->get("error") . "§cDer Teleportvorgang wurde abgebrochen!");
				return true;
			}
			$sender->teleport($level->getSafeSpawn());
            $sender->sendMessage($config->get("world") . "§6Du wurdest erfolgreich in die Welt §f: §e" . $args[1] . " §6teleportiert!");
        }
        if ($args[0] == "delete") {
            if (empty($args[1])) {
                $sender->sendMessage($config->get("info") . "Nutze : /world delete [worldname]");
                return false;
            }
            if (!$this->isLevelLoaded($args[1])) $this->plugin->getServer()->getWorldManager()->loadWorld($args[1]);

            if (!$this->plugin->getServer()->getWorldManager()->isWorldGenerated($args[0]) and !file_exists($this->plugin->getServer()->getDataPath() . "worlds/" . $args[1])) {
                if ($this->plugin->getServer()->getWorldManager()->getDefaultWorld() === $this->plugin->getServer()->getWorldManager()->getWorldByName($args[1])) {
                    $sender->sendMessage($config->get("error") . "§cDu kannst die Standartwelt nicht Löschen!");
                } else {
                    $sender->sendMessage($config->get("error") . "§cDie Welt mit dem Namen " . $args[1] . " existiert nicht!");
                    return false;
                }
            } else {
                $files = $this->removeLevel($args[1]);
                $sender->sendMessage($config->get("world") . "§6Du hast die Welt§f:§e " . $args[1] . " §6erfolgreich gelöscht!");
                $sender->sendMessage("§6$files Dataien Gelöscht!");
            }
        }

        if ($args[0] == "list") {
            foreach (scandir($this->plugin->getServer()->getDataPath() . "worlds") as $file) {
                if ($this->isLevelGenerated($file)) {
                    $isLoaded = $this->isLevelLoaded($file);
                    $players = 0;

                    if ($isLoaded) {
                        $players = count($this->plugin->getServer()->getWorldManager()->getWorldByName($file)->getPlayers());
                    }

                    $levels[$file] = [$isLoaded, $players];
                }
            }
            $sender->sendMessage($config->get("world") . "§eGeladene Welten :" . (string)count($levels));

            foreach ($levels as $level => [$loaded, $players]) {
                $loaded = $loaded ? "§aGeladen§7" : "§cUngeladen§7";
                $sender->sendMessage("§7{$level} > {$loaded} §7Spieler§f:§e {$players}");
            }
        }
        if ($args[0] == "create") {
            if (empty($args[1])) {
                $sender->sendMessage($config->get("info") . "§eBenutze : /world create (worldname) (generator) (seed)");
                return false;

            }
            if ($this->isLevelGenerated($args[1])) {
                $sender->sendMessage($config->get("error") . "§cDie Welt mit dem Namen §f:§e " . $args[1] . " §cexistiert bereits!");
                return false;
            }
            $seed = 0;
            if (isset($args[3]) && is_numeric($args[3])) {
                $seed = (int)$args[3];
            }
            $generatorName = "normal";
            $generator = null;

            if (isset($args[2])) {
                $generatorName = $args[2];
            }
            switch (strtolower($generatorName)) {
                case "normal":
                    $generator = WorldCommand::GENERATOR_NORMAL;
                    $generatorName = "Normal";
                    break;
                case "vanilla":
                    $generator = WorldCommand::GENERATOR_NORMAL_CUSTOM;
                    $generatorName = "Custom";
                    break;
                case "flat":
                    $generator = WorldCommand::GENERATOR_FLAT;
                    $generatorName = "Flat";
                    break;
                case "nether":
                    $generator = WorldCommand::GENERATOR_HELL;
                    $generatorName = "Nether";
                    break;
                case "ender":
                    $generator = WorldCommand::GENERATOR_ENDER;
                    $generatorName = "End";
                    break;
                case "void":
                    $generator = WorldCommand::GENERATOR_VOID;
                    $generatorName = "Void";
                    break;
                default:
                    $generator = WorldCommand::GENERATOR_NORMAL;
                    $generatorName = "Normal";
                    break;
            }
            $this->generateLevel($args[1], $seed, $generator);
            $sender->sendMessage($config->get("world") . "§6Die Welt wurde erfolgreich erstellt! §eName§f: " . $args[1] . " §eSeed§f: " . $seed . " §eGenerator§f: " . $generatorName);
        }
        return true;

    }

    public static function isLevelLoaded(string $levelName): bool
    {
        return Server::getInstance()->getWorldManager()->isWorldLoaded($levelName);
    }

    public static function isLevelGenerated(string $levelName): bool
    {
        return Server::getInstance()->getWorldManager()->isWorldGenerated($levelName) && !in_array($levelName, [".", ".."]);
    }

    public static function getLevel(string $name): ?World
    {
        return Server::getInstance()->getWorldManager()->getWorldByName($name);
    }

    public static function loadLevel(string $name): bool
    {
        return self::isLevelLoaded($name) ? false : Server::getInstance()->getWorldManager()->loadworld($name);
    }

    public static function unloadLevel(World $level): bool
    {
        return $level->getServer()->getWorldManager()->unloadWorld($level);
    }

    public static function generateLevel(string $levelName, int $seed = 0, int $generator = WorldCommand::GENERATOR_NORMAL): bool
    {
        if (self::isLevelGenerated($levelName)) {
            return false;
        }

        $generatorClass = Normal::class;
        switch ($generator) {
            case self::GENERATOR_HELL:
                $generatorClass = NetherGenerator::class;
                break;
            case self::GENERATOR_ENDER:
                $generatorClass = EnderGenerator::class;
                break;
            case self::GENERATOR_NORMAL_CUSTOM:
                $generatorClass = NormalGenerator::class;
                break;
            case self::GENERATOR_VOID:
                $generatorClass = VoidGenerator::class;
                break;
            case self::GENERATOR_FLAT:
                $generatorClass = Flat::class;
                break;
        }
        return Server::getInstance()->getWorldManager()->generateWorld($levelName, WorldCreationOptions::create()->setSeed($seed), $generatorClass) ;

	}

    public static function removeLevel(string $name): int
    {
        if (self::isLevelLoaded($name)) {
            $level = self::getLevel($name);

            if (count($level->getPlayers()) > 0) {
                foreach ($level->getPlayers() as $player) {
                    $player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
                }
            }
            $level->getServer()->getWorldManager()->unloadWorld($level);
        }
        return self::removeDir(Server::getInstance()->getDataPath() . "/worlds/" . $name);
    }
    
    public static function getAllLevels(): array
    {
        $levels = [];
        foreach (glob(Server::getInstance()->getDataPath() . "/worlds/*") as $world) {
            if (count(scandir($world)) >= 4) {
                $levels[] = basename($world);
            }
        }
        return $levels;
    }

    private static function removeFile(string $path): int
    {
        unlink($path);
        return 1;
    }

    private static function removeDir(string $dirPath): int
    {
        $files = 1;
        if (basename($dirPath) == "." || basename($dirPath) == ".." || !is_dir($dirPath)) {
            return 0;
        }
        foreach (scandir($dirPath) as $item) {
            if ($item != "." || $item != "..") {
                if (is_dir($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::removeDir($dirPath . DIRECTORY_SEPARATOR . $item);
                }
                if (is_file($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::removeFile($dirPath . DIRECTORY_SEPARATOR . $item);
                }
            }
        }
        rmdir($dirPath);
        return $files;
    }
}
