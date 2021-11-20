<?php


namespace TheNote\core\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class LavaCauldron extends Opaque
{
	public function __construct(BlockIdentifier $idInfo, ?BlockBreakInfo $breakInfo = null)
	{
		parent::__construct($idInfo, "Lava Cauldron",$breakInfo ?? new BlockBreakInfo(0.9, BlockToolType::PICKAXE));
	}

	public function canBePlaced() : bool{
		return true;
	}
}