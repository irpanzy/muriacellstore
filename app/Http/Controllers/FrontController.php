<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Handphone;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    protected $frontServices;

    public function __construct(FrontService $frontServices) // DIP depedency injection
    {
        $this->frontServices = $frontServices;
    }

    public function home()
    {
        $data = $this->frontServices->getFrontPageData();
        // dd($data);
        return view('front.home', $data);
    }

    public function details(Handphone $handphone) // model binding
    {
        return view('front.details', compact('handphone'));
    }

    public function category(Category $category)
    {
        return view('front.category', compact('category'));
    }
}
