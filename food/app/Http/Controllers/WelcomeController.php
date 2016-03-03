<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use \Api\Server\AdvertServer\Banner;
use App\Http\Controllers\ApiController;

class WelcomeController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @return Response
     */
    public function hello()
    {
        $bannerS = new Banner();
		$data = $bannerS->hello();
        return $data;
    }


    /**
     *
     * @param Request $request
     * @return Response
     */
    public function intro()
    {
        $bannerS = new Banner();
		$data = $bannerS->intro();
        return $data;
    }

}
