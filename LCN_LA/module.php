<?
//Modul steuert ein Lämpchen eines LCN Moduls ähnlich einem Relais

class LCNLA extends IPSModule {
  public function Create() {
    parent::Create();
    //$this->RegisterPropertyBoolean('Status', 0);
    $this->RegisterPropertyInteger('idLCNInstance', 0);
    $this->RegisterPropertyInteger('LaempchenNr', 0);
  }
  public function ApplyChanges() {
    parent::ApplyChanges();
    
    
    $this->RegisterPropertyInteger('idSourceInstance', 0); //Id der zu beobachtenden Variable
    $this->RegisterPropertyInteger('LaempchenNr', 0);	  
    $status=$this->RegisterPropertyBoolean('Status', FALSE);
    $statusID = $this->RegisterVariableBoolean('Status',FALSE);//
    //$DBLClickDetectId = $this->RegisterVariableBoolean('DBLClickDetect', 'DoppelKlickErkannt','', 1); //Boolean anlegen, der bei erkennung gesetzt wird 
    //$lastUpdID = $this->RegisterVariableInteger('LASTUPD','last_updated','~UnixTimestamp',3);//Hilfsvariable anlegen
    
//Inhalt für Skript erzeugen, das bei Erkennung ausgeführt wird 
/*  $stringInhalt="<?\n IPS_LogMessage('DBLClick_Script','Starte User_Script.....................'); \n SetValueBoolean($DBLClickDetectId, FALSE); \n//Start your code here\n\n?>"; */
    //Skript anlegen
//    $scriptID = $this->RegisterScript('SCRIPT', 'DBLClickScript',$stringInhalt,2);
//    $presentId = $this->RegisterVariableInteger('PRESENT_SINCE', 'Anwesend seit', '~UnixTimestamp', 3);
//    $absentId = $this->RegisterVariableInteger('ABSENT_SINCE', 'Abwesend seit', '~UnixTimestamp', 3);
//    $nameId = $this->RegisterVariableString('NAME', 'Name_Device', '', 2);
//    IPS_SetIcon($this->GetIDForIdent('DBLClickDetect'), 'Motion');
//    IPS_SetIcon($this->GetIDForIdent('SCRIPT'), 'Keyboard');
    IPS_SetIcon($this->GetIDForIdent('Status'), 'Bulb');
    
    /*if($this->ReadPropertyInteger('idSourceInstance')!=0){  
    	$this->RegisterTimer('OnVariableUpdate', 0, 'DBLC_Check($id)');
    }*/
  }
 /* protected function RegisterTimer($ident, $interval, $script) {
    $id = @IPS_GetObjectIDByIdent($ident, $this->InstanceID);
    if ($id && IPS_GetEvent($id)['EventType'] <> 1) {
      IPS_DeleteEvent($id);
      $id = 0;
    }
    if (!$id) {
      $id = IPS_CreateEvent(0);
      IPS_SetEventTrigger($id, 0, $this->ReadPropertyInteger('idSourceInstance')); //Bei Update von der gewählten Variable 
      IPS_SetEventActive($id, true);             //Ereignis aktivieren
      IPS_SetParent($id, $this->InstanceID);
      IPS_SetIdent($id, $ident);
    }
    IPS_SetName($id, $ident);
    IPS_SetHidden($id, true);
    IPS_SetEventScript($id, "\$id = \$_IPS['TARGET'];\n$script;");
    if (!IPS_EventExists($id)) throw new Exception("Ident with name $ident is used for wrong object type");
  }*/
 
  public function Control() {
    if(IPS_SemaphoreEnter('LCNLA', 1000)) {
//ID und Wert von "Status" ermitteln
      $statusID=$this->ReadPropertyBoolean('Status');
      $status=GetValue($statusID);
//ID der Instanz ermitteln   
      $lcn_instID=$this->ReadPropertyInteger('idLCNInstance');	
//Lämpchen Nr. ermitteln
      $lampNo=$this->ReadPropertyInteger('LaempchenNr');
//Auswertung 
      IPS_LogMessage('LCNLA-'.$inst_name,"Starte.....................");
//Überprüfen Status und sende Befehl an LCN_Instanz
      if($status){
        LCN_SetLamp($lcn_instID,$lampNo,'E');  
      }
      else{
        LCN_SetLamp($lcn_instID,$lampNo,'A');  
      }
        
        
            
      IPS_SemaphoreLeave('DBLClick');
     } 
     else {
      IPS_LogMessage('LCNLA-', 'Semaphore Timeout');
    }
   }
} 
?>

