<?php

namespace Modules\Icommerceopenpay\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Isite\Jobs\ProcessSeeds;

class IcommerceopenpayDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run()
  {
    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Icommercepayzen\Database\Seeders",
      "seeds" => ["IcommerceopenpayModuleTableSeeder", "IcommerceopenpaySeeder"]
    ]);
  }
}
