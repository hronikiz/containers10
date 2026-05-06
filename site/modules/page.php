<?php

class Page {
    private $template;

    public function __construct($template) {
        $this->template = $template;
    }

    public function Render($data) {
        $html = file_get_contents($this->template);

        foreach ($data as $key => $value) {
            $html = str_replace("{{".$key."}}", $value, $html);
        }

        return $html;
    }
}