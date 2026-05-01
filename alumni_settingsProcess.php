<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
use Gibbon\Domain\System\SettingGateway;

include '../../gibbon.php';

$URL = $session->get('absoluteURL')."/index.php?q=/modules/".getModuleName($_POST['address'])."/alumni_settings.php";

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_settings.php') == false) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    $settingGateway = $container->get(SettingGateway::class);
    $alumniSetting = $settingGateway->getAllSettingsByScope('Alumni');
    if (empty($alumniSetting)) {
        //Fail1
        $URL .= '&return=error1';
        header("Location: {$URL}");
    } else {
        $privacyPolicyLink = filter_var($_POST['privacyPolicyLink'] ?? '', FILTER_SANITIZE_URL);
        $socialNetworkLink = filter_var($_POST['socialNetworkLink'] ?? '', FILTER_SANITIZE_URL);
        $showPublicRegistration = $_POST['showPublicRegistration'] ?? 'N';
        
        if (!empty($privacyPolicyLink)) {
            $settingGateway->updateSettingByScope('Alumni', 'privacyPolicyLink', $privacyPolicyLink);
        }

        if (!empty($socialNetworkLink)) {
            $settingGateway->updateSettingByScope('Alumni', 'socialNetworkLink', $socialNetworkLink);
        }
        
        if (!empty($showPublicRegistration)) {
            $settingGateway->updateSettingByScope('Alumni', 'showPublicRegistration', $showPublicRegistration);
        }

        //Success 0
        $URL .= '&return=success0';
        header("Location: {$URL}");
    }
}
