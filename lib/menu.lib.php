<?php

class menu extends CoreLib {

    const CONTENT_TRIGGER_OPTION_PARAMETRIC = 1;
    const CONTENT_TROGGER_OPTION_SUBPAGE = 2;
    const MENU_OPTION_MAIN = 1;
    const MENU_OPTION_SUB = 2;
    const DEFAULT_SUBOPTION_PARAM_NAME = 'option';

    private $oDb = null;
    private $oDic = null;
    private $sParamName = self::DEFAULT_SUBOPTION_PARAM_NAME;

    public function __construct() {
        $this->oDb = $this->getLib('phpPdo');
        $this->oDic = $this->getLib('dic');
    }

    public function getOptions($sParamName = '') {
        $this->sParamName = empty($sParamName) ? $this->sParamName : $sParamName;

        $aOptions = $this->oDb->execute('SELECT name, url, content_trigger, content_source_table, content_source_column FROM dic_menu ORDER BY lp', phpPdo::RESULT_HASH);

        $aOptions = $this->constructAllOptions($aOptions);

        return $aOptions;
    }

    private function constructAllOptions($aOptions) {
        $aParsedOptions = [];
        foreach ($aOptions as $option) {
            $aParsedOptions[] = $this->constructOption($option);
        }
        return $aParsedOptions;
    }

    private function constructOption($aOption, $iLevel = self::MENU_OPTION_MAIN, $iSubpageUrlType = self::CONTENT_TRIGGER_OPTION_PARAMETRIC) {
        $aNewOption = [];

        switch ($iLevel) {
            case self::MENU_OPTION_MAIN :
                $aNewOption = $this->constructMainMenuOption($aOption);
                break;

            case self::MENU_OPTION_SUB :
                $aNewOption = $this->constructSubMenuOption($aOption, $iSubpageUrlType);
                break;
        }

        return $aNewOption;
    }

    private function constructMainMenuOption($aOption) {
        return [
            'name' => $this->oDic->get($aOption['name'])
            , 'url' => constant(trim(strtoupper($aOption['url'])))
            , 'suboptions' => $this->supplySuboptions($aOption)
        ];
    }

    private function supplySuboptions($aOption) {
        $aNewSuboptions = [];

        if (!empty($aOption['content_source_table']) && !empty($aOption['content_source_column'])) {
            $aSuboptions = $this->getSuboptions($aOption['content_source_table'], $aOption['content_source_column']);

            if (!empty($aSuboptions)) {
                foreach ($aSuboptions as $suboption) {
                    $aNewSuboptions[] = $this->constructOption($suboption, self::MENU_OPTION_SUB);
                }
            }
        }

        return $aNewSuboptions;
    }

    private function getSuboptions($sTableName, $sTableColumn) {
        $aSuboptions = $this->oDb->execute('SELECT ' . $sTableColumn . ' AS name FROM ' . $sTableName . '', phpPdo::RESULT_HASH);
        return $aSuboptions;
    }

    private function constructSubMenuOption($aOption, $iSubpageUrlType) {
        $sUrl = '';
        $sBaseUrl = urlencode($aOption['name']);

        switch ($iSubpageUrlType) {
            case self::CONTENT_TRIGGER_OPTION_PARAMETRIC :
                $sUrl = '?' . $this->sParamName . '=' . $sBaseUrl;
                break;

            case self::CONTENT_TROGGER_OPTION_SUBPAGE :
                $sUrl = '/' . $sBaseUrl;
                break;
        }

        return [
            'name' => $aOption['name']
            , 'url' => $sUrl
            , 'suboptions' => $this->supplySuboptions($aOption)
        ];
    }

}
