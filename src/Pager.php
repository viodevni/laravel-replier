<?php

namespace Viodev;

use Illuminate\Http\Request;

class Pager
{
    public $limit = null;
    public $order = null;
    public $last_id = null;

    public function __construct(Request $request, $limit, $order)
    {
        $request->validate([
            'limit' => ['nullable', 'integer', 'between:1,'.$limit],
            'order' => ['nullable', 'in:asc,desc'],
            'last_id' => ['nullable', 'integer']
        ]);

        $this->limit = $request->input('limit', $limit);
        $this->order = $request->input('order', $order);
        $this->last_id = $request->input('last_id', null);
    }
}