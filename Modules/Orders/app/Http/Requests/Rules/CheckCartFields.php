<?php
namespace Modules\Orders\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckCartFields implements Rule
{
    public function passes($attribute, $value)
    {
        foreach ($value['goods'] as $good) {
            if (!isset($good['id']) || !isset($good['count']) || !isset($good['price'])) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'The cart goods must have id, count, and price.';
    }
}
