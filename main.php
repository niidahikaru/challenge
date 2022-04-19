<?php
  require_once __DIR__.'/vendor/autoload.php';

  function run()
  {
    print("start\n");

    $instance = new App\Challenge();
    $instance->exec();

    print("finish\n");
  }

  run();