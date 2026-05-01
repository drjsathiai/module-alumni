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
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Forms\CustomFieldHandler;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage_add.php') == false) {
    //Access denied
    $page->addError(__m('You do not have access to this action.'));
} else {
    $page->breadcrumbs
      ->add(__m('Manage Alumni'), 'alumni_manage.php')
      ->add(__m('Add'));

    $graduatingYear = $_GET['graduatingYear'] ?? '';

    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $session->get('absoluteURL').'/index.php?q=/modules/Alumni/alumni_manage_edit.php&gibbonAlumniID='.$_GET['editID'].'&graduatingYear='.$graduatingYear;
    }
    $page->return->setEditLink($editLink);

    $form = Form::create('action', $session->get('absoluteURL').'/modules/'.$session->get('module').'/alumni_manage_addProcess.php?graduatingYear='.$graduatingYear);
    $form->setFactory(DatabaseFormFactory::create($pdo));
    
    if (!empty($graduatingYear)) { 
        $form->addHeaderAction('back', __m('Back to Search Results'))
            ->setURL('/modules/Alumni/alumni_manage.php')
            ->addParam('graduatingYear', $graduatingYear)
            ->displayLabel();
    }

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
        $row->addTextField('preferredName')->maxLength(150);

    $row = $form->addRow();
        $row->addLabel('email', __m('Email'))->description(__m('Current non-school email address.'));
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
        $row->addDate('dob');

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
        $row->addSelect('graduatingYear')->fromArray(range(date('Y'), date('Y')-100, -1))->selected($graduatingYear)->placeholder();

    $row = $form->addRow();
        $row->addLabel('address1Country', __m('Current Country of Residence'));
        $row->addSelectCountry('address1Country')->placeholder('');

    // Updated Required Fields
    $row = $form->addRow();
        $row->addLabel('profession', __m('Profession'));
        $row->addTextField('profession')->isRequired()->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('employer', __m('Employer'));
        $row->addTextField('employer')->isRequired()->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('jobTitle', __m('Job Title'));
        $row->addTextField('jobTitle')->isRequired()->maxLength(100);

    $form->addRow()->addHeading(__m('Link To Gibbon User'));

    $row = $form->addRow();
        $row->addLabel('gibbonPersonID', __m('Existing User'));
        $row->addSelectUsers('gibbonPersonID')->placeHolder();

    // Custom Fields
    $container->get(CustomFieldHandler::class)->addCustomFieldsToForm($form, 'Alumni', []);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
