<?php

namespace forkdetector;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;

/**
 * This class is deliberately meant to be silly
 * Class SpoonDetector
 * @ package falkirks\simplewarp\utils
 */
class ForkDetector
{
  private static $ascii = "
   __           _    
  / _|         | |   
 | |_ ___  _ __| | __
 |  _/ _ \\| '__| |/ /
 | || (_) | |  |   < 
 |_| \\___/|_|  |_|\\_\\";

  private static $text = "
    The author of this plugin does not provide support for third-party builds of 
    PocketMine-MP (forks). Forks detract from the overall quality of the MCPE plugin environment, which is already 
    lacking in quality. They force plugin developers to waste time trying to support conflicting APIs.
    
    In order to begin using this plugin you must understand that you will be offered no support. 
    
    Furthermore, the GitHub issue tracker for this project is targeted at vanilla PocketMine only. Any bugs you create which don't affect vanilla PocketMine will be deleted.
    
    Have you read and understood the above (type 'yes' after the question mark)?";

  private static $thingsThatAreNotFork = ['PocketMine-MP'];

  public static function isThisFork($notForks,bool $obfuscate = true): bool
  {
    if($obfuscate) {
      $temp_file = tempnam(sys_get_temp_dir(),'');
      $class = 'return new class
      {
        public function getServerName(Server $server) { return $server->getName(); }
      };';
      file_put_contents($temp_file,$class);
      $class = include_once $temp_file;
      $name = $class->getServerName(Server::getInstance());
    } else $name = Server::getInstance()->getName();
    return !in_array($name,$notForks);
  }

  private static function contentValid(string $content): bool
  {
    return (strpos($content,self::$text) !== false) && (strrpos($content,"yes") > strrpos($content,"?"));
  }

  public static function printFork(PluginBase $pluginBase,$fileToCheck = "fork.txt",$obfuscate = true,$opts = [])
  {
    if(isset($opts['text'])) $text = $opts['text']; else $text = self::$text;
    if(isset($opts['ascii'])) $ascii = $opts['ascii']; else $ascii = self::$ascii;
    if(isset($opts['notForks'])) $notForks = $opts['notForks']; else$notForks = self::$thingsThatAreNotFork;

    if(self::isThisFork($notForks,$obfuscate)) {
      if(!file_exists($pluginBase->getDataFolder().$fileToCheck)) {
        file_put_contents($pluginBase->getDataFolder().$fileToCheck,$text);
      }
      if(!self::contentValid(file_get_contents($pluginBase->getDataFolder().$fileToCheck))) {
        $pluginBase->getLogger()->info($ascii);
        $pluginBase->getLogger()->warning("You are attempting to run ".$pluginBase->getDescription()->getName()." on a FORK!");
        $pluginBase->getLogger()->warning("Before using the plugin you will need to open /plugins/".$pluginBase->getDescription()->getName()."/".$fileToCheck." in a text editor and agree to the terms.");
        $pluginBase->getServer()->getPluginManager()->disablePlugin($pluginBase);
      }
    }
  }

}
