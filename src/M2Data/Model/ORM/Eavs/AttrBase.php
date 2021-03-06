<?php

namespace ZentoSupport\M2Data\Model\ORM\Eavs;

use ZentoSupport\M2Data\Model\ORM\EavAttribute;

class AttrBase  extends \ZentoSupport\M2Data\Model\ORM\Magento2Model
{
    protected $primaryKey = 'value_id';
    public function codedesc() {
        return $this->belongsTo(EavAttribute::class, 'attribute_id', 'attribute_id');
    }

    public function getValueAttribute($value) {
        return $value;
    }

    public function getDisabledAttribute($value) {
        return false;
    }

    public function getSortAttribute($value) {
        return 0;
    }
}