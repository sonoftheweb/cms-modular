<?php


namespace App\Helpers;


use Illuminate\Http\Request;

class SearchSortPaginateHelper
{
    public static function searchSortAndPaginate(Request $request, $query)
    {


        $per_page = ($request->has('per_page')) ? $request->per_page : 10;
        return $query->paginate($per_page);
    }
}
