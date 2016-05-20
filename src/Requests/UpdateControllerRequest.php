<?php namespace Donny5300\ModulairRouter\Requests;

use App\Http\Requests\Request;

class UpdateControllerRequest extends Request
{
	public function authorize()
	{
		return true;
	}


	public function rules()
	{
		return [
			'title' => 'required'
		];
	}
}