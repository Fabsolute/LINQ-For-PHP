<?php
use Fabs\LINQ\LINQ;

include_once '../vendor/autoload.php';

class Bot
{
    public $item_count = 0;
    public $bot_name = 'a';

    public function __construct($item_count, $bot_name)
    {
        $this->item_count = $item_count;
        $this->bot_name = $bot_name;
    }
}

$array = [];
for ($i = 0; $i < 10000; $i++) {
    $array[] = new Bot(0, 'bot' . $i);
}

$time_start = microtime(true);
$bot_list = LINQ::from($array)
    ->orderBy(function ($bot) {
        return $bot->item_count;
    });
$bot_list->groupBy(function ($bot) {
    return $bot->item_count;
})
    ->firstOrDefault();
//foreach ($bot_list as $wtf) {
//    break;
//}
//for ($i = 0; $i < 200; $i++) {
//    $selected_bot = $bot_list->firstOrDefault();
//    $selected_bot->item_count += 5;
//
//}

echo '<pre>';

$time_end = microtime(true);

$execution_time = ($time_end - $time_start);

echo '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';