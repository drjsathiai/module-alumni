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

use Gibbon\Forms\Form;
use Gibbon\Domain\System\SettingGateway;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_settings.php') == false) {
    //Acess denied
    $page->addError(__m('You do not have access to this action.'));
} else {
    $page->breadcrumbs->add(__m('Alumni Settings'));
    
    $settingGateway = $container->get(SettingGateway::class);
    
    $alumniSocialNetworkSetting = $settingGateway->getSettingByScope('Alumni', 'socialNetworkLink');
    $alumniShowPublicSetting = $settingGateway->getSettingByScope('Alumni', 'showPublicRegistration');

    $form = Form::create('action', $session->get('absoluteURL').'/modules/'.$session->get('module').'/alumni_settingsProcess.php');

    $form->addHiddenValue('address', $session->get('address'));

    $form->addRow()->addHeading(__m('Settings'));

    $setting = $settingGateway->getSettingByScope('Alumni', 'privacyPolicyLink', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addURL($setting['name'])->setValue($setting['value'])->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('socialNetworkLink', __m('Social Network Link'))
                ->description(__m('A URL pointing to a Social Network page for the school\'s alumni group.'));
        $row->addURL('socialNetworkLink')->setValue($alumniSocialNetworkSetting)->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('showPublicRegistration', __m('Show Public Registration'))
                ->description(__m('Should the alumni registration form be displayed on the school\'s Gibbon homepage, or available via a link only?.'));
         $row->addYesNo('showPublicRegistration')->selected($alumniShowPublicSetting);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
    
}
