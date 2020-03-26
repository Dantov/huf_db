<?php
/**
 */


class Controller
{

    /**
     * @string $title
     * задаётся в контроллере или в файле страницы
     * */
    public $title = '';

    /**
     * @string $layout
     * Название шаблона задаётся в контроллере
     * */
    public $layout = '';

    /**
     * @string $layoutPath
     * дефолтный путь к шаблону
     * */
    public $layoutPath = _rootDIR_."views/layouts/";

    /**
     * @array $varBlock
     * $blocks = []; нужен для передачи переменных из вида в шаблон
     * */
    public $varBlock = [];

    public $blocks = [];
    /**
     * @var array
     * содержит имена открытых блоков
     * каждый вызов endBlock() берет имя первого элемента массива
     * создает в $blocks такой же и помещает туда ob_get_clean()
     */
    protected $blockNames = [];

    /**
     * @string - имя контроллера
     */
    public $controllerName;


    public function action()
    {
    }

    public function setTitle()
    {
        if ( empty( $this->title ) )  $this->title = "Powered by Dantov's Framework";
    }

    /**
     * setLayout()
     * Устанавливает шаблон из конфиг файла приложения, или из контроллера, если был задан в нем
     * return void
     * @param string $layout
     */
    protected function setLayout($layout='')
    {
        if ( !empty($layout) && is_string($layout) ) $this->layout = $layout;

        if ( empty( $this->layout ) ) $this->layout = 'default';
    }

    public function render($filename, $vars=[])
    {
        if ( !empty($vars) && is_array($vars) ) extract($vars);

        ob_start();
        {
            require_once _rootDIR_ . 'views/' . $this->controllerName . '/includes/'. $filename .'.php';
            $content = ob_get_contents();
        }
        ob_end_clean();

        return $this->renderLayout($content);
    }

    protected function renderLayout($content)
    {
        $this->setLayout();
        $this->setTitle();

        $this->layoutPath .= $this->layout . '.php';
        if ( !file_exists( $this->layoutPath ) )
        {
            throw new NotFoundException("Шаблон <i>" . $this->layout . "</i> не найден в /views/layouts/");
        }

        return require_once $this->layoutPath;
    }

    public function startBlock($name)
    {
        if ( empty($name) ) return;
        $name .= "";
        $this->blockNames[$name] = $name;
        ob_start();
    }
    public function endBlock()
    {
        $name = array_shift($this->blockNames);
        if ( empty($name) ) return;
        $this->blocks[$name] = ob_get_clean();
    }

    public function beforeAction()
    {

    }

    public function afterAction()
    {

    }

}