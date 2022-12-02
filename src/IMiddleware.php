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


interface IMiddleware {    
    /**
     * handle
     *
     * @return mixed
     */
    public function handle($params);
}
