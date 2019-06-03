<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Validator;

class Controller extends BaseController
{
    /**
     * 表单验证
     * 
     */
    public function validator(array $params, array $rules)
    {
        $validator = Validator::make($params, $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            throw new LogicException(
                current($errors)[0],
                \Utils\ErrorCode::VALIDATOR_INVALID,
                $errors
            );
        }
    }
}
