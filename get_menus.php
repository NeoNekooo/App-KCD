<?php
$menus = App\Models\Menu::whereIn('title', ['Kepegawaian', 'Tugas Pegawai', 'KGB', 'Data Pegawai'])->get(['title', 'route', 'params', 'slug']);
$out = "";
foreach($menus as $m) {
    echo "TITLE: " . $m->title . " | ROUTE: " . $m->route . " | PARAMS: " . json_encode($m->params) . "\n";
}
echo "DONE\n";
