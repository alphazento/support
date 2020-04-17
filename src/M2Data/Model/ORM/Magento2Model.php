<?php

namespace ZentoSupport\M2Data\Model\ORM;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryIntAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryTextAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryVarcharAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryDatetimeAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryDecimalAttribute;

class Magento2Model extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'magento2';
}