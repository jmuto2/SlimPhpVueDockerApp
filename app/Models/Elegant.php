<?php

namespace App\Models;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

use Illuminate\Database\Eloquent\Model;

class Elegant extends Model
{
	protected $errors;
	
	public function validate($request, $rules)
	{
		foreach ($rules as $field => $rule) {
			
			try {
				$rule->setName(ucfirst($field))->assert($request->getParam($field));
			} catch (NestedValidationException $e) {
				$this->errors[$field] = $e->getMessages();
			}
		}
		
		$_SESSION['errors'] = $this->errors;
		
		return $this;
	}
	
	public function failed()
	{
		return !empty($this->errors);
	}
	
	public function signinRules()
	{
		return [
			'email' => v::notEmpty(),
			'password' => v::notEmpty(),
		];
	}
}