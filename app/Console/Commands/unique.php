<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class unique extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unique';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shop = User::where('id', 1)->first();
        $response = $shop->api()->rest('GET', '/admin/api/2021-04/orders.json',$request->all());
        dd($response);
    }
}
