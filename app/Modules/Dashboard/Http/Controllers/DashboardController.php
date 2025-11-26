<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You can pass dynamic data here
        $data = [
            'pageTitle' => 'Dashboard Overview',
            'breadcrumbs' => [
                ['title' => 'Dashboard']
            ]
        ];

        return view('Dashboard::index', $data);
    }

    /**
     * Display analytics page.
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        $data = [
            'pageTitle' => 'Analytics',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => url('/dashboard')],
                ['title' => 'Analytics']
            ]
        ];

        return view('Dashboard::analytics', $data);
    }

    /**
     * Display the module welcome screen (keep for compatibility)
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Dashboard::welcome");
    }
}
