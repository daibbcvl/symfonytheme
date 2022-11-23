<?php

namespace App\Form\Model;

use App\Entity\Config;
use App\Entity\DateLog;

class ConfigModel
{
    public ?string $deduct;
    public ?array $deductGroups;

    public static function createDBConfig(Config $deduct, Config $deductGroups)
    {
        $configModel = new ConfigModel();
        $configModel->deduct = $deduct->getValue();
        $configModel->deductGroups = unserialize($deductGroups->getValue());
        return $configModel;
    }

    public static function mapToDeduct(Config $config, ConfigModel $configModel)
    {
        $config->setValue($configModel->deduct);
        return $config;
    }
    public static function mapToDeductGroups(Config $config, ConfigModel $configModel)
    {
        $config->setValue(serialize($configModel->deductGroups));
        return $config;
    }
}