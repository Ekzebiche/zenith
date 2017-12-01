<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Templater {

	/**
	 * @var Twig_Loader_Filesystem - объект подгрузчика
	 */
	protected $loader;
	/**
	 * @var Twig_Environment - объект шаблонизатора
	 */
	protected $twig;

	public function __construct($params) {
		$this->loader = new Twig_Loader_Filesystem($params['views_folder_path']);
		$this->twig = new Twig_Environment($this->loader);

		// добавление расширений для Twig
		$this->twig->addExtension(new Twig_Extensions_Extension_Text());
		$this->twig->addGlobal("session", $_SESSION);
	}

	public function render($template, $data){
		return $this->twig->render($template.'.twig', $data);
	}

}