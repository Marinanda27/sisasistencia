<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Menuoptioncategory;
use Illuminate\Support\Facades\DB;

class MenuHelper
{
    public static function generarMenu($usertype_id)
    {
        return Menuoptioncategory::with([
            'children' => function ($query) use ($usertype_id) {
                $query->with(['options' => function ($q) use ($usertype_id) {
                    $q->whereHas('permissions', function ($p) use ($usertype_id) {
                        $p->where('usertype_id', $usertype_id);
                    })->with(['permissions' => function ($p) use ($usertype_id) {
                        $p->where('usertype_id', $usertype_id);
                    }]);
                }])->whereHas('options.permissions', function ($q) use ($usertype_id) {
                    $q->where('usertype_id', $usertype_id);
                })->orderBy('order');
            },
            'options' => function ($query) use ($usertype_id) {
                $query->whereHas('permissions', function ($p) use ($usertype_id) {
                    $p->where('usertype_id', $usertype_id);
                })->with(['permissions' => function ($p) use ($usertype_id) {
                    $p->where('usertype_id', $usertype_id);
                }])->orderBy('order');
            }
        ])
        ->where('position', 'V')
        ->whereNull('menuoptioncategory_id')
        ->where(function ($query) use ($usertype_id) {
            $query->whereHas('options.permissions', function ($q) use ($usertype_id) {
                $q->where('usertype_id', $usertype_id);
            })->orWhereHas('children.options.permissions', function ($q) use ($usertype_id) {
                $q->where('usertype_id', $usertype_id);
            });
        })
        ->orderBy('order')
        ->get();
    }
}
