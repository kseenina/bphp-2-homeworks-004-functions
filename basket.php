<?php
declare(strict_types=1);

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_EDIT = 2;
const OPERATION_DELETE = 3;
const OPERATION_PRINT = 4;

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_EDIT => OPERATION_EDIT . '. Отредактировать товар в списке покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
];

$items = [];

function clearScreen(): void {
    system(PHP_OS_FAMILY === 'Windows' ? 'cls' : 'clear');
}

function listDisplay(array $items): void {
     if (count($items)) {
        echo 'Ваш список покупок: ' . PHP_EOL;
        foreach($items as $item) {
            echo $item['name'] . ' - ' . $item['quantity'] . ' шт.' . "\n";
        }
    } else {
        echo 'Ваш список покупок пуст.' . PHP_EOL;
    }
}

function operationDisplay(array $items, array $operations): int {
    clearScreen();
    do {
        listDisplay($items);
        echo 'Выберите операцию для выполнения: ' . PHP_EOL;
        $availableOperations = $operations;
        if (!count($items)) {
            unset($availableOperations[OPERATION_DELETE]);
            unset($availableOperations[OPERATION_EDIT]);
        }
        echo implode(PHP_EOL, $availableOperations) . PHP_EOL . '> ';
        $operationNumber = (int) trim(fgets(STDIN));

        if (!array_key_exists($operationNumber, $operations)) {
            clearScreen();
            echo '!!! Неизвестный номер операции, повторите попытку.' . PHP_EOL;
        }

    } while (!array_key_exists($operationNumber, $operations));

    return $operationNumber;
}

function operationAdd(array &$items): void {
    echo "Введите название товара для добавления в список: \n> ";
    $name = trim(fgets(STDIN));
    if ($name === '') {
        echo "Название не должно быть пустым, пожалуйста, попробуйте еще раз! \n";
        return;
    }
    echo "Введите количество: \n> ";
    $quantity = (int) trim(fgets(STDIN));
    if ($quantity <= 0) {
        echo "Количество должно быть больше нуля, пожалуйста, попробуйте еще раз! \n";
        return;
    }
    $items[] = [
        'name' => $name,
        'quantity' => $quantity,
    ];
    echo "Товар успешно добавлен. \n";
}

function operationEdit(array &$items): void {
    listDisplay($items);
    if (!count($items)) {
        echo "Нет товаров, доступных для редактирования. Пожалуйста, введите номер другой операции. \n";
        return;
    }
    echo 'Введите название товара из списка для редактирования: ' . PHP_EOL . '> ';
    $nameOld = trim(fgets(STDIN));
    $key = array_search($nameOld, array_column($items, 'name'), true);
    if ($key === false) {
        echo 'Такого товара нет в списке, пожалуйста, попробуйте снова.' . PHP_EOL;
        return;
    }
    $quantityOld = $items[$key]['quantity'];
    echo 'Введите новое название товара или оставьте пустым, чтобы оставить неизменным: ' . PHP_EOL . '> ';
    $nameNew = trim(fgets(STDIN));
    if ($nameNew === '') {
        $nameNew = $nameOld;
    }
    echo 'Введите новое количество товара или оставьте пустым, чтобы оставить неизменным: ' . PHP_EOL . '> ';
    $quantityNew = trim(fgets(STDIN));
    if ($quantityNew === '') {
        $quantityNew = $quantityOld;
    } else {
        $quantityNew = (int) $quantityNew;
    }
    if ($quantityNew <= 0) {
        echo "Количество должно быть больше нуля, пожалуйста, попробуйте еще раз! \n";
        return;
    }
    $items[$key]['name'] = $nameNew;
    $items[$key]['quantity'] = $quantityNew;
    echo "Товар успешно изменен. \n";
}

function operationDelete(array &$items): void {
    listDisplay($items);
    if (!count($items)) {
        echo "Нет товаров, доступных для удаления. Пожалуйста, введите номер другой операции. \n";
        return;
    }
    echo 'Введите название товара для удаления из списка:' . PHP_EOL . '> ';
    $itemName = trim(fgets(STDIN));
    $deleted = false;
    while (($key = array_search($itemName, array_column($items, 'name'), true)) !== false) {
        unset($items[$key]);
        $deleted = true;
    }
    if ($deleted === true) {
        echo "Товар удален. \n";
    } else {
        echo "Такого товара нет в списке, пожалуйста, попробуйте снова. \n";
    }
}

function operationPrint(array $items): void {
    listDisplay($items);
    echo 'Всего ' . count($items) . ' позиций. '. PHP_EOL;
    echo 'Нажмите Enter для продолжения...' . PHP_EOL;
    fgets(STDIN);
}

do {
    $operationNumber = operationDisplay($items, $operations);

    echo 'Выбрана операция: '  . $operations[$operationNumber] . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            operationAdd($items);
            break;

        case OPERATION_EDIT:
            operationEdit($items);
            break;

        case OPERATION_DELETE:
            operationDelete($items);
            break;

        case OPERATION_PRINT:
            operationPrint($items);
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber > 0);

echo 'Программа завершена' . PHP_EOL;