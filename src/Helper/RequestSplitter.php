<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestSplitter
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function splitData(Request $request)
    {
        $queryString = $request->query->all();

        $sort = array_key_exists('sort', $queryString)
            ? $queryString['sort']
            : null;
        unset($queryString['sort']);

        $currentPage = array_key_exists('page', $queryString)
            ? $queryString['page']
            : 1;
        unset($queryString['page']);

        $itemsPerPage = array_key_exists('itemsPerPage', $queryString)
            ? $queryString['itemsPerPage']
            : 10;
        unset($queryString['itemsPerPage']);

        $filters = $queryString;

        return [
            'filters'      => $filters,
            'sort'         => $sort,
            'currentPage'  => $currentPage,
            'itemsPerPage' => $itemsPerPage
        ];
    }
}