<?php

namespace App\Includes;

use App\Includes\Template;
use App\Includes\Request;
use App\Includes\Response;

class Controller {

    protected Request $request;
    protected bool $isPlugin;
    protected int $status;

    public function __construct(Request $request, $isPlugin = false) {
        $this->request = $request;
        $this->isPlugin = $isPlugin;
        $this->status = 200;
    }
    protected function status($code): self {
        $this->status = $code;
        return $this;
    }
    protected function view(string $path, array $vars = []) {
        $filePath = $this->isPlugin ? 'plugins.' . $path : 'views.' . $path;
        $filePath = str_replace('.', '/', $filePath);
        $filePath .= '.php';
        
        if (!file_exists($filePath)) {
            throw new \Exception("Template file not found: {$filePath}");
        }
        
        $template = new Template();
        $template->setTemplate($filePath);
        $template->setVariables($vars);
        $template->addVariables('request', clone $this->request);
        $d = $template->render();

        $response = new Response($d, $this->status);
        return $response;
    }

    protected function redirect(string $path, $code = 302) {
        $response = new Response("", $code);
        $response->setRedirect($this->request->getHomePath() . $path);
        return $response;
    }

    protected function json(array|object $data) {
        $response = new Response($data, $this->status);
        return $response;
    }
}