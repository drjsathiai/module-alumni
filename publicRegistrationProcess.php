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

use Gibbon\Services\Format;
use Gibbon\Module\Alumni\AlumniGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\CustomFieldHandler;

include '../../gibbon.php';

$URL = $session->get('absoluteURL').'/index.php?q=/modules/Alumni/publicRegistration.php';

$settingGateway = $container->get(SettingGateway::class);
$enablePublicRegistration = $settingGateway->getSettingByScope('Alumni', 'showPublicRegistration');
$loggedIn = $session->has('username');

if ($enablePublicRegistration != "Y" || ($enablePublicRegistration && !empty($loggedIn))) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    // Collect Public Data
    $title           = $_POST['title'] ?? '';
    $surname         = $_POST['surname'] ?? '';
    $firstName       = $_POST['firstName'] ?? '';
    $preferredName   = $_POST['preferredName'] ?? $_POST['officialName'] ?? ''; // Sync with verified schema
    $maidenName      = $_POST['maidenName'] ?? '';
    $gender          = $_POST['gender'] ?? '';
    $username        = $_POST['username'] ?? $_POST['username2'] ?? '';
    $dob             = !empty($_POST['dob']) ? Format::dateConvert($_POST['dob']) : '';
    $email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone1          = trim($_POST['phone1'] ?? ''); // Bridge Key
    $address1Country = $_POST['address1Country'] ?? '';
    $profession      = $_POST['profession'] ?? '';
    $employer        = $_POST['employer'] ?? '';
    $jobTitle        = $_POST['jobTitle'] ?? '';
    $graduatingYear  = $_POST['graduatingYear'] ?? null;
    $formerRole      = $_POST['formerRole'] ?? null;

    // Strict Validation: Including Phone and Professional Details
    if (empty($surname) || empty($firstName) || empty($email) || empty($phone1) || 
        empty($gender) || empty($dob) || empty($formerRole) || 
        empty($profession) || empty($employer) || empty($jobTitle)) {
        
        $URL .= '&return=error3';
        header("Location: {$URL}");
    } else {
        // Check publicRegistrationMinimumAge
        $publicRegistrationMinimumAge = $settingGateway->getSettingByScope('User Admin', 'publicRegistrationMinimumAge');
        $ageFail = false;

        if (!empty($publicRegistrationMinimumAge) && $publicRegistrationMinimumAge > 0) {
            $birthDate = new DateTime('@'.Format::timestamp($dob));
            $today = new DateTime();
            if ($birthDate->diff($today)->y < $publicRegistrationMinimumAge) {
                $ageFail = true;
            }
        }

        if ($ageFail) {
            $URL .= '&return=error5';
            header("Location: {$URL}");
        } else {
            $alumniGateway = $container->get(AlumniGateway::class);
            
            // Ensure Email Uniqueness
            $existEmail = $alumniGateway->selectBy(['email' => $email])->fetch();
            if (!empty($existEmail)) {
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            } else {
                $customRequireFail = false;
                $fields = $container->get(CustomFieldHandler::class)->getFieldDataFromPOST('Alumni', [], $customRequireFail);

                if ($customRequireFail) {
                    $URL .= '&return=error1';
                    header("Location: {$URL}");
                    exit;
                }

                // Prepare Data for Database
                $data = [
                    'title'           => $title, 
                    'surname'         => $surname, 
                    'firstName'       => $firstName, 
                    'preferredName'   => $preferredName, 
                    'maidenName'      => $maidenName, 
                    'gender'          => $gender, 
                    'username'        => $username, 
                    'dob'             => $dob, 
                    'email'           => $email, 
                    'phone1'          => $phone1, 
                    'address1Country' => $address1Country, 
                    'profession'      => $profession, 
                    'employer'        => $employer, 
                    'jobTitle'        => $jobTitle, 
                    'graduatingYear'  => $graduatingYear, 
                    'formerRole'      => $formerRole, 
                    'fields'          => $fields,
                    'status'          => 'Pending' // New public signups are set to Pending for review
                ];

                // Write to database
                $insertID = $alumniGateway->add($data);

                if ($insertID) {
                    // --- WHATSAPP BRIDGE HANDSHAKE ---
                    $whatsappFunctions = '../../modules/WhatsApp/functions.php';
                    if (file_exists($whatsappFunctions)) {
                        include_once $whatsappFunctions;
                        if (function_exists('whatsAppSendRegSuccess')) {
                            whatsAppSendRegSuccess($container, $phone1);
                        }
                    }
                    // ----------------------------------

                    $URL .= '&return=success0';
                } else {
                    $URL .= '&return=error2';
                }

                header("Location: {$URL}");
            }
        }
    }
}
