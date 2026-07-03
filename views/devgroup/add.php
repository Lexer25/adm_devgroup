<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Добавление группы устройств'); ?></h3>
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

        <?php if ($is_admin): ?>
            <form method="POST" action="<?php echo URL::site('devgroup/add'); ?>">
                <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name"><?php echo __('Название группы'); ?> *</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?php echo isset($post['name']) ? htmlspecialchars($post['name']) : ''; ?>"
                           required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="help-block"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="id_parent"><?php echo __('Родительская группа'); ?></label>
                    <select class="form-control" id="id_parent" name="id_parent">
                        <option value="1">Корневая группа</option>
                        <?php foreach ($parents as $id => $name): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($post['id_parent']) && $post['id_parent'] == $id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_db"><?php echo __('ID_DB'); ?></label>
                    <input type="number" class="form-control" id="id_db" name="id_db"
                           value="<?php echo isset($post['id_db']) ? intval($post['id_db']) : 1; ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?php echo __('Добавить'); ?></button>
                    <a href="<?php echo URL::site('devgroup'); ?>" class="btn btn-default"><?php echo __('Отмена'); ?></a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <span class="glyphicon glyphicon-lock" style="font-size: 48px; display: block; margin-bottom: 15px;"></span>
                <h4><?php echo __('Доступ запрещен'); ?></h4>
                <p><?php echo __('Только администраторы могут добавлять группы устройств.'); ?></p>
                <a href="<?php echo URL::site('devgroup'); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> <?php echo __('Вернуться к списку'); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div><div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Добавление группы устройств'); ?></h3>
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

        <?php if ($is_admin): ?>
            <form method="POST" action="<?php echo URL::site('devgroup/add'); ?>">
                <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name"><?php echo __('Название группы'); ?> *</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?php echo isset($post['name']) ? htmlspecialchars($post['name']) : ''; ?>"
                           required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="help-block"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="id_parent"><?php echo __('Родительская группа'); ?></label>
                    <select class="form-control" id="id_parent" name="id_parent">
                        <option value="1">Корневая группа</option>
                        <?php foreach ($parents as $id => $name): ?>
                            <option value="<?php echo $id; ?>" <?php echo (isset($post['id_parent']) && $post['id_parent'] == $id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_db"><?php echo __('ID_DB'); ?></label>
                    <input type="number" class="form-control" id="id_db" name="id_db"
                           value="<?php echo isset($post['id_db']) ? intval($post['id_db']) : 1; ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?php echo __('Добавить'); ?></button>
                    <a href="<?php echo URL::site('devgroup'); ?>" class="btn btn-default"><?php echo __('Отмена'); ?></a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <span class="glyphicon glyphicon-lock" style="font-size: 48px; display: block; margin-bottom: 15px;"></span>
                <h4><?php echo __('Доступ запрещен'); ?></h4>
                <p><?php echo __('Только администраторы могут добавлять группы устройств.'); ?></p>
                <a href="<?php echo URL::site('devgroup'); ?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-arrow-left"></span> <?php echo __('Вернуться к списку'); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>