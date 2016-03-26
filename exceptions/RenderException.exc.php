<?php

class RenderException extends Exception {

    const NO_PAGE = 'no_page';

    private $oRequest;

    public function __construct($sMessage) {

        $this->oRequest = new RequestParser();

        renderer::getLib('globalUtil', 'static');

        switch ($sMessage) {
            case self::NO_PAGE :
                header('Location: localhost');
                //globalUtil::redirect(URL_CREATE_NEW_PAGE, array('page_name'	=>	$this->oRequest->sParsedRequest));
                break;
        }
    }

}

?>
