<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class zeroTier extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */
  
  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*
   * Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
   * lors de la création semi-automatique d'un post sur le forum community
   public static function getConfigForCommunity() {
      return "les infos essentiel de mon plugin";
   }
   */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }


  public static function getUserList($networkId){
    $returnApi = self::apiRequest('https://api.zerotier.com/api/v1/network/'.$networkId.'/member', 'GET');
    if($returnApi['code'] == 200){
      $returnApi = json_decode($returnApi['response'], true);
      log::add('zeroTier', 'info', 'Liste des utilisateurs : '.json_encode($returnApi));
      return $returnApi;
    }

  }


  public static function getNetworkList() {
    $returnApi = self::apiRequest('https://api.zerotier.com/api/v1/network', 'GET');
    if($returnApi['code'] == 200){
      $returnApi = json_decode($returnApi['response'], true);
      log::add('zeroTier', 'info', 'Liste des réseaux : '.json_encode($returnApi));
      foreach($returnApi as $network){
        if($network['config']['name'] == '') continue;
        $eqLogic = eqLogic::byLogicalId($network['id'], 'zeroTier');
        if(!is_object($eqLogic)){
          $eqLogic = new zeroTier();
          $eqLogic->setLogicalId($network['id']);
          $eqLogic->setEqType_name('zeroTier');
          $eqLogic->setIsEnable(1);
          $eqLogic->setIsVisible(1);
          $eqLogic->setName($network['config']['name']);
          $eqLogic->save();
  
        }

        $cmd = $eqLogic->getCmd(null, 'accessType');
        if (!is_object($cmd)) {
          $cmd = new zeroTierCmd();
          $cmd->setLogicalId('accessType');
          $cmd->setName('Type d\'accès');
          $cmd->setIsVisible(1);
          $cmd->setIsHistorized(0);
          $cmd->setType('info');
          $cmd->setSubType('string');
          $cmd->setEqLogic_id($eqLogic->getId());
          $cmd->save();
        }
        $network['config']['private'] == True ? $cmd->event('Privé') : $cmd->event('Public');

        $cmd = $eqLogic->getCmd(null, 'enableBroadcast');
        if (!is_object($cmd)) {
          $cmd = new zeroTierCmd();
          $cmd->setLogicalId('enableBroadcast');
          $cmd->setName('Broadcast Activé');
          $cmd->setIsVisible(1);
          $cmd->setIsHistorized(0);
          $cmd->setType('info');
          $cmd->setSubType('string');
          $cmd->setEqLogic_id($eqLogic->getId());
          $cmd->save();
        }
        $network['config']['enableBroadcast'] == True ? $cmd->event('Activé') : $cmd->event('Désactivé');
        
      }
    }
  }

  public static function createNewNetwork($typeNetwork, $nameNetwork) {
    $data = array(
      'config' => array(
                    'name' => $nameNetwork,
                    'private' => $typeNetwork == 'privateNetwork' ? True : False
                   )
      );
    $returnApi = self::apiRequest('https://api.zerotier.com/api/v1/network', 'POST', $data);
    return $returnApi;
  }


  public static function updateNetworkMember($networkId, $name, $nodeId) {
    log::add('zeroTier', 'info', 'Update Network Member : '.$networkId.' - '.$name.' - '.$nodeId);
    $returnApi = self::getUserList($networkId);
    log::add('zeroTier', 'info', 'Liste des UPDATES : '.json_encode($returnApi));
    $configTimes = array();
    foreach($returnApi as $member){
      $id = $member['id'];
      $creationTime = $member['config']['creationTime'];
      $configTimes[$id] = $creationTime;
    }
    $latestNodeId = array_search(max($configTimes), $configTimes);
    $latestMember = array_filter($returnApi, function ($member) use ($latestNodeId) {
    return $member['id'] === $latestNodeId;
    });
    $latestMember = reset($latestMember);
    log::add('zeroTier', 'info', 'Latest Member : '.json_encode($latestMember));
    $data = array(
      'name' => $name
      );
      log::add('zeroTier', 'info', 'ID : '.$latestMember['id']);
      $updateUrl = "https://api.zerotier.com/api/v1/network/{$networkId}/member/{$latestMember['config']['id']}";
    $returnApi = self::apiRequest($updateUrl, 'POST', $data);
    log::add('zeroTier', 'info', 'Update Network Member : '.$returnApi['response']);
  }


  public static function updateDeviceWithKnownId($networkId, $userId, $inputValue){
    $data = array(
      'name' => $inputValue
      );
    $updateUrl = "https://api.zerotier.com/api/v1/network/{$networkId}/member/{$userId}";
    $returnApi = self::apiRequest($updateUrl, 'POST', $data);
  }


  public static function deleteNetwork($networkId){
    $eqLogic = eqLogic::byLogicalId($networkId, 'zeroTier');
    $returnApi = self::apiRequest('https://api.zerotier.com/api/v1/network/'.$networkId, 'DELETE');
    log::add('zeroTier', 'info', 'Suppression du réseau : '.$returnApi['response']);
    if($returnApi['code'] == 200){
      if(is_object($eqLogic)){
        $eqLogic->remove();
      }
    }
  }

