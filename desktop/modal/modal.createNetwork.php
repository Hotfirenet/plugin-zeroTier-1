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
?>

<div class="container" style="display:flex;flex-direction:column;">
    <div class="form-group" style="width:30%;">
      <label>{{Nom du Reseau}}</label>
        <input class="form-control" data-l1key="configuration" data-l2key="networkName" id="nameNetwork"/>
    </div>
		<div class="form-group" style="width:30%;">
      <label>{{Confidentialité du Reseau}}</label>
     <select class="form-control" id="typeNetwork" data-l1key="configuration" data-l2key="networkType">
				<option value="privateNetwork">{{Privé}}</option>
				<option value="publicNetwork">{{Public}}</option>
			</select>
    </div>
		<div class="form-group" style="width:30%;">
      <label>{{Description}}</label>
        <input class="form-control" data-l1key="configuration" data-l2key="networkDescription"/>
    </div>
    <div class="form-group" style="width:30%;display:none;" id="idNetwork">
      <label>{{Id Reseau}}</label>
        <span class="form-control" data-l1key="configuration" data-l2key="idNetwork"></span>
    </div>
		<div style="width: 20%;">
			<button class="btn btn-success" id="createNewNetwork">Créer le réseau</button>
		</div>
		
</div>

<script>

document.getElementById('createNewNetwork').addEventListener('click', function () {
  $.ajax({
    type: 'POST',
    url: 'plugins/zeroTier/core/ajax/zeroTier.ajax.php',
    data: {
      action: 'createNewNetwork',
      typeNetwork : document.getElementById('typeNetwork').value,
      nameNetwork :  document.getElementById('nameNetwork').value,
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
      var idNetworkDiv= document.getElementById('idNetwork');
      idNetworkDiv.style.display = 'block'; 
      var spanElement = idNetworkDiv.querySelector('span[data-l1key="configuration"][data-l2key="idNetwork"]');
      var jsonResponse = JSON.parse(data.result);
      spanElement.textContent = jsonResponse.config.id
      location.reload();
      $('#div_alert').showAlert({ message: '{{Network crée, Id ajouté}}', level: 'success' })
    }
  })
}) 

</script>


<?php include_file('desktop', 'zeroTier', 'js', 'zeroTier'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js'); ?>





