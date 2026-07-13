<?php
//echo Debug::vars('2-2 parents', $parents);//exit;
//echo Debug::vars('2-2 groupDevices', $groupDevices);//exit;
//echo Debug::vars('2-2 availableDevices', $availableDevices);//exit;

?><div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php echo __('Редактирование группы устройств'); ?>
            <span class="pull-right">
                <div class="btn-group btn-group-xs" role="group">
                    <button type="button" id="viewModeClassic" class="btn btn-default <?php echo (Session::instance()->get('devgroup_edit_mode', 'classic') == 'classic') ? 'active btn-primary' : ''; ?>" title="Классический режим">
                        <span class="glyphicon glyphicon-th-list"></span> Классический
                    </button>
                    <button type="button" id="viewModeCompact" class="btn btn-default <?php echo (Session::instance()->get('devgroup_edit_mode', 'classic') == 'compact') ? 'active btn-primary' : ''; ?>" title="Компактный режим">
                        <span class="glyphicon glyphicon-check"></span> Компактный
                    </button>
                </div>
            </span>
        </h3>
    </div>
    <div class="panel-body">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <!-- Информация о группе -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo __('Информация о группе'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label><?php echo __('ID'); ?></label>
                            <input type="text" class="form-control" value="<?php echo $group['id_devgroup']; ?>" disabled>
                        </div>

                        <form method="POST" action="<?php echo URL::site('devgroup/edit/' . $group['id_devgroup']); ?>">
                            <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                                <label for="name"><?php echo __('Название группы'); ?> *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($post['name']) ? htmlspecialchars($post['name']) : htmlspecialchars($group['name']); ?>"
                                       <?php echo $is_admin ? '' : 'disabled'; ?>>
                                <?php if (isset($errors['name'])): ?>
                                    <span class="help-block"><?php echo $errors['name']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group <?php echo isset($errors['id_parent']) ? 'has-error' : ''; ?>">
                                <label for="id_parent"><?php echo __('Родительская группа'); ?></label>
                                <select class="form-control" id="id_parent" name="id_parent" <?php echo $is_admin ? '' : 'disabled'; ?>>
                                    <option value="1">Корневая группа</option>
                                    <?php foreach ($parents as $id => $name): ?>
                                        <option value="<?php echo $id; ?>" <?php echo ($id == $group['id_parent']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_parent'])): ?>
                                    <span class="help-block"><?php echo $errors['id_parent']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <?php if ($is_admin): ?>
                                    <button type="submit" class="btn btn-primary"><?php echo __('Сохранить'); ?></button>
                                <?php endif; ?>
                                <a href="<?php echo URL::site('devgroup'); ?>" class="btn btn-default"><?php echo __('Отмена'); ?></a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="panel panel-default" style="margin-top: 15px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo __('Статистика'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label><?php echo __('Точек прохода в группе'); ?>:</label>
                            <h3><span id="selectedCount" class="label label-primary"><?php echo count($groupDevices); ?></span></h3>
                        </div>
                        <div class="form-group">
                            <label><?php echo __('Всего точек прохода'); ?>:</label>
                            <h3><span class="label label-default"><?php echo count($groupDevices) + count($availableDevices); ?></span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Классический режим - два окна -->
                <div id="viewClassic" style="<?php echo (Session::instance()->get('devgroup_edit_mode', 'classic') == 'classic') ? '' : 'display: none;'; ?>">
                    <!-- Добавление устройств -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo __('Добавить устройства'); ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <select id="availableDevicesSelect" class="form-control" multiple size="10" style="height: 200px;"
                                        <?php echo $is_admin ? '' : 'disabled title="' . __('Доступно только администраторам') . '"'; ?>>
                                        <?php foreach ($availableDevices as $device): ?>
                                            <option value="<?php echo $device['id_dev']; ?>">
                                                [<?php echo $device['id_dev']; ?>] <?php echo htmlspecialchars($device['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="addSelectedDevices" class="btn btn-success btn-block"
                                        <?php echo $is_admin ? '' : 'disabled title="' . __('Доступно только администраторам') . '"'; ?>>
                                        <span class="glyphicon glyphicon-arrow-right"></span> <?php echo __('Добавить'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        <?php echo __('Выберите устройства (Ctrl+Click для множественного выбора)'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Устройства в группе -->
                    <div class="panel panel-default" style="margin-top: 15px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <?php echo __('Устройства в группе'); ?>
                                <div class="btn-group pull-right">
                                    <button type="button" id="removeSelectedDevices" class="btn btn-xs btn-danger"
                                        <?php echo $is_admin ? '' : 'disabled title="' . __('Доступно только администраторам') . '"'; ?>>
                                        <span class="glyphicon glyphicon-remove"></span> <?php echo __('Удалить'); ?>
                                    </button>
                                </div>
                            </h4>
                        </div>
                        <div class="panel-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed table-bordered" style="margin-bottom: 0;">
                                    <thead>
                                        <tr class="active">
                                            <th width="5%">
                                                <input type="checkbox" id="selectAllAssigned"
                                                    <?php echo $is_admin ? '' : 'disabled'; ?>>
                                            </th>
                                            <th width="10%"><?php echo __('ID устройства'); ?></th>
                                            <th width="75%"><?php echo __('Название'); ?></th>
                                            <th width="10%"><?php echo __('Действия'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="assignedDevicesBody">
                                        <?php if (count($groupDevices) > 0): ?>
                                            <?php foreach ($groupDevices as $device): ?>
                                                <tr data-id="<?php echo $device['id_dev']; ?>">
                                                    <td class="text-center">
                                                        <input type="checkbox" class="assigned-checkbox" value="<?php echo $device['id_dev']; ?>"
                                                            <?php echo $is_admin ? '' : 'disabled'; ?>>
                                                    </td>
                                                    <td><?php echo $device['id_dev']; ?></td>
                                                    <td><?php echo htmlspecialchars($device['name']); ?></td>
                                                    <td>
                                                        <a href="<?php echo URL::site('door/doorInfo/' . $device['id_dev']); ?>" class="btn btn-xs btn-info" target="_blank">
                                                            <span class="glyphicon glyphicon-info-sign"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr id="noAssignedDataRow">
                                                <td colspan="4" class="text-center text-muted">
                                                    <?php echo __('Нет устройств в этой группе'); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Компактный режим - один список с чекбоксами -->
                <div id="viewCompact" style="<?php echo (Session::instance()->get('devgroup_edit_mode', 'classic') == 'compact') ? '' : 'display: none;'; ?>">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <?php echo __('Точки прохода'); ?>
                                <span class="pull-right">
                                    <button type="button" id="selectAllBtn" class="btn btn-xs btn-default" <?php echo $is_admin ? '' : 'disabled'; ?>>
                                        <span class="glyphicon glyphicon-check"></span> Выбрать все
                                    </button>
                                    <button type="button" id="deselectAllBtn" class="btn btn-xs btn-default" <?php echo $is_admin ? '' : 'disabled'; ?>>
                                        <span class="glyphicon glyphicon-unchecked"></span> Снять все
                                    </button>
                                </span>
                            </h4>
                        </div>
                        <div class="panel-body" style="padding: 0;">
                            <?php if ($is_admin): ?>
                                <div class="alert alert-info" style="margin: 10px;">
                                    <span class="glyphicon glyphicon-info-sign"></span>
                                    Отметьте галочкой точки прохода, которые должны входить в эту группу.
                                    <button type="button" id="saveDevicesBtn" class="btn btn-sm btn-success pull-right" style="margin-top: -5px;">
                                        <span class="glyphicon glyphicon-floppy-save"></span> Сохранить
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-condensed table-bordered" style="margin-bottom: 0;">
                                    <thead>
                                        <tr class="active">
                                            <th width="5%">
                                                <?php if ($is_admin): ?>
                                                    <input type="checkbox" id="selectAllCheckbox">
                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </th>
                                            <th width="10%"><?php echo __('ID'); ?></th>
                                            <th width="70%"><?php echo __('Название точки прохода'); ?></th>
                                            <th width="15%"><?php echo __('Действия'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="devicesBody">
                                        <?php 
                                        // Собираем все устройства в один массив
                                        $allDevices = array();
                                        
                                        // Сначала добавляем устройства уже в группе
                                        foreach ($groupDevices as $device) {
                                            $device['in_group'] = true;
                                            $allDevices[$device['id_dev']] = $device;
                                        }
                                        
                                        // Затем добавляем доступные устройства
                                        foreach ($availableDevices as $device) {
                                            if (!isset($allDevices[$device['id_dev']])) {
                                                $device['in_group'] = false;
                                                $allDevices[$device['id_dev']] = $device;
                                            }
                                        }
                                        
                                        // Сортируем по названию
                                        uasort($allDevices, function($a, $b) {
                                            return strcasecmp($a['name'], $b['name']);
                                        });
                                        ?>
                                        
                                        <?php if (count($allDevices) > 0): ?>
                                            <?php foreach ($allDevices as $device): ?>
                                                <tr data-id="<?php echo $device['id_dev']; ?>" data-in-group="<?php echo $device['in_group'] ? '1' : '0'; ?>">
                                                    <td class="text-center">
                                                        <?php if ($is_admin): ?>
                                                            <input type="checkbox" class="device-checkbox" value="<?php echo $device['id_dev']; ?>"
                                                                   <?php echo $device['in_group'] ? 'checked' : ''; ?>>
                                                        <?php else: ?>
                                                            <?php if ($device['in_group']): ?>
                                                                <span class="glyphicon glyphicon-check text-success"></span>
                                                            <?php else: ?>
                                                                <span class="glyphicon glyphicon-unchecked text-muted"></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $device['id_dev']; ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($device['name']); ?>
                                                        <?php if ($device['in_group']): ?>
                                                            <span class="label label-success" style="margin-left: 5px;">в группе</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo URL::site('door/doorInfo/' . $device['id_dev']); ?>" class="btn btn-xs btn-info" target="_blank">
                                                            <span class="glyphicon glyphicon-info-sign"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <?php echo __('Нет доступных точек прохода'); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_admin): ?>
            <div class="alert alert-info text-center" style="margin-top: 15px;">
                <span class="glyphicon glyphicon-lock"></span>
                <?php echo __('Режим только для просмотра. Для редактирования необходимы права администратора.'); ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    <?php if ($is_admin): ?>
    
    var groupId = <?php echo json_encode($group['id_devgroup']); ?>;
    var currentMode = '<?php echo Session::instance()->get("devgroup_edit_mode", "classic"); ?>';
    
    // ========== Переключение режимов ==========
    function switchMode(mode) {
        currentMode = mode;
        
        // Сохраняем в сессию через AJAX
        $.ajax({
            url: '<?php echo URL::site("devgroup/setEditMode"); ?>',
            type: 'POST',
            data: { mode: mode },
            dataType: 'json',
            cache: false
        });
        
        // Переключаем видимость
        if (mode === 'classic') {
            $('#viewClassic').show();
            $('#viewCompact').hide();
            $('#viewModeClassic').addClass('active btn-primary');
            $('#viewModeCompact').removeClass('active btn-primary');
        } else {
            $('#viewClassic').hide();
            $('#viewCompact').show();
            $('#viewModeCompact').addClass('active btn-primary');
            $('#viewModeClassic').removeClass('active btn-primary');
        }
    }
    
    $('#viewModeClassic').on('click', function() {
        if (currentMode !== 'classic') {
            switchMode('classic');
        }
    });
    
    $('#viewModeCompact').on('click', function() {
        if (currentMode !== 'compact') {
            switchMode('compact');
        }
    });
    
    // ========== Классический режим ==========
    var selectedDevices = [];

    <?php foreach ($groupDevices as $device): ?>
        selectedDevices.push(Number(<?php echo json_encode($device['id_dev']); ?>));
    <?php endforeach; ?>

    function updateSelectedCount() {
        // Для классического режима
        $("#selectedCount").text(selectedDevices.length);
    }

    function updateAssignedTable() {
        var $tbody = $("#assignedDevicesBody");
        $tbody.empty();

        if (selectedDevices.length === 0) {
            $tbody.append('<tr id="noAssignedDataRow"><td colspan="4" class="text-center text-muted"><?php echo __('Нет устройств в этой группе'); ?></td></tr>');
            updateSelectedCount();
            return;
        }

        var devicesData = {};
        <?php foreach ($availableDevices as $device): ?>
            devicesData[Number(<?php echo json_encode($device['id_dev']); ?>)] = {
                id: Number(<?php echo json_encode($device['id_dev']); ?>),
                name: <?php echo json_encode(htmlspecialchars($device['name'])); ?>
            };
        <?php endforeach; ?>

        <?php foreach ($groupDevices as $device): ?>
            devicesData[Number(<?php echo json_encode($device['id_dev']); ?>)] = {
                id: Number(<?php echo json_encode($device['id_dev']); ?>),
                name: <?php echo json_encode(htmlspecialchars($device['name'])); ?>
            };
        <?php endforeach; ?>

        var disabledCheckbox = <?php echo $is_admin ? 'false' : 'true'; ?>;
        var checkboxDisabled = disabledCheckbox ? ' disabled title="<?php echo __('Доступно только администраторам'); ?>"' : '';

        for (var i = 0; i < selectedDevices.length; i++) {
            var deviceId = selectedDevices[i];
            var device = devicesData[deviceId];
            if (device) {
                var $row = $('<tr>');
                $row.attr('data-id', device.id);
                $row.html(
                    '<td class="text-center"><input type="checkbox" class="assigned-checkbox" value="' + device.id + '"' + checkboxDisabled + '></td>' +
                    '<td>' + device.id + '</td>' +
                    '<td>' + device.name + '</td>' +
                    '<td><a href="<?php echo URL::site('door/doorInfo'); ?>/' + device.id + '" class="btn btn-xs btn-info" target="_blank"><span class="glyphicon glyphicon-info-sign"></span></a></td>'
                );
                $tbody.append($row);
            }
        }

        updateSelectedCount();
    }

    function updateAvailableDevices() {
        var $select = $("#availableDevicesSelect");
        $select.empty();

        <?php foreach ($availableDevices as $device): ?>
            var deviceId = Number(<?php echo json_encode($device['id_dev']); ?>);
            var deviceName = <?php echo json_encode(htmlspecialchars($device['name'])); ?>;

            if (selectedDevices.indexOf(deviceId) === -1) {
                $select.append('<option value="' + deviceId + '">[' + deviceId + '] ' + deviceName + '</option>');
            }
        <?php endforeach; ?>
    }

    $("#addSelectedDevices").on("click", function() {
        var selectedOptions = $("#availableDevicesSelect option:selected");
        var added = false;
        var devicesToAdd = [];

        if (selectedOptions.length === 0) {
            alert("<?php echo __('Не выбраны устройства для добавления'); ?>");
            return;
        }

        selectedOptions.each(function() {
            var deviceId = Number($(this).val());
            if (selectedDevices.indexOf(deviceId) === -1) {
                devicesToAdd.push(deviceId);
                added = true;
            }
        });

        if (added) {
            $.ajax({
                url: "<?php echo URL::site('devgroup/addDevices'); ?>",
                type: "POST",
                data: {
                    group_id: Number(<?php echo json_encode($group['id_devgroup']); ?>),
                    devices: devicesToAdd
                },
                dataType: "json",
                cache: false,
                beforeSend: function() {
                    $("#addSelectedDevices").prop("disabled", true).text("<?php echo __('Добавление...'); ?>");
                },
                success: function(response) {
                    if (response.success) {
                        for (var i = 0; i < devicesToAdd.length; i++) {
                            if (selectedDevices.indexOf(devicesToAdd[i]) === -1) {
                                selectedDevices.push(devicesToAdd[i]);
                            }
                        }
                        updateAssignedTable();
                        updateAvailableDevices();
                        // Обновляем компактный режим
                        updateCompactView();
                        alert("<?php echo __('Устройства успешно добавлены'); ?>");
                    } else {
                        alert(response.error || "<?php echo __('Ошибка при добавлении устройств'); ?>");
                    }
                },
                error: function() {
                    alert("<?php echo __('Ошибка при добавлении устройств'); ?>");
                },
                complete: function() {
                    $("#addSelectedDevices").prop("disabled", false).text("<?php echo __('Добавить'); ?>");
                }
            });
        } else {
            alert("<?php echo __('Выбранные устройства уже добавлены'); ?>");
        }
    });

    $("#removeSelectedDevices").on("click", function() {
        var checkedBoxes = $(".assigned-checkbox:checked");
        var removed = false;
        var devicesToRemove = [];

        checkedBoxes.each(function() {
            var deviceId = Number($(this).val());
            devicesToRemove.push(deviceId);
            removed = true;
        });

        if (removed) {
            if (confirm("<?php echo __('Удалить выбранные устройства?'); ?>")) {
                $.ajax({
                    url: "<?php echo URL::site('devgroup/removeDevices'); ?>",
                    type: "POST",
                    data: {
                        group_id: Number(<?php echo json_encode($group['id_devgroup']); ?>),
                        devices: devicesToRemove
                    },
                    dataType: "json",
                    cache: false,
                    beforeSend: function() {
                        $("#removeSelectedDevices").prop("disabled", true).text("<?php echo __('Удаление...'); ?>");
                    },
                    success: function(response) {
                        if (response.success) {
                            for (var i = 0; i < devicesToRemove.length; i++) {
                                var index = selectedDevices.indexOf(devicesToRemove[i]);
                                if (index !== -1) {
                                    selectedDevices.splice(index, 1);
                                }
                            }
                            updateAssignedTable();
                            updateAvailableDevices();
                            // Обновляем компактный режим
                            updateCompactView();
                            alert("<?php echo __('Устройства успешно удалены'); ?>");
                        } else {
                            alert(response.error || "<?php echo __('Ошибка при удалении устройств'); ?>");
                        }
                    },
                    error: function() {
                        alert("<?php echo __('Ошибка при удалении устройств'); ?>");
                    },
                    complete: function() {
                        $("#removeSelectedDevices").prop("disabled", false).text("<?php echo __('Удалить'); ?>");
                    }
                });
            }
        } else {
            alert("<?php echo __('Не выбраны устройства для удаления'); ?>");
        }
    });

    $("#selectAllAssigned").on("change", function() {
        var isChecked = $(this).prop("checked");
        $(".assigned-checkbox").prop("checked", isChecked);
    });

    $(document).on("change", ".assigned-checkbox", function() {
        var total = $(".assigned-checkbox").length;
        var checked = $(".assigned-checkbox:checked").length;
        $("#selectAllAssigned").prop("checked", total === checked && total > 0);
    });

    // ========== Компактный режим ==========
    function updateCompactView() {
        // Обновляем чекбоксы в компактном режиме
        $('.device-checkbox').each(function() {
            var deviceId = Number($(this).val());
            var isInGroup = selectedDevices.indexOf(deviceId) !== -1;
            $(this).prop('checked', isInGroup);
            
            var $row = $(this).closest('tr');
            $row.data('in-group', isInGroup ? 1 : 0);
            
            var $label = $row.find('.label-success');
            if (isInGroup) {
                if ($label.length === 0) {
                    $row.find('td:eq(2)').append(' <span class="label label-success" style="margin-left: 5px;">в группе</span>');
                }
            } else {
                $label.remove();
            }
        });
        
        // Обновляем счетчик
        updateSelectedCount();
        
        // Обновляем состояние "Выбрать все"
        updateSelectAllState();
    }

    function updateSelectAllState() {
        var total = $('.device-checkbox').length;
        var checked = $('.device-checkbox:checked').length;
        var $selectAll = $('#selectAllCheckbox');
        
        if (total === 0) {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', false);
        } else if (checked === total) {
            $selectAll.prop('checked', true);
            $selectAll.prop('indeterminate', false);
        } else if (checked === 0) {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', false);
        } else {
            $selectAll.prop('checked', false);
            $selectAll.prop('indeterminate', true);
        }
    }

    function saveDevices() {
        var checkedDevices = [];
        $('.device-checkbox:checked').each(function() {
            checkedDevices.push(Number($(this).val()));
        });
        
        // Определяем, какие устройства нужно добавить, а какие удалить
        var toAdd = [];
        var toRemove = [];
        
        // Устройства, которые отмечены, но не были в группе
        $.each(checkedDevices, function(i, id) {
            if ($.inArray(id, selectedDevices) === -1) {
                toAdd.push(id);
            }
        });
        
        // Устройства, которые были в группе, но теперь не отмечены
        $.each(selectedDevices, function(i, id) {
            if ($.inArray(id, checkedDevices) === -1) {
                toRemove.push(id);
            }
        });
        
        if (toAdd.length === 0 && toRemove.length === 0) {
            showNotification('Изменений не обнаружено', 'info');
            return;
        }
        
        $('#saveDevicesBtn').prop('disabled', true).html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Сохранение...');
        
        var completed = 0;
        var totalOperations = (toAdd.length > 0 ? 1 : 0) + (toRemove.length > 0 ? 1 : 0);
        var errors = [];
        
        function checkComplete() {
            completed++;
            if (completed === totalOperations) {
                if (errors.length === 0) {
                    // Обновляем selectedDevices
                    selectedDevices = checkedDevices.slice();
                    
                    // Обновляем оба режима
                    updateAssignedTable();
                    updateAvailableDevices();
                    updateCompactView();
                    
                    showNotification('Изменения успешно сохранены', 'success');
                } else {
                    showNotification('Ошибка при сохранении: ' + errors.join(', '), 'danger');
                }
                
                $('#saveDevicesBtn').prop('disabled', false).html('<span class="glyphicon glyphicon-floppy-save"></span> Сохранить');
            }
        }
        
        if (toAdd.length > 0) {
            $.ajax({
                url: "<?php echo URL::site('devgroup/addDevices'); ?>",
                type: "POST",
                data: {
                    group_id: groupId,
                    devices: toAdd
                },
                dataType: "json",
                cache: false,
                success: function(response) {
                    if (!response.success) {
                        errors.push('Ошибка добавления устройств');
                    }
                    checkComplete();
                },
                error: function() {
                    errors.push('Ошибка добавления устройств');
                    checkComplete();
                }
            });
        } else {
            checkComplete();
        }
        
        if (toRemove.length > 0) {
            $.ajax({
                url: "<?php echo URL::site('devgroup/removeDevices'); ?>",
                type: "POST",
                data: {
                    group_id: groupId,
                    devices: toRemove
                },
                dataType: "json",
                cache: false,
                success: function(response) {
                    if (!response.success) {
                        errors.push('Ошибка удаления устройств');
                    }
                    checkComplete();
                },
                error: function() {
                    errors.push('Ошибка удаления устройств');
                    checkComplete();
                }
            });
        } else {
            checkComplete();
        }
    }

    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : (type === 'danger' ? 'alert-danger' : 'alert-info');
        var $alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade in">' +
            '<button type="button" class="close" data-dismiss="alert">×</button>' +
            message +
            '</div>');
        
        $('.alert-dismissible').not('.alert-info').remove();
        $alert.insertAfter('.panel-heading');
        
        setTimeout(function() {
            $alert.alert('close');
        }, 5000);
    }

    // События компактного режима
    $(document).on('change', '.device-checkbox', function() {
        updateSelectedCount();
        updateSelectAllState();
    });
    
    $('#selectAllBtn').on('click', function() {
        $('.device-checkbox').prop('checked', true);
        updateSelectedCount();
        updateSelectAllState();
    });
    
    $('#deselectAllBtn').on('click', function() {
        $('.device-checkbox').prop('checked', false);
        updateSelectedCount();
        updateSelectAllState();
    });
    
    $('#selectAllCheckbox').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.device-checkbox').prop('checked', isChecked);
        updateSelectedCount();
        updateSelectAllState();
    });
    
    $('#saveDevicesBtn').on('click', function() {
        saveDevices();
    });

    // ========== Инициализация ==========
    updateAvailableDevices();
    updateAssignedTable();
    updateSelectedCount();
    updateSelectAllState();
    
    <?php else: ?>
    // Для не-администраторов
    var count = $('.device-checkbox:checked').length;
    $('#selectedCount').text(count);
    <?php endif; ?>
});
</script>

<style>
.glyphicon-spin {
    animation: spin 1s infinite linear;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.table-responsive .table > tbody > tr > td {
    vertical-align: middle;
}
.table-responsive .table > tbody > tr > td:first-child {
    text-align: center;
}
.table-responsive .table > tbody > tr:hover {
    background-color: #f5f5f5;
}
.table-responsive .table > tbody > tr .label {
    font-size: 10px;
}
.alert-info .btn-sm {
    margin-top: -3px;
}
.panel-heading .btn-group .btn.active {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}
.panel-heading .btn-group .btn {
    font-size: 11px;
    padding: 3px 8px;
}
</style>