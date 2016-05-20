<?php namespace Donny5300\ModulairRouter\Requests;

use App\Http\Requests\Request;

class UpdateMethodRequest extends Request
{
	public function authorize()
	{
		return true;
	}


	public function rules()
	{
		return [
			'title'          => 'required',
			'controller_id'  => 'required',
			'request_method' => 'required'
		];
	}
}