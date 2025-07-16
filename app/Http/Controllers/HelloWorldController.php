<?php

namespace App\Http\Controllers;

use App\Models\HelloWorld;
use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    public function index(Request $request)
    {
        $n = $request->input('n', null);

        if ($n !== null) {
            $n = (int) $n;
        }

        if(!$n || $n < 1){
            if($request->ajax()){
                return response()->json(['sequences' => []]); 
            }
            return view('index', ['sequences' => [], 'n' => null]);
        }

        $sequences = HelloWorld::generateSequence($n);

        if ($request->ajax()) {
            return response()->json(['sequences' => $sequences]);
        }

        return redirect('/')->with([
            'sequences' => $sequences, 
            'n' => $n,
            'show_result' => true
        ]);
    }
}