//   public static function apiRequest($string, $action) {
//     $apiKey = config::byKey('apiKeyZeroTier', __CLASS__);
//     if (empty($apiKey)) {
//         return;
//     }
//     $ch = curl_init($string);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $apiKey,
//     ));

//     if ($action == 'GET') {
//         $request = curl_exec($ch);
//         if (curl_errno($ch)) {
//             log::add(__CLASS__, 'error', 'Erreur cURL : ' . curl_error($ch));
//         } else {
//             log::add(__CLASS__, 'debug', 'Requête GET : ' . $request);
//         }
//     }
//     curl_close($ch);
// }


// public static function apiRequest($url, $method, $data = null) {
//   $apiKey = config::byKey('apiKeyZeroTier', __CLASS__);
//   if (empty($apiKey)) {
//       return;
//   }
//   $ch = curl_init($url);

//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//       'Content-Type: application/json',
//       'Authorization: Bearer ' . $apiKey,
//   ));

//   if ($method == 'GET') {
//   } elseif ($method == 'POST') {
//       curl_setopt($ch, CURLOPT_POST, 1);
//       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//   } elseif ($method == 'DELETE') {
//       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
//   } else {
//       log::add(__CLASS__, 'error', 'Méthode HTTP non supportée : ' . $method);
//       curl_close($ch);
//       return;
//   }

//   $response = curl_exec($ch);

//   if (curl_errno($ch)) {
//       log::add(__CLASS__, 'error', 'Erreur cURL : ' . curl_error($ch));
//   } else {
//       log::add(__CLASS__, 'debug', 'Requête ' . $method . ': ' . $response);
//   }

//   curl_close($ch);
//   return $response;
// }


public static function apiRequest($url, $method, $data = null) {
  $apiKey = config::byKey('apiKeyZeroTier', __CLASS__);
  if (empty($apiKey)) {
      return;
  }

  $ch = curl_init($url);

  $headers = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $apiKey,
  );

  $options = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
  );

  if ($method == 'POST') {
      $options[CURLOPT_POST] = 1;
      $options[CURLOPT_POSTFIELDS] = json_encode($data);
  } elseif ($method == 'DELETE') {
      $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
  } elseif ($method != 'GET') {
      log::add(__CLASS__, 'error', 'Méthode HTTP non supportée : ' . $method);
      curl_close($ch);
      return;
  }

  curl_setopt_array($ch, $options);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  log::add(__CLASS__, 'debug', 'Code HTTP : ' . $httpCode);
  if ($httpCode >= 400) {
      log::add(__CLASS__, 'error', 'Erreur HTTP : ' . $httpCode);
  }
  if (curl_errno($ch)) {
      log::add(__CLASS__, 'error', 'Erreur cURL : ' . curl_error($ch));
  } else {
      log::add(__CLASS__, 'debug', 'Requête ' . $method . ': ' . $response);
  }

  curl_close($ch);
  return array(
      'code' => $httpCode,
      'response' => $response,
  );
  //return $response;
}



  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*     * **********************Getteur Setteur*************************** */
}

class zeroTierCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
  }

  /*     * **********************Getteur Setteur*************************** */
}
