<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql = [];
$count = 0;

//v0.1.00 - FIRST VERSION, SO NO CHANGES
$sql[$count][0] = "0.1.00";
$sql[$count][1] = "";

//v0.2.00
++$count;
$sql[$count][0] = "0.2.00";
$sql[$count][1] = "
INSERT INTO `gibbonAction` (`gibbonActionID`, `gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES (NULL, (SELECT gibbonModuleID FROM gibbonModule WHERE name='Alumni'), 'Manage Alumni', 0, 'Admin', 'Allows privileged users to manage all alumni records.', 'alumni_manage.php, alumni_manage_add.php, alumni_manage_edit.php, alumni_manage_delete.php','alumni_manage.php', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Alumni' AND gibbonAction.name='Manage Alumni'));end
ALTER TABLE `alumniAlumnus` CHANGE `graduatingYear` `graduatingYear` INT(4) NULL DEFAULT NULL;end
UPDATE alumniAlumnus SET graduatingYear=NULL WHERE graduatingYear=0;end
";

//v0.3.00
++$count;
$sql[$count][0] = "0.3.00";
$sql[$count][1] = "
ALTER TABLE `alumniAlumnus` ADD `formerRole` ENUM('Staff','Student','Parent','Other') NULL DEFAULT NULL AFTER `graduatingYear`, ADD `gibbonPersonID` INT(10) NULL DEFAULT NULL AFTER `formerRole`;end
";

//v0.3.01
++$count;
$sql[$count][0] = "0.3.01";
$sql[$count][1] = "";

//v0.3.02
++$count;
$sql[$count][0] = "0.3.02";
$sql[$count][1] = "INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Alumni', 'facebookLink', 'Facebook Link', 'A URL pointing to a Facebook page for the school\'s alumni group.', '');";

//v0.3.03
++$count;
$sql[$count][0] = "0.3.03";
$sql[$count][1] = "";

//v0.3.04
++$count;
$sql[$count][0] = "0.3.04";
$sql[$count][1] = "";

//v0.3.05
++$count;
$sql[$count][0] = "0.3.05";
$sql[$count][1] = "";

//v0.3.06
++$count;
$sql[$count][0] = "0.3.06";
$sql[$count][1] = "";

//v0.3.07
++$count;
$sql[$count][0] = "0.3.07";
$sql[$count][1] = "";

//v0.3.08
++$count;
$sql[$count][0] = "0.3.08";
$sql[$count][1] = "";

//v0.4.00
++$count;
$sql[$count][0] = "0.4.00";
$sql[$count][1] = "";

//v0.5.00
++$count;
$sql[$count][0] = "0.5.00";
$sql[$count][1] = "";

//v0.6.00
++$count;
$sql[$count][0] = "0.6.00";
$sql[$count][1] = "";

//v0.6.01
++$count;
$sql[$count][0] = "0.6.01";
$sql[$count][1] = "";

//v1.0.00
++$count;
$sql[$count][0] = "1.0.00";
$sql[$count][1] = "
UPDATE `gibbonSetting` SET `name` = 'socialNetworkLink' ,`nameDisplay` = 'Social Network Link', `description` = 'A URL pointing to a Social Network page for the school\'s alumni group.' WHERE `scope` = 'Alumni' and name = 'facebookLink';end
INSERT INTO `gibbonAction` SET `name` = 'Alumni Settings', `precedence` = '0', `category` = 'Admin', `description` = 'Allows privileged users to manage all alumni settings.', `URLList` = 'alumni_settings.php', `entryURL` = 'alumni_settings.php', `defaultPermissionAdmin` = 'Y', `defaultPermissionTeacher` = 'N', `defaultPermissionStudent` = 'N', `defaultPermissionParent` = 'N', `defaultPermissionSupport` = 'N', `categoryPermissionStaff` = 'Y', `categoryPermissionStudent` = 'Y', `categoryPermissionParent` = 'Y', `categoryPermissionOther` = 'Y', `gibbonModuleID` = (SELECT `gibbonModuleID` FROM `gibbonModule` WHERE `name` = 'Alumni');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT `gibbonActionID` FROM `gibbonAction` JOIN `gibbonModule` ON (`gibbonAction`.`gibbonModuleID` = `gibbonModule`.`gibbonModuleID`) WHERE `gibbonModule`.`name` = 'Alumni' AND `gibbonAction`.`name` = 'Alumni Settings'));end
";

//v1.0.01
++$count;
$sql[$count][0] = "1.0.01";
$sql[$count][1] = "";

//v1.0.02
++$count;
$sql[$count][0] = "1.0.02";
$sql[$count][1] = "
ALTER TABLE `alumniAlumnus` ADD `fields` TEXT NULL AFTER `timestamp`;end
";

//v1.1.00
++$count;
$sql[$count][0] = "1.1.00";
$sql[$count][1] = "
UPDATE gibbonModule SET author='Gibbon Foundation', url='https://gibbonedu.org' WHERE name='Free Learning';end
";

//v1.1.01
++$count;
$sql[$count][0] = "1.1.01";
$sql[$count][1] = "";

//v1.1.02
++$count;
$sql[$count][0] = "1.1.02";
$sql[$count][1] = "";

//v1.1.03
++$count;
$sql[$count][0] = "1.1.03";
$sql[$count][1] = "INSERT INTO `gibbonSetting` (`gibbonSettingID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Alumni', 'privacyPolicyLink', 'Privacy Policy Link', 'A URL pointing to the school\'s privacy policy.', '');end";

//v1.1.04
++$count;
$sql[$count][0] = "1.1.04";
$sql[$count][1] = "";
