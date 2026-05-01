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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//This file describes the module, including database tables

//Basica variables
$name = "Alumni";
$description = "The Alumni module allows schools to accept alumni registrations, and then link these to existing user accounts.";
$entryURL = "alumni_manage.php";
$type = "Additional";
$category = "People";
$version = "1.1.04";
$author = "Gibbon Foundation";
$url = "https://gibbonedu.org";

//Module tables
$moduleTables[0] = "CREATE TABLE `alumniAlumnus` (  `alumniAlumnusID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,  `title` varchar(5) NOT NULL,  `surname` varchar(30) NOT NULL DEFAULT '',  `firstName` varchar(30) NOT NULL DEFAULT '',  `officialName` varchar(150) NOT NULL,  `maidenName` varchar(30) NOT NULL,  `gender` enum('M','F','Other','Unspecified') NOT NULL DEFAULT 'Unspecified',  `username` varchar(20) NOT NULL,  `dob` date DEFAULT NULL,  `email` varchar(50) DEFAULT NULL,  `address1Country` varchar(255) NOT NULL,  `profession` varchar(30) NOT NULL,  `employer` varchar(30) NOT NULL,  `jobTitle` varchar(30) NOT NULL,  `graduatingYear` int(4) DEFAULT NULL,`formerRole` enum('Staff','Student','Parent','Other') DEFAULT NULL, `gibbonPersonID` int(10) DEFAULT NULL, `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `fields` TEXT NULL, PRIMARY KEY (`alumniAlumnusID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//Settings
$gibbonSetting[0] = "INSERT INTO `gibbonSetting` (`gibbonSettingID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Alumni', 'showPublicRegistration', 'Show Public Registration', 'Should the alumni registration form be displayed on the school\'s Gibbon homepage, or available via a link only?.', 'Y');";
$gibbonSetting[1] = "INSERT INTO `gibbonSetting` (`gibbonSettingID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Alumni', 'socialNetworkLink', 'Social Network Link', 'A URL pointing to a Social Network page for the school\'s alumni group.', '');";
$gibbonSetting[2] = "INSERT INTO `gibbonSetting` (`gibbonSettingID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Alumni', 'privacyPolicyLink', 'Privacy Policy Link', 'A URL pointing to the school\'s privacy policy.', '');";

//Action rows
$actionRows[0]['name'] = "Manage Alumni";
$actionRows[0]['precedence'] = "0";
$actionRows[0]['category'] = "Admin";
$actionRows[0]['description'] = "Allows privileged users to manage all alumni records.";
$actionRows[0]['URLList'] = "alumni_manage.php, alumni_manage_add.php, alumni_manage_edit.php, alumni_manage_delete.php";
$actionRows[0]['entryURL'] = "alumni_manage.php";
$actionRows[0]['defaultPermissionAdmin'] = "Y";
$actionRows[0]['defaultPermissionTeacher'] = "N";
$actionRows[0]['defaultPermissionStudent'] = "N";
$actionRows[0]['defaultPermissionParent'] = "N";
$actionRows[0]['defaultPermissionSupport'] = "N";
$actionRows[0]['categoryPermissionStaff'] = "Y";
$actionRows[0]['categoryPermissionStudent'] = "Y";
$actionRows[0]['categoryPermissionParent'] = "Y";
$actionRows[0]['categoryPermissionOther'] = "Y";

$actionRows[1]['name'] = "Alumni Settings";
$actionRows[1]['precedence'] = "0";
$actionRows[1]['category'] = "Admin";
$actionRows[1]['description'] = "Allows privileged users to manage all alumni settings.";
$actionRows[1]['URLList'] = "alumni_settings.php";
$actionRows[1]['entryURL'] = "alumni_settings.php";
$actionRows[1]['defaultPermissionAdmin'] = "Y";
$actionRows[1]['defaultPermissionTeacher'] = "N";
$actionRows[1]['defaultPermissionStudent'] = "N";
$actionRows[1]['defaultPermissionParent'] = "N";
$actionRows[1]['defaultPermissionSupport'] = "N";
$actionRows[1]['categoryPermissionStaff'] = "Y";
$actionRows[1]['categoryPermissionStudent'] = "Y";
$actionRows[1]['categoryPermissionParent'] = "Y";
$actionRows[1]['categoryPermissionOther'] = "Y";

//Hooks
$array = [];
$array['toggleSettingName'] = "showPublicRegistration";
$array['toggleSettingScope'] = "Alumni";
$array['toggleSettingValue'] = "Y";
$array['title'] = "Alumni Registration";
$array['text'] = "Are you a former member of our school community? If so, please do <a href=\'./index.php?q=/modules/Alumni/publicRegistration.php\'>register as an alumnus of the school</a>.";
$hooks[0] = "INSERT INTO `gibbonHook` (`gibbonHookID`, `name`, `type`, `options`, gibbonModuleID) VALUES (NULL, 'Alumni', 'Public Home Page', '".serialize($array)."', (SELECT gibbonModuleID FROM gibbonModule WHERE name='$name'));";
