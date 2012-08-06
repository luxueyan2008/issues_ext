<?php

class HttpResponseTest {

    public $status;
    public $text;

    public function __construct($status, $text) {
        $this->status = $status;
        $this->text = $text;
    }

    public function is_ok() {
        return $this->status == 200;
    }

}

?>
