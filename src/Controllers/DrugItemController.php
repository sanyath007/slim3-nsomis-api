<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\DrugItem;

class DrugItemController extends Controller
{
    private function createDrugListsSQL($icodes)
    {
        $drugLists = implode("', '", $icodes);

        return $drugLists;
    }

    public function getAll($req, $res, $args)
    {
        $drugItems = DrugItem::all();

        return $res->withJson([
            'drugItems' => $drugItems,
        ]);
    }
}
