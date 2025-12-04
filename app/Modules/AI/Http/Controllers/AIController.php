<?php

namespace App\Modules\AI\Http\Controllers;

use Illuminate\Http\Request;

class AIController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("AI::welcome");
    }
}
