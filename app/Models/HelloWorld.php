<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelloWorld extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @param int $n
     * @return array
     */
    public static function generateSequence(int $n)
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