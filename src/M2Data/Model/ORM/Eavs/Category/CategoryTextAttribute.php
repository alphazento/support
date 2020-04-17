<?php

namespace ZentoSupport\M2Data\Model\ORM\Eavs\Category;

use Illuminate\Support\Collection;
use Zento\Catalog\Model\HasManyInAggregatedField;
use ZentoSupport\M2Data\Model\ORM\EavAttribute;

class CategoryTextAttribute extends \ZentoSupport\M2Data\Model\ORM\Eavs\AttrBase
{
    protected $table = 'catalog_category_entity_text';
}