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

if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$eqLogics = eqLogic::byType('zeroTier');
?>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" aria-controls="home" role="tab" data-toggle="tab" ><i class="fas fa-arrow-circle-left"></i></a></li>
    
    <?php
    $first = true;
    foreach ($eqLogics as $eqLogic) {
        $tabId = 'networktab' . $eqLogic->getId();
        //$tabName = $eqLogic->getHumanName(true);
        $tabName = $eqLogic->getName();
        $activeClass = $first ? 'class="active"' : ''; 
    ?>
    
    <li role="presentation" <?= $activeClass ?>><a href="#<?= $tabId ?>" aria-controls="<?= $tabId ?>" role="tab" data-toggle="tab"> <?= $tabName ?></a></li>
    
    <?php
        $first = false;
    }
    ?>
</ul>

<div class="tab-content">
    <?php
    $first = true;
    foreach ($eqLogics as $eqLogic) {
        $tabId = 'networktab' . $eqLogic->getId();
        $activeClass = $first ? 'active' : ''; 
    ?>

    <div role="tabpanel" class="tab-pane <?= $activeClass ?>" id="<?= $tabId ?>">
       
        <table class="table table-condensed tablesorter" id="table_healthneato">
						<thead>
							<tr>
								<th>{{Status Network}}</th>
								<th>{{Utilisateur}}</th>
								<th>{{Identifiant}}</th>
								<th>{{IP Assignement}}</th>
								<th>{{Nom}}</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
                <tbody>
                  <?php
                    $userList = zeroTier::getUserList($eqLogic->getLogicalId('networkId'));
										
										foreach ($userList  as $user) {
											   echo '<tr><td><span class="label label-info">' . $eqLogic->getCmd(null, 'accessType')->execCmd() . '</span></td>';
											   echo '<td><span value="' . $user['id'] . '">' . ($user['name'] == '' ? 'Pas de nom' : $user['name']) . '</span></td><';
											   echo '<td><span value="' . $user['config']['id']. '">' . $user['config']['id'] . '</span></td>';
											   echo '<td><span value="' . $user['config']['ipAssignments'][0]. '">' . $user['config']['ipAssignments'][0] . '</span></td>';

											   echo '<td><input value="' . $user['name']. '" data-userId="'.$user['config']['id'].'"></input></td>';
											   echo '<td><button class="btn btn-success majDevice" data-userId="'.$user['config']['id'].'" data-networkId="'.$eqLogic->getLogicalId().'">Mettre a jour</button></td>';
											   echo '<td><button class="btn btn-danger openLocalNetwork" data-userId="'.$user['config']['id'].'" data-networkId="'.$eqLogic->getLogicalId().'">Ouvrir Reseau Local</button></td></tr>';
	
										 }
                  ?>
                </tbody>
              </table>
        </table>
    </div>

    <?php
        $first = false;
    }
    ?>
</div>

<script>

var btnsMaj = document.querySelectorAll('.majDevice');
var btnsOpenLocal = document.querySelectorAll('.openLocalNetwork');




btnsMaj.forEach(function(btn) {
    btn.addEventListener('click', function() {
			let userId = this.getAttribute('data-userId');
			let networkId = this.getAttribute('data-networkId');
			let inputValue = document.querySelector('[data-userId="' + userId + '"]').value;
			if(inputValue == ''){
				$('#div_alert').showAlert({ message: '{{Veuillez saisir un nom pour le device}}', level: 'danger' })
				return
			}
			
				$.ajax({
						type: 'POST',
						url: 'plugins/zeroTier/core/ajax/zeroTier.ajax.php',
						data: {
							action: 'updateDevice',
							networkId : networkId,
							userId : userId,
							inputValue : inputValue

						},
						dataType: 'json',
						error: function (request, status, error) {
							handleAjaxError(request, status, error)
						},
						success: function (data) {
							if (data.state != 'ok') {
								$('#div_alert').showAlert({ message: data.result, level: 'danger' })
								return
							}
							var inputElement = document.querySelector('[data-userId="' + userId + '"]');
							var jsonResponse = JSON.parse(data.result);
							inputElement.value = jsonResponse.config.name;
							location.reload();
							
							$('#div_alert').showAlert({ message: '{{Device mis à jour}}', level: 'success' })
						}
					})
    });
});


btnsOpenLocal.forEach(function(btn) {
    btn.addEventListener('click', function() {
			let userId = this.getAttribute('data-userId');
			let networkId = this.getAttribute('data-networkId');
				$.ajax({
						type: 'POST',
						url: 'plugins/zeroTier/core/ajax/zeroTier.ajax.php',
						data: {
							action: 'openLocalNetwork',
							networkId : networkId,
							userId : userId,

						},
						dataType: 'json',
						error: function (request, status, error) {
							handleAjaxError(request, status, error)
						},
						success: function (data) {
							if (data.state != 'ok') {
								$('#div_alert').showAlert({ message: data.result, level: 'danger' })
								return
							}					
							$('#div_alert').showAlert({ message: '{{Reseau Ouvert}}', level: 'success' })
						}
					})
    });
});

</script>