<?php

namespace WalkerChiu\RoleSimple\Models\Forms;

use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class UserFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        $data['id'] = (string) $request->id;
        $this->getInputSource()->replace($data);

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return parent::attributes();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        return [
            'id'      => ['required','string','exists:'.config('wk-core.table.user').',id'],
            'roles'   => 'array',
            'roles.*' => ['required','string','exists:'.config('wk-core.table.role-simple.roles').',id']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'      => trans('php-core::validation.required'),
            'id.string'        => trans('php-core::validation.string'),
            'id.exists'        => trans('php-core::validation.exists'),
            'roles.array'      => trans('php-core::validation.array'),
            'roles.*.required' => trans('php-core::validation.required'),
            'roles.*.string'   => trans('php-core::validation.string'),
            'roles.*.exists'   => trans('php-core::validation.exists')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                $data['id'] == 1
                && !in_array(1, $data['roles'])
            )
                $validator->errors()->add('id', trans('php-core::validation.not_allowed'));
        });
    }
}
