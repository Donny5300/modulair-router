<?php namespace Donny5300\ModulairRouter\Requests;

use App\Http\Requests\Request;

class StoreNamespaceRequest extends Request
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