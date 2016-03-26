<?php

class globalUtil {
    public function forgeJSONResponse($mResponse) {
        return json_encode($mResponse);
    }

    public function validateEmail($sEmailAddress) {
        return filter_var($sEmailAddress, FILTER_VALIDATE_EMAIL);
    }

    public function getLangText($sIndex) {
        if (empty($sIndex)) {
            return '';
        }

        global $aLang;
        if (empty($aLang[$sIndex])) {
            print 'no index found: ' . $sIndex;
            return false;
        }
        return $aLang[$sIndex];
    }

    public function clearUrl($sUrl) {
        $sUrl = strtolower($sUrl);

        preg_match('#([a-z0-9-_./:]*)#', $sUrl, $aMatches);
        return $aMatches[1];
    }
}

?>
