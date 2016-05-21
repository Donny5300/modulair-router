<?php

	use App\Http\Controllers\Controller;

	return [
		'debug'            => true,
		/**
		 * Available RequestMethods
		 */
		'request_methods'  => [ '*', 'POST', 'GET' ],
		/**
		 * Path to the controllers
		 */
		'controller_path'  => 'Http/Controllers',
		/**
		 * Presets to render a Controller
		 */
		'template_builder' => [
			/**
			 * This controller will be used to extends
			 *
			 * Class Example extends Controller
			 */
			'base_controller'  => Controller::class,
			/**
			 * The namespace for the App
			 */
			'app_namespace'    => 'App',
			/**
			 * Validate the classes that will be added to the Uses in the
			 * TemplateBuilder
			 */
			'validate_classes' => false
		],
		/**
		 * Default templates
		 */
		'templates'        => [
			'controller' => __DIR__ . '/../templates/controller.txt',
			'view'       => [
				/**
				 * The view that will be created is depending on what controller method your missing. I.e.:
				 * on the HomeController we try to open the public function index(). There is no view for
				 * this controller. We use the `index` as key to grab the template
				 */
				'index'  => false,
				'create' => false,
				'edit'   => false,
			]
		],
		/**
		 * Extensions you use. For Blade use .blade.php
		 */
		'view_extension'   => '.twig',
		/**
		 * Default paths to controllers
		 */
		'system_routes'    => [
			/**
			 * Base path. http://domain.com/system
			 */
			'base_path'   => 'system',
			'namespaces'  => 'namespaces',
			'controllers' => 'controllers',
			'modules'     => 'modules',
			'methods'     => 'methods'
		],
		/**
		 * Add custom views without publishing the controllers
		 */
		'system_views'     => [
			'namespaces'  => false,
			'modules'     => false,
			'controllers' => false,
			'methods'     => false,
			'exceptions'  => false,
			'system'      => false
		],
		'merge_config'     => [
			'secure'        => true,
			'enable'        => true,
			'security_code' => 'test'
		]
	];