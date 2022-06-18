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

class LRedirectReponse implements IResponse {
    public $url;
    public $code;
    function __construct($url, $code = 301) {
        $this->__url = $url;
        $this->__code = $code;
    }

    public function handle() {
        if (!headers_sent()) {
            header("Location: $this->__url", true, $this->__code);
        } else {
            echo '<script>window.location.href="' . $this->__url . '";</script>';
        }
    }
}
