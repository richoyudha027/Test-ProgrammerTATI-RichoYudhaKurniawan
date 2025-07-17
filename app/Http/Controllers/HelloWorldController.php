<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    public function index(Request $request)
    {
        $n = $request->input('n', null);

        if ($n !== null) {
            $n = (int) $n;
        }
        
        if (!$n || $n < 1) {
            if ($request->ajax()) {
                return response()->json(['sequences' => []]);
            }
            return view('index', ['sequences' => [], 'n' => null]);
        }

        $sequences = $this->generateSequence($n);

        if ($request->ajax()) {
            return response()->json(['sequences' => $sequences]);
        }

        return redirect('/')->with([
            'sequences' => $sequences,
            'n' => $n,
            'show_result' => true
        ]);
    }

    /**
     * Generate the sequence.
     *
     * @param int $n
     * @return array
     */
    private function generateSequence(int $n)
    {
        $res = [];

        for ($i = 1; $i <= $n; $i++) {
            if ($i % 4 === 0 && $i % 5 === 0) {
                $res[] = 'helloworld';
            } elseif ($i % 4 === 0) {
                $res[] = 'hello';
            } elseif ($i % 5 === 0) {
                $res[] = 'world';
            } else {
                $res[] = (string)$i;
            }
        }

        return $res;
    }
}
