<?php

namespace App\Modules\Subsription\Http\Controllers;

use Illuminate\Http\Request;

class SubsriptionController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Subsription::welcome");
    }
}
