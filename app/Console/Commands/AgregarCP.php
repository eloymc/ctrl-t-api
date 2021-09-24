<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CodigosPostalesController;

class AgregarCP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctrlt:agregarcp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subir CP a la base de datos';

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
        $cp = CodigosPostalesController::subirCP();
        dd($cp);
    }
}
