<?php

namespace App\Modules\DemoFrontend\Http\Controllers;

use Illuminate\Http\Request;

class DemoFrontendController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("DemoFrontend::welcome");
    }
}
