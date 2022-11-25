<?php

namespace App\Form\Model;

use App\Entity\Config;
use App\Entity\DateLog;

class ConfigModel
{
    public ?string $deduct;
    public ?array $deductGroups;
    public ?array $monthData;
    public ?string $month;
    public ?string $fileName;

    public static function createDBConfig(Config $deduct, Config $deductGroups, Config $months)
    {
        $configModel = new ConfigModel();
        $configModel->deduct = $deduct->getValue();
        $configModel->deductGroups = unserialize($deductGroups->getValue());
        $configModel->monthData = unserialize($months->getValue());
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

    public static function mapToMonths(Config $config, ConfigModel $configModel, ?string $fileName)
    {
        $months = unserialize($config->getValue());
        if($fileName) {
            $months[$configModel->month]  = $fileName;
        }
        $config->setValue(serialize($months));
        return $config;
    }
}