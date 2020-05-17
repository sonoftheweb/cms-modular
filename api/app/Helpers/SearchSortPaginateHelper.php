<?php


namespace App\Helpers;


use Illuminate\Http\Request;

class SearchSortPaginateHelper
{
    public static function searchSortAndPaginate(Request $request, $query)
    {
        if ($request->has('search') && !empty($request->search))
            $query = SearchSortPaginateHelper::searchClauseBuilder($request, $query);

        $per_page = ($request->has('per_page')) ? $request->per_page : 10;
        return $query->paginate($per_page);
    }

    public static function searchClauseBuilder(Request $request, $query)
    {
        $table = $query->getModel()->getTable();

        switch ($table) {
            case 'users':
                $query->whereRaw("MATCH(name,email) AGAINST('" . $request->search . "')");
        }

        return $query;
    }
}
