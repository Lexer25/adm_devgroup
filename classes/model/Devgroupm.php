<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Devgroupm extends Model
{
    /**
     * Преобразование ключей массива из верхнего регистра в нижний
     * и конвертация кодировки из Windows-1251 в UTF-8
     */
    private function convertToUtf8($data)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $newKey = is_string($key) ? strtolower($key) : $key;
                
                if (is_array($value)) {
                    $result[$newKey] = $this->convertToUtf8($value);
                } elseif (is_string($value)) {
                    $result[$newKey] = iconv('Windows-1251', 'UTF-8//IGNORE', $value);
                } else {
                    $result[$newKey] = $value;
                }
            }
            return $result;
        } elseif (is_string($data)) {
            return iconv('Windows-1251', 'UTF-8//IGNORE', $data);
        }
        return $data;
    }

    /**
     * Получить группы по родителю (для AJAX дерева)
     */
    public function getGroupsByParent($parentId)
    {
        $sql = 'SELECT dg.id_devgroup, dg.id_db, dg.name, dg.id_parent,
                       (SELECT COUNT(*) FROM devgroup dg2 WHERE dg2.id_parent = dg.id_devgroup AND dg2.id_dev IS NULL) as child_count,
                       (SELECT COUNT(*) FROM devgroup dg3 WHERE dg3.id_parent = dg.id_devgroup AND dg3.id_dev IS NOT NULL) as device_count
                FROM devgroup dg
                WHERE dg.id_parent = ' . intval($parentId) . '
                AND dg.id_dev IS NULL
                ORDER BY dg.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить устройства в группе (для AJAX дерева)
     */
    public function getDevicesByGroup($groupId)
    {
        $sql = 'SELECT dg.id_devgroup, dg.id_dev, d.name
                FROM devgroup dg
                LEFT JOIN device d ON dg.id_dev = d.id_dev
                WHERE dg.id_parent = ' . intval($groupId) . '
                AND dg.id_dev IS NOT NULL
                ORDER BY d.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить группу по ID
     */
    public function getDevGroupById($id)
    {
        $sql = 'SELECT dg.id_devgroup, dg.id_db, dg.name, dg.id_parent
                FROM devgroup dg
                WHERE dg.id_devgroup = ' . intval($id);

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        if (count($query) > 0) {
            $result = $this->convertToUtf8($query);
            return $result[0];
        }

        return null;
    }

    /**
     * Получить все устройства в группе
     */
    public function getDevicesByGroupId($groupId)
    {
        $sql = 'SELECT dg.id_devgroup, dg.id_dev, d.name
                FROM devgroup dg
                LEFT JOIN device d ON dg.id_dev = d.id_dev
                WHERE dg.id_parent = ' . intval($groupId) . '
                AND dg.id_dev IS NOT NULL
                ORDER BY d.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить все доступные устройства (не входящие ни в одну группу)
     */
    public function getAvailableDevices($excludeGroupId = null)
    {
        $sql = 'SELECT d.id_dev, d.name
                FROM device d
                WHERE d.id_dev NOT IN (
                    SELECT dg.id_dev FROM devgroup dg WHERE dg.id_dev IS NOT NULL
                )';

        if ($excludeGroupId !== null) {
            $sql = 'SELECT d.id_dev, d.name
                    FROM device d
                    WHERE d.id_dev NOT IN (
                        SELECT dg.id_dev FROM devgroup dg
                        WHERE dg.id_dev IS NOT NULL
                        AND dg.id_parent != ' . intval($excludeGroupId) . '
                    )';
        }

        $sql .= ' ORDER BY d.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return $this->convertToUtf8($query);
    }

    /**
     * Получить все группы для выпадающего списка (родители)
     */
    public function getParentOptions($excludeId = null)
    {
        $options = array();
        
        // Получаем все группы (корневые и дочерние)
        $sql = 'SELECT dg.id_devgroup, dg.name
                FROM devgroup dg
                WHERE dg.id_dev IS NULL
                ORDER BY dg.name';

        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        $groups = $this->convertToUtf8($query);

        foreach ($groups as $group) {
            if ($excludeId !== null && $group['id_devgroup'] == $excludeId) {
                continue;
            }
            $options[$group['id_devgroup']] = $group['name'];
        }

        return $options;
    }

    /**
     * Добавить новую группу устройств
     */
    public function addDevGroup($name, $parentId = 1, $dbId = 1)
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);

        $sql = "INSERT INTO devgroup (id_db, id_dev, name, id_parent)
                VALUES (" . intval($dbId) . ", NULL, '{$nameForDb}', " . intval($parentId) . ")";

        try {
            $result = DB::query(Database::INSERT, $sql)
                ->execute(Database::instance('fb'));

            $lastId = DB::query(Database::SELECT, "SELECT MAX(id_devgroup) as last_id FROM devgroup")
                ->execute(Database::instance('fb'))
                ->as_array();

            return $lastId[0]['LAST_ID'];
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding dev group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Обновить группу устройств
     */
    public function updateDevGroup($id, $name, $parentId = 1, $dbId = 1)
    {
        $nameForDb = iconv('UTF-8', 'Windows-1251//IGNORE', $name);
        $nameForDb = addslashes($nameForDb);

        $sql = "UPDATE devgroup
                SET name = '{$nameForDb}',
                    id_parent = " . intval($parentId) . ",
                    id_db = " . intval($dbId) . "
                WHERE id_devgroup = " . intval($id);

        try {
            DB::query(Database::UPDATE, $sql)
                ->execute(Database::instance('fb'));

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error updating dev group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить группу устройств
     */
    public function deleteDevGroup($id)
    {
        try {
            $db = Database::instance('fb');

            // Получаем все дочерние группы
            $children = DB::query(Database::SELECT,
                "SELECT id_devgroup FROM devgroup WHERE id_parent = " . intval($id) . " AND id_dev IS NULL")
                ->execute($db)
                ->as_array();

            // Рекурсивно удаляем дочерние группы
            foreach ($children as $child) {
                $this->deleteDevGroup($child['ID_DEVGROUP']);
            }

            // Удаляем все устройства в группе
            DB::query(Database::DELETE,
                "DELETE FROM devgroup WHERE id_parent = " . intval($id) . " AND id_dev IS NOT NULL")
                ->execute($db);

            // Удаляем саму группу
            DB::query(Database::DELETE,
                "DELETE FROM devgroup WHERE id_devgroup = " . intval($id))
                ->execute($db);

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error deleting dev group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Добавить устройства в группу
     */
    public function addDevicesToGroup($groupId, $deviceIds)
    {
        try {
            $db = Database::instance('fb');

            foreach ($deviceIds as $deviceId) {
                $checkSql = "SELECT COUNT(*) as cnt FROM devgroup
                             WHERE id_parent = " . intval($groupId) . "
                             AND id_dev = " . intval($deviceId);

                $exists = DB::query(Database::SELECT, $checkSql)
                    ->execute($db)
                    ->get('CNT', 0);

                if ($exists == 0) {
                    $sql = "INSERT INTO devgroup (id_db, id_dev, name, id_parent)
                            VALUES (1, " . intval($deviceId) . ", NULL, " . intval($groupId) . ")";

                    DB::query(Database::INSERT, $sql)->execute($db);
                }
            }

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error adding devices to group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Удалить устройства из группы
     */
    public function removeDevicesFromGroup($groupId, $deviceIds)
    {
        try {
            $db = Database::instance('fb');

            foreach ($deviceIds as $deviceId) {
                $sql = "DELETE FROM devgroup
                        WHERE id_parent = " . intval($groupId) . "
                        AND id_dev = " . intval($deviceId);

                DB::query(Database::DELETE, $sql)->execute($db);
            }

            return true;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error removing devices from group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Проверить, существует ли группа
     */
    public function groupExists($id)
    {
        $sql = "SELECT COUNT(*) as cnt FROM devgroup WHERE id_devgroup = " . intval($id);

        $result = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return ($result[0]['CNT'] > 0);
    }

    /**
     * Проверить, есть ли у группы дети
     */
    public function hasChildren($id)
    {
        $sql = "SELECT COUNT(*) as cnt FROM devgroup
                WHERE id_parent = " . intval($id) . "
                AND id_dev IS NULL";

        $result = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        return ($result[0]['CNT'] > 0);
    }
}