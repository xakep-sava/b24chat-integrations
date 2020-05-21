<?php
/**
 * Copyright © B24 Chat. All rights reserved.
 *
 * PHP version 7
 *
 * @category B24Chat
 * @package  B24Chat_Integration
 * @author   Vladimir Savrov <i@sava.team>
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
  ComponentRegistrar::MODULE,
  'B24Chat_Integration',
  __DIR__
);
