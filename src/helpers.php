<?php

function paginate($model, $orderBy, $recordPerPage, $currenPage, $link)
{
    $count = $model->count();
    
    $perPage = $recordPerPage;
    $page = ($currenPage == 0 ? 1 : $currenPage);
    $offset = ($page - 1) * $perPage;
    $lastPage = ceil($count / $perPage);
    $prev = ($page != $offset + 1) ? $page - 1 : null;
    $next = ($page != $lastPage) ? $page + 1 : null;
    $lastRecordPerPage = ($page != $lastPage) ? ($page * $perPage) : ($count - $offset) + $offset;

    $items = $model->skip($offset)
                ->take($perPage)
                ->orderBy($orderBy)
                ->get();

    return [
        'items' => $items,
        'pager' => [
            'total' => $count,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => $lastRecordPerPage,
            'path'  => $link,
            'first_page_url' => $link. '?page=1',
            'prev_page_url' => (!$prev) ? $prev : $link. '?page=' .$prev,
            'next_page_url' => (!$next) ? $next : $link. '?page=' .$next,
            'last_page_url' => $link. '?page=' .$lastPage
        ]
    ];
}
