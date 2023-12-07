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

<table class="table table-condensed tablesorter" id="table_healthneato">
	<thead>
		<tr>
			<th>{{Réseau}}</th>
			<th>{{ ZeroTierId }}</th>
			<th>{{Status Network}}</th>
			<th>{{Nom de la box sur le réseau}}</th>
			<th>{{Rejoindre Network}}</th>
			<th>{{Supprimer Network}}</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($eqLogics as $eqLogic) {
			//$userList = zeroTier::getUserList($eqLogic->getLogicalId('networkId'));
			$networkId = $eqLogic->getLogicalId();
			echo '<tr><td width="40%">' . $eqLogic->getHumanName(true) . '</td>';
			echo '<td>' . $networkId . '</td>';
			echo '<td><span class="label label-info">' . $eqLogic->getCmd(null, 'accessType')->execCmd() . '</span></td>';
			echo '<td><input class="inputNode" data-networkId="'.$networkId .'"></input></td>';
			echo '<td><button class="btn btn-success joinNetwork" data-networkId="'.$networkId .'">Rejoindre</button></td>';
			echo '<td><button class="btn btn-danger deleteNetwork" data-networkId="'.$networkId .'">Supprimer</button></td>';
      echo '</tr>';
		}
		?>
	</tbody>
</table>



<script>

var btns = document.querySelectorAll('.joinNetwork');
var btnsDelete = document.querySelectorAll('.deleteNetwork');


btns.forEach(function(btn) {
    btn.addEventListener('click', function() {
			let networkId = this.getAttribute('data-networkid');
			let inputValue = document.querySelector('.inputNode[data-networkid="' + networkId + '"]').value;
			if(inputValue == ''){
				$('#div_alert').showAlert({ message: '{{Veuillez saisir un nom pour la box}}', level: 'danger' })
				return
			}
			
				$.ajax({
						type: 'POST',
						url: 'plugins/zeroTier/core/ajax/zeroTier.ajax.php',
						data: {
							action: 'joinNetwork',
							networkId : networkId,
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
							
							$('#div_alert').showAlert({ message: '{{Reseau rejoint}}', level: 'success' })
						}
					})
    });
});


btnsDelete.forEach(function(btn) {
    btn.addEventListener('click', function() {
			let networkId = this.getAttribute('data-networkid');
				$.ajax({
						type: 'POST',
						url: 'plugins/zeroTier/core/ajax/zeroTier.ajax.php',
						data: {
							action: 'deleteNetwork',
							networkId : networkId

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
							
							$('#div_alert').showAlert({ message: '{{Reseau supprimé}}', level: 'success' })
							location.reload()
						}
					})
    });
});





</script>