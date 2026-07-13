<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Devgroup extends Controller_Template
{
    public function before()
    {
        parent::before();
        $session = Session::instance();

        $this->is_admin = Auth::instance()->logged_in('admin');
        View::bind_global('is_admin', $this->is_admin);
    }

/**
 * Главная страница с деревом
 */
public function action_index()
{
    $model = Model::factory('Devgroupm');
    
    // Получаем корневые группы (используем новый метод)
    $rootGroups = $model->getRootGroups();

    $content = View::factory('devgroup/index', array(
        'rootGroups' => $rootGroups,
        'is_admin' => $this->is_admin,
    ));

    $this->template->content = $content;
}

    /**
     * AJAX: получить дочерние группы и устройства
     */
    public function action_getChildren()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        $parentId = (int)$this->request->param('id', 0);
        
        if ($parentId <= 0) {
            echo json_encode(array('success' => false, 'error' => 'Invalid parent ID'));
            return;
        }

        $model = Model::factory('Devgroupm');
        
        // Получаем дочерние группы
        $groups = $model->getGroupsByParent($parentId);
        
        // Получаем устройства в этой группе
        $devices = $model->getDevicesByGroup($parentId);
        
        // Формируем результат: сначала группы, потом устройства
        $result = array();
        
        // Добавляем группы
        foreach ($groups as $group) {
            $result[] = array(
                'id' => $group['id_devgroup'],
                'name' => $group['name'],
                'has_children' => ($group['child_count'] > 0),
                'device_count' => $group['device_count'],
                'is_group' => true
            );
        }
        
        // Добавляем устройства
        foreach ($devices as $device) {
            $result[] = array(
                'id' => $device['id_dev'],
                'name' => $device['name'],
                'is_group' => false
            );
        }

        echo json_encode(array('success' => true, 'data' => $result));
    }

    /**
     * AJAX: получить устройства в группе
     */
    public function action_getDevices()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        $groupId = (int)$this->request->param('id', 0);
        
        if ($groupId <= 0) {
            echo json_encode(array('success' => false, 'error' => 'Invalid group ID'));
            return;
        }

        $model = Model::factory('Devgroupm');
        $devices = $model->getDevicesByGroup($groupId);

        $result = array();
        foreach ($devices as $device) {
            $result[] = array(
                'id' => $device['id_dev'],
                'name' => $device['name'],
                'is_group' => false
            );
        }

        echo json_encode(array('success' => true, 'data' => $result));
    }

    /**
     * Добавление новой группы
     */
    public function action_add()
    {
        $model = Model::factory('Devgroupm');

        if ($this->request->method() == HTTP_Request::POST) {
            $post = $this->request->post();

            $name = Arr::get($post, 'name');
            $parentId = Arr::get($post, 'id_parent', 1);
            $dbId = 1; // Всегда 1

            $errors = array();
            if (empty($name)) {
                $errors['name'] = __('Название группы обязательно');
            }

            if (empty($errors)) {
                $result = $model->addDevGroup($name, $parentId, $dbId);

                if ($result) {
                    Session::instance()->set('message', __('Группа устройств успешно добавлена'));
                    Session::instance()->set('message_type', 'success');
                } else {
                    Session::instance()->set('message', __('Ошибка при добавлении группы устройств'));
                    Session::instance()->set('message_type', 'danger');
                }

                $this->redirect('devgroup');
            }

            $content = View::factory('devgroup/add', array(
                'errors' => $errors,
                'post' => $post,
                'parents' => $model->getParentOptions(),
                'is_admin' => $this->is_admin,
            ));
        } else {
            $content = View::factory('devgroup/add', array(
                'errors' => array(),
                'post' => array(),
                'parents' => $model->getParentOptions(),
                'is_admin' => $this->is_admin,
            ));
        }

        $this->template->content = $content;
    }

/**
 * Редактирование группы
 */
