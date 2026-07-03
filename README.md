# devgroup

Модуль для управления группами устройств в Kohana 3.3.

## Функциональность

- Отображение текущих настроек групп устройств
- Изменение и сохранение настроек
- Удаление групп устройств
- Создание групп устройств
- Три режима отображения: таблица, дерево, матрица
- Древовидная структура групп

## Установка

1. Скопируйте модуль в `modules/devgroup`
2. Добавьте в `bootstrap.php`: `'devgroup' => MODPATH.'devgroup'`
3. Выполните SQL-скрипт для создания таблицы:

```sql
CREATE TABLE DEVGROUP (
    ID_DEVGROUP  INTEGER NOT NULL,
    ID_DB        INTEGER DEFAULT 1 NOT NULL,
    ID_DEV       INTEGER,
    NAME         VARCHAR(50),
    ID_PARENT    INTEGER DEFAULT 1 NOT NULL
);