<?php

	abstract class basicEntity extends CoreLib implements EntityInterface {
            private $aOriginalData = [];
            private $sBaseCaller;
            
            public $aObjectProperties = [];
            
            public abstract function update();
    
            public abstract function save();

            public function setBaseCaller($sCallerName){
                $this->sBaseCaller = $sCallerName;
            }

            private function getBaseCaller(){
                return $this->sBaseCaller;
            }
            
            public function load($aData){
                foreach($aData as $sParamName => $mValue){
                    $sParamName = $this->parseParamName($sParamName);
                 
                    $this->aOriginalData[$sParamName] = $mValue;
                    $this->aObjectProperties[] = $sParamName;
                    
                    $sSetterName = 'set' . $sParamName;
                    
                    call_user_func([$this, $sSetterName], $mValue);
                }
            }
            
            private function parseParamName($sName){
                $aParamNameModifiers = ['-','_'];
                
                if($this->strposa($sName, $aParamNameModifiers)){
                    $sName = str_replace($aParamNameModifiers, "|", $sName);
                    
                    $aName = explode("|", $sName);
                    
                    $sName = '';
                    
                    foreach($aName as $sNamePart){
                        $sName .= ucfirst($sNamePart);
                    }
                }
                else{
                    $sName = ucfirst($sName);
                }
                
                return $sName;
            }
            
            /*
             * getters and setters router for entity properties
             * if caller class contains method of name get/setNameOfProperty, then it's result is returned instead
             * WARNING
             * by overwriting entity getter and/or setter, caller class method is called AS A STATIC, not initialized method.
             * i.e. called method does not have access to object scope ($this) of its parent class
             * 
             * RC_TODO:
             *  - figure out a way to allow access to initialized modules from scope of any CoreLib extended class
             */
            
            /**
             * getters router for entity properties
             * if caller class contains method of name getNameOfProperty, then it's result is returned instead
             * WARNING
             * by overwriting entity getter ,caller class method is called AS A STATIC, not initialized method.
             * i.e. called method does not have access to object scope ($this) of its parent class
             * 
             * @param string $sName
             * @return mixed
             */
            public function __get($sName) {
                $sName = $this->parseParamName($sName);
                
                $sGetterName = 'get' . $sName;
                
                if(!empty($this->getBaseCaller())
                   && method_exists($this->getBaseCaller(), $sGetterName)){
                    return call_user_func([$this->getBaseCaller(), $sGetterName], call_user_func([$this, $sGetterName]));
                }
                
                return call_user_func([$this, $sGetterName]);
            }
            
            /**
             * setters router for entity properties
             * if caller class contains method of name setNameOfProperty, then it's result is returned instead
             * WARNING
             * by overwriting entity setter ,caller class method is called AS A STATIC, not initialized method.
             * i.e. called method does not have access to object scope ($this) of its parent class
             * 
             * @param string $sName
             * @param mixed $mParam
             * @return void
             */
            public function __set($sName, $mParam) {
                $sName = $this->parseParamName($sName);
                
                $sSetterName = 'set' . $sName;
                
                if(!empty($this->getBaseCaller())
                   && method_exists($this->getBaseCaller(), $sSetterName)){
                    return call_user_func([$this->getBaseCaller(), $sSetterName]);
                }
                
                return call_user_func([$this, $sSetterName], $mParam);
            }
            
            public function __toString() {
                return print_r($this->aOriginalData, true);
            }
        }

?>
