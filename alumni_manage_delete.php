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

use Gibbon\Forms\Prefab\DeleteForm;
use Gibbon\Module\Alumni\AlumniGateway;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Alumni/alumni_manage_delete.php') == false) {
    //Acess denied
    $page->addError(__m('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs
      ->add(__m('Manage Alumni'), 'alumni_manage.php')
      ->add(__m('Delete'));

    //Check if alumniAlumnusID specified
    $alumniAlumnusID = $_GET['alumniAlumnusID'] ?? '';
    $graduatingYear = $_GET['graduatingYear'] ?? '';
    
    if (empty($alumniAlumnusID)) { 
        $page->addError(__m('You have not specified one or more required parameters.'));
    } else {
        $alumniGateway = $container->get(AlumniGateway::class);
        
        $alumni = $alumniGateway->getByID($alumniAlumnusID);

        if (empty($alumni)) {
            $page->addError(__m('The selected record does not exist, or you do not have access to it.'));
        } else {
            //Let's go!
            if (!empty($graduatingYear)) { 
                $form->addHeaderAction('back', __m('Back to Search Results'))
                    ->setURL('/modules/Alumni/alumni_manage.php')
                    ->addParam('graduatingYear', $graduatingYear)
                    ->displayLabel();
            }

            $form = DeleteForm::createForm($session->get('absoluteURL').'/modules/'.$session->get('module')."/alumni_manage_deleteProcess.php?alumniAlumnusID=$alumniAlumnusID&graduatingYear=".$alumni['graduatingYear']);

            echo $form->getOutput();
        }
    }
}
