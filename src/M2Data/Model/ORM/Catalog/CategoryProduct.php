<?php

namespace ZentoSupport\M2Data\Model\ORM\Catalog;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryIntAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryTextAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryVarcharAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryDatetimeAttribute;
use ZentoSupport\M2Data\Model\ORM\Eavs\Category\CategoryDecimalAttribute;

class CategoryProduct extends \ZentoSupport\M2Data\Model\ORM\Magento2Model
{
    protected $table = 'catalog_category_product';
}