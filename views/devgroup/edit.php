<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Редактирование группы устройств'); ?></h3>
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
                            <label><?php echo __('Устройств в группе'); ?>:</label>
                            <h3><span id="selectedCount" class="label label-primary"><?php echo count($groupDevices); ?></span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
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
    var selectedDevices = [];

    <?php foreach ($groupDevices as $device): ?>
        selectedDevices.push(Number(<?php echo json_encode($device['id_dev']); ?>));
    <?php endforeach; ?>

    function updateSelectedCount() {
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

    <?php if ($is_admin): ?>
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
    <?php endif; ?>

    updateAvailableDevices();
    updateAssignedTable();
    updateSelectedCount();
});
</script>