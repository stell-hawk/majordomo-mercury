<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'mercury/mercury.class.php');
$mercury_module = new mercury();


$cmd_rec = SQLSelectOne("SELECT value FROM mercury_config where parametr='EVERY'");
$checkEvery=$cmd_rec['value']*60;

 
echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=60; // poll every 5 minutesz
while (1)
{
   setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
   if ((time()-$latest_check)>$checkEvery) {
    $latest_check=time();
    //echo date('Y-m-d H:i:s').' Polling devices...\n';
    $mercury_module->processCycle();
   }
   if (file_exists('./reboot') || IsSet($_GET['onetime'])){
      $db->Disconnect();
      exit;
   }
   sleep(1);
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));
