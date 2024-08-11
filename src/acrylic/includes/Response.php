<?php

namespace App\Includes;

use App\Includes\Template;

class Response {
    private string $data = '';
    private int $status = 200;
    private bool $isRedirect = false;
    private string $redirectPath = "";

    public function __construct(mixed $data = '', $code = 200) {
        if (gettype($data) == 'array' || gettype($data) == 'object') {
            $this->json($data, $code);
        } else {
            $this->text($data, $code);
        }
    }
    public function text(string $data, $code = 200): void {
        $this->status = $code;
        $this->data = $data;
    }

    public function json(array|object $data, $code = 200): void {
        $this->status = $code;
        $this->data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function error($code, $msg = ""): Response {
        $errorTemplatePath = "views.error";
        $errorTemplatePath = str_replace('.', '/', $errorTemplatePath);
        $errorTemplatePath .= '.php';
        $template = new Template();
        $template->setTemplate($errorTemplatePath);
        $template->setVariables(['code' => $code, 'message' => $msg]);
        if ($template->existsTemplate()) {
            $this->data = $template->render();
        } else {
            $this->data = "";
        }
        $this->status = $code;
        return $this;
    }

    public function setRedirect($path): Response {
        $this->redirectPath = $path;
        $this->isRedirect = true;
        return $this;
    }

    public function render(): void {
        if ($this->isRedirect) {
            http_response_code($this->status);
            header('Location: ' . $this->redirectPath);
            exit;
        } else {
            http_response_code($this->status);
            echo $this->data;
            exit;
        }
    }
}