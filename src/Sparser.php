<?php

namespace Viodev;

use Illuminate\Http\Request;
use Viodev\Rules\CommaSeparatedList;

class Sparser
{
    public $fields = [];

    public function __construct(Request $request)
    {
        $request->validate([
            'fields' => [new CommaSeparatedList(CommaSeparatedList::STRING)]
        ]);

        if($request->has('fields')) $this->fields = explode(',', $request->input('fields'));
    }
}