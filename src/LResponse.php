<?php 

/*
* LightRouter
* Nabeel Ali Hashmi (Icon, TheIconicThing)
*
* @version 1.0.0
* @license MIT
* @author Nabeel Ali Hashmi
* @link https://iconiccodes.com
*/
namespace IconicCodes\LightRouter;


class LResponse implements IResponse {
    public $data;
    public $headers = [];
    public $status = 200;

    public function __construct($data = null, $status = 200, $headers = []) {
        $this->__data = $data;
        $this->__status = $status;
        $this->__headers = $headers;
    }

    public function handle() {
        http_response_code($this->__status);
        foreach ($this->__headers as $key => $value) {
            header($key . ': ' . $value);
        }
        echo $this->__data;
    }
}

