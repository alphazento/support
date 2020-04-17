<?php

$seeder = new \ZentoSupport\M2Data\Seeders\AttributeSetSeeder();
$seeder->run();

$seeder = new \ZentoSupport\M2Data\Seeders\CategorySeeder();
$seeder->run();

$seeder = new \ZentoSupport\M2Data\Seeders\ProductSeeder();
$seeder->run();

$seeder = new \ZentoSupport\M2Data\Seeders\CategoryProductSeeder();
$seeder->run();

$seeder = new \ZentoSupport\M2Data\Seeders\CategoryProductSuperLinkSeeder();
$seeder->run();