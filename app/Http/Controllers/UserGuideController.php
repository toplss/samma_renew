<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TbCompanyModel;

class UserGuideController extends Controller
{
    //

    public function CompanyGuide()
    {
        $items = TbCompanyModel::select(['title', 'content', 'content2', 'content3', 'content4', 'content5'])
        ->find(1);


        return view('customer_service.company', compact('items')); 
    }


    public function Agreement()
    {
        $items = TbCompanyModel::select(['title', 'content', 'content2', 'content3', 'content4', 'content5'])
        ->find(2);

        return view('customer_service.agreement', compact('items')); 
    }



    public function PrivatPolicy()
    {
        $items = TbCompanyModel::select(['title', 'content', 'content2', 'content3', 'content4', 'content5'])
        ->find(5);

        return view('customer_service.private', compact('items')); 
    }
}
