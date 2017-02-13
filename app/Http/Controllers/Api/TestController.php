<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;

class TestController extends Controller
{
    public function index()
    {
        $data = array(
            'str' => 'hello world2'
        );

        return $this->response($data);
    }

}