public function action_edit()
{
    $id = $this->request->param('id');
    $model = Model::factory('Devgroupm');

    if ($id === null || !$model->groupExists($id)) {
        $this->redirect('devgroup');
    }

    $group = $model->getDevGroupById($id);

    if ($this->request->method() == HTTP_Request::POST) {
        $post = $this->request->post();

        $name = Arr::get($post, 'name');
        $parentId = Arr::get($post, 'id_parent', 1);
        $dbId = 1;

        $errors = array();
        if (empty($name)) {
            $errors['name'] = __('Название группы обязательно');
        }

        if ($parentId == $id) {
            $errors['id_parent'] = __('Группа не может быть родителем самой себя');
        }

        if ($model->hasChildren($id)) {
            $childIds = $this->getChildGroupIds($id);
            if (in_array($parentId, $childIds)) {
                $errors['id_parent'] = __('Группа не может быть родителем своей дочерней группы');
            }
        }

        if (empty($errors)) {
            $result = $model->updateDevGroup($id, $name, $parentId, $dbId);

            if ($result) {
                Session::instance()->set('message', __('Группа устройств успешно обновлена'));
                Session::instance()->set('message_type', 'success');
            } else {
                Session::instance()->set('message', __('Ошибка при обновлении группы устройств'));
                Session::instance()->set('message_type', 'danger');
            }

            $this->redirect('devgroup');
        }

        $content = View::factory('devgroup/edit', array(
            'group' => $group,
            'errors' => $errors,
            'post' => $post,
            'parents' => $model->getParentOptions($id),
            'groupDevices' => $model->getDevicesByGroupId($id),
            'availableDevices' => $model->getAvailableDevices($id),
            'is_admin' => $this->is_admin,
        ));
    } else {
        $content = View::factory('devgroup/edit', array(
            'group' => $group,
            'errors' => array(),
            'post' => array(),
            'parents' => $model->getParentOptions($id),
            'groupDevices' => $model->getDevicesByGroupId($id),
            'availableDevices' => $model->getAvailableDevices($id),
            'is_admin' => $this->is_admin,
        ));
    }

    $this->template->content = $content;
}

    /**
     * Удаление группы
     */
    public function action_delete()
    {
        $id = $this->request->param('id');
        $model = Model::factory('Devgroupm');

        if ($id !== null && $model->groupExists($id)) {
            $result = $model->deleteDevGroup($id);

            if ($result) {
                Session::instance()->set('message', __('Группа устройств успешно удалена'));
                Session::instance()->set('message_type', 'success');
            } else {
                Session::instance()->set('message', __('Ошибка при удалении группы устройств'));
                Session::instance()->set('message_type', 'danger');
            }
        }

        $this->redirect('devgroup');
    }

    /**
     * AJAX: добавление устройств в группу
     */
    public function action_addDevices()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $groupId = (int)$this->request->post('group_id');
        $devices = $this->request->post('devices');

        if (!is_array($devices)) {
            $devices = array();
        }

        $devices = array_map('intval', $devices);

        if ($groupId <= 0 || empty($devices)) {
            echo json_encode(array('success' => false, 'error' => 'Invalid parameters'));
            return;
        }

        $model = Model::factory('Devgroupm');
        $result = $model->addDevicesToGroup($groupId, $devices);

        echo json_encode(array('success' => $result));
    }

    /**
     * AJAX: удаление устройств из группы
     */
    public function action_removeDevices()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        if ($this->request->method() != HTTP_Request::POST) {
            echo json_encode(array('success' => false, 'error' => 'Invalid request method'));
            return;
        }

        $groupId = (int)$this->request->post('group_id');
        $devices = $this->request->post('devices');

        if (!is_array($devices)) {
            $devices = array();
        }

        $devices = array_map('intval', $devices);

        if ($groupId <= 0 || empty($devices)) {
            echo json_encode(array('success' => false, 'error' => 'Invalid parameters'));
            return;
        }

        $model = Model::factory('Devgroupm');
        $result = $model->removeDevicesFromGroup($groupId, $devices);

        echo json_encode(array('success' => $result));
    }

    /**
     * Вспомогательный метод: получить ID всех дочерних групп
     */
    private function getChildGroupIds($parentId)
    {
        $ids = array();

        $sql = "SELECT id_devgroup FROM devgroup WHERE id_parent = " . intval($parentId) . " AND id_dev IS NULL";
        $query = DB::query(Database::SELECT, $sql)
            ->execute(Database::instance('fb'))
            ->as_array();

        foreach ($query as $row) {
            $ids[] = $row['ID_DEVGROUP'];
            $ids = array_merge($ids, $this->getChildGroupIds($row['ID_DEVGROUP']));
        }

        return $ids;
    }
	
	/**
 * AJAX: сохранение режима отображения в сессии
 */
public function action_setEditMode()
{
    $this->auto_render = false;
    header('Content-Type: application/json');

    if ($this->request->method() != HTTP_Request::POST) {
        echo json_encode(array('success' => false));
        return;
    }

    $mode = $this->request->post('mode');
    if (in_array($mode, array('classic', 'compact'))) {
        Session::instance()->set('devgroup_edit_mode', $mode);
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
}
}