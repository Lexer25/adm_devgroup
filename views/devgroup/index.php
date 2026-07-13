<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <span class="glyphicon glyphicon-tree-deciduous"></span>
            Группы устройств
        </h3>
    </div>
    <div class="panel-body">

        <!-- Кнопка добавления -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-xs-12">
                <?php if ($is_admin): ?>
                    <a href="<?php echo URL::site('devgroup/add'); ?>" class="btn btn-success">
                        <span class="glyphicon glyphicon-plus"></span> Добавить группу
                    </a>
                <?php endif; ?>
                <button type="button" id="expandAllBtn" class="btn btn-default" title="Развернуть все">
                    <span class="glyphicon glyphicon-plus-sign"></span> Развернуть
                </button>
                <button type="button" id="collapseAllBtn" class="btn btn-default" title="Свернуть все">
                    <span class="glyphicon glyphicon-minus-sign"></span> Свернуть
                </button>
                <button type="button" id="refreshTreeBtn" class="btn btn-default" title="Обновить">
                    <span class="glyphicon glyphicon-refresh"></span>
                </button>
                <div class="pull-right" style="width: 250px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                        <input type="text" id="treeSearch" class="form-control" placeholder="Поиск...">
                        <span class="input-group-btn">
                            <button id="clearSearch" class="btn btn-default" type="button">
                                <span class="glyphicon glyphicon-remove"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Сообщения -->
        <?php
        $message = Session::instance()->get_once('message');
        $message_type = Session::instance()->get_once('message_type', 'info');
        if ($message):
        ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade in">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Контейнер дерева -->
        <div class="tree-container explorer-tree" id="treeContainer">
            <ul class="tree" id="groupTree">
                <?php if (!empty($rootGroups)): ?>
                    <?php foreach ($rootGroups as $group): ?>
                        <?php 
                        // Определяем иконку и класс в зависимости от наличия дочерних папок и устройств
                        if ($group['child_count'] > 0) {
                            $folderIcon = 'glyphicon-folder-open';
                            $iconClass = 'folder-with-children';
                            $iconTitle = 'Есть дочерние папки';
                        } elseif ($group['device_count'] > 0) {
                            $folderIcon = 'glyphicon-folder-close';
                            $iconClass = 'folder-with-devices';
                            $iconTitle = 'Есть устройства (' . $group['device_count'] . ' шт.)';
                        } else {
                            $folderIcon = 'glyphicon-folder-close';
                            $iconClass = 'folder-empty';
                            $iconTitle = 'Пустая папка';
                        }
                        ?>
                        <li class="tree-group" data-group-id="<?php echo $group['id_devgroup']; ?>" data-group-name="<?php echo strtolower(htmlspecialchars($group['name'])); ?>">
                            <div class="tree-node tree-node-group" data-has-children="<?php echo ($group['child_count'] > 0) ? 'true' : 'false'; ?>" data-has-devices="<?php echo ($group['device_count'] > 0) ? 'true' : 'false'; ?>">
                                <?php if ($group['child_count'] > 0): ?>
                                    <span class="tree-toggle"><span class="glyphicon glyphicon-chevron-right"></span></span>
                                <?php else: ?>
                                    <span class="tree-toggle-placeholder"></span>
                                <?php endif; ?>
                                <span class="tree-icon <?php echo $iconClass; ?>" title="<?php echo $iconTitle; ?>">
                                    <span class="glyphicon <?php echo $folderIcon; ?>"></span>
                                </span>
                                <span class="tree-label"><?php echo htmlspecialchars($group['name']) .'.('. $group['id_devgroup'].')'; ?></span>
                                <?php if ($group['device_count'] > 0): ?>
                                    <span class="tree-badge device-count"><?php echo $group['device_count']; ?></span>
                                <?php endif; ?>
                                <div class="tree-actions">
                                    <a href="<?php echo URL::site('devgroup/edit/' . $group['id_devgroup']); ?>" class="action-btn" title="Редактировать">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <?php if ($is_admin): ?>
                                        <a href="<?php echo URL::site('devgroup/delete/' . $group['id_devgroup']); ?>" class="action-btn" onclick="return confirm('Удалить группу и все вложенные элементы?')" title="Удалить">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <ul class="tree-children" style="display: none;"></ul>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="tree-empty">
                        <div class="tree-node tree-node-empty">
                            <span class="tree-label-empty">Нет групп</span>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="tree-status">
            <span class="glyphicon glyphicon-dashboard"></span> Групп: <strong id="totalGroups"><?php echo count($rootGroups); ?></strong>
            <span id="loadingStatus" style="display: none; margin-left: 15px;">
                <span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Загрузка...
            </span>
            <span id="searchInfo" style="display: none;"> | <span class="glyphicon glyphicon-search"></span> Найдено: <strong id="searchResultsCount">0</strong></span>
        </div>

        <!-- Легенда -->
        <div class="tree-legend" style="margin-top: 10px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 3px; font-size: 11px; color: #666; display: flex; gap: 20px; flex-wrap: wrap; align-items: center;">
            <span><span class="glyphicon glyphicon-folder-open" style="color: #f39c12;"></span> Есть дочерние папки</span>
            <span><span class="glyphicon glyphicon-folder-close" style="color: #27ae60;"></span> Есть устройства</span>
            <span><span class="glyphicon glyphicon-folder-close" style="color: #bdc3c7;"></span> Пустая папка</span>
            <span style="margin-left: auto; color: #999;">
                <span class="glyphicon glyphicon-tower" style="color: #3498db;"></span> Устройство
            </span>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Функция для обновления иконки папки
    function updateFolderIcon($node, hasChildren, hasDevices, deviceCount) {
        var $icon = $node.find('.tree-icon .glyphicon');
        var $iconContainer = $node.find('.tree-icon');
        var $badge = $node.find('.tree-badge.device-count');
        
        // Удаляем все классы иконок
        $iconContainer.removeClass('folder-with-children folder-with-devices folder-empty');
        
        if (hasChildren) {
            $icon.removeClass('glyphicon-folder-close').addClass('glyphicon-folder-open');
            $iconContainer.addClass('folder-with-children');
            $iconContainer.attr('title', 'Есть дочерние папки');
        } else if (hasDevices) {
            $icon.removeClass('glyphicon-folder-open').addClass('glyphicon-folder-close');
            $iconContainer.addClass('folder-with-devices');
            $iconContainer.attr('title', 'Есть устройства (' + deviceCount + ' шт.)');
            
            // Добавляем/обновляем бейдж с количеством
            if ($badge.length === 0 && deviceCount > 0) {
                $node.find('.tree-label').after('<span class="tree-badge device-count">' + deviceCount + '</span>');
            } else if ($badge.length > 0 && deviceCount > 0) {
                $badge.text(deviceCount);
            }
        } else {
            $icon.removeClass('glyphicon-folder-open').addClass('glyphicon-folder-close');
            $iconContainer.addClass('folder-empty');
            $iconContainer.attr('title', 'Пустая папка');
            
            // Удаляем бейдж если он есть
            if ($badge.length > 0) {
                $badge.remove();
            }
        }
    }

    // Загрузка дочерних элементов
    function loadChildren($li) {
        var groupId = $li.data('group-id');
        var $childrenContainer = $li.children('ul.tree-children');
        var $node = $li.children('.tree-node-group');
        var hasChildren = $node.data('has-children');
        
        // Проверяем, загружены ли уже данные
        if ($childrenContainer.data('loaded')) {
            // Переключаем видимость
            if ($childrenContainer.is(':visible')) {
                $childrenContainer.slideUp(100);
                $node.find('.tree-toggle .glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
                // Восстанавливаем иконку в зависимости от данных
                var hasDevices = $node.data('has-devices') === 'true';
                var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
                updateFolderIcon($node, hasChildren === 'true', hasDevices, deviceCount);
            } else {
                $childrenContainer.slideDown(100);
                $node.find('.tree-toggle .glyphicon').removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
                // Открытая папка, так как есть дочерние элементы
                updateFolderIcon($node, true, false, 0);
            }
            return;
        }

        // Показываем индикатор загрузки
        $('#loadingStatus').show();
        $childrenContainer.html('<li><div class="tree-node" style="padding-left: 20px;"><span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Загрузка...</div></li>');
        $childrenContainer.slideDown(100);
        $node.find('.tree-toggle .glyphicon').removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        // Временно показываем открытую папку
        updateFolderIcon($node, true, false, 0);

        // AJAX запрос
        $.ajax({
            url: '<?php echo URL::site("devgroup/getChildren"); ?>/' + groupId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loadingStatus').hide();
                if (response.success && response.data) {
                    if (response.data.length === 0) {
                        $childrenContainer.html('<li class="tree-empty"><div class="tree-node tree-node-empty"><span class="tree-toggle-placeholder"></span><span class="tree-icon"><span class="glyphicon glyphicon-info-sign"></span></span><span class="tree-label-empty">Нет дочерних элементов</span></div></li>');
                        // Обновляем иконку - дочерних папок нет
                        var hasDevices = $node.data('has-devices') === 'true';
                        var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
                        updateFolderIcon($node, false, hasDevices, deviceCount);
                        $node.data('has-children', 'false');
                    } else {
                        var hasChildGroups = false;
                        var hasDevices = false;
                        var deviceCount = 0;
                        var html = '';
                        $.each(response.data, function(idx, item) {
                            if (item.is_group) {
                                hasChildGroups = true;
                                // Определяем иконку для дочерней группы
                                var childIcon;
                                var childIconClass;
                                var childIconTitle;
                                if (item.has_children) {
                                    childIcon = 'glyphicon-folder-open';
                                    childIconClass = 'folder-with-children';
                                    childIconTitle = 'Есть дочерние папки';
                                } else if (item.device_count > 0) {
                                    childIcon = 'glyphicon-folder-close';
                                    childIconClass = 'folder-with-devices';
                                    childIconTitle = 'Есть устройства (' + item.device_count + ' шт.)';
                                } else {
                                    childIcon = 'glyphicon-folder-close';
                                    childIconClass = 'folder-empty';
                                    childIconTitle = 'Пустая папка';
                                }
                                // Группа
                                html += '<li class="tree-group" data-group-id="' + item.id + '" data-group-name="' + item.name.toLowerCase() + '">';
                                html += '<div class="tree-node tree-node-group" data-has-children="' + (item.has_children ? 'true' : 'false') + '" data-has-devices="' + (item.device_count > 0 ? 'true' : 'false') + '">';
                                if (item.has_children) {
                                    html += '<span class="tree-toggle"><span class="glyphicon glyphicon-chevron-right"></span></span>';
                                } else {
                                    html += '<span class="tree-toggle-placeholder"></span>';
                                }
                                html += '<span class="tree-icon ' + childIconClass + '" title="' + childIconTitle + '"><span class="glyphicon ' + childIcon + '"></span></span>';
                                html += '<span class="tree-label">' + escapeHtml(item.name) + '</span>';
                                if (item.device_count > 0) {
                                    html += '<span class="tree-badge device-count">' + item.device_count + '</span>';
                                }
                                html += '<div class="tree-actions">';
                                html += '<a href="<?php echo URL::site("devgroup/edit"); ?>/' + item.id + '" class="action-btn" title="Редактировать"><span class="glyphicon glyphicon-edit"></span></a>';
                                <?php if ($is_admin): ?>
                                html += '<a href="<?php echo URL::site("devgroup/delete"); ?>/' + item.id + '" class="action-btn" onclick="return confirm(\'Удалить группу и все вложенные элементы?\')" title="Удалить"><span class="glyphicon glyphicon-trash"></span></a>';
                                <?php endif; ?>
                                html += '</div>';
                                html += '</div>';
                                html += '<ul class="tree-children" style="display: none;"></ul>';
                                html += '</li>';
                            } else {
                                // Устройство
                                hasDevices = true;
                                deviceCount++;
                                html += '<li class="tree-device">';
                                html += '<div class="tree-node tree-node-device">';
                                html += '<span class="tree-toggle-placeholder"></span>';
                                html += '<span class="tree-icon"><span class="glyphicon glyphicon-tower"></span></span>';
                                html += '<span class="tree-label">' + escapeHtml(item.name) + ' (ID: ' + item.id + ')</span>';
                                html += '</div></li>';
                            }
                        });
                        $childrenContainer.html(html);
                        
                        // Обновляем иконку текущей папки
                        if (hasChildGroups) {
                            updateFolderIcon($node, true, false, 0);
                            $node.data('has-children', 'true');
                            $node.data('has-devices', 'false');
                        } else if (hasDevices) {
                            updateFolderIcon($node, false, true, deviceCount);
                            $node.data('has-children', 'false');
                            $node.data('has-devices', 'true');
                        } else {
                            updateFolderIcon($node, false, false, 0);
                            $node.data('has-children', 'false');
                            $node.data('has-devices', 'false');
                        }
                    }
                    $childrenContainer.data('loaded', true);
                    updateTotalGroups();
                } else {
                    $childrenContainer.html('<li><div class="tree-node tree-node-empty"><span class="tree-label-empty">Ошибка загрузки</span></div></li>');
                    var hasDevices = $node.data('has-devices') === 'true';
                    var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
                    updateFolderIcon($node, false, hasDevices, deviceCount);
                }
            },
            error: function() {
                $('#loadingStatus').hide();
                $childrenContainer.html('<li><div class="tree-node tree-node-empty"><span class="tree-label-empty">Ошибка загрузки</span></div></li>');
                var hasDevices = $node.data('has-devices') === 'true';
                var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
                updateFolderIcon($node, false, hasDevices, deviceCount);
            }
        });
    }

    // Клик по группе
    $(document).on('click', '.tree-node-group', function(e) {
        if ($(e.target).closest('.tree-actions, .action-btn, a').length) return;
        var $li = $(this).closest('.tree-group');
        var hasChildren = $(this).data('has-children');
        
        if (hasChildren === 'true' || hasChildren === true) {
            loadChildren($li);
        } else {
            // Если нет дочерних групп, просто показываем/скрываем устройства
            var $childrenContainer = $li.children('ul.tree-children');
            var $node = $(this);
            var hasDevices = $node.data('has-devices') === 'true';
            var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
            
            if ($childrenContainer.data('loaded')) {
                if ($childrenContainer.is(':visible')) {
                    $childrenContainer.slideUp(100);
                    // Восстанавливаем иконку
                    updateFolderIcon($node, false, hasDevices, deviceCount);
                } else {
                    $childrenContainer.slideDown(100);
                    // Если есть устройства, показываем иконку с устройствами
                    if (hasDevices) {
                        updateFolderIcon($node, false, true, deviceCount);
                    }
                }
            } else {
                // Загружаем устройства
                loadDevices($li);
            }
        }
    });

    // Загрузка устройств для группы без дочерних групп
    function loadDevices($li) {
        var groupId = $li.data('group-id');
        var $childrenContainer = $li.children('ul.tree-children');
        var $node = $li.children('.tree-node-group');
        
        if ($childrenContainer.data('loaded')) {
            return;
        }

        $('#loadingStatus').show();
        $childrenContainer.html('<li><div class="tree-node" style="padding-left: 20px;"><span class="glyphicon glyphicon-refresh glyphicon-spin"></span> Загрузка устройств...</div></li>');
        $childrenContainer.slideDown(100);
        // Показываем иконку с устройствами во время загрузки
        updateFolderIcon($node, false, true, 0);

        $.ajax({
            url: '<?php echo URL::site("devgroup/getDevices"); ?>/' + groupId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loadingStatus').hide();
                if (response.success && response.data) {
                    if (response.data.length === 0) {
                        $childrenContainer.html('<li class="tree-empty"><div class="tree-node tree-node-empty"><span class="tree-toggle-placeholder"></span><span class="tree-icon"><span class="glyphicon glyphicon-info-sign"></span></span><span class="tree-label-empty">Нет устройств</span></div></li>');
                        // Нет устройств - пустая папка
                        updateFolderIcon($node, false, false, 0);
                        $node.data('has-devices', 'false');
                    } else {
                        var html = '';
                        var deviceCount = 0;
                        $.each(response.data, function(idx, device) {
                            deviceCount++;
                            html += '<li class="tree-device">';
                            html += '<div class="tree-node tree-node-device">';
                            html += '<span class="tree-toggle-placeholder"></span>';
                            html += '<span class="tree-icon"><span class="glyphicon glyphicon-tower"></span></span>';
                            html += '<span class="tree-label">' + escapeHtml(device.name) + ' (ID: ' + device.id + ')</span>';
                            html += '</div></li>';
                        });
                        $childrenContainer.html(html);
                        // Есть устройства - иконка с устройствами
                        updateFolderIcon($node, false, true, deviceCount);
                        $node.data('has-devices', 'true');
                    }
                    $childrenContainer.data('loaded', true);
                } else {
                    $childrenContainer.html('<li><div class="tree-node tree-node-empty"><span class="tree-label-empty">Ошибка загрузки</span></div></li>');
                    updateFolderIcon($node, false, false, 0);
                }
            },
            error: function() {
                $('#loadingStatus').hide();
                $childrenContainer.html('<li><div class="tree-node tree-node-empty"><span class="tree-label-empty">Ошибка загрузки</span></div></li>');
                updateFolderIcon($node, false, false, 0);
            }
        });
    }

    // Развернуть все
    $('#expandAllBtn').on('click', function() {
        $('.tree-group').each(function() {
            var $this = $(this);
            var $node = $this.children('.tree-node-group');
            var hasChildren = $node.data('has-children');
            if (hasChildren === 'true' || hasChildren === true) {
                var $children = $this.children('ul.tree-children');
                if (!$children.data('loaded')) {
                    loadChildren($this);
                } else {
                    $children.slideDown(100);
                    $node.find('.tree-toggle .glyphicon')
                        .removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
                    updateFolderIcon($node, true, false, 0);
                }
            } else {
                // Если нет дочерних папок, но есть устройства, показываем их
                var $children = $this.children('ul.tree-children');
                var hasDevices = $node.data('has-devices') === 'true';
                var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
                if (hasDevices && !$children.data('loaded')) {
                    loadDevices($this);
                } else if (hasDevices) {
                    $children.slideDown(100);
                    updateFolderIcon($node, false, true, deviceCount);
                }
            }
        });
    });

    // Свернуть все
    $('#collapseAllBtn').on('click', function() {
        $('.tree-children').slideUp(100);
        $('.tree-node-group').find('.tree-toggle .glyphicon')
            .removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        // Восстанавливаем иконки
        $('.tree-node-group').each(function() {
            var $node = $(this);
            var hasChildren = $node.data('has-children') === 'true';
            var hasDevices = $node.data('has-devices') === 'true';
            var deviceCount = parseInt($node.find('.tree-badge.device-count').text()) || 0;
            updateFolderIcon($node, hasChildren, hasDevices, deviceCount);
        });
    });

    // Обновить дерево
    $('#refreshTreeBtn').on('click', function() {
        location.reload();
    });

    // Подсчет групп
    function updateTotalGroups() {
        $('#totalGroups').text($('.tree-group').length);
    }

    // Поиск
    var searchTimeout;
    $('#treeSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        var term = $(this).val().trim().toLowerCase();
        searchTimeout = setTimeout(function() { performSearch(term); }, 300);
    });

    function performSearch(term) {
        $('.tree-node-group').removeClass('highlight');
        $('.tree-node-device').removeClass('highlight');
        $('#searchInfo').hide();

        if (!term) return;

        var matches = 0;
        $('.tree-group').each(function() {
            var groupName = $(this).data('group-name') || '';
            var $node = $(this).children('.tree-node-group');
            if (groupName.indexOf(term) !== -1) {
                $node.addClass('highlight');
                matches++;
                // Разворачиваем родительские группы
                $(this).parents('li').children('.tree-children').slideDown(100);
                $(this).parents('li').children('.tree-node-group').find('.tree-toggle .glyphicon')
                    .removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
                // Открываем иконки у родителей
                $(this).parents('li').children('.tree-node-group').each(function() {
                    updateFolderIcon($(this), true, false, 0);
                });
            }
        });

        // Поиск по устройствам (если они уже загружены)
        $('.tree-device').each(function() {
            var deviceName = $(this).find('.tree-label').text().toLowerCase();
            if (deviceName.indexOf(term) !== -1) {
                $(this).children('.tree-node-device').addClass('highlight');
                matches++;
                $(this).parents('li').children('.tree-children').slideDown(100);
                $(this).parents('li').children('.tree-node-group').find('.tree-toggle .glyphicon')
                    .removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
                // Открываем иконки у родителей
                $(this).parents('li').children('.tree-node-group').each(function() {
                    updateFolderIcon($(this), true, false, 0);
                });
            }
        });

        if (matches) {
            $('#searchInfo').show();
            $('#searchResultsCount').text(matches);
        }
    }

    $('#clearSearch').on('click', function() {
        $('#treeSearch').val('');
        performSearch('');
    });

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    updateTotalGroups();
});
</script>

