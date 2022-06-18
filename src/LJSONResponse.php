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

class LJSONResponse implements IResponse {
    public $data;
    public $status = 200;
    public $headers = [];

    public function __construct($data = null, $status = 200, $headers = []) {
        $this->__data = $data;
        $this->__status = $status;
        $this->__headers = $headers;
    }

 
    public function handle() {
        http_response_code($this->__status);
        header('Content-Type: application/json');
        foreach ($this->__headers as $key => $value) {
            header($key . ': ' . $value);
        }
        echo json_encode($this->__data);
    }
}


