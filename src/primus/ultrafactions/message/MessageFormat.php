<?php
# Formats message for UltraFactions
namespace primus\ultrafactions\message;

use pocketmine\utils\TextFormat;

class MessageFormat {
	
	const PREFIX = TextFormat::BOLD.TextFormat::GOLD.'['.TextFormat::RESET.TextFormat::WHITE.'UltraFactions'.TextFormat::BOLD.TextFormat::GOLD.'] '.TextFormat::WHITE.TextFormat::RESET;
	
	const ERROR = 0;
	const NOPERMISSION = 1;
	const NOTICE = 2;
	const WARNING = 3;
	
	const INFO = 4;
	const SUCCESS = 5;
	const NORMAL = 6;
	
	public function formatMessage($message, $type = self::NORMAL){
		switch($type){
			case self::ERROR:
			 return /*self::PREFIX.*/TextFormat::DARK_RED.$message;
			 break;
			case self::NOPERMISSION:
			 return self::PREFIX.TextFormat::RED.$message;
			 break;
			case self::NOTICE:
			 return self::PREFIX.TextFormat::YELLOW.$message;
			 break;
			case self::WARNING:
			 return self::PREFIX.TextFormat::BOLD.TextFormat::RED.$message;
			 break;
			case self::INFO:
			 return self::PREFIX.TextFormat::GRAY.$message;
			 break;
			case self::SUCCESS:
			 return self::PREFIX.TextFormat::GREEN.$message;
			 break;
			case self::NORMAL:
			 return self::PREFIX.TextFormat::WHITE.$message;
			 break;
			default:
			 return $message;
		}
	}
	
}
