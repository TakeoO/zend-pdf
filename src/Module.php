<?php

namespace Takeoo\Pdf;

/**
 * Class Module
 * @package Takeoo\Pdf
 */
class Module
{
  public function getConfig ()
  {
    $moduleConfig = require __DIR__ . '/../config/module.config.php';
    $serviceConfig = require __DIR__ . '/../config/service.config.php';

    return array_merge_recursive($moduleConfig, $serviceConfig);
  }
}