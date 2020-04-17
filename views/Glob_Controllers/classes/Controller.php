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

    public $phpFilesPack = [];
    public $jsPack = [];
    public $jsFilesPack = [];

    public $HEAD = 'head';
    public $BEGINBody = 'beginBody';
    public $ENDBody = 'endBody';

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


    public function includePHPFile($fileName, $position='')
    {
        if ( empty($fileName) || !is_string($fileName) ) return;
        if ( !$position ) $position = $this->ENDBody;

        $primalDir = _viewsDIR_ . $this->controllerName . '/includes/';
        if ( !file_exists($primalDir.$fileName) )
            throw new Error('Файл "' . $fileName . '" не найден в папе подключений текущего контроллера.',3);

        $php['position'] = $position;
        $php['php'] = $primalDir.$fileName;
        $this->phpFilesPack[] = $php;
    }

    /**
     * @param $js
     * @param array $options
     * @param string $position
     */
    public function includeJS($js, $options=[], $position='')
    {
        if ( empty($js) || !is_string($js) ) return;
        if ( !is_array($options) )
            throw new Error('Опции должен быть массивом - ',2);
        if ( !$position ) $position = $this->ENDBody;

        $script['js'] = $js;
        $script['position'] = $position;

        $optionsStr = '';
        foreach ($options as $key => $option) {
            if ( $key === 'id' ) $optionsStr .= ' id="'.$option.'" ';
            switch ($option)
            {
                case 'defer':
                    $optionsStr .= 'defer ';
                    break;
                case 'async':
                    $optionsStr .= 'async ';
                    break;
            }
        }
        $script['options'] = $optionsStr;

        $this->jsPack[] = $script;
    }
    /**
     * @param $fileName
     * @param array $options
     * @param string $position
     * @throws Error
     */
    public function includeJSFile($fileName, $options=[], $position='')
    {
        if ( empty($fileName) ) return;
        if ( !$position ) $position = $this->ENDBody;
        if ( !is_array($options) )
            throw new Error('Опции должен быть массивом - ',2);

        $primalDir = _viewsDIR_ . $this->controllerName . '/js/';
        $httpPath = _views_HTTP_ . $this->controllerName . '/js/';

        if ( !file_exists($primalDir.$fileName) )
            throw new Error('Файл "' . $fileName . '" не найден в папе скриптов текущего контроллера.',3);

        $script['position'] = $position;
        $script['src'] = $httpPath.$fileName;

        $optionsStr = '';
        foreach ($options as $key => $option) {
            if ( $key === 'id' ) $optionsStr .= ' id="'.$option.'" ';
            switch ($option)
            {
                case 'defer':
                    $optionsStr .= 'defer ';
                    break;
                case 'async':
                    $optionsStr .= 'async ';
                    break;
                case 'timestamp':
                    $script['src'] .= "?v=" . time();
                    break;
            }
        }
        $script['options'] = $optionsStr;

        $this->jsFilesPack[] = $script;
    }

    public function head() {
        $method = explode('::',__METHOD__)[1];

    }
    public function beginBody() {
        $method = explode('::',__METHOD__)[1];

    }
    public function endBody()
    {
        $method = explode('::',__METHOD__)[1];


        foreach ($this->phpFilesPack as $pack)
        {
            if ( $method !== $pack['position'] ) continue;
            require $pack['php'];
        }

        foreach ($this->jsPack as $pack)
        {
            if ( $method !== $pack['position'] ) continue;
            echo '<script '.$pack['options'].'>'.$pack['js'].'</script>';
        }

        foreach ($this->jsFilesPack as $pack)
        {
            if ( $method !== $pack['position'] ) continue;
            echo '<script '.$pack['options'].' src="'.$pack['src'].'"></script>';
        }
    }
}