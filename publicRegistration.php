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
use Gibbon\Services\Format;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\CustomFieldHandler;

$settingGateway = $container->get(SettingGateway::class);
$enablePublicRegistration = $settingGateway->getSettingByScope('Alumni', 'showPublicRegistration');

$loggedIn = $session->has('username');

if ($enablePublicRegistration != "Y") {
    //Access denied
    $page->addError(__m('You do not have access to this action.'));
}
else if ($enablePublicRegistration and !empty($loggedIn)) {
    $page->addError(__m('You need to log out in order to access this action.'));
}
else {
    //Proceed!
    $page->breadcrumbs->add(__m('{orgName} Alumni Registration', [
        'orgName' => $session->get('organisationNameShort') ?? ''
    ]));

    $publicRegistrationMinimumAge = $settingGateway->getSettingByScope('User Admin', 'publicRegistrationMinimumAge');

    $returns = [];
    $returns['error5'] = __m('Your request failed because you do not meet the minimum age for joining this site ({minimumAge} years of age).', ['minimumAge' => $publicRegistrationMinimumAge]);
    $returns['error7'] = __m('Your request failed because the specified email address has already been registered');
    $returns['success0'] = __m('Your registration was successfully submitted: a member of our alumni team will be in touch.');
    
    $page->return->addReturns($returns);

    $page->write(__m("This registration form is for former members of the {orgName} community who wish to reconnect. Please fill in your details here, and someone from our alumni team will get back to you.",
    ['orgName' => $session->get('organisationNameShort')]));

    $socialNetworkLink = $settingGateway->getSettingByScope('Alumni', 'socialNetworkLink');
    if (!empty($socialNetworkLink)) {
        $page->write(__m("Please don't forget to take a look at, and like, our {socialNetworkLink}",
                ['socialNetworkLink' => Format::link($socialNetworkLink, __m('alumni Social Network page'), ['target' => '_blank'])]));
    }

    $privacyPolicyLink = $settingGateway->getSettingByScope('Alumni', 'privacyPolicyLink');
    if (!empty($privacyPolicyLink)) {
        $page->write('<br/><br/>'.__m("Prior to completing the application form, please read our {privacyPolicyLink}",
                ['privacyPolicyLink' => Format::link($privacyPolicyLink, __m('Statement on Collection of Personal Data'), ['target' => '_blank'])]));
    }

    $form = Form::create('action', $session->get('absoluteURL').'/modules/Alumni/publicRegistrationProcess.php');
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $session->get('address'));

    $form->addRow()->addHeading(__m('Personal Details'));

    $row = $form->addRow();
        $row->addLabel('title', __m('Title'));
        $row->addSelectTitle('title');

    $row = $form->addRow();
        $row->addLabel('firstName', __m('First Name'));
        $row->addTextField('firstName')->isRequired()->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('surname', __m('Surname'));
        $row->addTextField('surname')->isRequired()->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('preferredName', __m('Preferred Name'))->description(__m('Full name as you wish it to appear in school records.'));
        $row->addTextField('preferredName')->isRequired()->maxLength(150);

    $row = $form->addRow();
        $row->addLabel('email', __m('Email'))->description(__m('Your current non-school email.'));
        $row->addEmail('email')->isRequired()->maxLength(50);

    // WhatsApp Integration Field
    $row = $form->addRow();
        $row->addLabel('phone1', __m('Phone Number'))->description(__m('International format (e.g. 919876543210) without + or 00.'));
        $row->addTextField('phone1')->setPlaceholder('91...')->isRequired()->maxLength(20);

    $row = $form->addRow();
        $row->addLabel('gender', __m('Gender'));
        $row->addSelectGender('gender')->isRequired();

    $row = $form->addRow();
        $row->addLabel('dob', __m('Date of Birth'));
        $row->addDate('dob')->isRequired();

    $formerRoles = [
        'Student' => __m('Student'),
        'Staff' => __m('Staff'),
        'Parent' => __m('Parent'),
        'Other' => __m('Other'),
    ];
    $row = $form->addRow();
        $row->addLabel('formerRole', __m('Main Role'))->description(__m('In what way, primarily, were you involved with the school?'));
        $row->addSelect('formerRole')->fromArray($formerRoles)->isRequired()->placeholder();

    $form->addRow()->addHeading(__m('Tell Us More About Yourself'));

    $row = $form->addRow();
        $row->addLabel('maidenName', __m('Maiden Name'))->description(__m('Your surname prior to marriage.'));
        $row->addTextField('maidenName')->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('username', __m('Username'))->description(__m('Previous school login or social media handle.'));
        $row->addTextField('username')->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('graduatingYear', __m('Graduating Year'));
        $row->addSelect('graduatingYear')->fromArray(range(date('Y'), date('Y')-100, -1))->placeholder();

    $row = $form->addRow();
        $row->addLabel('address1Country', __m('Current Country of Residence'));
        $row->addSelectCountry('address1Country')->placeholder('');

    // Mandatory Professional Fields
    $row = $form->addRow();
        $row->addLabel('profession', __m('Profession'));
        $row->addTextField('profession')->isRequired()->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('employer', __m('Employer'));
        $row->addTextField('employer')->isRequired()->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('jobTitle', __m('Job Title'));
        $row->addTextField('jobTitle')->isRequired()->maxLength(100);

    $privacyStatement = $settingGateway->getSettingByScope('User Admin', 'publicRegistrationPrivacyStatement');
    if (!empty($privacyStatement)) {
        $form->addRow()->addHeading(__m('Privacy Statement'));
        $row = $form->addRow();
            $row->addContent(__m($privacyStatement));
    }

    $agreement = $settingGateway->getSettingByScope('User Admin', 'publicRegistrationAgreement');
    if (!empty($agreement)) {
        $form->addRow()->addHeading(__m('Agreement'));
        $row = $form->addRow();
            $row->addContent(__m($agreement));

        $row = $form->addRow();
            $row->addLabel('agreement', __m('Do you agree to the above?'));
            $row->addCheckbox('agreement')->isRequired()->description(__m('Yes'));
    }

    // Custom Fields
    $container->get(CustomFieldHandler::class)->addCustomFieldsToForm($form, 'Alumni', []);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
