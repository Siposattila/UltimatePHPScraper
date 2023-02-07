<?php

abstract class AbstractDatabase
{
    protected abstract function getConnection();
    protected abstract function prepare(): void;
    protected abstract function execute(): void;
}
