<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GetRewardController extends Controller
{
    /**
     * 领奖的限制，24小时内只能领一次，365天内只能领7次
     * @return mixed
     */
    public function index()
    {

    }
}
