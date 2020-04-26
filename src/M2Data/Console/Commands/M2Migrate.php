<?php
/**
 *
 * @category   Framework support
 * @package    M2Data
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace ZentoSupport\M2Data\Console\Commands;

use Artisan;
use Zento\Kernel\Facades\PackageManager;

class M2Migrate extends \Zento\Kernel\PackageManager\Console\Commands\Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento2:migrate';

    protected $description = "";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start migrating attribute sets ...');
        $seeder = new \ZentoSupport\M2Data\Seeders\AttributeSetSeeder();
        $seeder->setCommand($this)->run();
        $this->info('Migrating attribute sets finished.');

        $this->info('Start migrating categories ...');
        $seeder = new \ZentoSupport\M2Data\Seeders\CategorySeeder();
        $seeder->setCommand($this)->run();
        $this->info('Migrating categories finished.');

        $this->info('Start migrating products ...');
        $seeder = new \ZentoSupport\M2Data\Seeders\ProductSeeder();
        $seeder->setCommand($this)->run();
        $this->info('Migrating products finished.');

        $this->info('Start migrating category-product bindings...');
        $seeder = new \ZentoSupport\M2Data\Seeders\CategoryProductSeeder();
        $seeder->setCommand($this)->run();
        $this->info('Migrating category-product bindings finished.');

        $this->info('Start migrating category-product super link bindings...');
        $seeder = new \ZentoSupport\M2Data\Seeders\CategoryProductSuperLinkSeeder();
        $seeder->run();
        $this->info('Migrating category-product super link bindings finished.');
    }
}
