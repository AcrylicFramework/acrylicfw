<?php

namespace App\Includes;

class Template {
    private $variables = [];
    private $templateFile;

    public function setTemplate($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    public function addVariables($key, $value)
    {
        $this->variables[$key] = $value;
    }

    public function setVariables(array $vars)
    {
        $this->variables = $vars;
    }

    public function existsTemplate(): bool {
        return file_exists(Config::appDir() . '/' . $this->templateFile);
    }

    public function getPath(): string {
        return Config::appDir() . '/' . $this->templateFile;
    }

    public function render()
    {
        $this->setOriginalVariables();
        extract($this->variables);
        ob_start();
        include($this->getPath());
        $content = ob_get_clean();
        return $content;
    }

    private function setOriginalVariables() {
        $this->addVariables('html', 'htmlspecialchars');
    }
}