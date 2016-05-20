<?php namespace Donny5300\ModulairRouter\Controllers;

use App\Http\Controllers\Controller;
use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Requests\StoreNamespaceRequest;
use Donny5300\ModulairRouter\Requests\UpdateNamespaceRequest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;

class AssetController extends Controller
{
	public function __construct( Filesystem $fs )
	{
		$this->fs = $fs;
	}

	public function css()
	{
		$file     = __DIR__ . '/../../assets/style.css';
		$content  = $this->fs->get( $file );
		$response = new Response(
			$content, 200, [
				'Content-Type' => 'text/css',
			]
		);

		return $response;
	}


}