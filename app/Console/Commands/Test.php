<?php

namespace App\Console\Commands;

use App\Http\Library\JWT;
use App\Http\Library\JwtAuth;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

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
        $re = JwtAuth::verifyJwt("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOjEsInVzZXJOYW1lIjoieWYifQ.qJH-PfZgLEOQ3AhPyBtT09dL7OxDN5nXUU1A0dHn3oE");
        dd($re);
    }
}
