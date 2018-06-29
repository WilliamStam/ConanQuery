<?php


class template {
	private $config = array(), $vars = array();

	function __construct($template, $folder = "templates/") {
		$this->f3 = Base::instance();
		$this->config['debug'] = FALSE;
		$this->config['cache_dir'] = $this->f3->get('CACHE') ? $this->f3->get('TEMP') : FALSE;
		//$this->config['cache_dir'] = false;

		//test_array($this->config['cache_dir']);




		$this->vars['folder'] = $folder;

		$this->template = $template;

		$this->timer = new \timer();


	}

	function __destruct() {
		$page = $this->template;
		//test_array($page);

		$this->timer->stop("Template: ".$page);
	}

	public function __get($name) {
		return $this->vars[$name];
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}

	private function default_vars() {

		if (isset($this->vars['page']['class'])){
			$class_filename = substr(strrchr(($this->vars['page']['class']), '\\'), 1);
//			$this->vars['page']['class'] = strtolower(substr($class_filename,0,-10));


		}


		if (isset($this->vars['page']['method'])){
			$method_filename = $this->vars['page']['method'];
//			$this->vars['page']['method'] = strtolower(substr($method_filename,6));
		}







		$this->vars['_v'] = $this->f3->get("VERSION");

	}


	public function load() {


		return $this->render_template();
	}

	public function render_template() {
		$this->default_vars();

		//debug($this->vars);
		$folder = $this->vars['folder'];





		//debug($this->vars);

		$twig = $this->twigify($folder);


		//test_array(array("template"=>$this->template,"vars"=>$this->vars));

		return $twig->render($this->template, $this->vars);


	}

	private function twigify($folder, $options = array()) {

		if ( !is_array($folder) ) {
			$folder = array(
				$folder,
			);
		}



		$loader = new Twig_Loader_Filesystem($folder, dirname(__DIR__));


		$options['autoescape'] = FALSE;
		$options['cache'] = $this->f3->get('CACHE') ? $this->f3->get('TEMP') : FALSE;


		$twig = new Twig_Environment($loader, $options);
		$twig->addExtension(new Twig_Extension_Debug());

		$twig->addFilter(new Twig_SimpleFilter('toAscii', function($string) {
			$string = toAscii($string);

			return ($string);
		}));

		$twig->addTest(new Twig_Test('numeric', function ($str) {
			return is_numeric($str);
		}));



		return $twig;
	}


	public function render_string($folder = "", $twig_options = array()) {
		$this->default_vars();

		if ($this->vars['template']){
			$twig = $this->twigify($folder, $twig_options);

			$template = $twig->createTemplate($this->vars['template']);
			$return = $template->render($this->vars);
			return $return;
		}

	}


	public function output($output = FALSE) {
		$return = $this->load();
		$this->f3->OUTPUT['TEMPLATE'] = $return;


		if ( $output ) {
			echo $return;
		} else {
			return $return;
		}


	}
	public function renderPage($obj="",$method="") {






		if (is_object($obj)){
			$class = get_class($obj);
			$classname = (new \ReflectionClass($obj))->getShortName();

			$this->vars['folder'] =substr($class,0,strpos($class,"controllers")).$this->vars['folder'];




//			debug($this->vars['folder']);

			if ($this->vars['page']){

				$cachebuster = $this->f3->get("VERSION");



				$template_folder = str_replace(array("\\","/"),DIRECTORY_SEPARATOR,str_replace("controllers\\page\\", "views\\", $class));

				$assets_folder = str_replace(array("\\","/"),DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR.substr($class,0,strpos($class,"controllers"))."assets");


				$js_folder = $assets_folder . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . $classname;
				$css_folder = $assets_folder . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $classname;

				$this->vars['assets'] = str_replace(DIRECTORY_SEPARATOR,"/",$assets_folder);
				$this->vars['section'] = $classname;
				$this->vars['sub'] = $method;



				if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$template_folder.".twig"))){
					$this->vars['page']['template'] = $classname.".twig";

					$this->vars['folder'] = array(
						str_replace(array("\\","/"),DIRECTORY_SEPARATOR,dirname($template_folder)),
						str_replace(array("\\","/"),DIRECTORY_SEPARATOR,$this->vars['folder'])
					);

				} else if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$template_folder.DIRECTORY_SEPARATOR.$method.".twig"))){
					$this->vars['page']['template'] = $method.".twig";

					$this->vars['folder'] = array(
						$template_folder,
						$this->vars['folder']
					);

				}

				if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$js_folder.".js"))){
					$this->vars['page']['template_js'] = $classname.".{$cachebuster}.js";
				} else if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$js_folder.DIRECTORY_SEPARATOR.$method.".js"))){
					$this->vars['page']['template_js'] = $classname."/".$method.".{$cachebuster}.js";
				}
				if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$css_folder.".css"))){
					$this->vars['page']['template_css'] = $classname.".{$cachebuster}.css";
				} else if (file_exists(str_replace(array("/"), DIRECTORY_SEPARATOR, dirname (__DIR__).DIRECTORY_SEPARATOR.$css_folder.DIRECTORY_SEPARATOR.$method.".css"))){
					$this->vars['page']['template_css'] = $classname."/".$method.".{$cachebuster}.css";
				}



			}

		}

		//debug($this->vars);

//		$this->vars['template_'] = $templatename;

		$return = $this->load();
		$this->f3->OUTPUT['RENDERED'] = $return;

	}


}
