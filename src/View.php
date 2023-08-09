<?php
namespace Base;

class View
{
    private $templatePath = '';
    private $data = [];
    private $twig;
    
    public function __construct()
    {
        $this->templatePath = PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'app/View';
    }
    
    public function assign(string $name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function render(string $tpl, $data = []) : string
    {
        $this->data += $data;
        //extract($data);
        ob_start();
        include $this->templatePath . DIRECTORY_SEPARATOR . $tpl;
        return ob_end_flush();
    }
    
    public function __get($varName)
    {
        return $this->data[$varName] ?? null;
    }
    
    public function renderTwig(string $tpl, $data = [])
    {
        if (!$this->twig) {
            $loader = new \Twig\Loader\FilesystemLoader($this->templatePath);
            $this->twig = new \Twig\Environment($loader);
        }
        return $this->twig->render($tpl, $data);
    }
}

