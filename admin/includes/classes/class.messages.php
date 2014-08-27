<?php

// messageStack is too inflexible for certain use cases

class Messages {

    protected $messages;
    protected $namespace;

    public function __construct($namespace = 'messages') {
        $this->namespace = $namespace;
        $this->messages = array();
        if (isset($_SESSION[$namespace])) {
            $this->messages = $_SESSION[$namespace];
        }
    }

    public function addMessage($message) {
        $this->messages[] = $message;
        $this->toSession();
    }

    protected function toSession() {
        $_SESSION[$this->namespace] = $this->messages;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function reset() {
        $this->messages = array();
        $this->toSession();
    }

    public function isEmpty() {
        return count($this->messages) == 0;
    }

}
