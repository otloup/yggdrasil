<?php

class users extends CoreLib {
  
    private $oUser = null;
    private $oDB = null;
    
    public function __construct() {
        $this->oUser = $this->getEntity('user');
        $this->oDB = $this->getLib('phpPdo');
    }
    
    
    public function getById($iUserId){
        $sSql = "SELECT "
                . "u.id, u.login, u.email, u.register_date"
                . ",u.status, ud.name, ud.surname"
                . "ud.phone, up.level"
                . "LEFT JOIN"
                . "user_data ud ON ud.user_id = u.id"
                . "LEFT JOIN"
                . "user_privileges up ON up.user_id = u.id"
                . "WHERE"
                . "u.id = :userid";
        
        return $this->oDB->execute($sSql, phpPdo::RESULT_ENTITY, [
            ':userid'   =>  $iUserId
        ], 'user');
    }
    
    public function getAll(){
        $sSql = "SELECT "
                . "u.id, u.login, u.email, u.register_date"
                . ",u.status, ud.name, ud.surname"
                . ",ud.phone, dp.value as level"
                . " FROM"
                . " users u"
                . " LEFT JOIN"
                . " user_data ud ON ud.user_id = u.id"
                . " LEFT JOIN"
                . " user_privileges up ON up.user_id = u.id"
                . " INNER JOIN"
                . " dic_privileges dp ON dp.id = up.level";  
        
        return $this->oDB->execute($sSql, phpPdo::RESULT_ENTITIES, null, 'user');
    }
    
    public function getLevel($mOriginalValue){
        switch($mOriginalValue){
            case user::USER_PRIVILEGE_ALL:
                return 'użytkownik';
                
            case user::USER_PRIVILEGE_LOGGED:
                return 'zarejestrowany użytkownik';
                
            case user::USER_PRIVILEGE_ADMIN:
                return 'użytkownik z prawami administratorskimi';
        }
    }
    
    public function addNewUser($sEmail, $sPassword, $iPrivilege, $aData){
        $sPassword = md5(SALT.$this->clearString($sPassword));
        
        $this->oDB->execute(""
                . "INSERT INTO "
                . " users ("
                . "login, password, email, status"
                . ") VALUES (:email, :password, :email, :status)", null, [
                    ':email'   =>  [$sEmail, PDO::PARAM_STR]
                    ,':password'    =>  [$sPassword, PDO::PARAM_STR]
                    ,':status'    =>  [DEFAULT_USER_STATUS, PDO::PARAM_INT]
                ]);
        
        $iUserId = $this->oDB->getLastInsertId('user_id_seq');
        
        if(!$iUserId){
            return false;
        }
        
        return $this->addUserData($iUserId, $aData) & $this->addUserPrivileges($iUserId, $iPrivilege);
    }
    
    private function addUserData($iUserId, $aUserData){
        return $this->oDB->execute(""
                . "INSERT INTO"
                . " user_data("
                . "user_id, name, surname, phone"
                . ") VALUES ("
                . ":user, :name, :surname, :phone"
                . ")", null, [
                    ':user' =>  [$iUserId, PDO::PARAM_INT]
                    ,':name' =>  [$aUserData['name'], PDO::PARAM_STR]
                    ,':surname' =>  [$aUserData['surname'], PDO::PARAM_STR]
                    ,':phone'   =>  [$aUserData['phone'], PDO::PARAM_INT]
                ]);
    }
    
    private function addUserPrivileges($iUserId, $iUserPrivilege){
        $iPrivilege = $this->oDB->execute("SELECT id FROM dic_privileges WHERE value = :privilege", phpPdo::RESULT_SINGLE, [
            ':privilege'    =>  [$iUserPrivilege, PDO::PARAM_INT]
        ]);
                
        if($iPrivilege){
            return $this->oDB->execute(""
                    . "INSERT INTO"
                    . " user_privileges ("
                    . "user_id, level"
                    . ") VALUES ("
                    . ":user, :level"
                    . ")", null, [
                        ':user' =>  [$iUserId, PDO::PARAM_INT]
                        ,':level'   =>  [$iPrivilege, PDO::PARAM_INT]
                    ]);
        }
        
        return false;
    }
    
    public function findByEmail($sEmail){
        return $this->oDB->execute("SELECT id FROM users WHERE email = :email OR login = :email", phpPdo::RESULT_SINGLE, [':email'=>[$sEmail, PDO::PARAM_STR]], true);
    }
    
}

?>