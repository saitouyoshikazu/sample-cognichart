<?php

namespace App\Expand\Validation;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Config;

class ExpValidation
{

    use ValidatesRequests;

    private $rules;
    private $attributes;

    public function __construct(array $parameterNames, string $validationFile = null)
    {
        $configFile = 'validation_rules';
        if (!empty($validationFile)) {
            $configFile = $validationFile;
        }
        if (!empty($parameterNames)) {
            $conf = Config::get($configFile);
            foreach ($parameterNames AS $parameterName) {
                $rule = null;
                if (!empty($conf[$parameterName])) {
                    $rule = $conf[$parameterName];
                }
                if (empty($rule)) {
                    throw new ExpValidationException("Isn't defined {$parameterName} in config/{$configFile}.php .");
                }
                $this->rules[$parameterName] = $rule;
                $this->attributes[$parameterName] = __($parameterName);
            }
        }
    }

    /**
     * To redirect to previous page after validation, request is necessary.
     * If you are using routing parameter, please use this method after execute $request->merge(['parametername' => 'routing parameter', ...]).
     *
     * @param   Illuminate\Http\Request $request    The request instance.
     */
    public function validateWithRedirect(Request $request)
    {
        $this->validate($request, $this->rules, [], $this->attributes);
    }

    /**
     * If you don't need ridirect to previous page after validation, please use this method.
     * If you are using routing parameter, please use this method after execute $inputs = $request->all(); $inputs['parametername'] = $routing parameter.
     *
     * @param   array   $parameters The hashmap of request parameters.
     * @return  Illuminate\Validation\Validator
     */
    public function validateOnly(array $parameters = [])
    {
        $validator = Validator::make($parameters, $this->rules, [], $this->attributes);
        return $validator;
    }

    public function mekeErrorMessage(array $errors)
    {
        if (empty($errors)) {
            return '';
        }
        return implode('\n', $errors);
    }

}
