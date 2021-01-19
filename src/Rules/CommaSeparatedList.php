<?php

namespace Viodev\Rules;

use Illuminate\Contracts\Validation\Rule;

class CommaSeparatedList implements Rule
{
    const INT = 'int';

    private $type;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type = self::INT)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!is_string($value)) return false;

        foreach(explode(',', $value) as $part){
            if(preg_match('/\s/', $part)) return false;
            if($this->type == self::INT && !is_numeric($part)) return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('replies.comma_separated_list');
    }
}