<style>
.explorer-tree { background: #fff; border: 1px solid #ddd; border-radius: 3px; font-size: 12px; max-height: 500px; overflow-y: auto; }
.explorer-tree .tree { margin: 0; padding: 2px 0; list-style: none; }
.explorer-tree .tree ul { margin: 0; padding-left: 18px; list-style: none; }
.explorer-tree .tree li { margin: 0; padding: 0; }
.explorer-tree .tree-node { display: flex; align-items: center; padding: 2px 4px 2px 0; cursor: pointer; border-radius: 2px; }
.explorer-tree .tree-toggle { width: 16px; margin-right: 2px; text-align: center; cursor: pointer; }
.explorer-tree .tree-toggle .glyphicon { font-size: 10px; color: #888; transition: transform 0.1s; }
.explorer-tree .tree-toggle-placeholder { width: 16px; margin-right: 2px; display: inline-block; }
.explorer-tree .tree-icon { width: 18px; margin-right: 4px; text-align: center; }
.explorer-tree .tree-icon .glyphicon { font-size: 12px; }

/* Новая цветовая палитра */
.tree-icon.folder-with-children .glyphicon { 
    color: #f39c12; /* Оранжевый/золотой - есть дочерние папки */
}
.tree-icon.folder-with-devices .glyphicon { 
    color: #27ae60; /* Зеленый - есть устройства */
}
.tree-icon.folder-empty .glyphicon { 
    color: #bdc3c7; /* Светло-серый - пустая папка */
}

/* Иконка устройства - синяя, чтобы отличаться от зеленой папки с устройствами */
.tree-node-device .tree-icon .glyphicon { 
    color: #3498db; /* Синий для устройств */
}

.explorer-tree .tree-label { flex: 0 1 auto; margin-right: 8px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.explorer-tree .tree-label-empty { color: #999; font-style: italic; }
.explorer-tree .tree-badge { display: inline-block; min-width: 18px; padding: 0 5px; margin-left: 8px; background: #e0e0e0; color: #666; font-size: 10px; border-radius: 10px; }
.explorer-tree .tree-badge.device-count { 
    background: #27ae60; 
    color: #fff; 
    font-weight: bold;
}
.explorer-tree .tree-actions { display: flex; gap: 2px; margin-left: 0; }
.explorer-tree .action-btn { padding: 2px 4px; color: #666; font-size: 11px; border-radius: 3px; }
.explorer-tree .action-btn:hover { background: #e0e0e0; color: #333; text-decoration: none; }
.explorer-tree .tree-node:hover { background-color: #e8f0fe; }
.tree-node-group:hover { background-color: #fdf5e6; }
.explorer-tree .tree-node.highlight { background-color: #fff3cd; }
.tree-status { margin-top: 8px; padding: 5px 10px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 3px; font-size: 11px; color: #666; }
.tree-empty .tree-node { cursor: default; }
.tree-empty .tree-node:hover { background-color: transparent; }
.glyphicon-spin { animation: spin 1s infinite linear; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

.tree-legend {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: center;
}
.tree-legend span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.tree-legend .glyphicon-folder-open { color: #f39c12; }
.tree-legend .glyphicon-folder-close.folder-with-devices-legend { color: #27ae60; }
.tree-legend .glyphicon-folder-close.folder-empty-legend { color: #bdc3c7; }
.tree-legend .glyphicon-tower { color: #3498db; }
</style>