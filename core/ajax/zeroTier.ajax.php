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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */
    ajax::init();


    if(init('action') == 'installZeroTier'){
      $checkCommand = 'which zerotier-one';
      $result = shell_exec($checkCommand);
      if (!empty($result)) {
        log::add('zeroTier', 'info', 'ZeroTier est déjà installé : '.$result);
        ajax::success($result);
      } else {
        $result = shell_exec('curl -s https://install.zerotier.com | sudo bash');
        log::add('zeroTier', 'info', 'Installation de ZeroTier : '.$result);
        ajax::success($result);
      }      
    }


    if(init('action') == 'getNetworkList'){
      zeroTier::getNetworkList();
      ajax::success();
    }


    if(init('action') == 'createNewNetwork'){
      $returnApi = zeroTier::createNewNetwork(init('typeNetwork'), init('nameNetwork'));
      if($returnApi['code'] == 200){
        zeroTier::getNetworkList();
        ajax::success($returnApi['response']);
      }
     
    }

    if(init('action') == 'joinNetwork'){
      $networkId = init('networkId');
      $name = init('inputValue');
      $output = shell_exec('sudo zerotier-cli join '.$networkId);
      sleep(5);
      $nodeInfo = shell_exec('sudo zerotier-cli info');
      log::add('zeroTier', 'info', 'Node Info : '.$nodeInfo);
      log::add('zeroTier', 'info', 'Join Network : '.$output);
      zeroTier::updateNetworkMember($networkId, $name, $nodeInfo);
      ajax::success();
    }

    if(init('action') == 'deleteNetwork'){
      $networkId = init('networkId');
      zeroTier::deleteNetwork($networkId);
     // $name = init('inputValue');
      ajax::success();
    }


    if(init('action') == 'updateDevice'){
      $returnApi = zeroTier::updateDeviceWithKnownId(init('networkId'), init('userId'), init('inputValue'));
      if($returnApi['code'] == 200){
        ajax::success($returnApi['response']);
      }
    }




    throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
    /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
