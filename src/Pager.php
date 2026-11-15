<?php

namespace Viodev;

use Illuminate\Http\Request;

class Pager
{
    public int $limit;
    public string $order;
    public ?int $lastId;

    public function __construct(Request $request, int $limit, string $order)
    {
        $request->validate([
            'limit' => ['integer', 'between:1,'.$limit],
            'order' => ['in:asc,desc'],
            'last_id' => ['integer']
        ]);

        $this->limit = (int) $request->input('limit', $limit);
        $this->order = $request->input('order', $order);
        $lastId = $request->input('last_id', null);
        $this->lastId = $lastId === null ? null : (int) $lastId;
    }
}
