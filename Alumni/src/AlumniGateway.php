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

namespace Gibbon\Module\Alumni;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Alumni Gateway
 *
 * @version 1.0.01
 * @since   1.0.00
 */
class AlumniGateway extends QueryableGateway
{
    use TableAware;

    // Updated to match the standardized table structure
    private static $tableName = 'gibbonAlumni';
    private static $primaryKey = 'gibbonAlumniID';

    private static $searchableColumns = ['preferredName', 'surname', 'username', 'email'];

    /**
     * @param int $gibbonAlumniID
     * @return array|false
     */
    public function getByID($gibbonAlumniID)
    {
        return $this->db()->select($this->getTableName(), [$this->getPrimaryKey() => $gibbonAlumniID])->fetch();
    }

    /**
     * @param array $data
     * @return string|false
     */
    public function add(array $data)
    {
        return $this->db()->insert($this->getTableName(), $data);
    }

    /**
     * @param int $gibbonAlumniID
     * @param array $data
     * @return int|false
     */
    public function update($gibbonAlumniID, array $data)
    {
        return $this->db()->update($this->getTableName(), $data, [$this->getPrimaryKey() => $gibbonAlumniID]);
    }

    /**
     * @param array $criteria
     * @return \PDOStatement
     */
    public function selectBy(array $criteria)
    {
        return $this->db()->select($this->getTableName(), $criteria);
    }

    /**
     * @param QueryCriteria $criteria
     * @param string $graduatingYear
     * @return \Gibbon\DataSet
     */
    public function queryAlumniAlumnusByGraduationYear(QueryCriteria $criteria, $graduatingYear = '')
    {
        $query = $this
            ->newQuery()
            ->from($this->getTableName())
            ->cols([
                'gibbonAlumniID',
                'title',
                'surname',
                'firstName',
                'preferredName', // Synced with the new database column
                'maidenName',
                'gender',
                'username',
                'dob',
                'email',
                'phone1',        // Added for WhatsApp Bridge integration
                'address1Country',
                'profession',
                'employer',
                'jobTitle',
                'graduatingYear',
                'formerRole',
                'status',
                'gibbonPersonID',
                'timestamp'
            ]);

        if (!empty($graduatingYear)) {
            $query->where('graduatingYear = :graduatingYear')
                ->bindValue('graduatingYear', $graduatingYear);
        }        

        return $this->runQuery($query, $criteria);
    }
}
