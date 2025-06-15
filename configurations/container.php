<?php

use RobertWesner\SimpleMvcPhp\Configuration;
use RobertWesner\SimpleMvcPhp\Handler\StderrThrowableHandler;
use RobertWesner\SimpleMvcPhp\Handler\ThrowableHandlerInterface;

Configuration::CONTAINER
    ::instantiate(ThrowableHandlerInterface::class, StderrThrowableHandler::class);
