<?php
declare(strict_types=1);

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_PRINT = 3;

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
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
        echo implode("\n", $items) . "\n";
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
    $itemName = trim(fgets(STDIN));
    $items[] = $itemName;
}

function operationDelete(array &$items): void {
    listDisplay($items);
    if (!count($items)) {
        echo 'Нет товаров, доступных для удаления. Пожалуйста, введите номер другой операции.';
    } else {
        echo 'Введите название товара для удаления из списка:' . PHP_EOL . '> ';
        $itemName = trim(fgets(STDIN));
        while (($key = array_search($itemName, $items, true)) !== false) {
            unset($items[$key]);
        }
    }
}

function operationPrint(array $items): void {
    listDisplay($items);
            echo 'Всего ' . count($items) . ' позиций. '. PHP_EOL;
            echo 'Нажмите Enter для продолжения';
            fgets(STDIN);
}

do {
    $operationNumber = operationDisplay($items, $operations);

    echo 'Выбрана операция: '  . $operations[$operationNumber] . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            operationAdd($items);
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