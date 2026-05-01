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
use Gibbon\Tables\DataTable;
use Gibbon\Module\Alumni\AlumniGateway;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage.php') == false) {
    //Acess denied
    $page->addError(__m('You do not have access to this action.'));
} else {
    $page->breadcrumbs->add(__m('Manage Alumni'));

    $graduatingYear = $_GET['graduatingYear'] ?? '';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__m('Filter'));
    $form->setClass('noIntBorder w-full');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/alumni_manage.php');

    $row = $form->addRow();
        $row->addLabel('graduatingYear', __m('Graduating Year'));
        $row->addSelect('graduatingYear')->fromArray(range(date('Y'), date('Y')-100, -1))->selected($graduatingYear)->placeholder();

    $row = $form->addRow();
        $row->addSearchSubmit($session, __m('Clear Search'));

    echo $form->getOutput();


    $gatewayAlumni = $container->get(AlumniGateway::class);
    $criteria = $gatewayAlumni->newQueryCriteria(true)
        ->sortBy('timestamp')
        ->fromPOST();

    $alumnis = $gatewayAlumni->queryAlumniAlumnusByGraduationYear($criteria, $graduatingYear);

    $table = DataTable::createPaginated('alumnis', $criteria);

    $table->setTitle(__m('View Records'));

    $table->addHeaderAction('add', __m('Add'))
        ->setURL('/modules/Alumni/alumni_manage_add.php')
        ->addParam('graduatingYear', $graduatingYear)
        ->displayLabel();

    $table->addExpandableColumn('details')->format(function ($alumniRow) {     
        
            $attributesDetails = [
                'officialName' => __m('Official Name'),
                'maidenName' => __m('Maiden Name'),
                'gender' => __m('Gender'),
                'username' => __m('Username'),
                'dob' => __m('Date Of Birth'),
                'address1Country' => __m('Country of Residence'),
                'profession' => __m('Profession'),
                'employer' => __m('Employer'),
                'jobTitle' => __m('Job Title'),
                'timestamp' => __m('Date Joined')
            ];
            
            $arrayGender = [
                'F'           => __('Female'),
                'M'           => __('Male'),
                'Other'       => __('Other'),
                'Unspecified' => __('Unspecified')
            ];
            
            $details = '';
            
            foreach ($attributesDetails as $attribute=>$label) {
        
                switch ($attribute){
                    case 'dob':
                    case 'timestamp':
                        if (!empty($alumniRow[$attribute])) {
                            $details .= Format::bold(__m($label).': ');
                            $details .= Format::date($alumniRow[$attribute]).'<br/>';
    
                        }
                        break;
                    case 'gender':
                        $details .= Format::bold(__m($label).': ');
                        $details .= ($arrayGender[$alumniRow[$attribute]] ?? '').'<br/>';
                        break;
                    default:
                        if (!empty($alumniRow[$attribute])) {
                            $details .= Format::bold(__m($label).': ');
                            $details .= $alumniRow[$attribute].'<br/>';
                              }
                }
            }
            return $details;
        });

    $table->addColumn('name', __m('Name'))
        ->sortable(['surname', 'firstName'])
        ->format(function ($person) {
            return Format::name($person['title'], $person['firstName'], $person['surname'], 'Parent', false, false);
        });
    $table->addColumn('email', __m('Email'));
    $table->addColumn('graduatingYear', __m('Graduating Year'));

    $table->addActionColumn()
          ->addParam('alumniAlumnusID')
          ->format(function ($item, $actions) {
            $actions->addAction('edit', __m('Edit'))
              ->setURL('/modules/Alumni/alumni_manage_edit.php');
            $actions->addAction('delete', __m('Delete'))
              ->setURL('/modules/Alumni/alumni_manage_delete.php');
          });

    echo $table->render($alumnis);
    
}
